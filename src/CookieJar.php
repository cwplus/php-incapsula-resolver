<?php

namespace Anastaszor\Incapsula;

class CookieJar
{
	
	private $_cookies = array();
	
	public function set($key, $value)
	{
		$this->_cookies[$key] = $value;
	}
	
	public function add($key, $value)
	{
		if(isset($this->_cookies[$key]))
			$this->_cookies[$key] .= ','.$value;
		else
			$this->set($key, $value);
	}
	
	public function get($key)
	{
		if(isset($this->_cookies[$key]))
			return $this->_cookies[$key];
		return null;
	}
	
	public function remove($key)
	{
		unset($this->_cookies[$key]);
	}
	
	public function reset()
	{
		$this->_cookies = array();
	}
	
	public function getFullCookieString()
	{
		$str = 'Cookie: ';
		foreach($this->_cookies as $name => $value)
		{
			$str .= ' '.$name.'='.$value.';';
		}
		return rtrim($str, ';');
	}
	
	public function getAllCookies()
	{
		$array = array();
		foreach($this->_cookies as $key => $value) $array[$key] = clone $value;
		return $array;
	}
	
}
