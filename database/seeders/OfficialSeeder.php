<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class OfficialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('officials')
            ->insert(array(
           // array('name'=>'Rituraj Borgohain','email'=> 'dcgolaghat@gmail.com','mobile'=> '7002274743','role'=> '2','designation'=> '2', 'password'=> bcrypt('admin@123')), 
            array('name'=>'Angelika','email'=> 'police@gmail.com','mobile'=> '6001200433','role'=> '3','designation'=> '3', 'password'=> bcrypt('admin@123')), 
        ));
        DB::table('official_assigns')
            ->insert(array(
                array('user_id'=>'1','role'=> '2','organisation_id'=> '1','district_id'=> '14','created_at'=>'2023-08-03'), 
                array('user_id'=>'2','role'=> '3','organisation_id'=> '11','district_id'=> '14','created_at'=>'2023-08-03'), 
        ));
    }
}
