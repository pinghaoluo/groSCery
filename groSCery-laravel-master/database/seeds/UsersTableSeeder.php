<?php

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
        DB::table('users')->insert([
            'name' => 'Kristof',
            'group_id' => 1,
            'email' => 'osswald@usc.edu',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'name' => 'Max',
            'group_id' => 1,
            'email' => 'max@usc.edu',
            'password' => bcrypt('password'),
        ]);
        DB::table('users')->insert([
            'name' => 'Naman',
            'group_id' => 1,
            'email' => 'naman@usc.edu',
            'password' => bcrypt('password'),
        ]);
    }
}
