<?php
class Link
{
	static function get()
	{
		try
		{
			$result = Sql::_fetchAll('SELECT * FROM links WHERE link_deletado=0 AND link_ativo=1');
			foreach($result as $key => $res)
			{
				$result[$key]['img'] = U::getImg('imagens/links/'.$res['link_id'].'_1_1.'.$res['link_extensao1']);
				$result[$key]['link_titulo'] = $res['link_titulo'];
			}
			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}


	static function whatsApp($num)
	{
		try
		{
			return 'https://wa.me/55'.U::getNumbersStr($num);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function telefone($num)
	{
		try
		{
			return 'tel:'.U::getNumbersStr($num);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	
	static function mapa($enderecoCompleto)
{
	try
	{
		return 'https://www.google.com.br/maps/place/'.urlencode($enderecoCompleto);
	}
	catch( Exception $e )
	{
		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
	}
}

	static function email($mail)
	{
		try
		{
			return 'mailto:'.$mail;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getLinks($padrao = 0)
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM links WHERE link_deletado=0 AND link_ativo=1 ";
			$result = Sql::_fetchAll($sql);

			foreach($result as $res)
			{
				$img = U::getImg('imagens/links/'.$res['link_id'].'_1_3.'.$res['link_extensao1']);
				$link = HTTP.'/convenio/'.U::setUrlAmigavel($res['link_titulo']);
				if($padrao == 0)
				{
            		$ret.='<li><a href="'.$link.'"><img src="'.$img.'" alt="'.$res['link_titulo'].'"></a></li>';
            	}
            	elseif($padrao == 1)
            	{
            		$img = U::getImg('imagens/links/'.$res['link_id'].'_1_2.'.$res['link_extensao1']);
            		$ret.='<div class="item"><a href="'.$link.'"><img src="'.$img.'" alt="'.$res['link_titulo'].'"></a></div>';
            	}
            	else
            	{
            		$img = U::getImg('imagens/links/'.$res['link_id'].'_1_3.'.$res['link_extensao1']);
            		$ret.='
					<div class="col-md-3" '.U::divLink($link).'>
						<div class="text-box padding-4 white div_convenio_x">
							<img src="'.$img.'" alt="" class="img-responsive">
							<div class="clearfix"></div>
							<br>
							<h4 class="uppercase">'.$res['link_titulo'].'</h4>
							<p>'.$res['link_descricao1'].'</p>
						</div>
					</div>';
					$ret.=U::clearFix(++$loop, 4);
            	}
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getLink()
	{
		try
		{
			$ret = '';
			$like = Sql::toLike($_GET['convenio']);
			$sql = "SELECT * FROM links WHERE link_deletado=0 AND link_ativo=1 AND link_titulo".$like;

			$result = Sql::_fetch($sql);

			if(! $result)
			{
				U::goHome();
			}

			$result['img'] = U::getImg('imagens/links/'.$result['link_id'].'_1_1.'.$result['link_extensao1']);

			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}
