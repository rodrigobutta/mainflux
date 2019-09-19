<?php

namespace App\Http\Requests;

use App\Repositories\TaskRepository;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    protected $repo;

    public function __construct(TaskRepository $repo)
    {
        $this->repo = $repo;
    }

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
        $uuid = $this->route('uuid');

        $rules = [
            'title' => 'required',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'task_category_id' => 'required',
            'task_priority_id' => 'required',
            'client_id' => 'required',
            'contractor_id' => 'required',
            'upload_token' => 'required'
        ];

        if ($this->method() === 'POST' && config('config.task_progress_type') === 'question') {
            $rules['question_set_id'] = 'required';
        }

        if ($this->method === 'PATCH') {
            $task = $this->repo->findByUuidOrFail($uuid);

            if ($task->progress_type === 'question') {
                $rules['question_set_id'] = 'required';
            }
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
            'title' => trans('task.title'),
            'start_date' => trans('task.start_date'),
            'due_date' => trans('task.due_date'),
            'task_category_id' => trans('task.task_category'),
            'task_priority_id' => trans('task.task_priority'),
            'question_set_id' => trans('task.question_set'),
            'upload_token' => trans('general.upload_token'),
            'client_id' => trans('client.client'),
            'contractor_id' => trans('contractor.contractor')
        ];
    }
}
