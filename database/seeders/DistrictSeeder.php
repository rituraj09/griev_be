<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('districts')->insert(array(
            array('name'=>'Baksa'),
            array('name'=>'Barpeta'),
            array('name'=>'Biswanath'),
            array('name'=>'Bongaigaon'),
            array('name'=>'Cachar'),
            array('name'=>'Charaideo'),
            array('name'=>'Chirang'),
            array('name'=>'Darrang'),
            array('name'=>'Dhemaji'),
            array('name'=>'Dhubri'),
            array('name'=>'Dibrugarh'),
            array('name'=>'Dima Hasao'),
            array('name'=>'Goalpara'),
            array('name'=>'Golaghat'),
            array('name'=>'Hailakandi'),
            array('name'=>'Hojai'),
            array('name'=>'Jorhat'),
            array('name'=>'Kamrup Rural'),
            array('name'=>'Kamrup Metro'),
            array('name'=>'Karbi Anglong'),
            array('name'=>'Karimganj'),
            array('name'=>'Kokrajhar'),
            array('name'=>'Lakhimpur'),
            array('name'=>'Majuli'),
            array('name'=>'Morigaon'),
            array('name'=>'Nagaon'),
            array('name'=>'Nalbari'),
            array('name'=>'Sivasagar'),
            array('name'=>'Sonitpur'),
            array('name'=>'South Salmara-Mankachar'),
            array('name'=>'Tinsukia'),
            array('name'=>'Udalguri'),
            array('name'=>'West Karbi Anlong'),
        ));
        DB::table('circle_offices')->insert([ ['name'=>'Bokakhat Revenue Circle','district_id'=>'14'],
            ['name'=>'Dergaon Revenue Circle','district_id'=>'14'],
            ['name'=>'Golaghat Revenue Circle','district_id'=>'14'],
            ['name'=>'Khumtai Revenue Circle','district_id'=>'14'],
            ['name'=>'Morangi Revenue Circle','district_id'=>'14'],
            ['name'=>'Sarupathar Revenue Circle','district_id'=>'14'],
        ]);

        DB::table('police_stations')->insert([ ['name'=>'Bokakhat PS','district_id'=>'14'],
            ['name'=>'Dergaon PS','district_id'=>'14'],
            ['name'=>'Golaghat PS','district_id'=>'14'],
            ['name'=>'Khumtai PS','district_id'=>'14'],
            ['name'=>'Numaligarh PS','district_id'=>'14'],
            ['name'=>'Sarupathar PS','district_id'=>'14'],
            ['name'=>'Merapani PS','district_id'=>'14'],
            ['name'=>'Sarupathar PS','district_id'=>'14'],
            ['name'=>'Sarupathar PS','district_id'=>'14'],
        ]);
    }
}
