<?php

namespace App\Http\Requests\References;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddSkill extends FormRequest
{
	public function rules()
	{
		return [
			'skill_name' => 'required|string|max:255|unique:skills',
			'skill_type' => 'required|boolean'
		];
	}
	public function messages()
	{
		return [
			'skill_name.unique' => 'Такое название уже существует',
			'skill_name.required' => 'Поле не может быть пустым',
			'skill_name.max:255' => 'Слишком длинное название',
			'skill_type.required' => 'Укажите тип',
		];
	}
	protected function failedValidation(Validator $validator)
	{
		throw (new ValidationException($validator))
			->errorBag($this->errorBag)
			->redirectTo($this->getRedirectUrl());
	}
}
