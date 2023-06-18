<?php
class LandPage
{
	static function count()
	{
		try
		{
			$count = Sql::_fetch("SELECT COUNT(*) as linhas FROM landpage WHERE landpage_deletada=0 AND landpage_ativa=1");
			return $count['linhas'];
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function addAcessoPaginaPouso($id)
	{
		try
		{
			$result = Sql::_query("UPDATE landpage SET landpage_acessos = landpage_acessos+1 WHERE landpage_id = {$id}");
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
    static function getPaginaPouso()
    {
        try
        {

        	$retorno='';
			if(isset($_GET['page']))
			{
				$like = Sql::toLike($_GET['page']);

				$query = "SELECT * FROM landpage WHERE landpage_deletada=0 AND landpage_ativa=1 AND landpage_h1 LIKE '{$like}'";

				$result = Sql::_fetch($query);
				if($result)
				{
					$GLOBALS['Xjs'][]='addAcessoPaginaPouso('.$result['landpage_id'].');';
					$i=1;
					$result['imagens'] = $result['imagem'] = '';
					while(isset($result['landpage_extensao'.$i]))
					{
						$img = U::getImg('/imagens/landpage/'.$result['landpage_id'].'_1_1.'.$result['landpage_extensao1'], true);
						if($img)
						{
							if($result['imagem'] == '')
							{
								$result['imagem'] = $img;
							}
							$result['imagens'].= '
							<li>
								<a href="#" title="">
									<img src="'.$img.'" alt="'.$result['landpage_h1'].'" title="'.$result['landpage_h1'].'" />
								</a>
							</li>';
						}
						$i++;
					}

					return $result;
				}
			}
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getPaginasDePouso($termo = false)
    {
        try
        {
        	$ret='';
        	$condicao=" WHERE landpage_deletada=0 AND landpage_ativa=1 ";


        	if($termo)
        	{
        		$condicao.= " AND landpage_h1 != '{$termo}' ";
        		$termo = 'Não estava procurando por <strong>'.$termo.'</strong>?';
        	}
        	else
        	{
        		$termo = 'Veja abaixo um pouco dos serviços que podemos oferecer';
        	}


			$query = "SELECT * FROM landpage {$condicao} ORDER BY RAND() LIMIT 25";
			$result = Sql::_fetchAll($query);

			foreach($result as $res)
			{
				$link = HTTP.'/manutencao/'.U::setUrlAmigavel($res['landpage_h1']);
				$ret.='
				<div class="col-sm-3" '.U::divLink($link).'>
					<div class="iconbox style4">
						<a href="'.$link.'" class="iconbox-title">'.$res['landpage_h1'].'</a>
					</div>
				</div>';
				$ret.=U::clearFix(++$loop, 4);
			}

			$ret = '
			<section class="flat-row flat-iconbox style4 buscaequip" id="buscas">
				<div class="container">
					<div class="row">
						<div class="col-md-12">
							<div class="flat-row-title">
								<h2>'.$termo.'</h2>
								<h6>Talvez sua procura esteja em alguns dos itens abaixo:</h6> 
							</div>
						</div>
						'.$ret.'
					</div>
				</div>
			</section>';

			return $ret;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
}