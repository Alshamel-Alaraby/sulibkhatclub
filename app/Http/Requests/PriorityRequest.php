<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriorityRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'name_e' => 'sometimes|string|max:255',
            'parent_id' => 'nullable|exists:general_priorities,id',
            'is_valid' => "nullable|in:true,false",
            'is_default' => "nullable|in:true,false"
        ];
    }
}
