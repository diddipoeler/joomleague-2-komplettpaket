/**
 * javascript validation for position form
 */
window.addEvent('domready', function() {
	document.formvalidator.setHandler('select-required', function(value) {
		return value != 0;
	});
});