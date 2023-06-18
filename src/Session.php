<?php
class Session
{
	static function get($data = false, $name = false)
	{
		try
		{
			if($name)
			{
				if(!$data)
				{
					return $_SESSION[X][$name];
				}

				if(isset($_SESSION[X][$name][$data]))
				{
					return $_SESSION[X][$name][$data];
				}
			}
			else
			{
				if(!$data)
				{
					return $_SESSION[X];
				}

				if(isset($_SESSION[X][$data]))
				{
					return $_SESSION[X][$data];
				}
			}

			return false;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function set($name, $data = true)
	{
		try
		{
			$_SESSION[X][$name] = $data;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function unSet($name)
	{
		try
		{
			if(isset($_SESSION[X][$name]))
			{
				unset($_SESSION[X][$name]);
			}
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}

