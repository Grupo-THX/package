<?php
class Gateway
{
	 static function setPedidoPagseguro()
    {
        try
        {
        	
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
}