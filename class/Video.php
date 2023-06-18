<?php
class Video
{
	static function getVideotecas()
	{
		try
		{
			$ret = '';

			$result = Sql::_fetchall('SELECT * FROM videoteca WHERE videoteca_deletada=0 AND videoteca_ativa=1 ORDER BY videoteca_ordem');
			foreach ($result as $res)
			{
				$link = HTTP.'/tv-operb/'.U::setUrlAmigavel($res['videoteca_nome']);
				$ret.= '<a class="btn btn-prim uppercase" href="'.$link.'"  style="width: 100%; margin-bottom: 15px">'.$res['videoteca_nome'].'</a>';
			}


			return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getVideos()
	{
		try
		{

			$condicao = " WHERE video_deletado=0 AND video_ativo=1 ";
			if(isset($_GET['videoteca']))
			{
				$likeSub = Sql::toLike($_GET['videoteca']);
				$condicao.=" AND vv.videoteca_nome".$likeSub." ";
			}

			if(isset($_GET['busca']))
			{
				$condicao.=" AND (v.video_nome LIKE '%{$_GET['busca']}%' OR v.video_descricao LIKE '%{$_GET['busca']}%') ";
			}


			$ret = array('videos' => '', 'titulo' => 'TV OPERB');

			$sql ="SELECT * FROM video v
										INNER JOIN videoteca vv ON vv.videoteca_id=v.videoteca_id
											{$condicao} ORDER BY video_data DESC";

			$pagSql = Paginacao::getPaginacao($sql, 6);

			$ret['paginacao'] = $pagSql['paginacao'];

			$result = Sql::_fetchAll($pagSql['query']);


			if(! $result)
			{
				if(isset($_GET['busca']))
				{
					$ret['videos'] = '<h4>Nenhuma noticia encontrado para a busca <i>'.$_GET['busca'].'</i> </h4>';
				}
				else
				{
					$ret['videos'] = '<h4>Essa Categoria nao possui noticias </h4>';
				}
			}
			else
			{

				if(isset($_GET['videoteca']))
				{
					$ret['titulo'] = 'TV OPERB: '.$result[0]['videoteca_nome'];
				}
			}


			foreach ($result as $res)
			{
				$img = 'imagens/videos/'.$res['video_id'].'_1_1.'.$res['video_capa_extensao1'];
				$data = U::formataData($res['video_data']);
				$link = HTTP.'/tv-operb-in/'.U::setUrlAmigavel($res['videoteca_nome']).'/'.U::setUrlAmigavel($res['video_nome']);
				$ret['videos'].= '
				<div class="col-md-6 col-sm-6 col-xs-12" '.U::divLink($link).'>
					<div class="ce-feature-box-52 margin-bottom div_blog_noticias_x">
						<div class="video_tv_operb_x">
							<iframe src="'.$res['video_url'].'" allowfullscreen></iframe>
						</div>
						<div class="text-box padd-1 shadow">
							<h5 class="less-mar-1 title titulo_blog_x">'.$res['video_nome'].'</h5>
							<p class="des_blog_x">'.$res['video_descricao2'].'</p>
							<div class="date-info-box">
								<div class="box-left">
									<i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; '.$data.'
								</div>
							</div>
						</div>
					</div>
				</div>';
			}


			return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getVideo()
	{
		try
		{

			if(! isset($_GET['video']))
			{
				U::goHome();
			}
			$like = Sql::toLike($_GET['video']);
			$likeSub = Sql::toLike($_GET['videoteca']);
			$sql = "SELECT * FROM video v
						INNER JOIN videoteca vv ON vv.videoteca_id=v.videoteca_id
							WHERE video_deletado=0 AND video_ativo=1 AND v.video_nome".$like." AND vv.videoteca_nome".$likeSub;

			$res = Sql::_fetch($sql);
			if(! $res)
			{
				U::goHome();
			}

			$res['video_data'] = U::formataData($res['video_data']);

			return U::clearStr($res);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}