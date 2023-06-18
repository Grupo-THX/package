<?php
class Seo
{
	static function dadosFixos($data)
	{
		try
		{
			$siteName = X::getParametro('FromName');
			$charset = 'utf-8';
			$telefone = '';//X::getDadoSite('campo_adicional1');
			$pais = 'Brasil';
			$cidade = '';//X::getDadoSite('campo_adicional4');
			$endereco = '';//X::getDadoSite('campo_adicional3');
			$email = '';//X::getDadoSite('campo_adicional2');
			$idioma = '';//'pt-BR';
			$diferenciadorRight = $diferenciadorLeft = '';

			if(isset($data['diferenciador'][1]))
			{
				$diferenciadorRight = ' - '.$data['diferenciador'];
				$diferenciadorLeft = $data['diferenciador'].' - ';
			}


			$retorno = array(
				'data_alteracao' => 'Mon, March  11th, 2019, 12:04 am',
				'charset' => $charset,
				'city' => $cidade,
				'country' => $pais,//pais
				'placename' => $endereco,
				'telefone' => $telefone,
				'email' => $email,
				'title' => $siteName.$diferenciadorRight,
				'description' => $diferenciadorLeft.$siteName,
				'keywords' => '',
				'image' => HTTP.'/imagens/ogimage.jpg',
				'subject' =>  $siteName,
				'abstract' =>  $siteName,
				'idioma' => $idioma,
				'sitename' =>  $siteName
			);


			foreach($retorno as $key => $valor)
			{
				if(isset($data[$key][1]))
				{
					$retorno[$key] = $data[$key];
				}
			}

			return $retorno;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	###PÁGINAS

	###PÁGINAS FIM

	static function blackSeo($campo)
	{
		try
		{
			return 'Arujá';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function favicon()
	{
		try
		{
			$favicon = X::getParametro('favicon');
			if($favicon == HTTP)
			{
				$favicon = '';
			}
			return $favicon;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function dadoEstruturadoServicoProduto($nome, $img, $descricao, $url = false)
	{
		try
		{
			if($url)
			{
				$url = HTTP.$_SERVER['REQUEST_URI'];
			}
			echo '
			<script type="application/ld+json">
			{
			  "@context": "http://schema.org/",
			  "@type": "Product",
			  "name": "'.$nome.'",
			  "image": "'.$img.'",
			  "description": "'.self::clearStr($descricao).'",
			  "mpn": "1",
			  "brand": {
			    "@type": "Product",
			    "name": "'.$nome.'"
			  },
			}
			</script>';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function debug()
	{
		try
		{
			$xget = '';
			foreach ($_GET as $key => $val)
			{
				 $xget.=$key.'['.$val.'],';
			}
			return '
			<meta name="xseofunction" content="'.self::seoFunction().'">
			<meta name="xself" content="'.$_SERVER['PHP_SELF'].'">
			<meta name="xget" content="'.$xget.'">';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function seoFunction()
	{
		try
		{
			$page = 'seo'.ucwords(basename($_SERVER['PHP_SELF']));
			$page = str_replace(array('.php','-'),array('','_'),$page);
			return $page;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getCabecalho($entradas = array())
	{
		try
		{
			$seo = self::dadosFixos($entradas);

			$seo['keywords'] = self::getKeyWords($seo['keywords']);
			$seo['description'] =  self::clearStr($seo['description'], 160);
			$seo['abstract'] =  self::clearStr($seo['abstract'], 160);
			$seo['subject'] =  self::clearStr($seo['subject'], 160);



			$data = '
			<meta charset="'.$seo['charset'].'">
			<title>'.$seo['title'].'</title>
			<meta name="title" content="'.$seo['title'].'"/>
			<meta name="keywords" content="'.$seo['keywords'].'"/>
			<meta name="description" content="'.$seo['description'].'"/>
			<meta name="subject" content="'.$seo['subject'].'">
			<meta property="og:title" content="'.$seo['title'].'"/>
			<meta property="og:description" content="'.$seo['description'].'"/>
			<meta property="og:image" content="'.$seo['image'].'"/>';


			return self::debug().$data.'
			<!--DEFAULTS-->
			<meta name="copyright" content="'.$seo['sitename'].'">
			<meta name="language" content="'.$seo['idioma'].'">
			<meta name="robots" content="index,follow" />
			<meta name="Classification" content="Business">
			<meta name="reply-to" content="'.$seo['email'].'">
			<meta name="url" content="'.HTTP.$_SERVER['REQUEST_URI'].'">
			<meta name="identifier-URL" content="'.HTTP.$_SERVER['REQUEST_URI'].'">
			<meta name="distribution" content="Global">
			<meta name="rating" content="General">
			<meta name="revisit-after" content="7 days">
			<meta name="language" content="PT-BR">
			<meta name="revised" content="'.$seo['data_alteracao'].'" />
			<meta name="abstract" content="'.$seo['abstract'].'">
			<!--PATHS-->
			<base href="'.HTTP.'" target="_self" />
			<!--LOCALIZAÇÂO-->
			<meta name="geo.placename" content="'.$seo['placename'].'" />
			<meta name="city" content="'.$seo['city'].'">
			<meta name="country" content="'.$seo['country'].'">
			<meta name="generator" content="Powered by X-Painel Rental Code."/>
			<!--OPEN GRAPH-->
			<meta property="og:site_name" content="'.$seo['sitename'].'"/>
			<meta property="og:phone_number" content="'.$seo['telefone'].'"/>
			<meta property="og:email" content="'.$seo['email'].'"/>
			<meta property="og:url" content="'.HTTP.$_SERVER['REQUEST_URI'].'"/>
			<meta property="og:type" content="website"/>

			<!--Icons by X-Painel-->
			<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha512-iBBXm8fW90+nuLcSKlbmrPcLa0OT92xO1BIsZ+ywDWZCvqsWgccV3gFoRBv0z+8dLJgyAHIhR35VZc2oM/gI1w==" crossorigin="anonymous" />
			<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
			<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
			<link href="//unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
			<!--End Icons by X-Painel-->

			<script type="application/ld+json">
			{
			  "@context" : "http://schema.org",
			  "@type" : "Organization",
			  "name" : "'.$seo['sitename'].'",
			  "url" : "'.HTTP.'",
			  "sameAs" : [
			    '.Layout::bladeList(Social::get(),"{{social_url}}, ").'
			 ]
			}
			</script>
			<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
			<script src="//www.google.com/recaptcha/api.js" async defer></script>

			'.self::favicon()
			.X::getParametro('head_close');
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getSeo($dado = false)
	{
		try
		{
			if($dado)
			{
				$ret = self::dadosFixos();
				return $ret[$dado];
			}
			$page = self::seoFunction();
			if(method_exists('Seo', $page))
			{
				return self::$page();
			}
			return self::getCabecalho();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function extrairMetasSeoBanco($sql, $metasAtuais = array())
	{
		try
		{
			echo 'IN EXTRAI METAS';
			$prefixo = 'seo_';
			$result = Sql::_fetch($sql);

			foreach ($result as $key => $val)
			{
				if(strstr($key, $prefixo))
				{
					$meta = str_replace($prefixo, '', $key);
					$metasAtuais[$meta] = $val;
				}
			}
			return $metasAtuais;

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getKeyWords($str, $separador = ', ', $padrao = '{keyword}', $tags = array('em', 'strong', 'b', 'i'))
	{
		try
		{
			if($str == '')
			{
				return '';
			}
			if(is_array($str))
			{
				return implode(', ', $str);
			}
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
						$ret .= str_replace('{keyword}', $dados, $padrao).$separador;
						$corridos[] = $dados;
					}
				}
			}

			if($ret == '')
			{
				return '';
			}

      		$ret = trim($ret, $separador);

      		return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function clearStr($str, $maxcaracteres = 160)
	{
		try
		{
			$str = stripslashes(stripslashes($str));
			$str = str_replace(array('<br />',"\n"),array(' ',''),$str);
			$str = strip_tags($str);
			$str = U::limitaCaracteres($str,$maxcaracteres);
			return $str;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}
