<?
class Cookie
{
	static function setCookie($cookie_name, $cookie_value = 1, $seconds = 31556926, $path = '/')
	{
		try
		{
			$cookie_name = X.$cookie_name;
			$set = setcookie($cookie_name, $cookie_value, time() + $seconds, $path);
			if(DEBUG)
			{
				var_dump($set);
			}
			return $set;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function delCookie($cookie_name, $seconds = 31556926)
	{
		try
		{
			$cookie_name = X.$cookie_name;
			$del = setcookie($cookie_name, '', time() - $seconds);
			if(DEBUG)
			{
				var_dump($del);
			}
			return $del;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getCookie($cookie_name)
	{
		try
		{
			$cookie_name = X.$cookie_name;
			if(isset ($_COOKIE[$cookie_name]))
			{
				return $_COOKIE[$cookie_name];
			}
			else
				return false;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}