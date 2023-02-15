<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use App\Models\Vacancy;
use App\Models\VacancySkill;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Skill;
use App\Models\Profession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendStudentVacancyLink;
use App\Models\Employer;
use App\Models\Resume;
use App\Models\SphereOfActivity;
use App\Models\StudentSkill;
use App\Models\SubsphereOfActivity;
use App\Models\TypeOfEmployment;
use App\Models\WorkExperience;
use App\Models\WorkType;
use DateTime;

class VacancyController extends Controller
{
	public function findCandidates($id)
	{
		$vacancy = Vacancy::find($id);
		$subsphere = Profession::find($vacancy->profession_id);
		$subsphere = $subsphere->subsphere_id;
		//проверяем взаимодействия с работодателей по откликам
		$employer_vacancies = Auth::guard('employer')->user()->active_vacancy;
		$employer_vacancies_ids = DB::table('vacancies')->select('id')->where('status', 0)->where('employer_id', Auth::user()->id)->get()->toArray();
		$ids = array();
		for ($i = 0; $i < count($employer_vacancies_ids); $i++) {
			array_push($ids, $employer_vacancies_ids[$i]->id);
		}
		$binded_vacancies = DB::table('vacancies')
			->join('interactions', 'vacancies.id', '=', 'interactions.vacancy_id')
			->whereIn('interactions.vacancy_id', $ids)
			->select('student_id')
			->get();
		//массив id студентов, которых нужно исключить
		$student_ids = array();
		for ($i = 0; $i < count($binded_vacancies); $i++) {
			array_push($student_ids, $binded_vacancies[$i]->student_id);
		}
		$students = DB::table('resumes')
			->join('students', 'students.id', '=', 'resumes.student_id')
			->join('professions', 'professions.id', '=', 'resumes.profession_id')
			->select('*', 'resumes.id as resume_id', 'students.id as student_id', 'resumes.created_at as resume_created_at')
			->whereNotIn('students.id', $student_ids);
		$students = $students->where('status', '=', 0);
		$students = $students->where('subsphere_id', '=', $subsphere);
		if (count($students->where('type_of_employment_id', '=', $vacancy->type_of_employment_id)->get()) != 0) {
			if ($vacancy->type_of_employment_id == 3) {
				$students = $students->where('type_of_employment_id', '=', $vacancy->type_of_employment_id);
			} else {
				$students = $students->where('type_of_employment_id', '=', $vacancy->type_of_employment_id);
				$students = $students->where('location', '=', $vacancy->location);
			}
		} else if (count($students->where('work_type_id', '=', $vacancy->work_type_id)->get()) != 0) {
			$students = $students->where('work_type_id', '=', $vacancy->work_type_id);
		}
		$students = $students->orderBy('resume_created_at', 'desc')->get();
		$work_experiences = WorkExperience::whereIn('resume_id', $students->pluck('resume_id')->toArray())->orderBy('resume_id', 'asc')->get();
		$student_hard_skills = StudentSkill::whereIn('resume_id', $students->pluck('resume_id')->toArray())
			->join('skills', 'skills.id', '=', 'student_skills.skill_id')
			->where('skill_type', 1)
			->get();
		$work_exps = [];
		if (count($work_experiences)) {
			$resume_id = $work_experiences[0]->resume_id;
			$result = new DateTime();
			$diff_res = clone $result;
			for ($i = 0; $i < count($work_experiences); $i++) {
				$ds = date_create($work_experiences[$i]->date_start);
				$de = date_create($work_experiences[$i]->date_end);
				$diff = date_diff($ds, $de);
				$result->add($diff);
				if ($i + 1 != count($work_experiences)) {
					if ($work_experiences[$i + 1]->resume_id != $resume_id) {
						if ($result->diff($diff_res)) {
							array_push($work_exps, [$resume_id, $result->diff($diff_res)->y, $result->diff($diff_res)->m]);
						}
						$resume_id = $work_experiences[$i + 1]->resume_id;
						$result = new DateTime();
						$diff_res = clone $result;
					}
				} else {
					if ($result->diff($diff_res)) {
						array_push($work_exps, [$resume_id, $result->diff($diff_res)->y, $result->diff($diff_res)->m]);
					}
				}
			}
		}
		$popular_professions = Resume::where('status', 0)
			->join('professions', 'professions.id', '=', 'resumes.profession_id')
			->select(DB::raw('count(*) as profession_name_count, profession_name'))
			->groupBy('profession_name')
			->orderBy('profession_name_count', 'desc')
			->paginate(3);
		return view('employer.vacancy.find-candidates', compact('vacancy', 'students', 'work_exps', 'student_hard_skills', 'popular_professions'));
	}
	public function vacancyDetails($id)
	{
		$vacancy = Vacancy::find($id);
		if ($vacancy->description) {
			$description = nl2br(\Illuminate\Support\Str::markdown($vacancy->description));
		} else $description = "";
		$vacancy_skills = VacancySkill::where('vacancy_id', $vacancy->id)
			->join('skills', 'skills.id', '=', 'vacancy_skills.skill_id')
			->get();

		return view('employer.vacancy.vacancy-details', compact('vacancy', 'description', 'vacancy_skills'));
	}
	public function editVacancyPage($id)
	{
		$vacancy = Vacancy::find($id);
		if ($vacancy->description) {
			$description = nl2br(\Illuminate\Support\Str::markdown($vacancy->description));
		} else $description = "";
		$profession = Profession::find($vacancy->profession_id);
		$category = SubsphereOfActivity::find($profession->subsphere_id);
		$sphere = SphereOfActivity::find($category->sphere_id);

		$type_of_employment = TypeOfEmployment::find($vacancy->type_of_employment_id);
		$work_type = WorkType::find($vacancy->work_type_id);
		$vacancy_skills = VacancySkill::where('vacancy_id', $id)->get()->pluck('skill_id')->toArray();
		$skill = Skill::whereNotIn('id', $vacancy_skills)->get();
		return view('employer.vacancy.edit-vacancy', compact('vacancy', 'description', 'vacancy_skills'))->with('sphere', $sphere)
			->with('category', $category)
			->with('profession', $profession)
			->with('type_of_employment', $type_of_employment)
			->with('work_type', $work_type)
			->with('skill', $skill);
	}
	public function editVacancy(Request $request)
	{
		$validator = Validator::make(
			$request->all(),
			[
				'type_of_employment' => 'required|integer',
				'work_type' => 'required|integer',
				'contacts' => ['required', 'string', 'email', 'max:255']
			]
		);
		if ($request->hard_skills || $request->soft_skills) {
			$i = 0;
			if ($request->soft_skills) {
				foreach ($request->soft_skills as $index) {
					VacancySkill::create([
						'vacancy_id' => $request->vacancy_id,
						'skill_id' => $request->soft_skills[$i]
					]);
					$i++;
				}
			}
			$i = 0;
			if ($request->hard_skills) {
				foreach ($request->hard_skills as $index) {
					VacancySkill::create([
						'vacancy_id' => $request->vacancy_id,
						'skill_id' => $request->hard_skills[$i],
						'skill_rate' => $request->skill_rate[$i] ?? 0
					]);
					$i++;
				}
			}
		}
		$vacancy = Vacancy::find($request->vacancy_id);
		if ($request->salary === null) {
			$salary = 0;
		} else $salary = $request->salary;
		if ($request->work_experience === null) {
			$work_experience = 0;
		} else $work_experience = $request->work_experience;
		$vacancy->work_experience = $work_experience;
		$vacancy->salary = $salary;
		$vacancy->contacts = $request->contacts;
		$vacancy->description = $request->description;
		$vacancy->save();
		return redirect(RouteServiceProvider::EMPLOYER_HOME);
	}
	public function unarchiveVacancy(Request $request)
	{
		if ($request->vacancy_id) {
			$new_vacancy = Vacancy::find($request->vacancy_id);
			$new_vacancy->archived_at = null;
			$new_vacancy->status = 0;
			$new_vacancy->save();
		}
	}
	public function archiveVacancy(Request $request)
	{
		$vacancy = Vacancy::find($request->vacancy_id);
		$vacancy->archived_at = date('Y-m-d H:i:s');
		$vacancy->status = 1;
		$vacancy->save();
		return redirect(RouteServiceProvider::EMPLOYER_HOME);
	}
	public function sendVacancyLink($vacancy_id, $email, $profession_name)
	{
		Mail::to($email)->queue(new sendStudentVacancyLink($vacancy_id, $profession_name));
	}
	public function createVacancy(Request $request)
	{
		$profession_id = $request->profession_id;
		$validator = Validator::make(
			$request->all(),
			[
				'profession_id' => 'required|integer',
				'type_of_employment' => 'required|integer',
				'work_type' => 'required|integer',
				'contacts' => ['required', 'string', 'email', 'max:255']
			],
			[
				'profession_id.required' => 'Выберите профессию',
			]
		);
		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput();
		}

