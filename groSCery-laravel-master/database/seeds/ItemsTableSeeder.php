<?php

use Illuminate\Database\Seeder;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('items')->insert([
            'group_id' => 1,
            'name' => 'Banana',
            'in_stock' => true,
        ]);
        DB::table('item_user')->insert([
            'user_id' => 1,
            'item_id' => 1
        ]);
        DB::table('item_user')->insert([
            'user_id' => 2,
            'item_id' => 1
        ]);

        DB::table('items')->insert([
            'group_id' => 1,
            'name' => 'Apple',
            'in_stock' => true,
        ]);
        DB::table('item_user')->insert([
            'user_id' => 2,
            'item_id' => 2
        ]);
        DB::table('item_user')->insert([
            'user_id' => 3,
            'item_id' => 2
        ]);

        DB::table('items')->insert([
            'group_id' => 1,
            'name' => 'Orange',
            'in_stock' => false,
        ]);
        DB::table('item_user')->insert([
            'user_id' => 1,
            'item_id' => 3
        ]);
        DB::table('item_user')->insert([
            'user_id' => 2,
            'item_id' => 3
        ]);
        DB::table('item_user')->insert([
            'user_id' => 3,
            'item_id' => 3
        ]);
    }
}
