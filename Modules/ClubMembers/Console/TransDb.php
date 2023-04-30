<?php

namespace Modules\ClubMembers\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmTransaction;

class TransDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trans:db';

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
        $this->membersDB();
        $this->transactionsDB();

    }

    private function membersDB()
    {

        // get data from sql file

        $sql = file_get_contents(base_path('Modules/ClubMembers/Resources/assets/db/members.sql'));

        // run file
        DB::unprepared($sql);

        $members = DB::table('Members')->get();

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

    private function transactionsDB()
    {
        // load sql file
        $sql = file_get_contents(base_path('Modules/ClubMembers/Resources/assets/db/transactions.sql'));

        // migrate it to Database
        DB::unprepared($sql);

        // get all data from PaymentTransactions

        $documentId = 8; // transactions document
        $branchId = 1; // GENERAL
        $serialId = 1; // OLD
        $type = 'renew'; // with MemberNo => Renew, with NULL => Subscribe
        $nYears = 1;

        $paymentTransactions = DB::table('PaymentTransactions')->get();

        foreach ($paymentTransactions as $paymentTransaction) {

            $amount = $paymentTransaction->AMOUNT;

            $docNo = $paymentTransaction->DocNo;

            $docDate = $paymentTransaction->DocDate;

            $memberId = $paymentTransaction->MEMBER_ID;

            $yearsPay = $paymentTransaction->YersPay;

            $membershipNumber = CmMember::where('id', $memberId)->value('membership_number');

            $sponsorId = CmMember::where('id', $memberId)->value('sponsor_id');

            if ($membershipNumber == 0) {$type = 'subscribe';} else {
                $type = 'renew';
            }

            CmTransaction::create([

                'document_id' => $documentId,

                'branch_id' => $branchId,

                'serial_id' => $serialId,

                'amount' => $amount ?? 0,

                'serial_number' => $docNo ?? 0,

                'date' => $docDate ?? 1900 - 01 - 01,

                'notes' => $paymentTransaction->DocDateTXT,

                'cm_member_id' => $memberId,

                'member_number' => $membershipNumber ?? 0,

                'sponsor_id' => $sponsorId,

                'type' => $type,

                'year_from' => $yearsPay ?? 1973,

                'year_to' => $yearsPay ?? 1973,

                'number_of_years' => $nYears,

            ]);

        }

        // drop table  PaymentTransactions
        Schema::dropIfExists('PaymentTransactions');
        $this->info('cm_transactions table is seeded!');
    }
}