		if ($request->salary === null) {
			$salary = 0;
		} else $salary = $request->salary;
		if ($request->work_experience === null) {
			$work_experience = 0;
		} else $work_experience = $request->work_experience;
		$vacancy = Vacancy::create([
			'employer_id' => Auth::guard('employer')->id(),
			'profession_id' => $profession_id,
			'type_of_employment_id' => $request->type_of_employment,
			'work_type_id' => $request->work_type,
			'work_experience' => $work_experience,
			'salary' => $salary,
			'location' => $request->location,
			'contacts' => $request->contacts,
			'description' => $request->description,
		]);
		if ($request->hard_skills || $request->soft_skills) {
			$i = 0;
			if ($request->soft_skills) {
				foreach ($request->soft_skills as $index) {
					VacancySkill::create([
						'vacancy_id' => $vacancy->id,
						'skill_id' => $request->soft_skills[$i]
					]);
					$i++;
				}
			}
			$i = 0;
			if ($request->hard_skills) {
				foreach ($request->hard_skills as $index) {
					VacancySkill::create([
						'vacancy_id' => $vacancy->id,
						'skill_id' => $request->hard_skills[$i],
						'skill_rate' => $request->skill_rate[$i] ?? 0
					]);
					$i++;
				}
			}
		}
		$subsphere = Profession::find($request->profession_id);
		$subsphere = $subsphere->subsphere_id;
		$students = DB::table('resumes')
			->join('students', 'students.id', '=', 'resumes.student_id')
			->join('professions', 'professions.id', '=', 'resumes.profession_id')
			->where('work_type_id', '=', $request->work_type);
		if ($request->type_of_employment == 3) {
			$students = $students->where('type_of_employment_id', '=', $request->type_of_employment);
		} else {
			$students = $students->where('type_of_employment_id', '=', $request->type_of_employment);
			$students = $students->where('location', '=', $request->location);
		}
		$students = $students->where('subsphere_id', '=', $subsphere);
		$students = $students->where('newsletter_subscription', '=', 1);
		$students = $students->get();
		$i = 0;
		if ($students) {
			foreach ($students as $student) {
				$this->sendVacancyLink($vacancy->id, $student->email, $student->profession_name);
				$i++;
			}
		}
		return redirect(RouteServiceProvider::EMPLOYER_HOME);
	}
	public function addSkill(Request $request)
	{
		Skill::create([
			'skill_name' => $request->skill_name,
			'skill_type' => $request->skill_type,
		]);
	}
	public function addProfession(Request $request)
	{
		Profession::create([
			'profession_name' => $request->profession_name,
			'subsphere_id' => $request->subsphere_id,
		]);
	}
	public function vacanciesList()
	{
		$all_vacancies = Auth::User()->all_vacancy->sortByDesc('created_at');
		$active_vacancies = Auth::User()->active_vacancy->sortByDesc('created_at');
		$archive_vacancies = Auth::User()->archived_vacancy->sortByDesc('created_at');
		return view('employer.vacancy.vacancies-list')
			->with('all_vacancies', $all_vacancies)
			->with('active_vacancies', $active_vacancies)
			->with('archive_vacancies', $archive_vacancies);
	}
	public function deleteVacancy(Request $request)
	{
		Vacancy::find($request->id)->delete();
	}
}
