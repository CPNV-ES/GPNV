<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
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
            'file' => 'required|max:10240'
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'You need to choose a photo.',
            'file.max' => 'Your file is too large, it must be less than 10 MB',
        ];
    }
}
