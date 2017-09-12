;(function(nf, window, document, undefined) {

	/**
	 * @since 1.3
	 **/
	nf.addValidator('domain', function(element, option, value) {
		var re = /^[a-zA-Z0-9][a-zA-Z0-9-]{, 61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/;
    	return (re.test(value)) ? true : 'Must be a valid domain name';
	});

	/**
	 * @since 1.3
	 */
	nf.addValidator('equalTo', function(element, option, value) {
		var targetElement = element.parent('[name="' + option + '"]');
		return (value == targetElement.val()) ? true : 'Must be equal to ' .targetElement.attr('name');
	});

	nf.addValidator('required', function(element, option, value) {
		return (value.length > 0) ? true : 'Required';
	});

	nf.addValidator('maxlen', function(element, option, value) {
		return (value.length <= option) ? true : 'Maximum ' + option + ' characters';
	});

	nf.addValidator('minlen', function(element, option, value) {
		return (value.length >= option) ? true : 'Minimum ' + option + ' characters';
	});

	nf.addValidator('min', function(element, option, value) {
		return (value >= option) ? true : 'Must be greater than ' + (option-1);
	});

	nf.addValidator('max', function(element, option, value) {
		return (value <= option) ? true : 'Must be less than ' + (option+1);
	});

	nf.addValidator('email', function(element, option, value) {
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    	return (re.test(value)) ? true : 'Must be a valid E-mail address';
	});

	nf.addValidator('url', function(element, option, value) {
		var re = /(https?:\/\/(?:www\.|(?!www))[^\s\.]+\.[^\s]{2,}|www\.[^\s]+\.[^\s]{2,})/;
		return (re.test(value)) ? true : 'Must be a valid URL';
	});

	nf.addValidator('alpha', function(element, option, value) {
		var re = /^[a-zA-Z]*$/;
		return (re.test(value)) ? true : 'Letters only';
	});

	nf.addValidator('numeric', function(element, option, value) {
		var re = /^[0-9]*$/;
		return (re.test(value)) ? true : 'Numbers only';
	});

	nf.addValidator('alphanumeric', function(element, option, value) {
		var re = /^[a-zA-Z0-9]*$/;
		return (re.test(value)) ? true : 'Letters & Numbers only';
	});

	nf.addValidator('creditcard', function(element, option, value) {
		var re = /^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/;
		return (re.test(value)) ? true: 'Must be a valid card mumber';
	});

	nf.addValidator('visa', function(element, option, value) {
		var re = /^4[0-9]{12}(?:[0-9]{3})?$/;
		return (re.test(value)) ? true: 'Must be a valid VISA card number';
	});

	nf.addValidator('mastercard', function(element, option, value) {
		var re = /^5[1-5][0-9]{14}$/;
		return (re.test(value)) ? true: 'Must be a valid MasterCard number';
	});

	nf.addValidator('americanexpress', function(element, option, value) {
		var re = /^3[47][0-9]{13}$/;
		return (re.test(value)) ? true: 'Must be a valid American Express card number';
	});

	nf.addValidator('dinersclub', function(element, option, value) {
		var re = /^3(?:0[0-5]|[68][0-9])[0-9]{11}$/;
		return (re.test(value)) ? true: 'Must be a valid DinersClub card number';
	});

	nf.addValidator('discover', function(element, option, value) {
		var re = /^6(?:011|5[0-9]{2})[0-9]{12}$/;
		return (re.test(value)) ? true: 'Must be a valid Discover card number';
	});

	nf.addValidator('jcb', function(element, option, value) {
		var re = /^(?:2131|1800|35\d{3})\d{11}/;
		return (re.test(value)) ? true: 'Must be a valid JCB card number';
	});

	

})(jQuery.fn.neatForm, window, document);