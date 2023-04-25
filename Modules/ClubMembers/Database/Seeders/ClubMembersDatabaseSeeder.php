<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class ClubMembersDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call([
            CmMembersTypesDatabaseSeederTableSeeder::class,
            CmFinancialStatusDatabaseSeederTableSeeder::class,
            CmMembersPermissionsDatabaseSeederTableSeeder::class,
            CmSponsorDatabaseSeederTableSeeder::class,
        ]);
    }
}
