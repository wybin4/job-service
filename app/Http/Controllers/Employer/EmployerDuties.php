<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Redirect;

class EmployerDuties extends Controller
{
    public function viewAlterProfilePage()
    {
        return view("employer.alter-profile");
    }
    public function alterPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', Rules\Password::defaults(), 'min:8'],
            'repeat_password' => ['required', 'same:password', Rules\Password::defaults()]
        ], [
            'same'    => 'Пароли не совпадают',
            'password.min'    => 'Длина пароля меньше 8 символов',
            'repeat_password.min'    => 'Длина повторно введенного пароля меньше 8 символов',
            'required' => 'Вы не заполнили все поля'
        ]);
        $employer = Employer::find(Auth::guard('employer')->id());
        $employer->password = Hash::make($request->password);
        $employer->save();
        return Redirect::back()->withSuccess('Вы успешно изменили пароль');
    }
    public function alterProfile(Request $request)
    {
        $employer = Employer::find(Auth::guard('employer')->id());
        if ($request->ajax() && !$request->image) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'location' => ['required', 'string', 'max:500'],
            ], [
                'name.required'    => 'Название компании не указано',
                'email.required'    => 'Email не указан',
                'location.required'    => 'Местоположение не указано',
                'surname.max:255'    => 'Название компании не должно превышать 255 символов',
                'email.max:255'    => 'Email не должен превышать 255 символов',
                'location.max:500'    => 'Местоположение не должно превышать 500 символов',
                'email'    => 'Email имеет неверный формат',
            ]);
            if ($employer->name != $request->name) {
                $employer->name = $request->name;
            }
            if ($employer->email != $request->email) {
                $employer->email = $request->email;
            }
            if ($employer->location != $request->location) {
                $employer->location = $request->location;
            }
            if ($employer->description != $request->description) {
                $employer->description = $request->description;
            }
            $employer->save();
        }
        if ($request->image) {
            $folderPath = 'C:\11\new_try\laravel-9-multi-auth-system\storage\app\public\images\\';

            $image_parts = explode(";base64,", $request->image);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);

            $imageName = uniqid() . '.png';

            $imageFullPath = $folderPath . $imageName;

            file_put_contents($imageFullPath, $image_base64);
            $employer->image = $imageName;
            $employer->save();
        }
        return redirect()->back();
    }
}
