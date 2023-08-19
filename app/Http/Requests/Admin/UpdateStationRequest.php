<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStationRequest extends FormRequest
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
            'title' => 'required|string|max:100|unique:stations,title,'. $this->route('station'),
            'description' => 'required|string',
            'phone' => 'present|nullable|numeric|digits_between:1,20',
            'email' => 'present|required|string|email|max:100|unique:stations,email,'. $this->route('station'),
            'facebook_link' => 'present|nullable|string|url|max:100',
            'messenger_link' => 'present|nullable|string|url|max:100',
            'public_key' => ['present','nullable','string'],
            'private_key' => ['present','nullable','string'],
        ];
        if ($request->old_upload_count[0]) {
            // $rules['image'] = 'filled|file|image|max:2000';
        } else {
            $rules['image'] = 'required|file|image|max:2000';
        }

        return $rules;
    }
}
