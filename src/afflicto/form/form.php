<?php
/**
 * @package NeatForm (NitroGFX.COM - Download Unique Web Templates)
 * @license (c) Copyright 2013 - Petter Thowsen
 * @author Petter Thowsen <afflicto@afflicto.net>
 * @version 1.4.0
 */
namespace afflicto\form;
use \Exception;

require_once 'html/element.php';
require_once 'element.php';

class form extends html\element {

	/**
	 * array of registered renderEngines.
	 * @see addRenderEngine
	 */
	static protected $renderEngines = array();

	/**
	 * Array of validators.
	 * @see addValidator
	 */
	static protected $validators = array();

	/**
	 * Whether the jQuery plugin is being used or not.
	 */
	static protected $jquery = false;

	/**
	 * The currently active CSS framework.
	 * Currenetly supports Kube and Twitter Bootstrap.
	 */
	static protected $framework = null;

	/**
	 * Whether this form should use AJAX.
	 */
	protected $ajax = false;

	/**
	 * Whether to use CSRF Protection or not.
	 * @since 1.4.0
	 * @see use the useCSRF() method to set this option.
	 */
	protected $csrf = false;

	/**
	 * Set the CSS framework to use, if any.
	 */
	static public function setFramework($fw) {
		switch($fw) {
			case 'bootstrap':
				static::$framework = array(
					'name' => 'bootstrap',
					'class' => array(
						'error' => 'inputError',
						'success' => 'inputSuccess',
					),
				);
			break;
			case 'kube':
				static::$framework = array(
					'name' => 'kube',
					'class' => array(
						'error' => 'input-error',
						'success' => 'input-success',
					)
				);
			break;
			default:
			break;
		}
	}

	/**
	 * Get the selected framework
	 */
	static public function getFramework() {
		return static::$framework;
	}

	/**
	 * Set whether to use the jQUery Plugin.
	 * @param boolean $bool
	 */
	static public function jQueryPlugin($bool = true) {
		static::$jquery = true;
	}

	/**
	 * Add a new rendering engine.
	 * 
	 * @desc the $closure receives the form instance as the first argument.
	 * @param string $name the name of the engine.
	 * @param closure $closure the closure/callback.
	 */
	static public function addRenderEngine($name, $closure) {
		static::$renderEngines[$name] = $closure;
	}

	/**
	 * Add a new validator.
	 * 
	 * @desc the callback should return true when valid and a string error message when invalid. It recieves three arguments, the element, the options passed to the validator and the value.
	 * @param string $name the name of the validator.
	 * @param closure $closure the callback.
	 */
	static public function addValidator($name, $closure) {
		static::$validators[$name] = $closure;
	}

	/**
	 * Run an element against validator passing the rule option and the element value.
	 * @param element $element the element instance to validate against.
	 * @param string $rule the rule to validate with.
	 * @param mixed $option the option of the rule.
	 * @param mixed $value the value of the element.
	 */ 
	static public function validate($element, $rule, $option, $value) {
		if (!isset(static::$validators[$rule])) {
			throw new Exception("Unknown validator $rule", 1);
			
		}
		$validator = static::$validators[$rule];
		$result = $validator($element, $option, $value);
		return $result;
	}

	/**
	 * Create a new form instance.
	 * @param string $name the name attribute of the form.
	 * @param string $method the method attribute.
	 * @param string $action the URL to post to. Omitting this will post to the current URL.
	 */
	public function __construct($name, $method = 'POST', $action = '') {
		parent::__construct('form', true);
		$this->attr('name', $name);
		$this->attr('method', $method);
		$this->attr('action', $action);
		$this->addClass('neatform');

		$this->__name = static::hidden('neatform_form', $this->attr('name'));
	}

	/**
	 * Enable or disable ajax for this form.
	 * @param boolean $bool true or false.
	 */
	public function ajax($bool = true) {
		$this->ajax = $bool;
	}

