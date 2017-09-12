<?php
#------------------------------------------------
#	EXAMPLE 11: Integration
#------------------------------------------------
# A question was put to me recently:
# "How do I create a form on one page and process it on another?"
# 
# The solution is to make sure you have the $form object available in both pages.
# In most PHP Frameworks and applications this is easily accomplished because of the fact that the framework runs in one index.php file.
# 'pages' in a framework that does that is handled dynamically using $_GET['page'] or something similar (or mod_rewrite site.com/path/to/file) etc.
# If you aren't using a framework, or are not familiar with the process, here's a neat solution:
#
# First, notice in the examples directory, a 'forms' directory. Open the forms/login.php file and read that... then come back here.
# Ok, you read the forms/form.php file?
# Now we can include that form on 'this' page....

if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}

# the include function can process any 'return' statement, which is kinda cool!
$form = include('forms/login.php');

# Let's say 'this' page displays the form.
# And integration_process.php processes it when POSTed to.
# Let's set the action attribute.
$form->attr('action', 'integration_process.php');

# display the form.
echo $form->display('table');
