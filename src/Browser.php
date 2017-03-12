<?php

namespace Anastaszor\Incapsula;

class Browser
{
	
	private $_data = array();
	
	public function __construct($name)
	{
		$file = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.$name.'.json';
		if(is_file($file))
			$this->_data = json_decode(file_get_contents($file), true);
		else
			throw new \Exception("Impossible to find browser data for file ".$file);
	}
	
	public function __get($name)
	{
		if(isset($this->_data[$name]))
			return $this->_data[$name];
		$parts = explode('.', $name);
		$d = $this->_data;
		foreach($parts as $partname)
		{
			if(isset($d[$partname]))
				$d = $d[$partname];
			elseif($partname === 'length')
			{
				if(is_array($d))
					return count($d);
				else
					return strlen("$d");
			}
			else
				return null;
		}
		return $d;
	}
	
	public function __isset($name)
	{
		if(isset($this->_data[$name]))
			return true;
		$parts = explode('.', $name);
		$d = $this->_data;
		foreach($parts as $partname)
		{
			if(isset($d[$partname]))
				$d = $d[$partname];
			else
				return false;
		}
		return $d !== null;
	}
	
}