	/**
	 * Set whether to use CSRF protection or not.
	 * @param boolean $bool true or false
	 * @since 1.4.0
	 */
	public function useCSRF($bool = false) {
		$this->csrf = $bool;

		if ($bool) {
			# make sure the session has started
			if (session_id() == '') {
				session_start();
			}
		}
	}

	public function generateCSRFToken() {
		$s = '';

		$alphabet = explode(',', 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z');

		for($i = 0; $i < 32; $i++) {
			$s .= md5($alphabet[array_rand($alphabet)] .mt_rand(0,9));
		}

		return str_shuffle(sha1($s));
	}

	public function validCSRF() {

		if ($this->csrf) {
			# get the token from session
			if (isset($_SESSION['csrf_token'])) {
				$token = $_SESSION['csrf_token'];

				if (isset($_POST['csrf_token'])) {
					if ($token === $_POST['csrf_token']) {
						#remember to unset the token, so that the token is only valid for ONE submit
						unset($_SESSION['csrf_token']);
						return true;
					}
				}
			}
		}

		# invalid, let's remove the token from session
		unset($_SESSION['csrf_token']);
		return false;
	}

	/**
	 * Loops through all form elements and sets their value to their $_POST equivalents
	 */
	protected function setValues($array = null) {
		if ($array == null) $array = $this->getContent();
		if (is_array($array)) {
			foreach($array as $element) {
				$this->setValues($element);
			}
		}else if ($array instanceof element) {
			if (isset($_POST[$array->getName()])) {
				$array->setValue($_POST[$array->getName()]);
			}
		}
	}

	/**
	 * Check whether anything was POSTed.
	 * @return bool true if something was posted, false otherwise.
	 */
	public function wasPosted() {
		if (isset($_POST['neatform_form'])) {
			return ($_POST['neatform_form'] == $this->attr('name'));
		}
	}

	/**
	 * Check whether the fields are valid according to their rules.
	 */
	public function valid() {
		$this->setValues();
		$return = true;
		foreach($this->getContent() as $element) {
			if ($element->valid() == false) {
				$return = false;
			}
		}
		return $return;
	}

	/**
	 * Export all the form field values as an associative array.
	 */
	public function export($array = null) {
		$return = array();
		if ($array == null) $array = $this->getContent();

		if (is_array($array)) {
			foreach($array as $element) {
				$return = array_merge_recursive($return, $this->export($element));
			}
		}else if (is_object($array)) {
			$return[$array->getName()] = isset($_POST[$array->getName()]) ? $_POST[$array->getName()] : null;
		}
		return $return;
	}

	/**
	 * Display the form with the default, inline markup.
	 */
	private function displayDefault($labels = true, $help = true, $errors = true) {
		$str = '<' .$this->tagName .' ' .$this->displayAttributes() .'>';
		
		# display content
		foreach($this->content as $element) {
			$str .= $element->displayLabel();
			$str .= $element->display();
		}

		# closing tag
		$str .= '</' .$this->tagName .'>';
		return $str;
	}

	/**
	 * Display the form
	 * @param string $mode the display mode or 'rendering engine' to use. (optional)
	 */
	public function display() {
		#CSRF?
		if ($this->csrf) {
			# generate a CSRF ID
			$token = $this->generateCSRFToken();
			$_SESSION['csrf_token'] = $token;
			$this->csrfelement = form::hidden('csrf_token', $token);
		}

		$args = func_get_args();
		$mode = array_shift($args);
		if (!$mode) $mode = 'default';

		if ($mode == 'default') {

			$return = call_user_func_array(array($this, 'displayDefault'), $args);

		}else {
			if (isset(static::$renderEngines[$mode])) {

				array_unshift($args, $this);
				$return = call_user_func_array(static::$renderEngines[$mode], $args);

			}else {
				throw new Exception("Unknown renderEngine $mode.", 1);
			}
		}

		if (static::$jquery) {
			$return .= '<script type="text/javascript">
			$("form[name=\'' .addslashes($this->attr('name')) .'\']").neatForm({';
				if ($this->ajax) {
					$return .='ajax: true,';
				}
					$return .= 'rules: {
					';

				foreach($this->getContent() as $element) {
					$elementRules = $element->getRules();
					if ($elementRules) {
					$return .= "'" .$element->getName() ."': ";
						$return .= '$.parseJSON(\'' .json_encode($elementRules) .'\'),';
					}

				}

				$return .=	'
				},
			});
			</script>';
		}

		return $return;
	}

