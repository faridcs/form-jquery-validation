<?php

use afflicto\form\form as form;

/**
 * @since 1.3
 */
form::addValidator('domain', function($element, $option, $value) {
	$pattern = '/^[a-zA-Z0-9][a-zA-Z0-9-]{, 61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/';
	return (preg_match($pattern, $value)) ? true: 'Must be a valid domain name';
});

/**
 * @since 1.3
 */
form::addValidator('equalTo', function($element, $option, $value) {
	return ($value == $_POST[$option])? true : 'Must be equal to ' .ucwords(preg_replace('/[-_]/', ' ', $option));
});

form::addValidator('required', function($element, $option, $value) {
	return (strlen($value) > 0) ? true : 'Required';
});

form::addValidator('maxlen', function($element, $option, $value) {
	return (strlen($value) <= $option) ? true : 'Maximum ' .$option .' characters';
});

form::addValidator('minlen', function($element, $option, $value) {
	return (strlen($value) >= $option) ? true : 'Minimum ' .$option .' characters';
});

form::addValidator('min', function($element, $option, $value) {
	return ($value >= $option) ? true : 'Must be greater than ' .($option-1);
});

form::addValidator('max', function($element, $option, $value) {
	return ($value <= $option) ? true : 'Must be less than ' .($option+1);
});

form::addValidator('email', function($element, $option, $value) {
	return (filter_var($value, FILTER_VALIDATE_EMAIL)) ? true : 'Must be a valid E-mail address';
});

form::addValidator('url', function($element, $option, $value) {
	return (filter_var($value, FILTER_VALIDATE_URL)) ? true : 'Must be a valid URL';
});

form::addValidator('alpha', function($element, $option, $value) {
	return (ctype_alpha($value)) ? true : 'Letters only';
});

form::addValidator('numeric', function($element, $option, $value) {
	return (ctype_digit($value)) ? true : 'Numbers only';
});

form::addValidator('alphanumeric', function($element, $option, $value) {
	return (ctype_alnum($value)) ? true : 'Letters & Numbers only';
});

form::addValidator('creditcard', function($element, $option, $value) {
	$pattern = '/(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/';
	return (preg_match($pattern, $value)) ? true: 'Must be a valid card mumber';
});

form::addValidator('visa', function($element, $option, $value) {
	$pattenr = '/^4[0-9]{12}(?:[0-9]{3})?$/';
	return (preg_match($pattern, $value)) ? true: 'Must be a valid VISA card number';
});

form::addValidator('mastercard', function($element, $option, $value) {
	$pattern = '/^5[1-5][0-9]{14}$/';
	return (preg_match($pattern, $value)) ? true: 'Must be a valid MasterCard number';
});

form::addValidator('americanexpress', function($element, $option, $value) {
	$pattern = '/^3[47][0-9]{13}$/';
	return (preg_match($pattern, $value)) ? true: 'Must be a valid American Express card number';
});

form::addValidator('dinersclub', function($element, $option, $value) {
	$pattern = '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/';
	return (preg_match($pattern, $value)) ? true: 'Must be a valid DinersClub card number';
});

form::addValidator('discover', function($element, $option, $value) {
	$pattern = '/^6(?:011|5[0-9]{2})[0-9]{12}$/';
	return (preg_match($pattern, $value)) ? true: 'Must be a valid Discover card number';
});

form::addValidator('jcb', function($element, $option, $value) {
	$pattern = '/^(?:2131|1800|35\d{3})\d{11}/';
	return (preg_match($pattern, $value)) ? true: 'Must be a valid JCB card number';
});