<?php

namespace afflicto\form;
use \Exception;

abstract class element extends html\element {

	private $rules = array();
	private $label;
	private $help;
	private $error;

	private $prepend = null;
	private $append = null;

	public function __construct($tagName, $endTag, $label, $name, $value = null) {
		parent::__construct($tagName, $endTag, array());
		$this->setLabel($label);
		$this->setName($name);
		$this->setValue($value);
	}

	final public function help($what) {
		$this->help = $what;
		return $this;
	}

	final public function error($what) {
		if (strlen($what) > 0) {
			$this->addClass('input-error');
		}else {
			$this->removeClass('input-error');
		}
		$this->error = $what;
		return $this;
	}

	public function prepend($str) {
		$this->prepend = $str;
		return $this;
	}

	public function append($str) {
		$this->append = $str;
		return $this;
	}

	final public function getError() {
		return $this->error;
	}

	final public function getHelp() {
		return $this->help;
	}

	final public function setLabel($label) {
		$this->label = $label;
	}

	final public function getLabel() {
		return $this->label;
	}

	final public function setName($name) {
		$this->attr('name', $name);
	}

	final public function getName() {
		return $this->attr('name');
	}

	abstract public function setValue($value);

	abstract public function getValue();

	/**
	 * Add validation rules
	 */
	final public function rules($array) {
		$this->rules = array_merge_recursive($this->rules, $array);
		return $this;
	}

	final public function getRules() {
		return $this->rules;
	}

	/**
	 * Check if the element is valid
	 */
	final public function valid() {
		$required = false;
		if (isset($this->rules['required'])) {
			$required = $this->rules['required'];
		}
		if ($required == false && strlen($this->getValue()) < 1) {
			return true;
		}
		foreach($this->rules as $rule => $option) {
			$result = form::validate($this, $rule, $option, $this->getValue());
			if ($result !== true) {
				$this->error($result);
				return false;
			}
		}
		return true;
	}

	/**
	 * Returns an html label string if the label property is a string.
	 */
	public function displayLabel() {
		$label = $this->getLabel();
		if (is_string($label)) {
			return '<label for="' .$this->getName() .'">' .$this->getLabel() .'</label>';
		}
	}

	public function displayHelp() {
		return $this->help;
	}

	public function display() {
		if ($this->prepend || $this->append) {
			$fw = form::getFramework();
			$fw = $fw['name'];
			if ($fw == 'bootstrap') {
				//prepend/append
				$str = '<div class="';
				if ($this->prepend) {
					$str .= 'input-prepend';
				}
				if ($this->append) {
					$str .= ' input-append';
				}
				$str .= '">';
				
				if ($this->prepend) {
					$str .= '<span class="add-on">' .$this->prepend .'</span>';
				}

				$str .= parent::display();

				if ($this->append) {
					$str .= '<span class="add-on">' .$this->append .'</span>';
				}

				$str .= '</div>';
				return $str;
			}else if ($fw == 'kube') {

				if ($this->prepend) {
					$str = '<span style="clear: both;" class="input-prepend">' .$this->prepend .'</span>';
				}

				$str .= parent::display();

				if ($this->append) {
					$str .= '<span class="input-append">' .$this->append .'</span>';
				}
				return $str;
			}else {
				throw new Exception("Unknown CSS Framework $fw", 1);
				
			}
		}else {
			return parent::display();
		}
	}

}




class radioList extends element {
	
	private $options = array();
	private $value = null;

	public function __construct($label, $name, $options, $value = null) {
		parent::__construct('div', true, $label, $name, null);
		$this->value = $value;
		$this->setOptions($options);
	}

	public function setOptions($options) {
		$this->options = $options;
	}

	public function getOptions() {
		return $this->options;
	}

	public function setValue($v) {
		$this->value = $v;
	}

	private function createElements() {
		$elements = array();
		foreach($this->options as $key => $value) {
			$elements[$key] = new radio($value, $this->getName(), $key);
		}
		return $elements;
	}

	public function getValue() {
		return $this->value;
	}

	public function display() {

		$str = '<ul style="list-style: none; margin: 0; padding-left: 0px;" class="radio-list">';

		foreach($this->createElements() as $radio) {
			$str .= '<li>' .$radio->display() .' ' .$radio->getLabel() .'</li>';
		}

		$str .= '</ul>';
		return $str;
	}

}


class textarea extends element {

	public function __construct($label, $name, $value) {
		parent::__construct('textarea', true, $label, $name, $value);
	}

	public function setValue($v) {
		$this->setContent($v);
	}

	public function getValue() {
		return $this->getContent();
	}
}



class input extends element {

	public function __construct($type = 'text', $label, $name, $value) {
		parent::__construct('input', false, $label, $name, $value);
		$this->attr('type', $type);
	}

	public function setValue($v) {
		$this->attr('value', $v);
	}

	public function getValue() {
		return $this->attr('value');
	}
}




class select extends element {

	private $options;
	private $value;

	public function __construct($label, $name, $options, $value = null) {
		parent::__construct('select', true, $label, $name, $value);
		$this->setOptions($options);
	}

	public function setValue($v) {
		$this->value = $v;
	}

	public function getValue() {
		return $this->value;
	}

	public function setOptions($array) {
		$this->options = $array;
	}

	public function displayOptions() {
		$selected = $this->getValue();
		$str = '';

		foreach($this->options as $key => $value) {
			if (is_array($value)) {
				$str .= '<optgroup label="' .$key .'">';
				foreach($value as $k => $v) {
					if ($k == $selected) {
						$str .= '<option selected="selected" value="' .$k .'">' .$v .'</option>';
					}else {
						$str .= '<option value="' .$k .'">' .$v .'</option>';
					}
				}
				$str .= '</optgroup>';
			}else {
				if ($key == $selected) {
					$str .= '<option selected="selected" value="' .$key .'">' .$value .'</option>';
				}else {
					$str .= '<option value="' .$key .'">' .$value .'</option>';
				}
			}
		}

		return $str;
	}

	public function display() {
		$str = '<' .$this->tagName .' ' .$this->displayAttributes() .'>';

		$str .= $this->displayOptions();

		# closing tag
		$str .= '</' .$this->tagName .'>';

		return $str;
	}
}


class hidden extends input {

	public function __construct($name, $value) {
		parent::__construct('hidden', null, $name, $value);
	}

	public function displayLabel() {
		return '';
	}

}


class text extends input {

	public function __construct($label, $name, $value) {
		parent::__construct('text', $label, $name, $value);
	}

}



class password extends input {

	public function __construct($label, $name, $value) {
		parent::__construct('password', $label, $name, $value);
	}

}



class email extends input {

	public function __construct($label, $name, $value) {
		parent::__construct('email', $label, $name, $value);
	}

}




/**
 * @since 1.3
 */
class file extends input {

	public function __construct($label, $name, $value) {
		parent::__construct('file', $label, $name, $value);
	}

}



class checkbox extends input {

	public function __construct($label, $name, $value) {
		parent::__construct('checkbox', $label, $name, $value);
	}

}



class radio extends input {

	public function __construct($label, $name, $value) {
		parent::__construct('radio', $label, $name, $value);
	}

}


class submit extends input {

	public function __construct($name, $value) {
		parent::__construct('submit', null, $value, $name);
		if (form::getFramework() == 'bootstrap') {
			$this->addClass('btn btn-primary');
		}
	}

}



class reset extends input {

	public function __construct($name, $value) {
		parent::__construct('reset', null, $value, $name);
		if (form::getFramework() == 'bootstrap') {
			$this->addClass('btn');
		}
	}

}
