<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')
        ->insert(array(
        array('name'=>'Admin'),
        array('name'=>'DC User'),
        array('name'=>'Police User')
    ));

            DB::table('designations')
                ->insert(array(
                array('name'=>'Admin'),
                array('name'=>'District Commissioner'),
                array('name'=>'Inspector'), 
                array('name'=>'Sub Inspector'),
                array('name'=>'Asst Sub Inspector'),
            ));
            DB::table('organisations')->insert([ 
                
            ['name'=>'O/o of the District Commissioner','district_id'=>'14', 'office_type'=>'1'],
            ['name'=>'Bokakhat Revenue Circle','district_id'=>'14', 'office_type'=>'2'],
            ['name'=>'Dergaon Revenue Circle','district_id'=>'14', 'office_type'=>'2'],
            ['name'=>'Golaghat Revenue Circle','district_id'=>'14', 'office_type'=>'2'],
            ['name'=>'Khumtai Revenue Circle','district_id'=>'14', 'office_type'=>'2'],
            ['name'=>'Morangi Revenue Circle','district_id'=>'14', 'office_type'=>'2'],
            ['name'=>'Sarupathar Revenue Circle','district_id'=>'14', 'office_type'=>'2'],
            ['name'=>'Bokakhat PS','district_id'=>'14', 'office_type'=>'3'],
                ['name'=>'Dergaon PS','district_id'=>'14', 'office_type'=>'3'],
                ['name'=>'Golaghat PS','district_id'=>'14', 'office_type'=>'3'],
                ['name'=>'Khumtai PS','district_id'=>'14', 'office_type'=>'3'],
                ['name'=>'Numaligarh OP','district_id'=>'14', 'office_type'=>'3'],
                ['name'=>'Sarupathar PS','district_id'=>'14', 'office_type'=>'3'],
                ['name'=>'Merapani PS','district_id'=>'14', 'office_type'=>'3'],
                ['name'=>'Borpathar PS','district_id'=>'14', 'office_type'=>'3'],
                ['name'=>'Furkating OP','district_id'=>'14', 'office_type'=>'3'],
        ]);
        DB::table('office_types')
            ->insert(array(
            array('name'=>'District Commissioner'),
            array('name'=>'Circle Office'),
            array('name'=>'Police Station'),  
        ));
    }
}
