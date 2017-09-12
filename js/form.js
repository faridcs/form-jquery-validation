;(function ($, window, document, undefined) {
	
	
	/* method calling logic */
	$.fn.neatForm = function() {
		var method = arguments[0];
		if (this.data('neatform')) {//data has been set, now run the method and pass the data
			//grab the method from arguments 0
			
			//remove the first element in the array
			Array.prototype.splice.call(arguments, 0,1);
			
			//prepend data
			Array.prototype.unshift.call(arguments, this.data('neatform'));
			
			if ($.fn.neatForm.methods[method]) {
		      	return $.fn.neatForm.methods[method].apply( this, arguments);
		    }else {
		    	$.error('Method ' +  method + ' does not exist on jQuery.neatForm!');
		    }
		}else {
			if ( $.fn.neatForm.methods[method] ) {
		      	return $.fn.neatForm.methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		    } else if ( typeof method === 'object' || typeof method === 'string' || ! method ) {
		    	//we are initializing
		    	this.data('neatform', {'options': $.extend({}, $.fn.neatForm.options, method)});
		      	return $.fn.neatForm.methods.init.apply( this, [this.data('neatform')]);
		    } else {
		      	$.error('Method ' +  method + ' does not exist on jQuery.neatForm!');
		    }
		}
	}

	/* holds available validators */
	$.fn.neatForm.validators = {};

	$.fn.neatForm.addValidator = function(name, callback) {
		$.fn.neatForm.validators[name] = callback;
	}

	/* default ajaxHandler */
	$.fn.neatForm.ajaxHandler = function(form, data) {
		form.after(data);
	};

	$.fn.neatForm.setAjaxHandler = function(callback) {
		$.fn.neatForm.ajaxHandler = callback;
	}

	/* default options */
	$.fn.neatForm.options = {
		ajax: false,
		rules: {
			
		},
	};
	
	/* methods */
	$.fn.neatForm.methods = {
		init: function(data) {
			var self = this;
			
			console.log('neatForm initialized.');

			this.submit(function(e) {
				if (data.options.ajax === true) {
					e.preventDefault();
				}

				if (self.neatForm('validate') === true) {

					if (data.options.ajax === true) {
						var action = self.attr('action');
						if (action === undefined) {
							action = '';
						}
						$.post(action, self.serialize(), function(data) {
							$.fn.neatForm.ajaxHandler(self, data);
							self.trigger('valid', data);
						});
					}
				}else {
					self.trigger('invalid');
					e.preventDefault();
					return false;
				}
			});
			

			// Validate on keyup
			this.keyup(function() {
				self.neatForm('validate');
			});

			return this;
		},

		onValid: function(data, callback) {
			this.bind('valid', callback);
		},

		onInvalid: function(data, callback) {
			this.bind('invalid', callback);
		},

		validate: function(data) {
			var self = this;
			var valid = true;

			// loop through all rules
			var element,rules,element,rule,option,value,result,validator,elementValid,elementError,required;
			for(element in data.options.rules) {
				// get the rules
				rules = data.options.rules[element];

				// get the element
				element = self.find('[name="' +element +'"]');

				//get the value
				value = element.val();

				//set the state to valid
				elementValid = true;

				//loop through rules
				required = false;
				if (rules.required === true) {
					required = true;
				}
				if (required == false && value.length < 1) {
					self.neatForm('setSuccess', element);
					continue;
				}
				for(rule in rules) {
					option = rules[rule];

					//test it
					validator = $.fn.neatForm.validators[rule];
					if (typeof validator == 'function') {
						result = validator(element, option, value);

						//if it's valid, it will return true. If not, it'll return a string error message.
						if (result !== true) {
							elementValid = false;
							valid = false;
							elementError = result;
						}

					}else {
						throw 'unknown validator "' +rule +'".';
					}
				}
				//check if it's valid or not. (we will only show the last error that occured)
				if (elementValid === true) {
					self.neatForm('setSuccess', element);
				}else {
					self.neatForm('setError', element, elementError);
				}
			}

			return valid;
		},

		setError: function(data, element, error) {
			element.removeClass('inputSuccess').addClass('inputError');

			//if we're using twitter bootstrap, set the control group
			element.parents('.control-group').removeClass('success').addClass('error');

			//get error & help block
			var errorBlock = this.find('.error-block[data-field="' +element.attr('name') +'"]');
			var helpBlock = this.find('.help-block[data-field="' +element.attr('name') +'"]');

			//save the original data
			element.data('error', errorBlock.html());

			//we want to save the help text as well
			element.data('help', helpBlock.html());

			//set the error
			errorBlock.html(error);

			return this;
		},

		setSuccess: function(data, element) {
			element.removeClass('inputError').addClass('inputSuccess');

			//if we're using twitter bootstrap, set the control group
			element.parents('.control-group').removeClass('error').addClass('success');
			
			//get error & help block
			var errorBlock = this.find('.error-block[data-field="' +element.attr('name') +'"]');
			var helpBlock = this.find('.help-block[data-field="' +element.attr('name') +'"]');

			//remove error from error block
			errorBlock.html("");

			//it may be that the error block is the same as the help text, in that case. we want to restore the original help text as well
			helpBlock.html(element.data('help'));


			return this;
		},
	}
	
})(jQuery, window, document);