<?php

namespace Humweb\Menus\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemSaveRequest extends FormRequest
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
            'menu_id'   => 'required',
            'parent_id' => 'integer',
            'url'       => 'required',
            'title'     => 'required',
        ];

        return $rules;
    }
}
