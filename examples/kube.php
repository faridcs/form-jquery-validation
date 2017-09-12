<?php
#------------------------------------------------
#	EXAMPLE 10: Using the Kube CSS Framework
#------------------------------------------------
# The Kube CSS Framework has two layout modes.
# It also supports prepend and append.



if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}

use afflicto\form\form as form;



form::jQueryPlugin(true);
form::setFramework('kube');

$form = new form('kube', 'POST');
$form->ajax();
$form->autocomplete(false);

# chaining!
$form->twitter = form::text('Twitter Username', 'twitter')->autofocus()->rules(array('required' => true))->prepend('@');

# without chaining
$form->donate = form::text('Amount', 'amount');
$form->donate->setValue('10');
$form->donate->prepend('$');
$form->donate->append('.00');
$form->donate->rules(array(
	'min' => 10,
	'max' => 999,
	'numeric' => true,
));

$form->submit = form::submit('Sign up', 'signup')->addClass('btn');
$form->reset = form::reset('Reset', 'reset')->addClass('btn');


if ($form->wasPosted()) {

	if ($form->valid()) {
		$response = array('status' => 'success', 'message' => 'Thank you for signing up!');
	}else {
		$response = array('status' => 'error', 'message' => 'Validation did not succeed on the server.');
	}

	die(json_encode($response));

}else {
	$content = $form->display('kube');
}
?><!DOCTYPE html>
<html>
<head>
	<script src="lib/jquery.min.js"></script>
	<script src="../js/form.js"></script>
	<script src="../js/validators.js"></script>
	
	<!-- kube framework -->
	<script src="lib/kube/js/kube.buttons.js"></script>
	<link rel="stylesheet" href="lib/kube/css/kube.min.css">

	<script>
	$.fn.neatForm.setAjaxHandler(function(form, data) {
		var response;
		try {
			response = $.parseJSON(data);
		}catch(e) {
			response = {'status': 'error', 'message': 'Invalid JSON Retreived from server: <br><pre>' + data + '</pre>'};
		}
		//if response status is success, we can slide up the form
		if (response.status == 'success') {
			form.slideUp();
		}
		form.next('.form-response').remove();
		form.after('<div style="display: none;" class="form-response message message-' + response.status +'"><span class="close" ></span>' + response.message +'</div>');
		form.parent().find('.form-response')
			.slideDown()
			.find('span.close').click(function() {
				$(this).parent().slideUp(function() {
					$(this).remove();
				});
			});
	});
	</script>
</head>
<body>
	<div style="margin: 20px auto; max-width: 620px;" class="container-fluid">
		<div class="row">
			<h2>Using Kube Framework</h2>
			<?php
				echo $content;
			?>
		</div>
	</div>
</body>
</html>