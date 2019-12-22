<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('groups')->insert([
            'name' => '201'
        ]);
        DB::table('groups')->insert([
            'name' => '270'
        ]);
    }
}
