<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class GeneralExcelFiltersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('general_excel_filters')->delete();
        DB::table('general_excel_filters')->insert([
            [
                'id' => 1,
                'name' => 'يساوي',
                'name_e' => 'Equals',
                'type' => 'numeric/text',
            ],
            [
                'id' => 2,
                'name' => 'لا يساوي',
                'name_e' => 'Not Equal To', 
                'type' => 'numeric/text',
            ],
            [
                'id' => 3,
                'name' => 'اكبر من',
                'name_e' => 'Greater Than',
                'type' => 'numeric',
            ],
            [
                'id' => 4,
                'name' => 'اكبر من او يساوي',
                'name_e' => 'Greater Than Or Equal To',
                'type' => 'numeric',
            ],
            [
                'id' => 5,
                'name' => 'اصغر من',
                'name_e' => 'Less Than',
                'type' => 'numeric',
            ],
            [
                'id' => 6,
                'name' => 'اصغر من او يساوي',
                'name_e' => 'Less Than Or Equal To',
                'type' => 'numeric',
            ],
            [
                'id' => 7,
                'name' => 'بين',
                'name_e' => 'Between',  
                'type' => 'numeric',
            ],
            [
                'id' => 8,
                'name' => 'يبدأ بـ',
                'name_e' => 'Begins With',
                'type' => 'text',
            ],
            [
                'id' => 9,
                'name' => 'ينتهي بـ',
                'name_e' => 'Ends With',
                'type' => 'text',
            ],
            [
                'id' => 10,
                'name' => 'يحتوي',
                'name_e' => 'Contains',
                'type' => 'text',
            ],
            [
                'id' => 11,
                'name' => 'لا يحتوي',
                'name_e' => 'Does Not Contain',
                'type' => 'text',
            ],
            [
                'id' => 12,
                'name' => 'في',
                'name_e' => 'On',
                'type' => 'date',
            ],
            [
                'id' => 13,
                'name' => 'ليس في',
                'name_e' => 'Not On',
                'type' => 'date',
            ],
            [
                'id' => 14,
                'name' => 'من',
                'name_e' => 'From',
                'type' => 'date',
            ],
            [
                'id' => 15,
                'name' => 'الى',
                'name_e' => 'To',
                'type' => 'date',
            ],
            [
                'id' => 16,
                'name' => 'من - الى',
                'name_e' => 'From - To',
                'type' => 'date',
            ],

        ]);
        
    }
}
