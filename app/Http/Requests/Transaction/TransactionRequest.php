<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'transactions' => 'required|array',
            'transactions.*.date' => 'required|date',
            'transactions.*.amount' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'transactions.*.module_type' =>"nullable|string",
            'transactions.*.invoice_id' => "nullable|exists:rlst_invoices,id",
            'transactions.*.break_id' => "nullable|exists:rp_break_downs,id",
        ];
    }
}
