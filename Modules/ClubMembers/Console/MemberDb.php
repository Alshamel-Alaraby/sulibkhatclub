<?php

namespace Modules\ClubMembers\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MemberDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        // get data from sql file

        $sql = file_get_contents(base_path('Modules/ClubMembers/Resources/assets/db/members.sql'));


        // run file
        DB::unprepared($sql);

        $members = DB::table('Members')->limit(1000);

        if (Schema::hasColumn('cm_members', 'applying_number')) {
            Schema::table('cm_members', function ($table) {
                $table->dropColumn("applying_number");

            });
        }
        // change gender to nullable in cm_members
        Schema::table('cm_members', function ($table) {
            $table->boolean("gender")->nullable()->change();
            $table->string("third_name")->nullable()->change();
            $table->string("last_name")->nullable()->change();
            $table->string("family_name")->nullable()->change();
            $table->integer("applying_number")->nullable();

        });

        $members = DB::table('Members')->get();
        foreach ($members as $member) {
            $new_member = \Modules\ClubMembers\Entities\CmMember::create([
                "applying_number" => $member->ORDER_NO,
                "membership_number" => $member->MemberNo,
                "home_address" => $member->HouseAddress,
                "membership_date" => $member->ORDER_DATE,
                "nationality_class" => $member->NationalNo,
                "national_id" => $member->Cvlid,
                "first_name" => $member->FNAME,
                "second_name" => $member->SNAME,
                "third_name" => $member->TNAME,
                "last_name" => (string) $member->FORNAME,
                "family_name" => $member->ZFAM_NAME,
                "birth_date" => $member->BIRTH_DATE,
                "acceptance" => $member->ACCEPTED,
                "degree" => $member->StudyDegree,
                "job" => $member->employee,
                "work_phone" => $member->JobTel,
                "work_address" => $member->JobAddress,
                "home_phone" => $member->HouseTel,
                "status_id" => $member->ZSTATUS,
                "session_date" => $member->MeetingDate,
                "session_number" => $member->MeetingNumber,

            ]);

            if ($member->Sponsor) {
                $sponsor = DB::table('Members')
                    ->where('MEMBER_ID', $member->Sponsor)
                    ->first();
                if ($sponsor) {
                    $name = $sponsor->FNAME . ' ' . $sponsor->SNAME . ' ' . $sponsor->TNAME . ' ' . $sponsor->FORNAME;
                    $new_sponsor = \Modules\ClubMembers\Entities\CmSponser::where('name', $name)->first();
                    if (!$new_sponsor) {
                        $new_sponsor = \Modules\ClubMembers\Entities\CmSponser::create([
                            "name" => $name,
                            "name_e" => $name
                            ,
                        ]);}
                    $new_member->update(['sponsor_id' => $new_sponsor->id]);

                } else {
                    $new_member->update(['sponsor_id' => 1]);

                }

            }
        }

        // drop table  Members
        Schema::dropIfExists('Members');
        $this->info('Members table seeded!');

    }

}
