<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user=[
            
            'name'=>'zein',
            'email'=>'z@gmail.com',
            'password' => Hash::make('123456'),
   ];
 
   $user= User::create($user);
   $user->assignRole('Admin');
  
}
}