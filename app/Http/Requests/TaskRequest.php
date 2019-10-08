<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'name' => 'required',
            'project_id' => 'required',
            'task_relevance_id' => 'required',
            'task_frequency_id' => 'required',
            'task_complexity_id' => 'required',
            'task_family_id' => 'required'
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
            'name' => trans('task.name'),
            'project_id' => trans('project.project'),
            'task_relevance_id' => trans('task_relevance.task_relevance'),
            'task_frequency_id' => trans('task_frequency.task_frequency'),
            'task_complexity_id' => trans('task_complexity.task_complexity'),
            'task_family_id' => trans('task_family.task_family')
        ];
    }
}
