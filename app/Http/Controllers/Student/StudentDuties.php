<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\EmployerRate;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Vacancy;
use App\Models\VacancySkillRate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Redirect;

class StudentDuties extends Controller
{
    public function viewPlacesOfWork()
    {
        $status = [3, 8, 9];
        $places_of_work = Interaction::where('student_id', Auth::user()->id)
            ->whereIn('interactions.status', $status)
            ->join('vacancies', 'vacancies.id', '=', 'interactions.vacancy_id')
            ->join('employers', 'employers.id', '=', 'vacancies.employer_id');
        $vacancy_ids = Student::find(Auth::user()->id)->interaction->pluck('vacancy_id')->toArray();
        $employer_ids = Vacancy::find($vacancy_ids)->unique('employer_id')->pluck('employer_id')->toArray();
        $vacancies_with_rate = EmployerRate::whereIn('employer_id', $employer_ids)
            ->where('student_id', Auth::user()->id)
            ->get()
            ->unique('employer_id')
            ->pluck('employer_id')
            ->toArray();
        $vacancies_with_rate = Vacancy::whereIn('employer_id', $vacancies_with_rate)
            ->whereIn('vacancies.id', $vacancy_ids)
            ->pluck('vacancies.id')
            ->toArray();
        $vacancies = Vacancy::whereIn('vacancies.id', Student::find(Auth::user()->id)->interaction->pluck('vacancy_id')->toArray())
            ->join('employers', 'employers.id', '=', 'vacancies.employer_id')
            ->join('professions', 'professions.id', '=', 'vacancies.profession_id')
            ->get(['vacancies.id', 'professions.profession_name as work_title', 'employers.name as company_name'])
            ->toArray();
        $existed_we_company_names = Auth::user()->resume->work_experience->pluck('company_name')->toArray();
        $existed_we_work_titles = Auth::user()->resume->work_experience->pluck('work_title')->toArray();
        $work_experiences = array_filter($vacancies, function ($vacancy) use ($existed_we_company_names, $existed_we_work_titles) {
            return !in_array($vacancy["work_title"], $existed_we_work_titles) && !in_array($vacancy["company_name"], $existed_we_company_names);
        });
        $work_experiences = array_values($work_experiences);
        $work_experiences = array_map(function ($we) {
            return $we["id"];
        }, $work_experiences);
        $places_of_work = $places_of_work->select(
            '*',
            'vacancies.profession_id as vacancy_profession_id',
            'employers.id as employer_id',
            'interactions.status as work_status',
            'interactions.id as interaction_id',
            'interactions.updated_at as date_end',
            'vacancies.location as company_location'
        )
            ->orderBy('interactions.created_at', 'desc')
            ->get();
        return view("student.places-of-work", compact('places_of_work', 'vacancies_with_rate', 'work_experiences'));
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
            $folderPath = storage_path() . '\app\public\images\\';
            dd($folderPath);
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
