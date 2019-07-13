<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskSignOffLogRequest extends FormRequest
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
        $rules = [
            'status' => 'required',
            'sign_off_remarks' => 'required'
        ];

        return $rules;
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'sign_off_remarks' => trans('task.sign_off_remarks'),
            'sign_off_action_remarks' => trans('task.sign_off_remarks'),
            'status' => trans('task.sign_off_status')
        ];
    }
}
