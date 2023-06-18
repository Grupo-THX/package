<?php
class Chat
{
	static function get($msg = 'Quero falar com um consultor')
	{
		try
		{
			return "javascript:jivo_api.sendMessage('{$msg}')";
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function set($msg = '{bom}Seu orçamento para instalação de câmeras aqui 🎦 ? {nl1}Serviço em até 7 dias :){end}')
	{
		try
		{
			echo '
			<script src="//grupothx.com.br/atendimento-online/apiTHX/scriptAtendimentoOnlineTodosOsSitesJs.php?cor=000000"></script>
    		<input type="hidden" id="botCall" value="'.$msg.'" >';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}