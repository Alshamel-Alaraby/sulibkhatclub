<?php

namespace Modules\ClubMembers\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\ClubMembers\Entities\CmMember;
use Modules\ClubMembers\Entities\CmTransaction;

use Modules\ClubMembers\Database\Seeders;

class TransactionDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:db';

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
        // load sql file
        $sql = file_get_contents(base_path('Modules/ClubMembers/Resources/assets/db/transactions.sql'));

        // migrate it to Database
        DB::unprepared($sql);
        
        $transactions =DB::table('PaymentTransactions')->orderBy('DocNo')->limit(1000);

        $i = 0;
        $transactions->chunk(100, function ($records) {
            $this->insertTransaction($records);
            $this->info("chunck '.$i.' in cm_transactions table is seeded!");
            $i++;
        });

         // drop table  PaymentTransactions
         Schema::dropIfExists('PaymentTransactions');
         $this->info('All cm_transactions table is seeded!');
       
    }

    public function insertTransaction($records)
    {

        // get all data from PaymentTransactions

        $documentId = 8; // transactions document
        $branchId = 1; // GENERAL
        $serialId = 1; // OLD
        $type = 'renew'; // with MemberNo => Renew, with NULL => Subscribe
        $nYears = 1;

        $i=1;

        foreach ($records as $paymentTransaction) {

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
                'date' => $docDate ? $docDate : '1900-01-01',
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

    }
}