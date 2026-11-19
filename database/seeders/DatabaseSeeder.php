<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceSeeder::class,
            ProductSeeder::class,
            TestimonialSeeder::class,
        ]);

        if (! app()->runningUnitTests()) {
            $this->call(LeadSeeder::class);
        }
    }
}
