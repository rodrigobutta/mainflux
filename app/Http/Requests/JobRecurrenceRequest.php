<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobRecurrenceRequest extends FormRequest
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
            'recurrence_start_date' => 'required_if:is_recurring,1|date',
            'recurrence_end_date' => 'required_if:is_recurring,1|date|after_or_equal:recurrence_start_date',
            'recurring_frequency' => 'required'
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
            'recurrence_start_date' => trans('job.recurrence_start_date'),
            'recurrence_end_date' => trans('job.recurrence_end_date'),
            'recurring_frequency' => trans('job.recurring_frequency')
        ];
    }
}
