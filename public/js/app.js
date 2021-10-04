$(function() {
	$('.quizAlert').fadeOut(7000, function() {
		$(this).remove();
	});

	$('.timeago').timeago();

	/** if you want to display cool tooltips do this */
	$('[data-toggle="tooltip"]').tooltip({placement: 'bottom'});
});
