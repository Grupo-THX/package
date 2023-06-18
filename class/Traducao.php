<?php
class Traducao{

	static function idioma()
	{
		try
		{
			self::setIdioma();
			require_once($_SERVER['DOCUMENT_ROOT'].'/xpainel/class/connection'.$_SESSION[X]['idioma'].'.php');
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function setIdioma()
	{
		try
		{
			if(! isset($_SESSION[X]['idioma']))
			{
				$_SESSION[X]['idioma'] = '';
			}

			if(isset($_GET['idioma']))
			{
				$_SESSION[X]['idioma'] = $_GET['idioma'];
			}

			$arquivo_de_idioma = ROOT.'/xpainel/idiomas/'.$_SESSION[X]['idioma'].".lang";
			if(file_exists($arquivo_de_idioma))
			{
				$idioma = fopen($arquivo_de_idioma, "r") or die("Arquivo não encontrado!");
				$_SESSION[X]['texto_idioma'] = unserialize(fread($idioma,filesize($arquivo_de_idioma)));
				//echo '<pre>'.print_r($_SESSION[X]['texto_idioma'],1).'</pre>';
				fclose($idioma);
			}

			define( 'VENTS_IDIOMA', $_SESSION[X]['idioma'] == 'ingles' ? 'ingles' : 'padrao') ;
			define( 'VENTS_DIRETORIO_IMAGENS', $_SESSION[X]['idioma'] == 'ingles' ? 'ventimagens_ingles' : 'ventimagens') ;
			define( 'VENTS_DIRETORIO', $_SESSION[X]['idioma'] == 'ingles' ? 'ingles' : '') ;

			Traducao::_setLocale();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function _setLocale()
	{
		try
		{
			switch ($_SESSION[X]['idioma'])
			{
				case 'alemao':
					setlocale(LC_TIME, "de_DE.UTF8" );
				break;

				case 'ingles':
					setlocale(LC_TIME, "en_US.UTF8" );
				break;

				default:
					setlocale(LC_TIME, 'pt_BR.UTF8' );
				break;
			}


		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function setHtmlLang()
	{
		try
		{
			switch ($_SESSION[X]['idioma'])
			{

				case 'ingles':
					return 'en-US';
				break;

				default:
					return 'pt-BR';
				break;
			}


		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function traduz($id, $trataStr = false)
	{
		try
		{
			$sulfixo =  ' ';

			$id = U::setUrlAmigavel($id);

			$ret = $id.' _NAO_TRADUZIDO_ '.$sulfixo;

			if(isset($_SESSION[X]['texto_idioma'][$id]))
			{
				$ret = $_SESSION[X]['texto_idioma'][$id].$sulfixo;
			}
			elseif(isset($_SESSION[X]['texto_idioma'][$id]))
			{
				$ret = $_SESSION[X]['texto_idioma'][$id].$sulfixo;
			}

			if($trataStr == 'getformname')
			{
				$ret = U::getNameFormPost($ret);
			}

			if(DEBUG)
			{
				$ret = $_SESSION[X]['idioma'].'-'.$id.'-'.$ret;
			}

			return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


}