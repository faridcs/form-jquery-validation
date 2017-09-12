<?php
#------------------------------------------------
#	EXAMPLE 7: Using the jQuery plugin with the AJAX Option.
#------------------------------------------------
# In the previous example, we made use of the neatform jQuery plugin.
# But in addition to providing client-side validation, it also enables you to submit the form via AJAX.
# (Ajax let's you send data from a browser to a server without refreshing the page.)
# So let's make use of ajax to submit our form without refreshing the page!


if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}

use afflicto\form\form as form;



# We need the jQueryPlugin for this.
form::jQueryPlugin(true);

# this time, we'll create a form...
$form = new form('ajax', 'POST');

# and enable ajax
$form->ajax();

$form->autocomplete(false);

$form->username = form::text('Username', 'username')->autofocus();
$form->username->rules(array(
	'required' => true,		# makes the field required
	'alphanumeric' => true,	# letters & numbers only
	'minlen' => 3,			# minimum character length of 3
	'maxlen' => 10,			# maximum character length of 10
));

$form->email = form::email('Email', 'email');
$form->email->rules(array(
	'email' => true
));

$form->username->help('Letters & numbers only.');

$form->password = form::password('Password', 'password');

$form->submit = form::submit('Sign up', 'signup');
$form->reset = form::reset('Reset', 'reset');


if ($form->wasPosted()) {

	# run validation
	if ($form->valid()) {

		# Since we're using ajax this time, we don't want to send the entire page back to the client.
		# We just want to send a short message saying whether it was a success or not.
		# I advice you not to define contextual error or success messages in the javascript.
		# That way you only have to deal with PHP.
		# The only thing we need to do in javascript is define the default ajax response handler. (which we do on line 90)

		$response = array('status' => 'success', 'message' => 'Thank you for signing up!');
	}else {
		$response = array('status' => 'error', 'message' => 'Validation did not succeed on the server.');
	}

	# Here, we die with our json encoded array
	# We will parse this on the client using $.parseJSON().#
	# Then we might setup a global, default callback to use for all ajax forms.
	die(json_encode($response));

}else {
	$content = $form->display('table');
}
?><!DOCTYPE html>
<html>
<head>
	<!-- jQuery -->
	<script src="lib/jquery.min.js"></script>
		
	<!-- The jQuery Form Plugin -->
	<script src="../js/form.js"></script>

	<!-- You should probably also load the default validators.js file, it contains all the validators (email, url, alpha etc) validators -->
	<script src="../js/validators.js"></script>
	
	<!-- Here we can define our default ajax response handler -->
	<script>
	//this ajax handler is called when the client validation succeeds, submits the form and retrieves data from the server.
	//it receives two arguments, the form and the data from the server
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
		form.after('<div class="form-success alert-' + response.status +'">' + response.message +'</div>');
		form.find('.form-success').slideDown();
	});
	</script>

	<!-- If you aren't using twitter bootsrap or any other supported CSS framework, you can use this CSS file for minimal error/success styling -->
	<link rel="stylesheet" href="../css/neatform.css">
</head>
<body>
	<h2>Client-side Validation</h2>
	<?php
		echo $content;
	?>
</body>
</html>