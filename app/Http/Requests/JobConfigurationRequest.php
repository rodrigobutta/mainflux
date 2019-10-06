<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobConfigurationRequest extends FormRequest
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
            'progress_type' => 'required',
            'rating_type' => 'required'
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
            'progress_type' => trans('job.progress_type'),
            'rating_type' => trans('job.rating_type')
        ];
    }
}
