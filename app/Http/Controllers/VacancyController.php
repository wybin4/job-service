<?php

namespace App\Http\Controllers;

use App\Http\Requests\References\AddSkill;
use App\Http\Requests\Vacancy\CreateRequest;
use App\Http\Requests\Vacancy\CreateVacancyRequest;
use App\Http\Requests\Vacancy\EditRequest;
use App\Http\Requests\Vacancy\EditVacancyRequest;
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
use App\Models\ResumeSkillRate;
use App\Models\SphereOfActivity;
use App\Models\StudentSkill;
use App\Models\SubsphereOfActivity;
use App\Models\TypeOfEmployment;
use App\Models\WorkExperience;
use App\Models\WorkType;
use DateTime;
use GuzzleHttp\Promise\Create;

class VacancyController extends Controller
{

	public function findCandidates($id)
	{
		$algo = new AlgorithmController;

		$vacancy = Vacancy::find($id);
		if (!$vacancy) {
			$text = "Такой вакансии не существует, подбор кандидатов невозможен";
			return view('error.employer-error-404', compact("text"));
		} else {
			//проверяем взаимодействия с работодателем по откликам
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
			$resume = Resume::where('status', 0)
				->whereNotIn('student_id', $student_ids)
				->join('students', 'students.id', '=', 'resumes.student_id');
			if ($vacancy->type_of_employment_id == 3) {
				$resume = $resume->where('type_of_employment_id', '=', $vacancy->type_of_employment_id);
			} else {
				$resume = $resume->where('type_of_employment_id', '=', $vacancy->type_of_employment_id);
				$resume = $resume->where('location', '=', $vacancy->location);
			}
			$resume = $resume->where('work_type_id', '=', $vacancy->work_type_id);
			$resume = $resume->pluck('resumes.id')->toArray();
			$ungrouped_ss = StudentSkill::whereIn('resume_id', $resume)->get();
			$grouped_student_skills = $algo->_group_by($ungrouped_ss, 'resume_id');
			$vacancy_skills = VacancySkill::where('vacancy_id', $id)->pluck('skill_id')->toArray();
			$percent = []; // процент совпадения навыков в вакансии и навыков в каждом из резюме
			foreach ($grouped_student_skills as $gss) {
				array_push($percent, [$gss[0]->resume_id, count(array_intersect($vacancy_skills, array_map(function ($g) {
					return $g->skill_id;
				}, $gss))) / count($vacancy_skills)]);
			}
			usort($percent, function ($a, $b) {
				if ($a[0] == $b[0]) return 0;
				return ($a[0] < $b[0]) ? -1 : 1;
			});
			$skill_percent = $percent;
			$work_experiences = WorkExperience::whereIn('resume_id', $resume)->orderBy('resume_id', 'asc')->get();
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
			$id_with_exps = array_map(function ($we) {
				return $we[0];
			}, $work_exps);
			$work_exps_diff = [];
			foreach ($resume as $r) {
				if (in_array($r, $id_with_exps)) {
					array_push($work_exps_diff, $work_exps[array_search($r, array_column($work_exps, 0))]);
				} else {
					array_push($work_exps_diff, [$r, 0, 0]);
				}
			}
			usort($work_exps_diff, function ($a, $b) {
				if ($a[0] == $b[0]) return 0;
				return ($a[0] < $b[0]) ? -1 : 1;
			});
			$vacancy_work_exp = $vacancy->work_experience;
			$work_exps_diff = array_map(function ($awe) use ($vacancy_work_exp) {
				$days_resume = $awe[2] * 30.417 + $awe[1] * 365;
				$days_vacancy = $vacancy_work_exp * 365;
				return [$awe[0], abs($days_vacancy - $days_resume) / 365];
			}, $work_exps_diff);
			$employer_rates = ResumeSkillRate::whereIn('resume_id', $resume)
				->orderBy('updated_at', 'asc')
				->get();
			$self_skills = StudentSkill::whereIn('resume_id', $resume)
				->select('skill_id', 'skill_rate', 'resume_id')
				->get()->toArray();
			//сгруппированные по resume_id оценки работодателей
			$grouped_employer_rates = $algo->_group_by($employer_rates, 'resume_id');
			$self_diff_averages = [];
			$employer_averages = [];
			foreach ($grouped_employer_rates as $ger) {
				$resume_id = $ger[0]->resume_id;
				$unique_skill_ids = array_unique(array_map(function ($el) {
					return $el->skill_id;
				}, $ger)); //выделяем уникальные skill_id из оценок по одному резюме
				$employer_rates = array();
				$unique_skill_ids = array_values($unique_skill_ids);
				//проходим по оценкам работодателей за каждый навык
				for ($i = 0; $i < count($unique_skill_ids); $i++) {
					$usi = $unique_skill_ids[$i];
					//выбираем оценки одного и того же навыка разными работодателями в одном и том же резюме
					$current_skills = array_filter($ger, function ($el) use ($usi) {
						return $el->skill_id == $usi;
					});
					//выделяем updated_at и переводим его в дни
					$time = array_values(array_map(function ($el) {
						return strtotime($el->updated_at->toDateString()) / (3600 * 24);
					}, $current_skills));
					//выделяем оценки за навык
					$skill_rate = array_values(array_map(function ($el) {
						return $el->skill_rate;
					}, $current_skills));
					//если оценок больше одной, то рассчет тренда по методу наименьших квадратов
					if (count($current_skills) > 1) {
						array_push($employer_rates, array($unique_skill_ids[$i], $algo->get_trend(array($time, $skill_rate))));
					} else { //если оценка только одна, то тренда не будет
						array_push($employer_rates, array($unique_skill_ids[$i], array($time, $skill_rate)));
					}
				}
				$employer_ema = array();
				//получаем ema для каждой оценки
				foreach ($employer_rates as $rate) {
					array_push($employer_ema, array($rate[0], $algo->get_ema($rate[1][1])));
				}
				$selfs = array();
				//выделяем из всех оценок студентов оценки текущего резюме
				$current_selfs = array_filter($self_skills, function ($sr) use ($resume_id) {
					return $sr["resume_id"] == $resume_id;
				});
				$current_selfs = array_values($current_selfs);
				//выделяем те ema_ids, что есть в student_skills - которые мы можем сравнить
				$need_ema_ids = array_map(function ($self) {
					return $self["skill_id"];
				}, $current_selfs);

				$need_ema = array_values(array_filter($employer_ema, function ($ema) use ($need_ema_ids) {
					return in_array($ema[0], $need_ema_ids);
				}));

				$self_diff = [];
				for ($i = 0; $i < count($need_ema); $i++) {
					if ($need_ema[$i][1] - $current_selfs[$i]['skill_rate'] <= 0) {
						array_push($self_diff, [$need_ema[$i][0], 0]);
					} else {
						array_push($self_diff, [$need_ema[$i][0], $need_ema[$i][1] - $current_selfs[$i]['skill_rate']]);
					}
				}
				$self_diff_average = $algo->get_average($self_diff); //среднее по разности оценок студента и оценок работодателя
				$employer_average = $algo->get_average($employer_ema); //среднее по оценкам работодателей
				array_push($self_diff_averages, array($ger[0]['resume_id'], $self_diff_average));
				array_push($employer_averages, array($ger[0]['resume_id'], $employer_average));
			}
			//отделяем те резюме, где есть оценки работодателей
			$used_resumes = array_map(function ($r) {
				return $r[0];
			}, $employer_averages);
			$ur = $used_resumes;

			//выделяем резюме только с самооценкой
			$ungrouped_selfs = array_filter($self_skills, function ($skill) use ($ur) {
				return !in_array($skill['resume_id'], $ur);
			});
			//группируем по resume_id
			$grouped_self = array_values($algo->_group_by($ungrouped_selfs, 'resume_id'));
			$self_average = array();
			for ($i = 0; $i < count($grouped_self); $i++) {
				//вычисляем среднее по каждому резюме и умножаем на вес?????
				array_push($self_average, array(
					$grouped_self[$i][0]['resume_id'],
					array_sum(array_map(function ($el) {
						return $el['skill_rate'];
					}, $grouped_self[$i])) / count($grouped_self[$i])
				));
			}
			usort($employer_averages, function ($a, $b) {
				if ($a[0] == $b[0]) return 0;
				return ($a[0] < $b[0]) ? -1 : 1;
			});
			usort($self_diff_averages, function ($a, $b) {
				if ($a[0] == $b[0]) return 0;
				return ($a[0] < $b[0]) ? -1 : 1;
			});
			usort($self_average, function ($a, $b) {
				if ($a[0] == $b[0]) return 0;
				return ($a[0] < $b[0]) ? -1 : 1;
			});
			// 1) employer_averages = X1, self_diff_averages = X2, skill_percent = X3, work_exps_diff = X4
			// 2) self_average = X1, skill_percent = X2, work_exps_diff = X3
			$z_x_matrix_1 = [];
			for ($i = 0; $i < count($employer_averages); $i++) {
				$arr = [];
				$resume_id = $employer_averages[$i][0];
				array_push($arr, $employer_averages[$i][1] / 5);
				array_push($arr, $self_diff_averages[array_search($resume_id, array_column($self_diff_averages, 0))][1] / 4);
				array_push($arr, $skill_percent[array_search($resume_id, array_column($skill_percent, 0))][1]);
				array_push($arr, $work_exps_diff[array_search($resume_id, array_column($work_exps_diff, 0))][1] / 4);
				array_push($z_x_matrix_1, [$resume_id, $arr]);
			}
			$z_x_matrix_2 = [];
			for ($i = 0; $i < count($self_average); $i++) {
				$arr = [];
				$resume_id = $self_average[$i][0];
				array_push($arr, $self_average[$i][1] / 5);
				array_push($arr, $skill_percent[array_search($resume_id, array_column($skill_percent, 0))][1]);
				array_push($arr, $work_exps_diff[array_search($resume_id, array_column($work_exps_diff, 0))][1] / 4);
				array_push($z_x_matrix_2, [$resume_id, $arr]);
			}
			$x_matrix_1 = [[2, 3, 3, 4, 5], [2.5, 1.5, 1, 0.9, 0.5], [0.4, 0.5, 0.7, 0.8, 0.9], [3, 3, 2, 1, 0]];
			$x_matrix_2 = [[3, 4, 4, 5, 5], [0.4, 0.5, 0.7, 0.8, 0.9], [3, 3, 2, 1, 0]];
			$u1 = [5, 4, 1, 4];
			$u2 = [5, 1, 4];
			$y_matrix = [0.2, 0.4, 0.6, 0.8, 1];
			$equation_1 = $algo->find_linear_regression($x_matrix_1, $y_matrix, $u1);
			$res_1 = array_map(function ($matrix) use ($equation_1, $algo) {
				if ($algo->vectorvectormult($equation_1, $matrix[1]) * 5 > 0) {
					return [$matrix[0], intval(round($algo->vectorvectormult($equation_1, $matrix[1]) * 5, 0))];
				}
			}, $z_x_matrix_1);
			$equation_2 = $algo->find_linear_regression($x_matrix_2, $y_matrix, $u2);
			$res_2 = array_map(function ($matrix) use ($equation_2, $algo) {
				if ($algo->vectorvectormult($equation_2, $matrix[1]) * 5 > 0) {
					return [$matrix[0], intval(round($algo->vectorvectormult($equation_2, $matrix[1]) * 5, 0))];
				}
			}, $z_x_matrix_2);
			$resume_order = array_merge($res_1, $res_2);
			usort($resume_order, function ($a, $b) {
				if ($a[1] == $b[1]) return 0;
				return ($a[1] < $b[1]) ? 1 : -1;
			});
			$resume_order = array_filter($resume_order, function ($ro) {
				return ($ro[1] > 2);
			});
			$resume_order_ids = array_map(function ($ro) {
				return $ro[0];
			}, $resume_order);
			if (!$resume_order_ids) {
				$popular_professions = Resume::where('status', 0)
					->join('professions', 'professions.id', '=', 'resumes.profession_id')
					->select(DB::raw('count(*) as profession_name_count, profession_name'))
					->groupBy('profession_name')
					->orderBy('profession_name_count', 'desc')
					->paginate(3);
				return view('employer.vacancy.find-candidates', compact('resume_order_ids', 'popular_professions'));
			}
			$students = DB::table('resumes')
				->join('students', 'students.id', '=', 'resumes.student_id')
				->join('professions', 'professions.id', '=', 'resumes.profession_id')
				->select('*', 'resumes.id as resume_id', 'students.id as student_id', 'resumes.created_at as resume_created_at')
				->whereIn('resumes.id', $resume_order_ids)
				->orderByRaw('FIELD (resumes.id, ' . implode(', ', $resume_order_ids) . ') ASC')
				->get();
			$work_exps = array_filter($work_exps, function ($we) use ($resume_order_ids) {
				return in_array($we[0], $resume_order_ids);
			});
			$work_exps = array_values($work_exps);
			$student_skills = StudentSkill::whereIn('resume_id', $resume_order_ids)
				->join('skills', 'skills.id', '=', 'student_skills.skill_id')
				->get();
			return view('employer.vacancy.find-candidates', compact('vacancy', 'students', 'work_exps', 'student_skills', 'resume_order', 'resume_order_ids'));
		}
	}
	public function vacancyDetails($id)
	{
		$vacancy = Vacancy::find($id);
		if (!$vacancy) {
			$text = "Вакансия, которую Вы хотели посмотреть, не найдена";
			return view('error.employer-error-404', compact("text"));
		} else {
			if ($vacancy->description) {
				$description = nl2br(\Illuminate\Support\Str::markdown($vacancy->description));
			} else $description = "";
			$vacancy_skills = VacancySkill::where('vacancy_id', $vacancy->id)
				->join('skills', 'skills.id', '=', 'vacancy_skills.skill_id')
				->get();
		}
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
		return view(
			'employer.vacancy.edit-vacancy',
			compact('vacancy', 'description', 'vacancy_skills'),
			compact('sphere', 'category', 'profession', 'type_of_employment', 'work_type', 'skill')
		);
	}
	public function editVacancy(EditVacancyRequest $request)
	{
		$request->validated();
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
		return redirect(RouteServiceProvider::EMPLOYER_HOME)->with('title', 'Редактирование вакансии')->with('text', 'Вакансия успешно отредактирована');
	}
	public function unarchiveVacancy(Request $request)
	{
		if ($request->vacancy_id) {
			$new_vacancy = Vacancy::find($request->vacancy_id);
			$new_vacancy->archived_at = null;
			$new_vacancy->status = 0;
			$new_vacancy->save();
		}
		return redirect(RouteServiceProvider::EMPLOYER_HOME)->with('title', 'Восстановление вакансии')->with('text', 'Вакансия успешно восстановлена');
	}
	public function archiveVacancy(Request $request)
	{
		$vacancy = Vacancy::find($request->vacancy_id);
		$vacancy->archived_at = date('Y-m-d H:i:s');
		$vacancy->status = 1;
		$vacancy->save();
		return redirect(RouteServiceProvider::EMPLOYER_HOME)->with('title', 'Архивация вакансии')->with('text', 'Вакансия успешно архивирована');
	}
	public function sendVacancyLink($vacancy_id, $email, $profession_name)
	{
		Mail::to($email)->queue(new sendStudentVacancyLink($vacancy_id, $profession_name));
	}
	public function createVacancyView()
	{
		$sphere = SphereOfActivity::all();
		$category = SubsphereOfActivity::all();
		$profession = Profession::all();
		$type_of_employment = TypeOfEmployment::all();
		$work_type = WorkType::all();
		$skill = Skill::all();
		return view('employer.vacancy.create-vacancy', compact("sphere", "category", "profession", "type_of_employment", "work_type", "skill"));
	}
	public function createVacancy(CreateVacancyRequest $request)
	{
		$request->validated();
		$profession_id = $request->profession_id;

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
		return redirect(RouteServiceProvider::EMPLOYER_HOME)->with('title', 'Создание вакансии')->with('text', 'Вакансия успешно создана');
	}
	public function addSkill(AddSkill $request)
	{
		$request->validated();

		Skill::create([
			'skill_name' => $request->skill_name,
			'skill_type' => $request->skill_type,
		]);
		if ($request->skill_type == 0) {
			return redirect()->back()->with('title', 'Добавление качества')->with('text', 'Успешно добавили качество');
		} else {
			return redirect()->back()->with('title', 'Добавление навыка')->with('text', 'Успешно добавили навык');
		}
	}
	public function addProfession(Request $request)
	{
		Profession::create([
			'profession_name' => $request->profession_name,
			'subsphere_id' => $request->subsphere_id,
		]);
		return redirect()->back()->with('title', 'Добавление профессии')->with('text', 'Успешно добавили профессию');
	}
	public function vacanciesList()
	{
		$all_vacancies = Auth::User()->all_vacancy->sortByDesc('created_at');
		$active_vacancies = Auth::User()->active_vacancy->sortByDesc('created_at');
		$archive_vacancies = Auth::User()->archived_vacancy->sortByDesc('created_at');
		return view('employer.vacancy.vacancies-list', compact('all_vacancies', 'active_vacancies', 'archive_vacancies'));
	}
}
