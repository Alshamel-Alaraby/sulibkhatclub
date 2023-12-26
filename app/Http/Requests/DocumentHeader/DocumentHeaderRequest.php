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

            'document_module_type_id' => 'nullable',
            'document_status_id' => 'nullable|integer|exists:general_document_statuses,id',
            'reason' => 'nullable|string',
            'is_break' => 'nullable',
            'branch_id' => 'required|integer|exists:general_branches,id',
            'date' => 'nullable|date',
            'document_id' => 'required|integer|exists:general_documents,id',
            'unit_id' => 'nullable',
            'related_document_prefix' => 'nullable',
            'company_id' => 'nullable',
            'related_document_id' => 'nullable|integer|exists:general_documents,id',
            'related_document_number' => 'nullable|numeric|integer',
            'sell_method_id' => 'nullable|integer|exists:boards_rent_sell_methods,id',
            'employee_id' => 'nullable|integer|exists:general_employees,id',
            'customer_id' => 'nullable|integer|exists:general_customers,id',
            'task_id' => 'nullable|integer|exists:general_tasks,id',
            'external_salesmen_id' => 'nullable|integer|exists:general_external_salesmen,id',
            'total_invoice' => 'nullable|numeric',
            'invoice_discount' => 'nullable|numeric',
            'unrelaized_revenue' => 'nullable|numeric',
            'sell_method_discount' => 'nullable|numeric',
            'external_commission' => 'nullable|numeric',
            'net_invoice' => 'nullable',
            'remaining' => 'nullable|',
            'complete_status' => 'nullable|in:UnDelivered,partially,Delivered',
            "check_out_time" => 'nullable',
            "check_in_time" => 'nullable',
            "attendants.*" => 'nullable|numeric|integer',
            "attendans_num" => 'nullable|numeric|integer',

            'installment_payment_type_id'   => 'nullable|exists:rp_installment_payment_types,id',
            'print_day'                     => 'nullable|integer|min:1|max:28',
            'due_day'                       => 'nullable|integer|min:1|max:28',

            "header_details" => 'required|array',
            'header_details.*.unit_id' => 'nullable',
            'header_details.*.item_id' => 'nullable',
            'header_details.*.category_id' => 'nullable|exists:general_categories,id',
            'header_details.*.date_from' => 'nullable|date',
            'header_details.*.sell_method_id' => 'nullable|integer|exists:boards_rent_sell_methods,id',
            'header_details.*.sell_method_discount' => 'nullable|numeric',
            'header_details.*.rent_days' => 'nullable|integer',
            'header_details.*.date_to' => 'nullable|date|after_or_equal:date_from',
            'header_details.*.is_stripe' => 'nullable|in:1,0',
            'header_details.*.governorate_id' => 'nullable|exists:general_governorates,id',
            'header_details.*.package_id' => 'nullable|exists:boards_rent_packages,id',
            'header_details.*.quantity' => 'required|numeric|integer',
            'header_details.*.price_per_uint' => 'nullable',
            'header_details.*.total' => 'nullable',
            'header_details.*.unit_type' => 'nullable|string',
            'header_details.*.building_id' => 'nullable|numeric',
            'header_details.*.break_downs' => 'nullable|array',
            'header_details.*.category_booking' => 'nullable|in:single,family',

            'header_details.*.discount' => 'nullable|numeric',
            'header_details.*.check_in_time' => 'nullable|string',
            'header_details.*.note'     => 'nullable|string',
            'header_details.*.prefix_related'     => 'nullable|string',

           //hms
           'company_insurance_id' => 'nullable|integer|exists:general_insurance_companies,id',
           'doctor_id' => 'nullable|integer|exists:h_m_s_doctors,id',
           'from_doctor_id' => 'nullable|integer|exists:h_m_s_doctors,id|different:doctor_id',
           'patient_id' => 'nullable|integer|exists:h_m_s_patients,id',
           'appointment_id' => 'nullable|integer|exists:h_m_s_appointments,id',
           "patient_insurance_number" => 'nullable',
           'total_company_insurance_amount' => 'nullable|numeric',
           'total_patient_amount' => 'nullable|numeric',
           'header_details.*.company_insurance_amount' => 'nullable|numeric',
           'header_details.*.patient_amount' => 'nullable|numeric',
           'header_details.*.service_id'     => 'nullable|exists:h_m_s_service_types,id',
           ///hms

            'break_settlement.*.amount'          => 'required',
            'break_settlement.*.document_id'        => 'nullable|exists:general_documents,id',
            'break_settlement.*.affect'        => 'nullable|int:1,-1',

            'voucher_headers'        => 'nullable|array',


            "payment_method_id" => 'nullable|integer|exists:general_payment_methods,id',
            "customer_type" => 'nullable|in:1,0',


            "header_break_downs" => 'nullable|array',
            'header_break_downs.*.customer_id' => 'nullable',
            'header_break_downs.*.net_invoice' => 'nullable',
            'header_break_downs.*.client_type_id' => 'nullable',




//            "break_downs" => 'nullable|array',
//            "break_downs.*.id" => 'nullable',
//            "break_downs.*.date_from" => 'nullable|date',
//            "break_downs.*.date_to" => 'nullable|date',
//            "break_downs.*.module_type" => 'nullable',
//            "break_downs.*.serial_number" => 'nullable',

        ];
    }
}
