<?php
namespace GrupoThx;
class U
{
	static function valorPorExtenso($valor, $bolExibirMoeda = true, $bolPalavraFeminina = false)
	{
		try
		{
				$valor = number_format($valor, 2, ".", ".");
				$singular = null;
				$plural = null;

				if ( $bolExibirMoeda )
				{
						$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
						$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
				}
				else
				{
						$singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
						$plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
				}

				$c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
				$d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
				$d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezessete", "dezoito", "dezenove");
				$u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");


				if ( $bolPalavraFeminina )
				{

						if ($valor == 1)
						{
							$u = array("", "uma", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
						}
						else
						{
							$u = array("", "um", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
						}


						$c = array("", "cem", "duzentas", "trezentas", "quatrocentas","quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");


				}


				$z = 0;

				$inteiro = explode( ".", $valor );

				for ( $i = 0; $i < count( $inteiro ); $i++ )
				{
						for ( $ii = mb_strlen( $inteiro[$i] ); $ii < 3; $ii++ )
						{
							$inteiro[$i] = "0" . $inteiro[$i];
						}
				}

				// $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
				$rt = null;
				$fim = count( $inteiro ) - ($inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2);
				for ( $i = 0; $i < count( $inteiro ); $i++ )
				{
						$valor = $inteiro[$i];
						$rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
						$rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
						$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

						$r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
						$t = count( $inteiro ) - 1 - $i;
						$r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
						if ( $valor == "000")
							$z++;
						elseif ( $z > 0 )
							$z--;

						if ( ($t == 1) && ($z > 0) && ($inteiro[0] > 0) )
							$r .= ( ($z > 1) ? " de " : "") . $plural[$t];

						if ( $r )
							$rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
				}

				$rt = mb_substr( $rt, 1 );

				return($rt ? trim( $rt ) : "zero");
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function setUrlAmigavel($string, $isento = false, $coringa = ' ')
	{
		try
		{
			$string = trim($string);
			$url_limpa=U::tiracento($string);
     		$proibidos=array('&','%','(','?','.','\\','#',')','°' );
     		$url_limpa=str_replace($proibidos,'',$url_limpa);
			$url_limpa = preg_replace("/[^a-zA-Z0-9 ]/", "-",$url_limpa);

			$url_limpa= strtolower( strip_tags( preg_replace( array( '/[`^~\'"]/', '/([\s]{1,})/', '/[-]{2,}/' ), array( null, '-', '-' ), iconv( 'UTF-8', 'ASCII//TRANSLIT', $url_limpa ) ) ) );

			if($isento)
			{
				$url_limpa = str_replace($isento,$coringa,$url_limpa);
			}
			return $url_limpa;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function urlAtual($remove_GET = false)
	{
		try
		{
			$protocolo =  isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? "https://" : 'http://';
			$retorno = $protocolo.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			if($remove_GET)
			{
				$urlParts = explode('?', $retorno);
				return $urlParts[0];
			}
			return $retorno;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function copyright($ano)
	{
		try
		{
			$anoAtual = date('Y');
			if($ano != $anoAtual)
			{
				$ano = $ano.' - '.$anoAtual;
			}
			return $ano;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function ancorar($ancora, $ajustefino = 0, $prefixjs = 'javascript:', $home = '/index.php')
	{
		try
		{
			if($_SERVER['PHP_SELF'] == $home)
			{
				return $prefixjs."ancorar('{$ancora}', {$ajustefino})";
			}

			return HTTP.'#'.$ancora;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function strToJs($str)
	{
		try
		{
			$str = str_replace("\n",'', $str);
			return U::clearStr($str);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getLoading()
	{
		try
		{
			return '<img class="loadingImgX" style="max-width: 32px !important;max-height: 32px !important;" src="'.HTTP.'/xpainel/imagens/loading.gif" />';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function checkDuplicate($tabela, $coluna, $valor)
	{
		try
		{
			return Sql::_fetch("SELECT {$coluna} FROM {$tabela} WHERE {$coluna}='{$valor}'");
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getTags($str, $padrao = '<a href="#{url}">{tag}</a>', $tags = array('em', 'strong', 'b', 'i'))
	{
		try
		{
			$ret = '';
			$corridos = array();
			foreach($tags as $tag)
			{
				$texto = U::clearStr($str);
				preg_match_all( "/<".$tag."[^>]*>([^<]+)<\/".$tag.">/iu", $texto, $matches );
				foreach ($matches[0] as $key => $dados)
				{
					$dados = trim(strip_tags($dados));
					if(! in_array($dados,$corridos))
					{
						$ret .= str_replace(array('{url}', '{tag}'), array(U::setUrlAmigavel($dados),$dados), $padrao);
						$corridos[] = $dados;
					}
				}
			}

			return $ret;

      		return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function setTable($content)
	{
		try
		{
			$style = ' style="width:100%; height:100%; border:none; text-align: center;" ';
			return '<table'.$style.'><tr'.$style.'><td'.$style.'>'.$content.'</td></tr></table>';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function clearFix($loop, $quebra = 3, $padrao = '<div class="xclearFix"></div>')
	{
		try
		{
			if($loop > 0 && $loop%$quebra==0)
			{
				return $padrao;
			}

			return;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function contadorDeVisitas($cookie = false)
	{
		try
		{
			$atual=parse_ini_file('countnow.ini', false);
			if($cookie)
			{
				if (!isset($_COOKIE[$cookie]))
				{
				     setcookie($cookie, 'grupothx', time()+3700*12);
				     $atual['contagem'] ++;
				     $abre = fopen('countnow.ini', "w+");
				     $escreve = fwrite($abre, 'contagem = '.$atual['contagem']);
				     $fecha = fclose($abre);
				 }
			}
			else
			{
				$atual['contagem'] ++;
				$abre = fopen('countnow.ini', "w+");
				$escreve = fwrite($abre, 'contagem = '.$atual['contagem']);
				$fecha = fclose($abre);
			}
			return $atual['contagem'];

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function tiracento($texto)
	{
		try
		{
	        $trocarIsso = array('à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ü','ú','ÿ','À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','O','Ù','Ü','Ú','Ÿ',' ');
       		$porIsso = array('a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','y','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','o','U','U','U','Y','_');
        	$titletext = str_replace($trocarIsso, $porIsso, $texto);
        	return $titletext;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function ocultaX($str)
	{
		try
		{
			return "
			<script>
				var divsToHide = document.getElementsByClassName('ocultaX');
			    for(var i = 0; i < divsToHide.length; i++){
			        divsToHide[i].style.display = 'none';
			    }
			</script>".$str;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function setLocation($url = false)
	{
		try
		{
			$url = $url ? $url : HTTP;
			return ' onClick="setLocation(\''.$url.'\')" ';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function formataData($data, $padrao = false)
	{
		try
		{
			if($data == '')
			{
				$data = date('Y-m-d G:i:s');
			}

			if(! $padrao)
			{
				$padrao = '%d de %B de %Y';
				if(strstr($data, ':') && !strstr($data, '00:00:00'))
				{
					$padrao = '%d de %B de %Y -  %H:%M';
				}
			}

			return strftime($padrao, strtotime($data));
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function firstName($name)
	{
		try
		{
			$firstName = explode(' ',$name);
            return trim($firstName[0]);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getDadosMes($mes = false, $ano = false)
	{
		try
		{
			$mes = $mes ? $mes : date('m');
			$ano = $ano ? $ano : date('Y');
			$ret = array();
			$ret['days'] = 	date("t", mktime(0,0,0, $mes, 1, $ano));
			$ret['firstday'] = "{$ano}-{$mes}-01";
			$ret['lastday'] = "{$ano}-{$mes}-{$ret['days']}";
			return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function goHome($url = HTTP)
	{
		try
		{
			die("<script>top.location='".$url."'</script>");
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getExtensao($arqNome)
	{
		try
		{
			$arqNome = explode(".", $arqNome);
			if(! isset($arqNome[1]))
			{
				return false;
			}
			return end($arqNome);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function clearToVar($string)
	{
		$string = U::clearStr($string);
		$string = str_replace("\r", "", $string);
		$string = str_replace("\n", "", $string);
		return $string;
	}

	static function utf8($str)
	{
		try
		{
			if (preg_match('!!u', $str))
			{
			   // this is utf-8
			   return $str;
			}
			else
			{
			   // definitely not utf-8
			   return utf8_encode($str);
			}
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function pre($text)
	{
		try
		{
			return "<pre>{$text}</pre>";
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getImg($path, $retornoBoleano = false)
	{
		if(LOCAL_MODE)
		{
			return APP_URL.'/'.$path;
		}

		$debugPath = '?debugPath='.$path;

		if(! file_exists(ROOT.'/'.$path))
		{
			if($retornoBoleano)
			{
				return false;
			}

			if(file_exists(ROOT.'/xpainel/imagens/nofoto.png'.$debugPath))
			{
				return APP_URL.'/xpainel/imagens/nofoto.png'.$debugPath;
			}

			return X::getParametro('logomarca').$debugPath;
		}
		return  APP_URL.'/'.$path;
	}
	static function getFile($path, $retornoBoleano = false)
	{
		if(LOCAL_MODE)
		{
			return APP_URL.'/'.$path;
		}

		$debugPath = '?debugPath='.$path;

		if(! file_exists(ROOT.'/'.$path))
		{
			if($retornoBoleano)
			{
				return false;
			}

			return X::getParametro('logomarca').$debugPath;
		}
		return  APP_URL.'/'.$path;
	}
	static function clearStr($str)
	{
		try
		{

			if(is_array($str))
			{
				foreach($str as $key => $value)
				{
					$str[$key] = self::clearStr($value);
				}
			}
			else
			{

				$str = stripslashes($str);
				if(strstr($str,'\"') || strstr($str,"\'"))
				{
					$str = self::clearStr($str);
				}
			}
			return $str;

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	
	static function getDataExtenso($data, $param = false)
	{
		try
		{
			if($param)
			{
				return strftime('%'.$param, strtotime($data));
			}

			return strftime('%d de %B de %Y', strtotime($data));
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function validaEmail($email)
	{
		try
		{
			if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$email))
			{
				list($username,$domain)=explode('@',$email);
				if(checkdnsrr($domain,'MX'))
				{
					return true;
				}
			}
			return false;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function serialize($array)
	{
		try
		{
			if(! is_array($array))
			{
				$array = array();
			}

			return serialize($array);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function unserialize($str)
	{
		try
		{
			if(! $str)
			{
				return array();
			}

			return unserialize($str);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function limitaCaracteres($str,$max,$sufixo='')
	{
		try
		{
			if(strlen($str) > $max)
			{
				$str=substr($str, 0, $max).$sufixo;
			}
			return $str;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function limitaParagrafos($str,$max = 1,$sufixo='')
	{
		try
		{
			$ret = '';
			$paragrafos = explode('<br />', $str);
			for($i=0; $i<$max; $i++)
			{
				if(isset($paragrafos[$i]))
				{
					$ret.=$paragrafos[$i].'<br />';
				}
			}
			return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function moeda($valor,$separador='.')
	{
		try
		{
			return @number_format($valor, 2, $separador, '');
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getNumbersStr($str)
	{
		try
		{
			preg_match_all('!\d+!', $str, $matches);
			return implode('',$matches[0]);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function divLink($url, $literal=false)
	{
		try
		{
			return ' style="cursor:pointer" '.U::setLocation($url);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function filtro($str, $tipo = 'numero')
	{
		try
		{
			switch ($tipo) {
				case 'numero':
					preg_match_all('!\d+!', $str, $matches);
					return (int)implode('',$matches[0]);
				break;
				default:
					return 'Não há filtro para '.$tipo;
			}
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getToken($tamanho)
	{
		try
		{
			$autenticacao="";
			$letra=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','y','x','z');
			for ($i=0;$i<$tamanho;$i++)
			{
				$caracter=rand(1,3);
				$numero = rand(1,24);
				$numero_de_um_digito = rand(0,9);
				if($caracter==1)
					$autenticacao=$autenticacao.$numero_de_um_digito;
				if($caracter==2)
				{
					$letra[$numero]=strtoupper($letra[$numero]);
					$autenticacao=$autenticacao.$letra[$numero];
				}
				if($caracter==3)
				{
					$autenticacao=$autenticacao.$letra[$numero];
				}
			}
			return($autenticacao);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}