	/**
	 * Creates a file field
	 * 
	 * @since 1.3
	 * @param string $label the label for the field
	 * @param string $name the name attribute of the field
	 * @param mixed $value the default value (optional)
	 * @return file object
	 */
	static public function file($label, $name, $value = null) {
		return new file($label, $name, $value);
	}

	/**
	 * Creates a text field
	 * 
	 * @param string $label the label for the field
	 * @param string $name the name attribute of the field
	 * @param mixed $value the default value (optional)
	 * @return text object
	 */
	static public function text($label, $name, $value = null) {
		return new text($label, $name, $value);
	}

	/**
	 * Creates a hidden field
	 * 
	 * @param string $label the label for the field
	 * @param string $name the name attribute of the field
	 * @param mixed $value the default value (optional)
	 * @return text object
	 */
	static public function hidden($name, $value = null) {
		return new hidden($name, $value);
	}

	/**
	 * Creates a password field
	 * 
	 * @param string $label the label for the field
	 * @param string $name the name attribute of the field.
	 * $param mixed $value the defautl value (optional)
	 */
	static public function password($label, $name, $value = null) {
		return new password($label, $name, $value);
	}

	/**
	 * Creates a email field
	 * 
	 * @param string $label the label for the field.
	 * @param string $name the name attribute of the field.
	 */
	static public function email($label, $name, $value = null) {
		return new email($label, $name, $value);
	}

	/**
	 * Creates a email field
	 * 
	 * @param string $label the label for the field.
	 * @param string $name the name attribute of the field.
	 */
	static public function checkbox($label, $name, $value = null) {
		return new checkbox($label, $name, $value);
	}

	/**
	 * Creates a radio field
	 * 
	 * @param string $label the label for the field.
	 * @param string $name the name attribute of the field.
	 */
	static public function radio($label, $name, $value = null) {
		return new radio($label, $name, $value);
	}

	/**
	 * Creates a self-contained list of related radio fields.
	 * @since 1.2
	 * @param string $label the label for the field.
	 * @param string $name the name attribute of the field.
	 */
	static public function radioList($label, $name, $options, $value = null) {
		return new radioList($label, $name, $options, $value);
	}

	/**
	 * Creates a select field
	 * 
	 * @param string $label the label for the field.
	 * @param string $name the name attribute of the field.
	 */
	static public function select($label, $name, $options, $value = null) {
		return new select($label, $name, $options, $value);
	}

	/**
	 * Creates a textarea field
	 * 
	 * @param string $label the label for the field.
	 * @param string $name the name attribute of the field.
	 */
	static public function textarea($label, $name, $value = null) {
		return new textarea($label, $name, $value);
	}

	/**
	 * Creates a submit button
	 * 
	 * @param string $name the name attribute of the field.
	 */
	static public function submit($name, $value) {
		return new submit($name, $value);
	}

	/**
	 * Creates a reset button
	 * 
	 * @param string $name the name attribute of the field.
	 */
	static public function reset($name, $value) {
		return new reset($name, $value);
	}
}



#------------------------------------------------
#	Vertical
#------------------------------------------------
form::addRenderEngine('vertical', function($form) {
	$str = '<' .$form->getTagName() .' ' .$form->displayAttributes() .'>';
	
	# display content
	foreach($form->getContent() as $element) {
		if ($element->isButton() == false) $str .= '<p>';

		# get values
		$label = $element->displayLabel();
		$field = $element->display();
		$help = $element->getHelp();
		
		# display
		if ($label) {
			$str .= $element->displayLabel() .'<br>';
		}
		$str .= $element->display() .'<br>';

		# buttons do not have help/description or errors.
		if ($element->isButton() == false) {
			$str .= '<small data-field="' .$element->getName() .'" class="error-block help-block">' .$help .'</small>';
		}

		if ($element->isButton() == false) $str .= '</p>';
	}

	# closing tag
	$str .= '</' .$form->getTagName() .'>';
	return $str;
});



