<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder; 
use DB;
class MatterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('matters')
        ->insert(array(
        array('name'=>'Child Labour'),
        array('name'=>'Child Marriage')
    ));
    }
}
