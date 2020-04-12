<?php
namespace Modules\Klusbib\Http\Requests;

class AssetRequest extends \App\Http\Requests\AssetRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['asset_tag'] = 'max:255';
        return $rules;
    }

//    protected function failedValidation(Validator $validator)
//    {
//        return parent::failedValidation($validator);
//    }
}