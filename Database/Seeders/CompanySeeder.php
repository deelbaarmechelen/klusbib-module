<?php

namespace Modules\Klusbib\Database\Seeders;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::info('Running Company Seeder');
        Company::updateOrCreate(['id' => 1], ['name' => 'Klusbib'] );
        Company::updateOrCreate(['id' => 2], ['name' => 'Deel-IT'] );
        Company::updateOrCreate(['id' => 3], ['name' => 'Zadenbib'] );
    }
}
