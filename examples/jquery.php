<?php
#------------------------------------------------
#	EXAMPLE 6: Using the jQuery plugin with client-side validation.
#------------------------------------------------
# In the previous example, we added validation rules to our form elements.
# This time, we'll include jQuery and the neatform jQuery plugin to add client-side validation.
# For AJAX form submission, check example 6 (examples/ajax.php)


if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}

use afflicto\form\form as form;

# This will initialize the jQuery form plugin on the form.
# Everything from here to line 87 is the same as the previous example.
# So scroll down to line 87!
form::jQueryPlugin(true);

$form = new form('jquery', 'POST');
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

		$content = 'Success! :)';

	}else {
		# this time around, elements will have errors attached to them
		$content = $form->display('table');
	}

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