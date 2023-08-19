<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreVolunteerRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'phone' => 'required|numeric|digits_between:1,20|unique:volunteers,phone',
            'email' => 'present|nullable|string|email|max:100|unique:volunteers,email',
            'password' => 'required|string|min:8|max:100|confirmed',
            'password_confirmation' => 'required|string|min:8|max:100',
            'address' => 'max:1000',
            'station_id' => 'required|string',
            'image' => 'max:2000',
        ];
    }
}