<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ad;


class AdsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    Ad::factory()->count(20)->create();
}

}
