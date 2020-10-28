<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhoneTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 15; $i++) {
            DB::table('phone')->insert(
                [
                    'number' => '79' . rand(100000000, 999999999),
                    'user_id' => rand(1, 20),
                ]
            );
        }
    }
}
