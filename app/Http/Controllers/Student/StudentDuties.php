<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Redirect;

class StudentDuties extends Controller
{
    public function viewPlacesOfWork()
    {
        $places_of_work = Interaction::where('student_id', Auth::user()->id)
            ->where('interactions.status', '=', 8)
            ->orWhere('interactions.status', '=', 9)
            ->orWhere('interactions.status', '=', 3)
            ->join('vacancies', 'vacancies.id', '=', 'interactions.vacancy_id')
            ->join('employers', 'employers.id', '=', 'vacancies.employer_id')
            ->select('*', 'vacancies.profession_id as vacancy_profession_id', 
            'interactions.status as work_status', 'interactions.id as interaction_id', 
            'interactions.updated_at as date_end', 'vacancies.location as company_location')
            ->orderBy('interactions.created_at', 'desc')
            ->get();
        return view("student.places-of-work", compact('places_of_work'));
    }
    public function viewAlterProfilePage()
    {
        return view("student.alter-profile");
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
        $student = Student::find(Auth::guard('student')->id());
        $student->password = Hash::make($request->password);
        $student->save();
        return Redirect::back()->withSuccess('Вы успешно изменили пароль');
    }
    public function alterProfile(Request $request)
    {
        $student = Student::find(Auth::guard('student')->id());
        if ($request->ajax() && !$request->image) {
            $request->validate([
                'firstname' => ['required', 'string', 'max:255', 'regex:/^[а-яА-Я]+$/u'],
                'surname' => ['required', 'string', 'max:255', 'regex:/^[а-яА-Я]+$/u'],
                'fathername' => ['max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'location' => ['required', 'string', 'max:500'],
            ], [
                'surname.regex'    => 'Фамилия должна состоять только из русских букв',
                'firstname.regex'    => 'Имя должно состоять только из русских букв',
                'surname.required'    => 'Фамилия не указана',
                'firstname.required'    => 'Имя не указано',
                'email.required'    => 'Email не указан',
                'location.required'    => 'Местоположение не указано',
                'surname.max:255'    => 'Фамилия не должна превышать 255 символов',
                'firstname.max:255'    => 'Имя не должно превышать 255 символов',
                'fathername.max:255'    => 'Отчество не должно превышать 255 символов',
                'email.max:255'    => 'Email не должен превышать 255 символов',
                'location.max:500'    => 'Местоположение не должно превышать 500 символов',
                'email'    => 'Email имеет неверный формат',
            ]);
            if ($student->student_fio != $request->surname . " " . $request->firstname . " " . $request->fathername) {
                $student->student_fio = $request->surname . " " . $request->firstname . " " . $request->fathername;
            }
            if ($student->email != $request->email) {
                $student->email = $request->email;
            }
            if ($student->location != $request->location) {
                $student->location = $request->location;
            }
            $newsletter = false;
            if ($request->newsletter_subscription) {
                $newsletter = true;
            }
            $student->newsletter_subscription = $newsletter;
            $student->save();
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
            $student->image = $imageName;
            $student->save();
        }
        return redirect()->back();
    }
}
