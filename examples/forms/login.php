<?php

use afflicto\form\form as form;

# create a form, we won't both setting the ACTION attribute here.
$form = new form('login', 'POST');
$form->autocomplete(false);

# add some form elements
$form->username = form::email('E-mail', 'email')->autofocus()->rules(array('email' => true, 'required' => true));

$form->password = form::password('Password', 'password')->rules(array('required' => true));

$form->login = form::submit('login', 'Log in');

# here, we return the form.
# That way, when we include it, we can do $form = include('login.php')
return $form;