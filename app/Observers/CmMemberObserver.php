<?php

namespace App\Observers;

use Illuminate\Support\Facades\DB;
use Modules\ClubMembers\Entities\CmMember;

class CmMemberObserver
{
    protected function pStaticsMounir()
    {
        DB::beginTransaction();

        try {
            $pDate = now()->addHours(7); // Adjusted for Kuwait local time

            // Get active year and due date
            $activeYearData = DB::table('general_financial_years')
                ->select('year', 'due_date')
                ->where('is_active', 1)
                ->first();

            $pActiveYear = $activeYearData->year;
            $pDueDate = $activeYearData->due_date;

            // Update last_transaction_year and last_transaction_date to null for active members
            DB::table('cm_members')
                ->where('member_status_id', 1)
                ->update([
                    'last_transaction_year' => null,
                    'last_transaction_date' => null
                ]);

            // Update last transaction data from transactions to members table
            DB::table('cm_members')
                ->where('member_status_id', 1)
                ->update([
                    'last_transaction_date' => DB::table('cm_transactions')
                        ->whereColumn('cm_members.membership_number', 'cm_transactions.member_number')
                        ->where('cm_transactions.year', $pActiveYear)
                        ->max('date')
                ]);

            // Update last transactions year from transaction to members table
            DB::table('cm_members')
                ->where('member_status_id', 1)
                ->update([
                    'last_transaction_year' => DB::table('cm_transactions')
                        ->whereColumn('cm_members.membership_number', 'cm_transactions.member_number')
                        ->where('cm_transactions.year', $pActiveYear)
                        ->max('YEAR')
                ]);

            // Reset financial status to 2 (not paid)
            DB::table('cm_members')
                ->where('member_status_id', 1)
                ->where('last_transaction_year', $pActiveYear)
                ->update(['financial_status_id' => 2]);

            // Update financial status to actual value based on last_transaction_date and last_transaction_year
            DB::table('cm_members')
                ->where('member_status_id', 1)
                ->where('last_transaction_year', $pActiveYear)
                ->update([
                    'financial_status_id' => DB::raw('CASE WHEN last_transaction_date <= ? THEN 3 ELSE 4 END', [$pDueDate])
                ]);

            // Reset permissions to 1 (no rights)
            DB::table('cm_members')
                ->whereNull('members_permissions_id')
                ->where('member_status_id', 1)
                ->where('last_transaction_year', $pActiveYear)
                ->where('financial_status_id', 3)
                ->update(['members_permissions_id' => 1]);

            // Update permissions to actual values
            DB::table('cm_members')
                ->where('member_status_id', 1)
                ->where('financial_status_id', 3)
                ->update([
                    'members_permissions_id' => DB::table('cm_type_permissions')
                        ->whereColumn('cm_members.member_kind_id', 'cm_members_type_id')
                        ->where(DB::raw('TIMESTAMPDIFF(YEAR, cm_members.membership_date, ?)', [$pDate]), '>=', 'membership_period')
                        ->max('cm_permissions_id')
                ]);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            // Something went wrong, rollback the transaction
            DB::rollBack();
            // Handle the exception
        }
    }
    /**
     * Handle the CmMember "created" event.
     *
     * @param  \App\Models\CmMember  $cmMember
     * @return void
     */
    public function created(CmMember $cmMember)
    {
//        $this->pStaticsMounir();
    }

    /**
     * Handle the CmMember "updated" event.
     *
     * @param  \App\Models\CmMember  $cmMember
     * @return void
     */
    public function updated(CmMember $cmMember)
    {
//        $this->pStaticsMounir();
    }

    /**
     * Handle the CmMember "deleted" event.
     *
     * @param  \App\Models\CmMember  $cmMember
     * @return void
     */
    public function deleted(CmMember $cmMember)
    {
//        $this->pStaticsMounir();
    }

    /**
     * Handle the CmMember "restored" event.
     *
     * @param  \App\Models\CmMember  $cmMember
     * @return void
     */
    public function restored(CmMember $cmMember)
    {
        //
    }

    /**
     * Handle the CmMember "force deleted" event.
     *
     * @param  \App\Models\CmMember  $cmMember
     * @return void
     */
    public function forceDeleted(CmMember $cmMember)
    {
        //
    }
}
