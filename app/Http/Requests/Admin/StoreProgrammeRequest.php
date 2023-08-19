<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class StoreProgrammeRequest extends FormRequest
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
            'title' => 'required|string|max:100|unique:programmes,title,1,permanently_delete',
            'description' => 'required|string',
            'station_id' => 'required|string',
            'type' => 'required',
            'content' => 'required|array',
            'thumbNail' => 'nullable|mimes:jpeg,png,jpg|max:2000'
        ];

        if($request->type == 'video') {
            $rules['content.*'] = 'required|mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi';
        } else if($request->type == 'audio') {
            $rules['content.*'] = 'required|mimetypes:audio/mpeg,mpga,mp3,audio/wav,audio/x-wav,audio/wave';
        } else if($request->type == 'photo') {
            $rules['content.*'] = 'required|mimes:jpeg,png,jpg';
        } else if($request->type == 'file') {
            $rules['content.*'] = 'required|mimes:pdf';
        }

        return $rules;
    }
}