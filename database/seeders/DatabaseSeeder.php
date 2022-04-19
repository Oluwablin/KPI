<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\ConnectRelationshipsSeeder;
use Database\Seeders\UsersTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

            $this->call(PermissionsTableSeeder::class);
            $this->call(RolesTableSeeder::class);
            $this->call(ConnectRelationshipsSeeder::class);
            $this->call(UsersTableSeeder::class);

        Model::reguard();
    }
}
