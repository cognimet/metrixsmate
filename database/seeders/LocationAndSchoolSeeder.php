<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\School;

class LocationAndSchoolSeeder extends Seeder
{
    public function run(): void
    {
        // ── India ──
        $india = Country::create(['name' => 'India', 'code' => 'IN']);

        $states = [
            'Maharashtra' => [
                'Mumbai' => [
                    ['name' => 'Don Bosco High School', 'type' => 'general', 'board' => 'ICSE', 'rating' => 4.5, 'address' => 'Matunga, Mumbai', 'phone' => '+91-22-24143015', 'website' => 'https://www.donboscohs.com', 'strengths' => ['academics', 'sports', 'arts', 'leadership'], 'facilities' => ['library', 'labs', 'sports ground', 'auditorium', 'computer lab', 'music room']],
                    ['name' => 'Podar International School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.3, 'address' => 'Santacruz, Mumbai', 'phone' => '+91-22-26496242', 'website' => 'https://www.podarinternationalschool.com', 'strengths' => ['academics', 'technology', 'arts'], 'facilities' => ['library', 'labs', 'smart classrooms', 'sports', 'computer lab']],
                    ['name' => 'Jamnabai Narsee School', 'type' => 'general', 'board' => 'ICSE', 'rating' => 4.6, 'address' => 'Juhu, Mumbai', 'website' => 'https://www.jfrschool.org', 'strengths' => ['academics', 'arts', 'community', 'sports'], 'facilities' => ['library', 'labs', 'swimming pool', 'auditorium', 'art studio', 'gymnasium']],
                    ['name' => 'KJ Somaiya Vidyamandir', 'type' => 'science', 'board' => 'CBSE', 'rating' => 4.2, 'address' => 'Vidyanagar, Mumbai', 'strengths' => ['science', 'research', 'mathematics', 'technology'], 'facilities' => ['labs', 'library', 'computer center', 'research wing']],
                    ['name' => 'Sir JJ School of Art', 'type' => 'arts', 'board' => 'SSC', 'rating' => 4.4, 'address' => 'Fort, Mumbai', 'strengths' => ['arts', 'creative', 'design', 'music'], 'facilities' => ['art studio', 'gallery', 'workshop', 'library']],
                ],
                'Pune' => [
                    ['name' => 'The Bishops School', 'type' => 'general', 'board' => 'ICSE', 'rating' => 4.5, 'address' => 'Camp, Pune', 'website' => 'https://www.thebishopsschool.org', 'strengths' => ['academics', 'sports', 'leadership', 'community'], 'facilities' => ['library', 'labs', 'sports grounds', 'chapel', 'auditorium', 'gym']],
                    ['name' => 'Symbiosis International School', 'type' => 'general', 'board' => 'IB', 'rating' => 4.4, 'address' => 'Viman Nagar, Pune', 'strengths' => ['academics', 'business', 'languages', 'technology'], 'facilities' => ['library', 'labs', 'smart classrooms', 'sports', 'language lab']],
                    ['name' => 'MIT Junior College', 'type' => 'technical', 'board' => 'HSC', 'rating' => 4.1, 'address' => 'Kothrud, Pune', 'strengths' => ['technology', 'engineering', 'science', 'mathematics'], 'facilities' => ['labs', 'computer center', 'workshop', 'library']],
                ],
            ],
            'Karnataka' => [
                'Bangalore' => [
                    ['name' => 'Bishop Cotton Boys School', 'type' => 'general', 'board' => 'ICSE', 'rating' => 4.6, 'address' => 'St Marks Road, Bangalore', 'website' => 'https://www.bishopcottonsboys.edu.in', 'strengths' => ['academics', 'sports', 'leadership', 'community'], 'facilities' => ['library', 'labs', 'sports grounds', 'chapel', 'auditorium', 'swimming pool']],
                    ['name' => 'National Public School', 'type' => 'science', 'board' => 'CBSE', 'rating' => 4.5, 'address' => 'Indiranagar, Bangalore', 'strengths' => ['science', 'research', 'technology', 'mathematics'], 'facilities' => ['labs', 'library', 'computer center', 'smart classrooms', 'robotics lab']],
                    ['name' => 'Inventure Academy', 'type' => 'creative', 'board' => 'IGCSE', 'rating' => 4.3, 'address' => 'Whitefield, Bangalore', 'strengths' => ['creative', 'arts', 'entrepreneurship', 'technology'], 'facilities' => ['art studio', 'maker space', 'labs', 'library', 'theatre']],
                    ['name' => 'Delhi Public School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.4, 'address' => 'North Bangalore', 'strengths' => ['academics', 'sports', 'debate', 'leadership'], 'facilities' => ['library', 'labs', 'sports complex', 'auditorium', 'computer lab']],
                ],
            ],
            'Delhi' => [
                'New Delhi' => [
                    ['name' => 'Modern School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.7, 'address' => 'Barakhamba Road, New Delhi', 'website' => 'https://www.modernschool.net', 'strengths' => ['academics', 'sports', 'leadership', 'debate'], 'facilities' => ['library', 'labs', 'sports complex', 'auditorium', 'swimming pool', 'art studio']],
                    ['name' => 'Delhi Public School, RK Puram', 'type' => 'science', 'board' => 'CBSE', 'rating' => 4.8, 'address' => 'R K Puram, New Delhi', 'strengths' => ['science', 'research', 'technology', 'mathematics', 'analytics'], 'facilities' => ['labs', 'library', 'robotics center', 'computer lab', 'sports', 'auditorium']],
                    ['name' => 'Sanskriti School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.5, 'address' => 'Chanakyapuri, New Delhi', 'strengths' => ['academics', 'arts', 'community', 'humanities'], 'facilities' => ['library', 'labs', 'art studio', 'music room', 'sports ground']],
                    ['name' => 'Springdales School', 'type' => 'humanities', 'board' => 'CBSE', 'rating' => 4.4, 'address' => 'Pusa Road, New Delhi', 'strengths' => ['humanities', 'languages', 'social', 'community', 'arts'], 'facilities' => ['library', 'language lab', 'auditorium', 'sports', 'art gallery']],
                    ['name' => 'Amity International School', 'type' => 'business', 'board' => 'CBSE', 'rating' => 4.2, 'address' => 'Saket, New Delhi', 'strengths' => ['business', 'entrepreneurship', 'leadership', 'commerce', 'debate'], 'facilities' => ['library', 'seminar hall', 'computer lab', 'sports', 'business incubator']],
                ],
            ],
            'Tamil Nadu' => [
                'Chennai' => [
                    ['name' => 'Padma Seshadri Bala Bhavan', 'type' => 'science', 'board' => 'CBSE', 'rating' => 4.6, 'address' => 'Nungambakkam, Chennai', 'strengths' => ['science', 'mathematics', 'research', 'technology'], 'facilities' => ['labs', 'library', 'computer center', 'auditorium', 'sports ground']],
                    ['name' => 'Chettinad Vidyashram', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.5, 'address' => 'RA Puram, Chennai', 'strengths' => ['academics', 'arts', 'music', 'sports'], 'facilities' => ['library', 'labs', 'music room', 'art studio', 'sports complex', 'auditorium']],
                    ['name' => 'DAV Group of Schools', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.2, 'address' => 'Gopalapuram, Chennai', 'strengths' => ['academics', 'community', 'sports'], 'facilities' => ['library', 'labs', 'sports', 'computer lab']],
                ],
            ],
            'Telangana' => [
                'Hyderabad' => [
                    ['name' => 'Hyderabad Public School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.6, 'address' => 'Begumpet, Hyderabad', 'website' => 'https://www.hpsbegumpet.org', 'strengths' => ['academics', 'sports', 'leadership', 'debate'], 'facilities' => ['library', 'labs', 'sports complex', 'swimming pool', 'auditorium', 'gymnasium']],
                    ['name' => 'CHIREC International', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.4, 'address' => 'Kondapur, Hyderabad', 'strengths' => ['academics', 'technology', 'arts', 'languages'], 'facilities' => ['library', 'labs', 'smart classrooms', 'art studio', 'sports']],
                    ['name' => 'Oakridge International School', 'type' => 'technical', 'board' => 'IB', 'rating' => 4.3, 'address' => 'Gachibowli, Hyderabad', 'strengths' => ['technology', 'engineering', 'science', 'robotics'], 'facilities' => ['robotics lab', 'computer center', 'labs', 'maker space', 'library']],
                ],
            ],
            'Rajasthan' => [
                'Jaipur' => [
                    ['name' => 'Modern School Jaipur', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.5, 'address' => 'C-Scheme, Jaipur', 'phone' => '+91-141-2226122', 'website' => 'https://www.modernschooljaipur.com', 'strengths' => ['academics', 'sports', 'arts', 'leadership'], 'facilities' => ['library', 'labs', 'sports ground', 'auditorium', 'computer lab', 'art studio']],
                    ['name' => 'Mahaveer Public School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.3, 'address' => 'Mansarovar, Jaipur', 'strengths' => ['academics', 'technology', 'sports'], 'facilities' => ['library', 'labs', 'smart classrooms', 'sports complex', 'computer lab']],
                    ['name' => 'Vidyut International School', 'type' => 'science', 'board' => 'ICSE', 'rating' => 4.4, 'address' => 'Bani Park, Jaipur', 'strengths' => ['science', 'research', 'mathematics', 'technology'], 'facilities' => ['labs', 'library', 'computer center', 'smart classrooms', 'research wing']],
                    ['name' => 'Delhi Public School Jaipur', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.2, 'address' => 'Malviya Nagar, Jaipur', 'strengths' => ['academics', 'debate', 'sports', 'leadership'], 'facilities' => ['library', 'labs', 'auditorium', 'sports ground', 'computer lab']],
                ],
                'Udaipur' => [
                    ['name' => 'Mayo College Girls', 'type' => 'general', 'board' => 'ICSE', 'rating' => 4.6, 'address' => 'Udaipur', 'website' => 'https://www.mayocollegegirls.edu.in', 'strengths' => ['academics', 'arts', 'community', 'languages'], 'facilities' => ['library', 'labs', 'art studio', 'music room', 'sports ground', 'auditorium']],
                    ['name' => 'Vidyapith Public School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.3, 'address' => 'Udaipur', 'strengths' => ['academics', 'traditional arts', 'sports'], 'facilities' => ['library', 'labs', 'sports ground', 'art studio']],
                    ['name' => 'Rajasthan Vidyapith', 'type' => 'humanities', 'board' => 'HSC', 'rating' => 4.2, 'address' => 'Udaipur', 'strengths' => ['humanities', 'languages', 'social studies', 'arts'], 'facilities' => ['library', 'language lab', 'sports ground', 'art gallery']],
                ],
                'Jodhpur' => [
                    ['name' => 'St. Ursuline Convent School', 'type' => 'general', 'board' => 'ICSE', 'rating' => 4.4, 'address' => 'Jodhpur', 'strengths' => ['academics', 'sports', 'arts', 'community'], 'facilities' => ['library', 'labs', 'sports ground', 'auditorium', 'art studio', 'gymnasium']],
                    ['name' => 'Vidyamandir Academy', 'type' => 'science', 'board' => 'CBSE', 'rating' => 4.2, 'address' => 'Jodhpur', 'strengths' => ['science', 'technology', 'engineering', 'research'], 'facilities' => ['labs', 'library', 'computer center', 'workshop']],
                    ['name' => 'Dayanand Academy', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.1, 'address' => 'Jodhpur', 'strengths' => ['academics', 'sports', 'debate'], 'facilities' => ['library', 'labs', 'sports ground', 'computer lab']],
                ],
                'Sikar' => [
                    ['name' => 'Delhi Public School Sikar', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.6, 'address' => 'Sikar', 'phone' => '+91-1572-245678', 'strengths' => ['academics', 'sports', 'debate', 'leadership'], 'facilities' => ['library', 'labs', 'sports ground', 'auditorium', 'computer lab', 'gymnasium']],
                    ['name' => 'Maharaj School Sikar', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.3, 'address' => 'Sikar', 'strengths' => ['academics', 'sports', 'traditional values'], 'facilities' => ['library', 'labs', 'sports ground', 'auditorium', 'computer center']],
                    ['name' => 'Modern School Sikar', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.2, 'address' => 'Sikar', 'strengths' => ['academics', 'technology', 'sports'], 'facilities' => ['library', 'labs', 'smart classrooms', 'sports ground', 'computer lab']],
                    ['name' => 'Gajendra Singh Public School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.1, 'address' => 'Sikar', 'strengths' => ['academics', 'sports', 'discipline'], 'facilities' => ['library', 'labs', 'sports ground', 'auditorium']],
                    ['name' => 'Bhartiya Vidya Niketan', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.0, 'address' => 'Sikar', 'strengths' => ['academics', 'character education', 'sports'], 'facilities' => ['library', 'labs', 'sports ground', 'assembly hall']],
                    ['name' => 'Arya Senior Secondary School', 'type' => 'science', 'board' => 'CBSE', 'rating' => 4.2, 'address' => 'Sikar', 'strengths' => ['science', 'mathematics', 'research', 'technology'], 'facilities' => ['science labs', 'library', 'computer center', 'math lab']],
                    ['name' => 'Vidya Vihar School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.0, 'address' => 'Sikar', 'strengths' => ['academics', 'arts', 'sports'], 'facilities' => ['library', 'labs', 'art studio', 'sports ground', 'music room']],
                    ['name' => 'Gyan Niketan Senior Secondary', 'type' => 'general', 'board' => 'CBSE', 'rating' => 3.9, 'address' => 'Sikar', 'strengths' => ['academics', 'community service', 'sports'], 'facilities' => ['library', 'labs', 'sports ground', 'community center']],
                    ['name' => 'St. Xavier Senior Secondary School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.3, 'address' => 'Sikar', 'strengths' => ['academics', 'discipline', 'character formation'], 'facilities' => ['library', 'labs', 'chapel', 'sports complex', 'auditorium']],
                    ['name' => 'Shishu Niketan School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 3.8, 'address' => 'Sikar', 'strengths' => ['academics', 'child development', 'creative activities'], 'facilities' => ['library', 'labs', 'play area', 'art studio']],
                    ['name' => 'Gyan Mandir Public School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.1, 'address' => 'Sikar', 'strengths' => ['academics', 'technology integration', 'sports'], 'facilities' => ['library', 'labs', 'smart classrooms', 'computer lab', 'sports ground']],
                    ['name' => 'Vidya Niketan Senior Secondary School', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.2, 'address' => 'Sikar', 'strengths' => ['academics', 'arts', 'sports', 'debate'], 'facilities' => ['library', 'labs', 'sports complex', 'art studio', 'auditorium']],
                    ['name' => 'Birla School Sikar', 'type' => 'general', 'board' => 'CBSE', 'rating' => 4.2, 'address' => 'Sikar', 'strengths' => ['academics', 'sports', 'innovation'], 'facilities' => ['library', 'labs', 'maker space', 'sports ground', 'computer lab']],
                ],
            ],
        ];

        foreach ($states as $stateName => $cities) {
            $state = State::create([
                'country_id' => $india->id,
                'name' => $stateName,
            ]);

            foreach ($cities as $cityName => $schools) {
                $city = City::create([
                    'state_id' => $state->id,
                    'name' => $cityName,
                ]);

                // foreach ($schools as $schoolData) {
                //     School::create(array_merge($schoolData, [
                //         'city_id' => $city->id,
                //         'is_active' => true,
                //     ]));
                // }
            }
        }

        $this->command->info('Seeded: India with ' . State::count() . ' states, ' . City::count() . ' cities, ' . School::count() . ' schools.');
    }
}
