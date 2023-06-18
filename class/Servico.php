<?php
class Servico
{
	static function count()
	{
		try
		{
			$count = Sql::_fetch("SELECT COUNT(*) as linhas FROM servicos WHERE servico_deletado=0 AND servico_ativo=1");
			return $count['linhas'];
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}



	static function getServicosLiMenu()
	{
		try
		{
			$ret = array('servicos'=>'','servicosComplementares'=>'');
			$result = Sql::_fetchall("SELECT * FROM servicos WHERE servico_deletado=0 AND servico_ativo=1 ORDER BY ordem");
			foreach ($result as $res)
			{

				$link = HTTP.'/servicos?servico='.$res['servico_id'];
				if($res['subcategoria_id'] == 1)
				{
					$ret['servicos'].='
					<li>
						<a href="servico?servico='.$res['servico_id'].'">'.$res['servico_titulo'].'</a>
					</li>';
				}
				else
				{
					$ret['servicosComplementares'].='
					<li>
						<a href="servico?servico='.$res['servico_id'].'">'.$res['servico_titulo'].'</a>
					</li>';
				}

			}
			return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getServico()
	{
		try
		{
			if(! isset($_GET['servico']))
			{
				U::goHome(HTTP.'/servicos.php');
			}

			$sql = "SELECT * FROM servicos WHERE servico_deletado=0 AND servico_ativo=1 AND servico_id = ".$_GET['servico'];
			$result = Sql::_fetch($sql);

			if(! $result)
			{
				U::goHome(HTTP.'/servicos.php');
			}

			$result['img1'] = U::getImg('imagens/servicos/'.$result['servico_id'].'_1_1.'.$result['servico_extensao1'],true);

			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function get()
	{
		try
		{
			$condicao = ' ';
			if(isset($_GET['servico']))
			{
				$id=$_GET['servico'];
				$condicao = " AND s.servico_titulo ".Sql::toLike($_GET['servico']);
				$condicao .= " AND sub.subcategoria_nome ".Sql::toLike($_GET['subcategoria']);
				$condicao .= " AND cat.categoria_nome ".Sql::toLike($_GET['categoria']);
			}

			$sql = "SELECT * FROM servicos s 
							INNER JOIN subcategoria_servicos sub ON s.subcategoria_id = sub.subcategoria_id  
								INNER JOIN categoria_servicos cat on cat.categoria_id = sub.categoria_id 
									WHERE s.servico_deletado=0 
										AND s.servico_ativo=1 
											{$condicao}";
			
			$result = Sql::_fetchAll($sql);

			foreach($result as $key => $res)
			{
				$result[$key]['img'] = U::getImg('imagens/servicos/'.$res['servico_id'].'_1_1.'.$res['servico_extensao1']);
				$result[$key]['url'] = HTTP.'/servico/'.U::setUrlAmigavel($res['categoria_nome']).'/'.U::setUrlAmigavel($res['subcategoria_nome']).'/'.U::setUrlAmigavel($res['servico_titulo']);

			}

			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getCategorias()
	{
		try
		{
			$condicao = ' ';
			$servicos = '';
			$sql = "SELECT * FROM categoria WHERE categoria_deletada=0 AND categoria_ativa=1 ".$condicao." ";
			// die($sql);

			$result = Sql::_fetchAll($sql);

			foreach($result as $key => $res)
			{
				$result[$key]['url'] = HTTP.'/servico/'.U::setUrlAmigavel($res['categoria_nome']);
			}
			if($condicao != ' '){
				return $result[0];
			}
			// die(var_dump($result));
			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}