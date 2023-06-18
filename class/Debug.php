<?php
class Debug
{
    static function echo($str)
    {
        try
        {
            if(! defined('DEBUG') || ! DEBUG)
            {
                return;
            }

            return '<small class="badge rounded-pill bg-primary Debugecho xDisplayDebugs" style="display: none">'.$str.'</small>';
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function logXpainel($arquivo = 'INDEFINIDO',$dados = '')
	{
		try
		{
			$quebra = chr(13).chr(10);
			$dados='LOG:'.$dados.$quebra;
			$variaveis = $quebra.'ERROS:'.$quebra.'<pre>'.print_r(error_get_last(),true).'</pre>'.$quebra;
			$variaveis .= $quebra.'POST:'.$quebra.'<pre>'.print_r($_POST,true).'</pre>'.$quebra;
			$variaveis.= $quebra.'GET:'.$quebra.'<pre>'.print_r($_GET,true).'</pre>'.$quebra;
			$variaveis.= $quebra.'SERVER:'.$quebra.'<pre>'.print_r($_SERVER,true).'</pre>'.$quebra;
			$cabecalho =$quebra.date('d/m/Y G:s:i').$quebra.'INICIO_______________________________________________________________';
			$fim = $quebra.'_______________________________________________________________END';
			$dados = $cabecalho.$dados.$variaveis.$fim;
			$dirLog = ROOT.'/xpainel/logsXpainel';
			if(! file_exists($dirLog))
			{
				mkdir($dirLog);
			}

			$arquivoLog = $dirLog.'/'.$arquivo.'-'.date('d-m-Y').'.txt';
			$abre = fopen($arquivoLog, 'a');
			$escreve = fwrite($abre, $quebra.$dados);
			fclose($abre);
            Debug::emailLog('logXpainel',$dados, [$arquivoLog]);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

    static function emailLog($assunto, $msg, $anexo = false)
    {
        try
        {
            if(!defined('EMAIL_LOG') || !EMAIL_LOG)
            {
                return;
            }

            if(Is_array($msg))
            {
                $msg = 'Array Contents:<hr /><pre>'.print_r($msg, 1).'</pre>';
            }

            E::email(EMAIL_LOG,APP_NAME, $assunto, $msg, $anexo);
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
}