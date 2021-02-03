<?php

namespace Modules\Klusbib\Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Populates test data in inventory for use with Klusbib module
 *
 * Run with 'php artisan module:seed Klusbib'
 * @package Modules\Klusbib\Database\Seeders
 */
class KlusbibDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::info('Running Klusbib Database Seeder');
        Model::unguard();
        try {
            $this->call(\Modules\Klusbib\Database\Seeders\CompanySeeder::class);
            $this->call(\Modules\Klusbib\Database\Seeders\UserSeeder::class);

        } catch (\Error $err) {
            throw new \Exception($err->getMessage());
        }

        // Only create default settings if they do not exist in the db.
        if(!Setting::first()) {
            factory(Setting::class)->create();
        }

        $output = Artisan::output();
        Log::info($output);

        Model::reguard();
        Log::info('Klusbib Database Seeder completed');
    }
}
