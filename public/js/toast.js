function create_notify(status, title, text, gap = 80, position = 'right top'){
	new Notify({
		status: status,
		title: title,
		text: text,
		effect: 'slide',
		speed: 300,
		customClass: '',
		customIcon: '',
		showIcon: true,
		showCloseButton: true,
		autoclose: true,
		autotimeout: 3000,
		gap: gap,
		distance: 20,
		type: 3,
		position: position
	})
}