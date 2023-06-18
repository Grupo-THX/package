<?php
class Blog
{

	static function get($filtro = '')
	{
		try
		{
			$condicao = " WHERE noticias.noticia_deletada=0 AND noticias.noticia_ativa=1 ";
			if(isset($_GET['subcategoria']))
			{
				$likeSub = Sql::toLike($_GET['subcategoria']);
				$condicao.=" AND subcategoria_noticias.subcategoria_nome".$likeSub." ";
			}

			if(isset($_GET['busca']))
			{
				$condicao.=" AND (noticias.noticia_titulo LIKE '%{$_GET['busca']}%' OR noticias.noticia_texto LIKE '%{$_GET['busca']}%') ";
			}

			$condicao.=$filtro;

			$sql = "SELECT * FROM noticias
						INNER JOIN subcategoria_noticias ON subcategoria_noticias.subcategoria_id = noticias.subcategoria_id
							INNER JOIN categoria_noticias ON categoria_noticias.categoria_id = subcategoria_noticias.categoria_id
								 {$condicao} ORDER BY UNIX_TIMESTAMP(noticias.noticia_data) DESC";

			$result = Sql::_fetchAll(Paginacao::getPaginacao([$sql, '*']));

			foreach ($result as $key => $res)
			{
				$i=1;
				while(isset($res['noticia_extensao'.$i]))
				{
					$result[$key]['img'.$i] = U::getImg('imagens/noticias/'.$res['noticia_id'].'_1_1.'.$res['noticia_extensao'.$i]);
					$i++;
				}

				$result[$key]['url'] = HTTP.'/post/'.U::setUrlAmigavel($res['categoria_nome']).'/'.U::setUrlAmigavel($res['subcategoria_nome']).'/'.U::setUrlAmigavel($res['noticia_titulo']);
				$result[$key]['noticia_data'] = U::formataData($res['noticia_data']);
				$result[$key]['divlink'] = U::divLink($result[$key]['url']);

         }

        //X::dd($result);
         return U::clearStr($result);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getNoticias($condicao = '')
	{
		try
		{
			$condicao = " WHERE n.noticia_id > 0 ".$condicao;

			$sql = "SELECT * FROM noticias n
						INNER JOIN subcategoria_noticias s ON s.subcategoria_id = n.subcategoria_id
							INNER JOIN categoria_noticias c ON c.categoria_id = s.categoria_id
								 {$condicao}";

			$result = Sql::_fetchAll($sql);

			foreach ($result as $key => $res)
			{
				$i=1;
				while(isset($res['noticia_extensao'.$i]))
				{
					$result[$key]['img'.$i] = U::getImg('imagens/noticias/'.$res['noticia_id'].'_1_1.'.$res['noticia_extensao'.$i]);
					$i++;
				}

				$result[$key]['link'] = HTTP.'/post/'.U::setUrlAmigavel($res['categoria_nome']).'/'.U::setUrlAmigavel($res['subcategoria_nome']).'/'.U::setUrlAmigavel($res['noticia_titulo']);
				$result[$key]['data'] = U::formataData($res['noticia_data']);
				$result[$key]['divlink'] = U::divLink($result[$key]['link']);

            }
            return U::clearStr($result);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getNoticiasRelacionadas($id)
	{
		try
		{
			$noticias = '';
			$sql = "SELECT * FROM noticias WHERE noticia_deletada=0 AND noticia_ativa=1 AND noticia_id != ".$id." ORDER BY UNIX_TIMESTAMP(noticia_data) DESC LIMIT 3";

			$result = Sql::_fetchAll($sql);

			foreach ($result as $res)
			{

				$result[$key]['img1'] = U::getImg('imagens/noticias/'.$res['noticia_id'].'_1_1.'.$res['noticia_extensao1']);
				$result[$key]['img2'] = U::getImg('imagens/noticias/'.$res['noticia_id'].'_2_1.'.$res['noticia_extensao2']);
				$result[$key]['link'] = HTTP.'/post/'.U::setUrlAmigavel($res['noticia_titulo']);
            }


            return U::clearStr($result);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getNoticiasRecentes()
	{
		try
		{
			$noticias = '';
			$sql = "SELECT * FROM noticias n
							INNER JOIN subcategoria_noticias s ON s.subcategoria_id = n.subcategoria_id
								INNER JOIN categoria_noticias c ON c.categoria_id = s.categoria_id
									WHERE n.noticia_deletada=0 AND n.noticia_ativa=1 ORDER BY UNIX_TIMESTAMP(n.noticia_data) DESC LIMIT 5";

			$result = Sql::_fetchAll($sql);

			foreach ($result as $key => $res)
			{
				$i=1;
				$result[$key]['img'.$i] = U::getImg('imagens/noticias/'.$res['noticia_id'].'_1_1.'.$res['noticia_extensao'.$i]);

				$result[$key]['url'] = HTTP.'/post/'.U::setUrlAmigavel($res['categoria_nome']).'/'.U::setUrlAmigavel($res['subcategoria_nome']).'/'.U::setUrlAmigavel($res['noticia_titulo']);
				$result[$key]['data'] = U::formataData($res['noticia_data']);
				$result[$key]['divlink'] = U::divLink($result[$key]['url']);

         }

			return U::clearStr($result);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	static function getBlogArquivo()
	{
		try
		{
			$arquivo = '';
			$result = Sql::_fetchAll("SELECT DATE_FORMAT(noticia_data, '%b %Y') AS sDate,DATE_FORMAT(noticia_data, '%Y') AS ano, DATE_FORMAT(noticia_data, '%c') AS mes, COUNT(noticia_id) AS cont FROM noticias WHERE noticia_deletada=0 AND noticia_ativa=1 GROUP BY sDate ORDER BY noticia_data DESC");
			$anolistado = false;
			foreach ($result as $ano)
			{
				if($anolistado != $ano['ano'])
				{
					$arquivo.='<li><a href="#">'.$ano['ano'].'</a><ul>';

					foreach ($result as $mes)
					{
						if($mes['ano'] == $ano['ano'])
						{
							$arquivo.='<li><a href="blog.php?ano='.$mes['ano'].'&mes='.$mes['mes'].'">'.U::getMesExtenso($mes['mes']).' ('.$mes['cont'].')</a></li>';
						}
					}


					$arquivo.='</ul></li>';

					$anolistado = $ano['ano'];
				}
			}

			return $arquivo;
			return'
			 <li><a href="#">Janeiro</a><ul><li><a href="#">Março</a></li></ul></li>
            <li><a href="#">Fevereiro</a></li>
            <li><a href="#">Março</a></li>
            <li><a href="#">Abril</a></li>
            <li><a href="#">Maio</a></li>
            <li><a href="#">Junho</a></li>
            <li><a href="#">Julho</a></li>';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	static function getNoticia()
	{
		try
		{
			if(! isset($_GET['post']))
			{
				U::goHome();
			}

			$likeCat = Sql::toLike($_GET['categoria']);
			$likeSub = Sql::toLike($_GET['subcategoria']);
			$likeLinha = Sql::toLike($_GET['post']);

			$sql = "SELECT * FROM noticias n
						INNER JOIN subcategoria_noticias s ON s.subcategoria_id = n.subcategoria_id
							INNER JOIN categoria_noticias c ON s.categoria_id = c.categoria_id
								WHERE noticia_deletada=0 AND noticia_ativa=1 AND categoria_nome {$likeCat} AND subcategoria_nome {$likeSub} AND noticia_titulo {$likeLinha}";

			$res = Sql::_fetch($sql);
			if(! $res)
			{
				U::goHome('/blog');
			}
			$i=1;
			while(isset($res['noticia_extensao'.$i]))
			{
				$res['img'.$i] = U::getImg('imagens/noticias/'.$res['noticia_id'].'_1_1.'.$res['noticia_extensao'.$i]);
				$i++;	
			}

			$res['noticia_data'] = U::formataData($res['noticia_data']);

			return U::clearStr($res);

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	static function getSubcategorias()
	{
		try
		{
			$sql = "SELECT *, 
						(SELECT count(*) FROM noticias n WHERE s.subcategoria_id=n.subcategoria_id AND n.noticia_deletada=0 AND n.noticia_ativa=1) as linhas 
							FROM subcategoria_noticias s
									INNER JOIN categoria_noticias c ON s.categoria_id = c.categoria_id
										WHERE s.subcategoria_deletada=0 AND s.subcategoria_ativa=1  ORDER BY  s.subcategoria_ordem";
			
			$result = Sql::_fetchAll($sql);

			foreach($result as $key => $res)
			{
				$result[$key]['data'] = $res['linhas'];
				$result[$key]['url'] = HTTP.'/blog/'.U::setUrlAmigavel($res['categoria_nome']).'/'.U::setUrlAmigavel($res['subcategoria_nome']);
				$result[$key]['divlink'] = U::divLink($result[$key]['url']);
			}

			return U::clearStr($result);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


}
