<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userRole = config('roles.models.role')::where('name', '=', 'Employee')->first();
        $adminRole = config('roles.models.role')::where('name', '=', 'Admin')->first();
        $permissions = config('roles.models.permission')::all();

        /*
         * Add Users
         *
         */
        if (config('roles.models.defaultUser')::where('email', '=', 'admin@admin.com')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'firstname'     => 'Admin',
                'lastname'     => 'Admin',
                'email'    => 'admin@admin.com',
                'password' => bcrypt('password'),
                'is_verified' => 1,
            ]);

            $newUser->attachRole($adminRole);
            foreach ($permissions as $permission) {
                $newUser->attachPermission($permission);
            }
        }

        if (config('roles.models.defaultUser')::where('email', '=', 'user@user.com')->first() === null) {
            $newUser = config('roles.models.defaultUser')::create([
                'firstname'     => 'Default',
                'lastname'     => 'User',
                'email'    => 'user@user.com',
                'password' => bcrypt('password'),
                'is_verified' => 1,
            ]);

            $newUser->attachRole($userRole);
        }
    }
}
