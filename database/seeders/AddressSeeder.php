<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('addresses')->insert([
            [
                'id' => 0,
                'user_id' => 3,
                'address_line_1' => '123 Maple Street',
                'address_line_2' => 'Apt 4B',
                'city' => 'Berlin',
                'state' => null,
                'postcode' => '62704',
                'country_id' => 64,
                'phone_number' => '555-1234',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 1,
                'user_id' => 3,
                'address_line_1' => '456 Oak Avenue',
                'address_line_2' => null,
                'city' => 'Paris',
                'state' => null,
                'postcode' => '10001',
                'country_id' => 60,
                'phone_number' => '555-5678',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'user_id' => 3,
                'address_line_1' => '789 Pine Road',
                'address_line_2' => 'Suite 12',
                'city' => 'Gotham',
                'state' => 'NJ',
                'postcode' => '07030',
                'country_id' => 187,
                'phone_number' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
