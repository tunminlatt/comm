<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStationRequest extends FormRequest
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
            'title' => 'required|string|max:100|unique:stations,title',
            'description' => 'required|string',
            'phone' => 'present|nullable|numeric|digits_between:1,20',
            'email' => 'required|string|email|max:100|unique:stations,email',
            'facebook_link' => 'present|nullable|string|url|max:100',
            'messenger_link' => 'present|nullable|string|url|max:100',
            'public_key' => ['present','nullable','string'],
            'private_key' => ['present','nullable','string'],
            'image' => 'required|file|image|max:2000',
        ];
    }
}
