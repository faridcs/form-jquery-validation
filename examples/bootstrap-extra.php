<?php
#------------------------------------------------
#	EXAMPLE 9: Extending form elements with twitter bootstrap
#------------------------------------------------
# Bootstrap provides a number of extra features we can use such as prepend and append.
# Here's how to use them.



if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}

use afflicto\form\form as form;



form::jQueryPlugin(true);
form::setFramework('bootstrap');

$form = new form('bootstrap-extra', 'POST');
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
	'numbers' => true,
));

$form->submit = form::submit('Sign up', 'signup');
$form->reset = form::reset('Reset', 'reset');



if ($form->wasPosted()) {

	if ($form->valid()) {

		$response = array('status' => 'success', 'message' => 'Thank you for signing up!');
	}else {
		$response = array('status' => 'error', 'message' => 'Validation did not succeed on the server.');
	}

	die(json_encode($response));

}else {
	$content = $form->display('bootstrap-vertical');
}
?><!DOCTYPE html>
<html>
<head>
	<script src="lib/jquery.min.js"></script>
	<script src="../js/form.js"></script>
	<script src="../js/validators.js"></script>
	
	<!-- twitter bootstrap -->
	<script src="lib/bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="lib/bootstrap/css/bootstrap.min.css">

	<script>
	$.fn.neatForm.setAjaxHandler(function(form, data) {
		var response;
		//let's parse it as json, we'll wrap this in a try/catch block.
		//What we can do is slide Up the form, remove it and replace it with the success message
		try {
			response = $.parseJSON(data);
		}catch(e) {
			response = {'status': 'error', 'message': 'Invalid JSON Retreived from server'};
		}
		//if response status is success, we can slide up the form
		if (response.status == 'success') {
			form.slideUp();
		}
		form.after('<div class="alert alert-' + response.status +'"><button type="button" class="close" data-dismiss="alert">&times;</button>' + response.message +'</div>');
		form.find('.form-success').slideDown();
	});
	</script>
</head>
<body>
	<div style="margin: 20px auto; max-width: 620px;" class="container-fluid">
		<div class="row">
			<h2>Extending fields using Twitter Bootstrap</h2>
			<?php
				echo $content;
			?>
		</div>
	</div>
</body>
</html>