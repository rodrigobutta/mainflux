<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskPriorityRequest extends FormRequest
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
        $id = $this->route('id');

        $rules = [];

        if ($this->method() === 'POST') {
            $rules['name'] = 'required|unique:task_priorities';
        } elseif ($this->method() === 'PATCH') {
            $rules['name'] = 'required|unique:task_priorities,name,'.$id.',id';
        }

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
            'name' => trans('task.task_priority_name')
        ];
    }
}
