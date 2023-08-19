<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVolunteerRequest extends FormRequest
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
            'name' => 'required|string|max:100',
            'phone' => 'required|numeric|digits_between:1,20|unique:volunteers,phone,'. $this->route('volunteer'),
            'email' => 'required|string|email|max:100|unique:volunteers,email,'. $this->route('volunteer'),
            'address' => 'max:1000',
            'station_id' => 'required|string',
        ];

        if ($request->password_toggle) {
            $rules['password_toggle'] = 'required|accepted';
            $rules['new_password'] = 'required|string|min:8|max:100|confirmed';
            $rules['new_password_confirmation'] = 'required|string|min:8|max:100';
        }

        if ( $request->old_upload_count[0]) {
            // $rules['image'] = 'filled|file|image|max:2000';
        } else {
            $rules['image'] = 'max:2000';
        }

        return $rules;
    }
}