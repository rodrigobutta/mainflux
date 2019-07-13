<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskNoteRequest extends FormRequest
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
            'title' => 'required',
            'is_public' => 'required|boolean'
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
            'title' => trans('task.task_note_title'),
            'is_public' => trans('task.is_public')
        ];
    }
}
