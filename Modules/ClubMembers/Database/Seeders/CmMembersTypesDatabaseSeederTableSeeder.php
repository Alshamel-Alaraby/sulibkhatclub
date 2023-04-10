<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMemberType;

class CmMembersTypesDatabaseSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cm_members_types')->delete();

        $attributes = [
            [
                'id' => 1,
                'name' => 'عضو عادي',
                'name_e' => 'ٌRegular Member',
                
            ],
            [
                'id' => 2,
                'name' => 'عضو تابع',
                'name_e' => 'Subordinate Member',
                
            ],
            [
                'id' => 3,
                'name' => 'عضو عامل',
                'name_e' => 'Active Member',
                
            ],
            [
                'id' => 4,
                'name' => 'عضو مؤسس',
                'name_e' => 'Founding Member',
                
            ],
            [
                'id' => 5,
                'name' => 'عضو شرفي',
                'name_e' => 'Honorary Member',
                
            ],
            [
                'id' => 6,
                'name' => 'عضو مشطوب',
                'name_e' => 'Canceled member',
                
            ],
            [
                'id' => 7,
                'name' => 'عضو موقوف',
                'name_e' => 'suspended member',
                
            ],

        ];

        foreach ($attributes as $attribute) {
            CmMemberType::create($attribute);
        }

    }
}
