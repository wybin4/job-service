<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlgorithmController extends Controller
{
    public function reverse($matrix)
    {
        $a = $matrix;
        $e = array();
        $count = count($a);
        for ($i = 0; $i < $count; $i++)
            for ($j = 0; $j < $count; $j++)
                $e[$i][$j] = ($i == $j) ? 1 : 0;

        for ($i = 0; $i < $count; $i++) {
            $tmp = $a[$i][$i];
            for ($j = $count - 1; $j >= 0; $j--) {
                $e[$i][$j] /= $tmp;
                $a[$i][$j] /= $tmp;
            }

            for ($j = 0; $j < $count; $j++) {
                if ($j != $i) {
                    $tmp = $a[$j][$i];
                    for ($k = $count - 1; $k >= 0; $k--) {
                        $e[$j][$k] -= $e[$i][$k] * $tmp;
                        $a[$j][$k] -= $a[$i][$k] * $tmp;
                    }
                }
            }
        }

        for ($i = 0; $i < $count; $i++)
            for ($j = 0; $j < $count; $j++)
                $a[$i][$j] = $e[$i][$j];


        return $a;
    }
    public function vectorvectormult($vector1, $vector2)
    {
        $v2_size = count($vector2);
        $m3 = 0;
        $m3 += $vector1[0];
        for ($j = 0; $j < $v2_size; $j++) {
            $m3 += $vector1[$j + 1] * $vector2[$j];
        }
        return $m3;
    }
    public function vectormatrixmult($vector, $matrix)
    {
        $m3 = [];
        for ($i = 0; $i < count($matrix); $i++) {
            $m3[$i] = 0;
            for ($j = 0; $j < count($matrix[0]); $j++) {
                $m3[$i] += $matrix[$i][$j] * $vector[$j];
            }
        }
        return $m3;
    }
    public function matrixmult($m1, $m2)
    {
        $r = count($m1);
        $c = count($m2[0]);
        $p = count($m2);
        $m3 = array();
        for ($i = 0; $i < $r; $i++) {
            for ($j = 0; $j < $c; $j++) {
                $m3[$i][$j] = 0;
                for ($k = 0; $k < $p; $k++) {
                    $m3[$i][$j] += $m1[$i][$k] * $m2[$k][$j];
                }
            }
        }
        return ($m3);
    }
    public function transpose($array)
    {
        return array_map(null, ...$array);
    }
    public function find_linear_regression($x, $y, $u)
    {
        for ($i = 0; $i < count($x); $i++) {
            $divizor = $u[$i];
            $x[$i] = array_map(
                function ($l_x) use ($divizor) {
                    return $l_x / $divizor;
                },
                $x[$i]
            );
        }
        $arr = array_fill(0, count($x[0]), 1);
        array_unshift($x, $arr);
        $x_t = $this->transpose($x);
        $x_t_and_x = $this->matrixmult($x, $x_t);
        $x_t_and_y = $this->vectormatrixmult($y, $x);
        $x_t_and_x_reverse = $this->reverse($x_t_and_x);
        $result = $this->vectormatrixmult($x_t_and_y, $x_t_and_x_reverse);
        return $result;
    }
    public function get_average($arr)
    {
        $summ = array_sum(array_map(
            function ($el) {
                return $el[1];
            },
            $arr
        ));
        $length = count($arr);
        return $summ / $length;
    }
    public function get_diff($emp_val, $self_val)
    {
        return 1 - tanh((max($emp_val, $self_val) - min($emp_val, $self_val)) / 5);
    }
    public function get_ema($arr)
    {
        $alpha = 2 / (count($arr) + 1);
        $ema = $arr[0];
        for ($i = 1; $i < count($arr); $i++) {
            $ema *= (1 - $alpha);
            $ema += $arr[$i] * $alpha;
        }
        return $ema;
    }
    public function solver($a, $b, $c, $d, $e, $f)
    {
        $y = ($a * $f - $c * $d) / ($a * $e - $b * $d);
        $x = ($c * $e - $b * $f) / ($a * $e - $b * $d);
        return array($x, $y);
    }
    public function get_trend($arr)
    {
        $t = $arr[0];
        $y = $arr[1];
        $square_t =
            array_map(
                function ($el) {
                    return $el * $el;
                },
                $t
            );
        $square_y =
            array_map(
                function ($el) {
                    return $el * $el;
                },
                $y
            );
        $t_x_y = array();
        for ($i = 0; $i < count($t); $i++) {
            array_push($t_x_y, $t[$i] * $y[$i]);
        }
        $sum_t = array_sum($t);
        $sum_y = array_sum($y);
        $sum_square_t = array_sum($square_t);
        $sum_square_y = array_sum($square_y);
        $sum_t_x_y = array_sum($t_x_y);
        $a_and_b = $this->solver(count($t), $sum_t, $sum_y, $sum_t, $sum_square_t, $sum_t_x_y);
        $count = ($t[count($t) - 1] - $t[0]) / 30;
        $result_t = array();
        $result_y = array();
        $val = null;
        for ($i = 0; $i < $count; $i++) {
            $val = $t[0] + 30 * $i;
            array_push($result_t, $val);
            array_push($result_y, $a_and_b[1] * $val + $a_and_b[0]);
        }
        return array($result_t, $result_y);
    }
    public function _group_by($array, $key)
    {
        $resultArr = [];
        foreach ($array as $val) {
            $resultArr[$val[$key]][] = $val;
        }
        return $resultArr;
    }
    public function std_deviation($arr)
    {
        $num_of_elements = count($arr);
        $variance = 0.0;
        $average = array_sum($arr) / $num_of_elements;
        foreach ($arr as $i) {
            $variance += pow(($i - $average), 2);
        }
        return (float)sqrt($variance / $num_of_elements);
    }
    public function z_normalize($val, $arr)
    {
        //среднее количество
        $avg_xi = array_sum($arr) / count($arr);
        $standard_deviation_xi = $this->std_deviation($arr);
        if ($standard_deviation_xi != 0) {
            $xi_result = ($val - $avg_xi) / $standard_deviation_xi; //z-нормализованное значение xi
            return $xi_result;
        } else return 1;
    }
}
