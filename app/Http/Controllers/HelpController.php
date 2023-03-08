<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller
{
	public function get_average_marks($algo, $grouped_employer_rates)
	{
		$rating = [];
		foreach ($grouped_employer_rates as $ger) {
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
			$employer_average = $algo->get_average($employer_ema); //среднее по оценкам работодателей
			array_push($rating, array($ger[0]['resume_id'], $employer_average));
		}
		return $rating;
	}
	public function get_average_quality_marks($algo, $grouped_employer_rates)
	{
		$rating = [];
		foreach ($grouped_employer_rates as $ger) {
			$unique_quality_ids = array_unique(array_map(function ($el) {
				return $el->quality_id;
			}, $ger)); //выделяем уникальные quality_id из оценок по одному работодателю
			$employer_rates = array();
			$unique_quality_ids = array_values($unique_quality_ids);
			for ($i = 0; $i < count($unique_quality_ids); $i++) {
				$usi = $unique_quality_ids[$i];
				$current_qualities = array_filter($ger, function ($el) use ($usi) {
					return $el->quality_id == $usi;
				});
				//выделяем updated_at и переводим его в дни
				$time = array_values(array_map(function ($el) {
					return strtotime($el->updated_at->toDateString()) / (3600 * 24);
				}, $current_qualities));
				//выделяем оценки за quality
				$quality_rate = array_values(array_map(function ($el) {
					return $el->quality_rate;
				}, $current_qualities));
				//если оценок больше одной, то рассчет тренда по методу наименьших квадратов
				if (count($current_qualities) > 1) {
					array_push($employer_rates, array($unique_quality_ids[$i], $algo->get_trend(array($time, $quality_rate))));
				} else { //если оценка только одна, то тренда не будет
					array_push($employer_rates, array($unique_quality_ids[$i], array($time, $quality_rate)));
				}
			}
			$employer_ema = array();
			//получаем ema для каждой оценки
			foreach ($employer_rates as $rate) {
				array_push($employer_ema, array($rate[0], $algo->get_ema($rate[1][1])));
			}
			$employer_average = $algo->get_average($employer_ema); //среднее по оценкам работодателей
			array_push($rating, array($ger[0]['employer_id'], $employer_average));
		}
		return $rating;
	}
}
