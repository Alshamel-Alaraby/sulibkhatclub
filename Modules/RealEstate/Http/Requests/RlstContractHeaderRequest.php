<?php

namespace Modules\RealEstate\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RlstContractHeaderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'branch_id'               => 'required|exists:general_branches,id',
            'date'                    => 'required|date',
            'related_document_id'     => 'nullable|exists:general_documents,id',
            'related_document_number' => 'nullable',
            'related_document_prefix' => 'nullable',
            'employee_id'             => 'required|exists:general_employees,id',
            'tenant_id'               => 'required|exists:rlst_tenants,id',
            'notice_period'           => 'required',
            'insurance_amount'        => 'required',
            'attendants'              => 'nullable|string',
            'automatic_renews'        => 'nullable|int:1,0',
            'external_salesmen_id'    => 'nullable|exists:general_external_salesmen,id',
            'commission'              => 'nullable',
            'posted'                  => 'nullable|int:1,0',
            'receipt_print_detail'    => 'nullable|int:1,0',
            'document_id'             => 'required|exists:general_documents,id',



            'header_details'                      => 'nullable|array',
            'header_details.*.from_date'          => 'nullable|date',
            'header_details.*.to_date'            => 'nullable|date',
            'header_details.*.rent_amount'        => 'nullable',
            'header_details.*.payment_type_id'    => 'required|exists:rp_installment_payment_types,id',
            'header_details.*.print_day'          => 'nullable|integer|min:2|max:28',
            'header_details.*.due_day'            => 'nullable|integer|min:2|max:28',

            'units.*.unit_id'                     => 'nullable|exists:rlst_units,id',
            'units.*.unit_services'               => 'nullable|array',




        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
