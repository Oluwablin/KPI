<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Permission Types
         *
         */
        $Permissionitems = [
            [
                'name'        => 'Can View Employees',
                'slug'        => 'view.employees',
                'description' => 'Can view employees',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Create Employees',
                'slug'        => 'create.employees',
                'description' => 'Can create new employees',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Edit Employees',
                'slug'        => 'edit.employees',
                'description' => 'Can edit employees',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Delete Employees',
                'slug'        => 'delete.employees',
                'description' => 'Can delete employees',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Reviews',
                'slug'        => 'view.reviews',
                'description' => 'Can view reviews',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Create Reviews',
                'slug'        => 'create.reviews',
                'description' => 'Can create new reviews',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Edit Reviews',
                'slug'        => 'edit.reviews',
                'description' => 'Can edit reviews',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Assign Employees To Participate in Reviews',
                'slug'        => 'assign.employees',
                'description' => 'Can aasign employees to participate in reviews',
                'model'       => 'Permission',
            ],
        ];

        /*
         * Add Permission Items
         *
         */
        foreach ($Permissionitems as $Permissionitem) {
            $newPermissionitem = config('roles.models.permission')::where('slug', '=', $Permissionitem['slug'])->first();
            if ($newPermissionitem === null) {
                $newPermissionitem = config('roles.models.permission')::create([
                    'name'          => $Permissionitem['name'],
                    'slug'          => $Permissionitem['slug'],
                    'description'   => $Permissionitem['description'],
                    'model'         => $Permissionitem['model'],
                ]);
            }
        }
    }
}
