<?php
class Banner
{
	static function get()
	{
		try
		{
			$result = Sql::_fetchAll('SELECT * FROM banner WHERE banner_ativo = 1 AND estilos_banners_id=1 ORDER BY banner_ordem');
			$active = 'active';
			foreach($result as $key => $res)
			{
				$result[$key]['active'] = $active;
				$result[$key]['img'] = U::getImg('imagens/banners/'.$res['estilos_banners_id'].'/'.$res['banner_id'].'.'.$res['banner_extensao']);
				$result[$key]['link'] = '';

				if($res['banner_link'] != '')
				{
					$textoBotao = $res['campo_adicional1'] != '' ? $res['campo_adicional1'] : 'Saiba Mais';
					$result[$key]['link'] = '<a href="'.$res['banner_link'].'" class="btn btn-default">'.$textoBotao.'</a>';
				}
				$active = '';
			}
			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}

	}
}