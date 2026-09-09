<?php

namespace App\Jobs;

use App\Models\City;
use App\Services\SchoolDataFetcherService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RefreshCitySchoolsData implements ShouldQueue
{
    use Queueable;

    private $city;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    public function handle(SchoolDataFetcherService $fetcher)
    {
        try {
            Log::info("Background job: Refreshing schools for city {$this->city->name}");

            $result = $fetcher->fetchSchoolsForLocation($this->city);

            Log::info("City {$this->city->name} schools refreshed", [
                'total' => $result['count'],
                'saved' => $result['saved'],
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to refresh schools for city {$this->city->name}: {$e->getMessage()}");
            $this->fail($e);
        }
    }
}
