<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
   
        $adminRole=Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'api']);
        $userRole=Role::firstOrCreate(['name' => 'User', 'guard_name' => 'api']);

     
        $permissions = [
            'create task',
            'delete task',
            'assign task to user',
            'delete comment',
            'delete user',
            'reassign task to another user',
            'create account',
            'update task status',
            'add comment to task',
            'add attachment to task',
            'view daily tasks'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

     
        $adminRole->givePermissionTo([
            'create task',
            'delete task',
            'assign task to user',
            'delete comment',
            'delete user',
            'reassign task to another user'
        ]);

        $userRole->givePermissionTo([
            'create account',
            'update task status',
            'add comment to task',
            'add attachment to task',
            'view daily tasks'
        ]);
    }
}
