<?php
class Request
{

	static function get($name, $array = false, $length = 1)
	{
		try
		{
			$length--; //Melhorar contagem de tamanho
			if(! $array)
			{
				$array = $_REQUEST;
			}

			if(isset($array[$name][$length]))
			{
				return $array[$name];
			}

			return false;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}



	static function file($name)
	{
		try
		{
			if(isset($_FILES[$name]['name'][1]))
			{
				return $_FILES[$name];
			}
			return false;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function set($name, $value, $verbo = 'REQUEST')
	{
		try
		{
			if($verbo == 'POST')
			{
				return $_POST[$name] = $value;
			}

			if($verbo == 'GET')
			{
				return $_GET[$name] = $value;
			}

			return $_REQUEST[$name] = $value;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function unset($name, $verbo = 'REQUEST')
	{
		try
		{
			if($verbo == 'POST')
			{
				if(isset($_POST[$name]))
				{
					unset($_POST[$name]);
					return;
				}
			}

			if($verbo == 'GET')
			{
				if(isset($_GET[$name]))
				{
					unset($_GET[$name]);
					return;
				}
			}

			if(isset($_REQUEST[$name]))
			{
				unset($_REQUEST[$name]);
			}

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function toSql($array = false)
    {
        try
        {
			if(! $array)
			{
				$array = $_REQUEST;
			}

            $ret = array();
            foreach($array as $campo => $valor)
            {
                $ret['u'][] = "$campo = '".$valor."'";
                $ret['c'][] = $campo;
                $ret['v'][] = "'".$valor."'";
            }
            $ret['u'] = implode(', ',$ret['u']);
            $ret['c'] = implode(', ',$ret['c']);
            $ret['v'] = implode(', ',$ret['v']);

            return $ret;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
}
