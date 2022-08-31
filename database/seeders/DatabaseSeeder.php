<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // insert user roles in DB for once 
        DB::table('roles')->delete();        
        $roles= [
            ['title'=>'Admin'],
            ['title'=>'User']
        ];
        Role::insert($roles);

        // insert Admin User in DB 
        // DB::table('users')->delete();
        // $user = [
        //     'user_full_name'=>'Admin',
        //     'email'=>'admin@toypodcast.com',
        //     'password'=>'admin'
        // ];
        // User::insert($user);

        // // insert Admin Role in DB 
        // DB::table('user_roles')->delete();
        // $user_roles = ['role_id'=>1,'user_id'=>'1'];
        // UserRole::insert($user_roles);

    }
}
