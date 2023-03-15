<?php

namespace App\Http\Requests\Vacancy;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateVacancyRequest extends FormRequest
{
	public function rules()
	{
		return [
			'profession_id' => 'required|integer',
			'type_of_employment' => 'required|integer',
			'work_type' => 'required|integer',
			'contacts' => ['required', 'string', 'email', 'max:255']
		];
	}
	public function messages()
	{
		return [
			'work_type.required' => 'Выберите тип работы',
			'type_of_employment.required' => 'Выберите вид занятости',
			'profession_id.required' => 'Выберите профессию',
			'contacts.required' => 'Укажите контакты',
			'contacts.email' => 'Поле контакты должно содержать email',
			'contacts.max:255' => 'Поле контакты не должно быть таким длинным',
		];
	}
	protected function failedValidation(Validator $validator)
	{
		throw (new ValidationException($validator))
			->errorBag($this->errorBag)
			->redirectTo($this->getRedirectUrl());
	}
}
