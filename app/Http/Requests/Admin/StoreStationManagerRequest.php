<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MultipleUnique;

class StoreStationManagerRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:100', new MultipleUnique('users', 'email', false, ['user_type_id' => 2])],
            'password' => 'required|string|min:8|max:100|confirmed',
            'password_confirmation' => 'required|string|min:8|max:100',
            'station_id' => 'required|string',
        ];
    }
}