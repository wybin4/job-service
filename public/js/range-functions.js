function get_ema(arr) {
	const alpha = 2 / (arr.length + 1);
	let ema = arr[0];
	for (let i = 1; i < arr.length; i++) {
		ema *= (1 - alpha);
		ema += arr[i] * alpha;
	}
	return ema;
}

function solver(a, b, c, d, e, f) {
	var y = (a * f - c * d) / (a * e - b * d)
	var x = (c * e - b * f) / (a * e - b * d)
	return [x, y];
}

function get_trend(arr) {
	let t = arr[0];
	let y = arr[1];
	let square_t = t.map(a => a * a);
	let square_y = y.map(a => a * a);
	let t_x_y = [];
	for (let i = 0; i < t.length; i++) {
		t_x_y.push(t[i] * y[i]);
	}
	const sum_t = t.reduce(function (sum, elem) {
		return sum + elem;
	}, 0);
	const sum_y = y.reduce(function (sum, elem) {
		return sum + elem;
	}, 0);
	const sum_square_t = square_t.reduce(function (sum, elem) {
		return sum + elem;
	}, 0);
	const sum_square_y = square_y.reduce(function (sum, elem) {
		return sum + elem;
	}, 0);
	const sum_t_x_y = t_x_y.reduce(function (sum, elem) {
		return sum + elem;
	}, 0);
	const a_and_b = solver(t.length, sum_t, sum_y, sum_t, sum_square_t, sum_t_x_y);
	const count = (t[t.length - 1] - t[0]) / 30;
	let result_t = [];
	let result_y = [];
	let val;
	for (let i = 0; i < count; i++) {
		val = t[0] + 30 * i;
		result_t.push(val);
		result_y.push(a_and_b[1] * val + a_and_b[0]);
	}
	return [result_t, result_y];
}

function get_diff(emp_val, self_val) {
	return 1 - Math.tanh((Math.max(emp_val, self_val) - Math.min(emp_val, self_val)) / 5);
}

function getNoun(number, one, two, five) {
	let n = Math.abs(number);
	n %= 100;
	if (n >= 5 && n <= 20) {
		return five;
	}
	n %= 10;
	if (n === 1) {
		return one;
	}
	if (n >= 2 && n <= 4) {
		return two;
	}
	return five;
}

function get_average(arr) {
	const sum = arr.reduce((acc, number) => acc + number[1], 0);
	const length = arr.length;
	return sum / length;
}

function compare(a, b) {
	return a[1] - b[1]
}