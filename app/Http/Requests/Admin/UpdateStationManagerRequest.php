<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Request;
use App\Rules\OldPassword;
use App\Rules\MultipleUnique;
use Illuminate\Foundation\Http\FormRequest;
use Auth;

class UpdateStationManagerRequest extends FormRequest
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
        $userTypeID = Auth::user()->user_type_id;

        $rules = [
            'name' => 'required|string|max:100',
            'email' => ['required', 'string', 'email', 'max:100', new MultipleUnique('users', 'email', $this->route('stationManager'), ['user_type_id' => 2])],
        ];

        if ($request->password_toggle) {
            $rules['password_toggle'] = 'required|accepted';

            if ($userTypeID == 1) {
                $rules['new_password'] = 'required|string|min:8|max:100';
            } else {
                $rules['old_password'] = ['required', 'string', 'min:8', 'max:100', new OldPassword];
                $rules['new_password'] = 'required|string|min:8|max:100|different:old_password|confirmed';
            }

            $rules['new_password_confirmation'] = 'required|string|min:8|max:100';
        }

        if ($userTypeID == 1) {
            $rules['station_id'] = 'required|string';
        }

        return $rules;
    }
}
