<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
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
        
        if ($this->method() === 'POST') {
            $rules = [
                'title' => 'required|unique:announcements'
            ];
        } elseif ($this->method() === 'PATCH') {
            $rules = [
                'title' => 'required|unique:announcements,title,'.$id.',id'
            ];
        }

        return $rules + [
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date',
            'is_public' => 'required|boolean',
            'restricted_to' => 'required_if:is_public,0',
            'designation_id' => 'array|required_if:restricted_to,designation',
            'location_id' => 'array|required_if:restricted_to,location',
            'user_id' => 'array|required_if:restricted_to,user'
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
            'title' => trans('announcement.title'),
            'start_date' => trans('announcement.start_date'),
            'end_date' => trans('announcement.end_date'),
            'is_public' => trans('announcement.public'),
            'restricted_to' => trans('announcement.restricted_to'),
            'designation_id' => trans('designation.designation'),
            'location_id' => trans('location.location'),
            'user_id' => trans('user.user'),
        ];
    }
}
