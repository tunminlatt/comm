<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Request;
use App\Rules\OldPassword;
use App\Rules\MultipleUnique;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:100', new MultipleUnique('users', 'email', $this->route('user'), ['user_type_id' => 1])],
        ];

        if ($request->password_toggle) {
            $rules['password_toggle'] = 'required|accepted';
            $rules['old_password'] = ['required', 'string', 'min:8', 'max:100', new OldPassword];
            $rules['new_password'] = 'required|string|min:8|max:100|different:old_password|confirmed';
            $rules['new_password_confirmation'] = 'required|string|min:8|max:100';
        }

        return $rules;
    }
}