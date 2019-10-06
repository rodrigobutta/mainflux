<?php

namespace App\Http\Requests;

use App\Repositories\JobRepository;
use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
{
    protected $repo;

    public function __construct(JobRepository $repo)
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
            'job_category_id' => 'required',
            'job_priority_id' => 'required',
            'client_id' => 'required',
            'contractor_id' => 'required',
            'upload_token' => 'required'
        ];

        if ($this->method() === 'POST' && config('config.job_progress_type') === 'question') {
            $rules['question_set_id'] = 'required';
        }

        if ($this->method === 'PATCH') {
            $job = $this->repo->findByUuidOrFail($uuid);

            if ($job->progress_type === 'question') {
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
            'title' => trans('job.title'),
            'start_date' => trans('job.start_date'),
            'due_date' => trans('job.due_date'),
            'job_category_id' => trans('job.job_category'),
            'job_priority_id' => trans('job.job_priority'),
            'question_set_id' => trans('job.question_set'),
            'upload_token' => trans('general.upload_token'),
            'client_id' => trans('client.client'),
            'contractor_id' => trans('contractor.contractor')
        ];
    }
}
