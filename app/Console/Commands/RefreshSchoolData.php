<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Services\SchoolDataFetcherService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshSchoolData extends Command
{
    protected $signature = 'schools:refresh {--city-id= : Refresh specific city} {--force : Force refresh all schools} {--limit=10 : Limit number of cities to process}';
    protected $description = 'Refresh dynamic school data from live sources (Google Places, AI discovery, web scraping)';

    private $fetcher;

    public function __construct(SchoolDataFetcherService $fetcher)
    {
        parent::__construct();
        $this->fetcher = $fetcher;
    }

    public function handle()
    {
        $this->info('🔄 Starting school data refresh...');
        Log::info('School data refresh command initiated');

        try {
            if ($this->option('city-id')) {
                // Refresh specific city
                $city = City::with('state.country')->findOrFail($this->option('city-id'));
                $this->refreshCitySchools($city, $this->option('force'));
            } else {
                // Refresh cities with most views/needs
                $this->refreshAllCities();
            }

            $this->info('✅ School data refresh completed successfully');
            Log::info('School data refresh completed successfully');
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            Log::error('School data refresh failed', ['error' => $e->getMessage()]);
        }
    }

    private function refreshCitySchools($city, $force = false)
    {
        $this->info("Refreshing schools for: {$city->name}, {$city->state->name}");

        $result = $this->fetcher->fetchSchoolsForLocation($city, $force);

        $this->info("  ✓ Found: {$result['count']} schools");
        $this->info("  ✓ Saved/Updated: {$result['saved']} schools");
    }

    private function refreshAllCities()
    {
        $limit = $this->option('limit') ?? 10;

        // Get popular cities (by recommendation count or view count)
        $cities = City::query()
            ->leftJoin('dynamic_schools', 'cities.id', '=', 'dynamic_schools.city_id')
            ->select('cities.*')
            ->selectRaw('COUNT(dynamic_schools.id) as school_count')
            ->groupBy('cities.id', 'cities.name', 'cities.state_id', 'cities.created_at', 'cities.updated_at')
            ->orderByDesc('school_count')
            ->limit($limit)
            ->get();

        // Also add cities with outdated data
        \App\Models\DynamicSchool::query()
            ->where('last_refreshed_at', '<', now()->subDays(30))
            ->orWhereNull('last_refreshed_at')
            ->with('city.state.country')
            ->get()
            ->pluck('city')
            ->unique('id')
            ->each(function ($city) use ($cities) {
                if (!$cities->contains('id', $city->id)) {
                    $cities->push($city);
                }
            });

        // Limit to requested number
        $cities = $cities->take($limit);

        $this->info("Refreshing {$cities->count()} cities...\n");

        foreach ($cities as $city) {
            $this->line("📍 {$city->name}...");
            try {
                $this->refreshCitySchools($city);
            } catch (\Exception $e) {
                $this->warn("  ⚠ Failed: {$e->getMessage()}");
            }
        }
    }
}
