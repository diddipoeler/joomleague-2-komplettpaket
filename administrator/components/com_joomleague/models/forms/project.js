/**
 * javascript validation for project form
 */
window.addEvent('domready', function() {
	document.formvalidator.setHandler('date',
			function(value) {
				if (value == "") {
					return true;
				} else {
					timer = new Date();
					time = timer.getTime();
					regexp = new Array();
					regexp[time] = new RegExp(
							'^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$', 'gi');
					return regexp[time].test(value);
				}
			});
/*
	document.formvalidator.setHandler('matchday', function(value) {
		if (value == "") {
			return false;
		} else {
			var regexp = new RegExp('^[0-9]+$', 'gi');
			if (!regexp.test(value)) {
				return false;
			} else {
				return (getInt(value) > 0);
			}
		}
	});
*/
	document.formvalidator.setHandler('select-required', function(value) {
		return value != 0;
	});

	document.formvalidator.setHandler('time',
		function (value) {
			regex=/^[0-9]{1,2}:[0-9]{1,2}$/;
			return regex.test(value);
	});
});