<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            return [
                'name' => 'required|unique:departments'
            ];
        } elseif ($this->method() === 'PATCH') {
            return [
                'name' => 'required|unique:departments,name,'.$id.',id'
            ];
        }
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => trans('department.name')
        ];
    }
}
