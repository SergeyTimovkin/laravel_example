<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 25; $i++) {
            DB::table('user')->insert(
                [
                    'username' => 'user_' . str_random(5),
                    'gender' => rand(0, 1),
                    'age' => rand(1, 99),
                    'region_id' => rand(1, 85),
                ]
            );
        }
    }
}
