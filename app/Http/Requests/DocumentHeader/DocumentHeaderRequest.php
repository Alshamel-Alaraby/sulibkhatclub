<?php

namespace App\Http\Requests\DocumentHeader;

use Illuminate\Foundation\Http\FormRequest;

class DocumentHeaderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [

            'document_status_id'      => 'nullable|integer|exists:general_document_statuses,id',
            'reason'                  => 'nullable|string',
            'branch_id'               => 'required|integer|exists:general_branches,id',
            'date'                    => 'nullable|date',
            'document_id'             => 'required|integer|exists:general_documents,id',
            'related_document_prefix' => 'nullable|string',
            'related_document_id'     => 'nullable|integer|exists:general_documents,id',
            'related_document_number' => 'nullable|numeric|integer',
            'sell_method_id'          => 'required|integer|exists:boards_rent_sell_methods,id',
            'employee_id'             => 'required|integer|exists:general_employees,id',
            'customer_id'             => 'required|integer|exists:general_customers,id',
            'task_id'                 => 'nullable|integer|exists:general_tasks,id',
            'external_salesmen_id'    =>  'nullable|integer|exists:general_external_salesmen,id',
            'total_invoice'           => 'nullable|numeric',
            'invoice_discount'        => 'nullable|numeric',
            'net_invoice'             => 'required|nullable',
            'remaining'               => 'nullable|',
            'complete_status'         => 'nullable|in:UnDelivered,partially,Delivered',

            "header_details" => 'nullable|array',
            'header_details.*.category_id'    => 'required_if:header_details.*.is_stripe,==,0|integer|exists:general_categories,id',
            'header_details.*.date_from'      => 'nullable|date',
            'header_details.*.date_to'        => 'nullable|date|after_or_equal:date_from',
            'header_details.*.is_stripe'      => 'nullable|in:1,0',
            'header_details.*.governorate_id' => 'required_if:header_details.*.is_stripe,==,0|integer|exists:general_governorates,id',
            'header_details.*.package_id'     => 'required_if:header_details.*.is_stripe,==,1|integer|exists:boards_rent_packages,id',
            'header_details.*.quantity'       => 'nullable|numeric|integer',
            'header_details.*.price_per_uint' => 'nullable',
            'header_details.*.total'          => 'nullable',
            'header_details.*.unit_type'      => 'required|string',
            'header_details.*.break_downs'    => 'nullable|array',


//            "break_downs" => 'nullable|array',
//            "break_downs.*.id" => 'nullable',
//            "break_downs.*.date_from" => 'nullable|date',
//            "break_downs.*.date_to" => 'nullable|date',
//            "break_downs.*.module_type" => 'nullable',
//            "break_downs.*.serial_number" => 'nullable',



        ];
    }
}
