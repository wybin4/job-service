<?php

namespace App\Http\Requests\References;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddProfession extends FormRequest
{
	public function rules()
	{
		return [
			'profession_name' => 'required|string|max:255|unique:professions',
			'subsphere_id' => 'required|integer'
		];
	}
	public function messages()
	{
		return [
			'profession_name.unique' => 'Такая профессия уже существует',
			'profession_name.required' => 'Поле не может быть пустым',
			'profession_name.max:255' => 'Слишком длинное название профессии',
			'subsphere_id.required' => 'Укажите область деятельности',
		];
	}
	protected function failedValidation(Validator $validator)
	{
		throw (new ValidationException($validator))
			->errorBag($this->errorBag)
			->redirectTo($this->getRedirectUrl());
	}
}
