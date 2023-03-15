<?php

namespace App\Http\Requests\Vacancy;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class EditVacancyRequest extends FormRequest
{
	public function rules()
	{
		return [
			'contacts' => ['required', 'string', 'email', 'max:255']
		];
	}
	public function messages()
	{
		return [
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
