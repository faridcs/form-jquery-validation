<?php

namespace afflicto\form\html;

class element {

	protected $tagName = 'div';
	protected $endTag = true;
	protected $attributes = array();
	protected $classes = array();
	protected $content = array();

	public function __construct($tagName = 'div', $endTag = true, $attributes = array()) {
		$this->tagName = $tagName;
		$this->endTag = $endTag;
		$this->attributes = $attributes;
	}

	final public function isButton() {
		if ($this->tagName == 'input') {
			$t = $this->attr('type');
			if ($t == 'submit' || $t == 'reset' || $t == 'button') {
				return true;
			}
		}
		if ($this->tagName == 'button') return true;
		return false;
	}

	public function getTagName() {
		return $this->tagName;
	}

	public function hasEndTag() {
		return $this->endTag;
	}

	public function addClass($class) {
		$class = explode(' ', $class);
		foreach($class as $k) {
			if (!in_array($k, $this->classes)) $this->classes[] = $k;
		}
		return $this;
	}

	public function removeClass($class) {
		$class = explode(' ', $class);
		foreach($class as $k) {
			if (in_array($k, $this->classes)) {
				unset($this->classes[array_search($k, $this->classes)]);
			}
		}
		return $this;
	}

	public function getClasses() {
		return $this->classes;
	}

	public function getAttributes() {
		return $this->attributes;
	}

	public function autocomplete($bool = true) {
		if ($bool) {
			$this->attr('autocomplete', 'on');
		}else {
			$this->attr('autocomplete', 'off');
		}
		return $this;
	}

	public function disabled($bool = true) {
		if ($bool) {
			$this->attr('disabled', 'disabled');
		}else {
			$this->attr('disabled', null);
		}
		return $this;
	}

	public function autofocus($bool = true) {
		if ($bool) {
			$this->attr('autofocus', true);
		}else {
			$this->attr('autofocus', null);
		}
		return $this;
	}

	public function __get($key) {
		if (is_array($this->content)) {
			return (isset($this->content[$key])) ? $this->content[$key] : null;
		}
	}

	public function __set($key, $val) {
		if (is_array($this->content)) {
			$this->content[$key] = $val;
		}
	}

	public function setContent($array) {
		$this->content = $array;
		return $this;
	}

	public function getContent() {
		return $this->content;
	}

	public function attr($key, $val = null) {
		if ($val == null) {
			return (isset($this->attributes[$key])) ? $this->attributes[$key] : null;
		}
		if (is_array($key)) {
			$this->attributes = array_merge_recursive($this->attributes, $key);
		}else {
			$this->attributes[$key] = $val;
		}
		return $this;
	}

	/**
	 * Attributes set to NULL value will not be displayed.
	 */
	public function displayAttributes() {
		$str = '';

		$this->attributes['class'] = implode(' ', $this->classes);

		foreach($this->attributes as $key => $value) {
			if ($value !== null) {
				$str .= ' ' .$key .'="' .$value .'"';
			}
		}

		return trim($str);
	}

	public function displayContent($content = null) {
		if ($content == null) $content = $this->content;

		$str = '';
		if (isset($content)) {
			if (is_array($content)) {
				foreach($content as $c) {
					$str .= $this->displayContent($c);
				}
			}else if (is_string($content)) {
				$str .= $content;
			}else if (is_object($content)) {
				if (method_exists($content, 'display')) {
					$str .= $content->display();
				}
			}
		}
		return $str;
	}

	public function display() {
		$str = '<' .$this->tagName .' ' .$this->displayAttributes() .'>';

		if ($this->endTag) {
			# display content
			$str .= $this->displayContent();

			# closing tag
			$str .= '</' .$this->tagName .'>';
		}

		return $str;
	}

}