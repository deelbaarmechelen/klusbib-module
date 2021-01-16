<?php

namespace Modules\Klusbib\Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //User::truncate();
        factory(User::class, 1)->states('first-admin')->create();
        factory(User::class, 1)->states('snipe-admin')->create();
        factory(User::class, 3)->states('superuser')->create();
        factory(User::class, 1)->states('admin')->create([
            'first_name' => 'Klusbib',
            'username' => 'klusbib',
            'company_id' => 1
        ]);
        factory(User::class, 1)->states('admin')->create([
            'first_name' => 'Digibib',
            'username' => 'digibib',
            'company_id' => 2
        ]);
//        factory(User::class, 3)->states('admin')->create();
//        factory(User::class, 50)->states('view-assets')->create();

    }
}
