<?php

namespace App\Http\Requests\Resume;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class EditResumeRequest extends FormRequest
{
	public function rules()
	{
		return [
			'company_name.*' => 'required',
			'work_title.*' => 'required',
			'university_name.*' => 'required',
			'speciality_name.*' => 'required',
			'platform_name.*' => 'required',
			'course_name.*' => 'required'
		];
	}
	public function messages()
	{
		return [
			'company_name.*.required' => 'Укажите название компании',
			'work_title.*.required' => 'Укажите название должности',
			'university_name.*.required' => 'Укажите название учебного заведения',
			'speciality_name.*.required' => 'Укажите название специальности',
			'platform_name.*.required' => 'Укажите название платформы',
			'course_name.*.required' => 'Укажите название курса',
		];
	}
	protected function failedValidation(Validator $validator)
	{
		throw (new ValidationException($validator))
			->errorBag($this->errorBag)
			->redirectTo($this->getRedirectUrl());
	}
}
