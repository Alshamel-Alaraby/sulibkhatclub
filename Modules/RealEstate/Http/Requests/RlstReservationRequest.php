<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstReservationRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "date" => "sometimes|date",
            "salesman_id" => "sometimes|exists:general_salesmen,id",
            "customer_id" => "sometimes|exists:general_customers,id",
            "branch_id" => ["sometimes", "exists:general_branches,id", new \App\Rules\BranchSerialExistRule()],
            "serial_id" => "sometimes|exists:general_serials,id",
            "document_id"=>"nullable|exists:general_documents,id",
            "unit_id" => "sometimes|exists:rlst_units,id",
            "start_date" => "sometimes|date",
            "end_date" => "sometimes|date|after_or_equal:start_date",
            "serial_number" => "sometimes|unique:rlst_reservations,serial_number," . $this->id,
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            "details" => "nullable|array",
            "details.*.building_id" => "nullable|exists:rlst_buildings,id",
            "details.*.unit_id" => "nullable|exists:rlst_units,id",
            "details.*.unit_value" => "nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/",
            "details.*.reservation_value" => "nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/",
            "details.*.installment_payment_plans_id" => "nullable|exists:rp_installment_payment_plans,id",
        ];
    }

}
