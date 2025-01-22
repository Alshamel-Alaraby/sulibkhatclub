<?php

namespace Modules\ClubMembers\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;
use Modules\ClubMembers\Transformers\StatusResource;

class CmMemberRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $memberType = $this->memberType;
        $financialStatus = $this->financialStatus;
        $status = $this->status;

        $cacheKey = 'transaction_' . $this->id . '_' . $request->has('hasTransaction');

        $transaction = Cache::remember($cacheKey, now()->addMinutes(10080), function () use ($request) {
            return $request->has('hasTransaction') && $this->cmTransaction
                ? collect(new CmTransactionResource($this->cmTransaction))->only(['id', 'document_no', 'year', 'amount', 'serial', 'date'])
                : ($this->cmTransaction
                    ? collect(new CmTransactionResource($this->cmTransaction))->only(['id', 'serial', 'document_no', 'year', 'amount', 'date'])
                    : null);
        });

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'second_name' => $this->second_name,
            'third_name' => $this->third_name,
            'last_name' => $this->last_name,
            'family_name' => $this->family_name,
            'full_name' => $this->full_name,
            'birth_date' => $this->birth_date,
            'national_id' => $this->national_id,
            'nationality_class' => $this->nationality_class,
            'phone_code' => $this->phone_code,
            'home_phone' => $this->home_phone,
            'work_phone' => $this->work_phone,
            'home_address' => $this->home_address,
            'work_address' => $this->work_address,
            'membership_date' => $this->membership_date,
            'membership_number' => $this->membership_number,
            'job' => $this->job,
            'degree' => $this->degree,
            'acceptance' => $this->acceptance,
            'session_date' => $this->session_date,
            'session_number' => $this->session_number,
            'applying_date' => $this->applying_date,
            'applying_number' => $this->applying_number,
            'sponsor_id' => $this->sponsor_id,
            'member_type_id' => $this->member_type_id,
            'financial_status_id' => $this->financial_status_id,
            "status_id" => $this->status_id,
            'member_type' => $memberType,
            'notes' => $this->notes,
            'gender' => $this->gender,
            'sponsors' => $this->sponsor,
            'financial_status' => $financialStatus,
            'status' => new StatusResource($status),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cm_transaction_count' => $request->has('include_transaction_count') ? $this->cm_transaction_count : null,
            'transaction' => $transaction,
            'executive_office_date' => $this->executive_office_date,
            'executive_office_number' => $this->executive_office_number,
            'board_of_directors_date' => $this->board_of_directors_date,
            'board_of_directors_number' => $this->board_of_directors_number,
            'application_number' => $this->application_number,
        ];
    }
}
