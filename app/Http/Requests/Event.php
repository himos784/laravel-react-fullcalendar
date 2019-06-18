<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Validator;

class Event extends FormRequest
{
    public function __construct(){
        Validator::extend(
            'valid_days_of_the_weeks',
            function($attribute, $value, $parameters) {
                foreach ($value as $day_of_the_week) {
                    if(!in_array($day_of_the_week,['monday','tuesday','wednesday','thursday','friday','saturday','sunday'])){
                        return false;
                    }
                }

                return true;
            },
            "Invalid days of the week was included."
        );
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
        $rules = [];

        if(request()->isMethod('POST') || request()->isMethod('PATCH')){
            $rules = [
                'event'             => 'required',
                'start_date'        => 'required|date',
                'end_date'          => 'required|date',
                'days_of_the_weeks' => 'valid_days_of_the_weeks'
            ];

            if(count(explode('-',request()->get('start_date'))) == 3){
                $rules['end_date'] = $rules['end_date'] . '|after:start_date';
            }

            if(count(explode('-',request()->get('end_date'))) == 3){
                $rules['start_date'] = $rules['start_date'] . '|before:end_date';
            }
        }

        return $rules;
    }
}
