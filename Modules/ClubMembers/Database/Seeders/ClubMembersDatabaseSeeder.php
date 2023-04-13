<?php

namespace Modules\ClubMembers\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

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

        // $this->call("OthersTableSeeder");
        $this->call(CmMembersTypesDatabaseSeederTableSeeder::class);
        $this->call(CmFinancialStatusDatabaseSeederTableSeeder::class);
        $this->call(CmMembersPermissionsDatabaseSeederTableSeeder::class);
        $this->call(CmSponsorDatabaseSeederTableSeeder::class);
    }
}
