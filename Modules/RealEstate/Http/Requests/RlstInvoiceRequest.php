<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstInvoiceRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            "payment_method_id" => "sometimes|exists:general_payment_methods,id",
            "branch_id" => ["sometimes", "exists:general_branches,id", new \App\Rules\BranchSerialExistRule()],
            "customer_id" => "sometimes|exists:general_customers,id",
            "salesman_id" => "sometimes|exists:general_salesmen,id",
            "date" => "sometimes|date",

            // "serial_id" => "nullable|exists:general_serials,id",
            "serial_number" => "sometimes|unique:rlst_invoices,serial_number," . $this->id,

            "payment_plan_id" => "nullable|numeric",
            "document_id" => "nullable|exists:general_documents,id",
            "building_id" => "nullable|exists:rlst_buildings,id",
            "unit_id" => "nullable|exists:rlst_units,id",
            "start_date" => "nullable|date",
            "end_date" => "nullable|date|after_or_equal:start_date",
            'flat_amount' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            "paid_amount" => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            "media" => "nullable|array",
            "media.*" => ["nullable", "exists:media,id", new \App\Rules\MediaRule()],
            'items' => 'sometimes|array',
            'items.*.item_id' => 'sometimes|exists:rlst_items,id',
            'items.*.quantity' => 'sometimes|integer',
            'items.*.amount' => 'sometimes|numeric',
            "break_downs.*" => "nullable|exists:rp_break_downs,id",
            'transactions' => 'nullable|array',
            'transactions.*.date' => 'nullable|date',
            'transactions.*.amount' => 'nullable|regex:/^\d+(\.\d{1,2})?$/',
            'transactions.*.module_type' => "nullable|string",
            'transactions.*.break_id' => "nullable|exists:rp_break_downs,id",
        ];
    }

}
