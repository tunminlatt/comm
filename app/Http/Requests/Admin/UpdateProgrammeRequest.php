<?php

namespace App\Http\Requests\Admin;

use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProgrammeRequest extends FormRequest
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
            'title' => 'required|string|max:100|unique:programmes,title,'. $this->route('programme').',id,permanently_delete,0',
            'description' => 'required|string',
            'station_id' => 'required|string',
            'content' => 'array',
            'thumbNail' => 'nullable|mimes:jpeg,png,jpg|max:2000'
        ];

        if($request->file('content') != null) {
            $rules['new_type'] = 'required';
        }
        if($request->new_type != null) {
            $rules['content'] = 'required|array';
        }

        if($request->file('content') != null && $request->new_type == 'video') {
            $rules['content.*'] = 'required|mimetypes:video/x-ms-asf,video/x-flv,video/mp4,application/x-mpegURL,video/MP2T,video/3gpp,video/quicktime,video/x-msvideo,video/x-ms-wmv,video/avi';
        } else if($request->file('content') != null && $request->new_type == 'audio') {
            $rules['content.*'] = 'required|mimetypes:audio/mpeg,mpga,mp3,audio/wav,audio/x-wav,audio/wave';
        } else if($request->file('content') != null && $request->new_type == 'photo') {
            $rules['content.*'] = 'required|mimes:jpeg,png,jpg';
        } else if($request->file('content') != null && $request->new_type == 'file') {
            $rules['content.*'] = 'required|mimes:pdf';
        }

        return $rules;
    }
}