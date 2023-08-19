<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
     * @return array
     */
    public function rules(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:100',
            'note' => 'required|string|max:1000',
            'station_id' => 'required|string',
            'uploaded_by' => 'required|string',
            'file' => 'required|file|max:2000000'
        ];
        return $rules;
    }
}