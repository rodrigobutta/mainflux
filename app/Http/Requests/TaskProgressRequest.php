<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskProgressRequest extends FormRequest
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
            'progress' => 'required|integer|min:0|max:100'
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'progress' => trans('task.progress')
        ];
    }
}
