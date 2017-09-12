<?php
#------------------------------------------------
#	EXAMPLE 8: Using neatform with twitter bootstrap, jQuery plugin and AJAX.
#------------------------------------------------
# In this example, we'll use the bootstrap-vertical render mode/layout.



if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}

use afflicto\form\form as form;



form::jQueryPlugin(true);
form::setFramework('bootstrap');

$form = new form('bootstrap-vertical', 'POST');
$form->ajax();
$form->autocomplete(false);

$form->username = form::text('Username', 'username')->autofocus();
$form->username->rules(array(
	'required' => true,
	'alphanumeric' => true,
	'minlen' => 3,
	'maxlen' => 10,
));
$form->username->help('Letters & numbers only.');

$form->email = form::email('Email', 'email');
$form->email->rules(array(
	'email' => true
));

$form->password = form::password('Password', 'password');

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
	/**
	* now that we're using twitter bootstrap, let's create a proper alert box for the status message.
	* We'll follow the bootstrap advice and add a dismiss button in the alert as well.
	*/
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
			<h2>Twitter Bootstrap</h2>
			<?php
				echo $content;
			?>
		</div>
	</div>
</body>
</html>