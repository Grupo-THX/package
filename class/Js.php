<?php
class Js
{
    static function clearToVar($string)
	{
		$string = str_replace("\r", "", $string);
		$string = str_replace("\n", "", $string);
		return addslashes($string);
	}

    static function getJsCss()
	{
		try
		{

			$retorno ='
			<script> var HTTP = "'.X::protocolo().'"; </script>
			<script src="'.X::protocolo().'/xpainel/js/funcoes.php" type="text/javascript" charset="utf-8"></script>
			<link href="'.X::protocolo().'/xpainel/css/style.php" rel="stylesheet">
			';
			if(X_ECOMMERCE && !(defined(X_ADMIN)))
			{
				$retorno.='<script type="text/javascript" src="//stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js"></script>';
			}
			$js="
			if(document.getElementById('clientes_estado') != null && document.getElementById('clientes_cidade') != null)
			{
				new dgCidadesEstados({
	                cidade : document.getElementById('clientes_cidade'),
	                estado : document.getElementById('clientes_estado'),
	                estadoVal : '".Cliente::getDado('clientes_estado')."',
	                cidadeVal : '".Cliente::getDado('clientes_cidade')."'
	            });
        	}";
			foreach($GLOBALS['Xjs'] as $script)
			{
				$js.= $script;
			}

			$retorno.= '
            <script id="Js::getJsCss">
                function addLoadEvent(func) {
                    var oldonload = window.onload;
                    if (typeof window.onload != "function") {
                    window.onload = func;
                    } else {
                    window.onload = function() {
                        if (oldonload) {
                        oldonload();
                        }
                        func();
                    }
                    }
                }
                //addLoadEvent(nameOfSomeFunctionToRunOnPageLoad);
                addLoadEvent(function() {
                    '.$js.'
                });
            </script>';
			return $retorno;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

    static function logError($error)
    {
        try
        {
            $msg = "console.error('{$error}');";
            return Js::addScript($msg);
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function addScript($script)
    {
        try
        {
            $script = "\n<!-- addScript open -->\n".$script."\n<!-- addScript close-->\n";
            $GLOBALS['Xjs'][] = $script;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function callAjax($function, $target = 'xgetDados')
	{
        $url = X::protocolo()."/xpainel/lib/ajax.php?function={$function}&nocache=".U::getToken(10);
        if(isset($_GET['tk_pwd']))
        {
            $url = X::protocolo()."/xpainel/lib/ajax.php?function=redefinicaoDeSenha&tk_pwd={$_GET['tk_pwd']}&nocache=".U::getToken(10);
        }

        Js::addScript("createIframe('{$target}', '{$url}');");
	}

    static function xSetHtml($html, $target="#xSetHtml")
    {
        try
        {
            $html = Js::clearToVar($html);
           echo("<script>parent.xSetHtml('{$html}', '{$target}'); parent.loadedX();</script>");
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function script($script)
    {
        try
        {
           echo("<script>{$script}</script>");
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function alert($msg=false,$redireciona=false, $stopLoading=true, $confirmButtonText = 'Fechar Aviso')
	{
		try
		{
			$stopLoading = $stopLoading ? "parent.loadedX();" : '';

			if(!$msg || $msg == '')
			{

				if($redireciona)
				{
					$redireciona = "top.location = '{$redireciona}'";
				}
                else
                {
                    if($redireciona == '')
                    {
                        $redireciona = "top.location = top.location";
                    }
                }
				return "<script>{$stopLoading} {$redireciona}</script>";
			}


			if($redireciona)
			{
				$redireciona=".then(function() { parent.loadingX(); top.location = '{$redireciona}';})";
			}

			$msg = Js::clearToVar($msg);

			return "
			<script>
				{$stopLoading}
				parent.Swal.fire(
					{
						title: '',
						html: '{$msg}',
						imageUrl: '".X::getParametro('logomarca')."',
						imageAlt: '',
						confirmButtonText: '{$confirmButtonText}',
					}
				){$redireciona};
			</script>";
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}