#------------------------------------------------
#	Table
#------------------------------------------------
form::addRenderEngine('table', function($form) {
	$str = '<' .$form->getTagName() .' ' .$form->displayAttributes() .'>';
	
	# display content
	$str .= '<table>';
	foreach($form->getContent() as $element) {
		$str .= '<tr>';

		# get values
		$label = $element->displayLabel();
		$field = $element->display();
		$help = $element->getHelp();
		$error = $element->getError();

		$str .= '<td>' .$label .'</th>';

		$str .= '<td>' .$field .'</td>';

		if ($error) {
			$str .= '<td><small data-field="' .$element->getName() .'" class="error-block help-block">' .$error .'</small></td>';
		}else {
			$str .= '<td><small data-field="' .$element->getName() .'" class="error-block help-block">' .$help .'</small></td>';
		}

		$str .= '</tr>';
	}
	$str .= '</table>';

	# closing tag
	$str .= '</' .$form->getTagName() .'>';
	return $str;
});


#------------------------------------------------
#	Kube
#------------------------------------------------
form::addRenderEngine('kube', function($form) {
	#kube needs this class on the form
	$form->addClass('forms')->attr('data-layout', 'kube');

	$str = '<' .$form->getTagName() .' ' .$form->displayAttributes() .'>';
	
	# display content
	foreach($form->getContent() as $element) {
		# is this a button?
		$isButton = true;
		if ($element->attr('type') != 'submit' && $element->attr('type') != 'reset') {
			$isButton = false;
		}

		# get values
		$label = $element->displayLabel();
		$field = $element->display();
		$help = $element->getHelp();
		
		# is this a checkbox? they're kinda special in kube.
		if ($element->getTagName() == 'input' && $element->attr('type') == 'checkbox') {
			$str .= '<ul class="forms-list">';
			$str .= '<li>' .$element->display() .' ' .$element->getLabel() .'</li>';
			$str .= '</ul>';
			$str .= '<div data-field="' .$element->getName() .'" class="error-block help-block forms-desc">' .$help .'</div>';
		}else {
			# kube wraps all form fields inside a label
			if ($label) {
				$str .= '<label class="forms-inline" for="' .$element->getName() .'">' .$element->getLabel() .'<br>';
					$str .= $field;
					$str .= '<div data-field="' .$element->getName() .'" class="error-block help-block forms-desc">' .$help .'</div>';
				$str .= '</label>';
			}else {
				$str .= $field;
				$str .= '<div data-field="' .$element->getName() .'" class="error-block help-block forms-desc">' .$help .'</div>';
			}
		}
	}

	# closing tag
	$str .= '</' .$form->getTagName() .'>';
	return $str;
});



#------------------------------------------------
#	Bootstrap Vertical
#------------------------------------------------
form::addRenderEngine('bootstrap-vertical', function($form) {
	$str = '<' .$form->getTagName() .' ' .$form->displayAttributes() .'>';
	
	# display content
	foreach($form->getContent() as $element) {
		$isButton = true;
		if ($element->attr('type') != 'submit' && $element->attr('type') != 'reset') {
			$isButton = false;
		}

		if ($isButton == false) {
			$str .= '<div class="control-group">';
		}

		# get values
		$label = $element->displayLabel();
		$field = $element->display();
		$help = $element->getHelp();
		
		# display
		if ($label) {
			$str .= $element->displayLabel();
		}

		$str .= $element->display();

		$str .= '<span data-field="' .$element->getName() .'" class="error-block help-block">' .$help .'</span>';

		if ($isButton == false) {
			$str .= '</div>';
		}
	}

	# closing tag
	$str .= '</' .$form->getTagName() .'>';
	return $str;
});