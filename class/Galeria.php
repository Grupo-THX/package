<?php
class Galeria
{
	static function getAlbuns()
    {
    	try
    	{
    		$albuns = '<li><a href="galeria-de-fotos.php">Todas Fotos</a></li>';
    		$sql = 'SELECT *, a.colecao_id as colecao FROM album a
    					INNER JOIN foto f ON a.album_id = f.album_id
    						WHERE album_deletado = 0 AND album_ativo = 1
                            GROUP BY (a.album_id)
                            	ORDER BY album_ordem ASC, foto_capa DESC';

    		$result = Sql::_fetchAll($sql);

    		foreach ($result as $res)
    		{
    			$img = 'imagens/colecoes/albuns/'.$res['colecao'].'/'.$res['album_id'].'/'.$res['foto_id'].'_1.'.$res['foto_extensao'];
    			$link = 'galeria-de-fotos.php?album='.$res['album_id'];
    			$class=isset($_GET['album']) && $_GET['album'] == $res['album_id'] ? ' class="albumSelected" ' : '';
    			$albuns.= '
    			<li><a '.$class.' href="'.$link.'">'.$res['album_nome'].'</a></li>';
    		}

    		return $albuns;
    	}
    	catch( Exception $e )
    	{
    		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    	}
    }

    static function getAlbunsLi()
    {
        try
        {
            $albuns = '';
            $sql = 'SELECT * FROM album WHERE album_deletado = 0 AND album_ativo = 1 AND album_id != 1 ORDER BY album_ordem';

            $result = Sql::_fetchAll($sql);
            foreach ($result as $res)
            {
                if(! isset($_GET['album']))
                {
                    $_GET['album'] = $res['album_id'];
                }
                $link = HTTP.'/fotos?album='.$res['album_id'];

                $active = $_GET['album'] == $res['album_id'] ? 'active' : '';
                $albuns.= '<li class="'.$active.'" ><a href="'.$link.'">'.$res['album_nome'].'</a></li>';

            }

            return $albuns;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getAlbunsFotos()
    {
    	try
    	{
    		$albuns = '';
    		$sql = 'SELECT * FROM album a
    					INNER JOIN foto f ON a.album_id = f.album_id
    						WHERE album_deletado = 0 AND album_ativo = 1
                            	ORDER BY a.album_ordem, a.album_id';

    		$result = Sql::_fetchAll($sql);
    		$albumListado = false;
    		foreach ($result as $res)
    		{
    			if($albumListado != $res['album_id'])
    			{
    				$i = 1;
    				$albuns.='
    				<h2 class="main-title"> '.$res['album_nome'].' <span> '.$res['album_campo_adicional2'].' </span> </h2>
                    <div class="video-container">
                             <iframe src="'.$res['album_campo_adicional1'].'" frameborder="0" width="560" height="315"></iframe>
                    </div>
                    <div class="dt-sc-portfolio-container animate" data-animation="fadeIn" data-delay="100">
                    ';

                    foreach($result as $foto)
                    {
                    	if($res['album_id'] == $foto['album_id'])
                    	{
                    		$img = 'imagens/colecoes/albuns/'.$foto['colecao_id'].'/'.$foto['album_id'].'/'.$foto['foto_id'].'_1.'.$foto['foto_extensao'];
    						$link = 'album.php?album='.$res['album_id'];
                    		$albuns.='
                    		<div class="dt-sc-portfolio f'.$foto['album_nome'].' width3 adjust">
                                <figure>
                                    <img src="'.$img.'" alt="'.$foto['album_nome'].' '.$i.'" title="'.$foto['album_nome'].' '.$i.'">
                                    <figcaption>
                                        <div class="fig-overlay">
                                            <h6><a href="#">'.$foto['album_nome'].' '.$i.'</a></h6>
                                            <div class="external-icons">
                                                <a class="zoom" href="'.$img.'" data-gal="prettyPhoto[gallery]" title="Foto '.$i.' do album"> <span class="fa fa-search-plus"> </span> </a>
                                            </div>
                                        </div>
                                    </figcaption>
                               </figure>
                            </div>';

                            $i++;
                    	}
                    }

                	$albuns.='
                	</div>
                	<div class="dt-sc-hr-invisible-large"> </div>';

                	$albumListado = $res['album_id'];
    			}
    		}

    		return $albuns;

    	}
    	catch( Exception $e )
    	{
    		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    	}
    }

    static function getAlbunsDaColecao($colecao)
    {
    	try
    	{
    		$albuns = '';
    		$sql = 'SELECT *, a.colecao_id as colecao FROM album a
    					INNER JOIN foto f ON a.album_id = f.album_id
    						WHERE album_deletado = 0 AND album_ativo = 1
    							AND a.colecao_id = '.$colecao.'
                            			ORDER BY a.album_ordem ASC, a.album_id ASC, foto_capa DESC';

    		$result = Sql::_fetchAll($sql);
    		$albumListado = false;
    		foreach ($result as $res)
    		{
    			$style = 'style="display: none"';
    			if($albumListado != $res['album_id'])
    			{
    				$style = '';
    				$albumListado = $res['album_id'];
    				$foto=1;
    			}
    			$img = 'imagens/colecoes/albuns/'.$res['colecao'].'/'.$res['album_id'].'/'.$res['foto_id'].'_1.'.$res['foto_extensao'];
    			$link = 'album.php?album='.$res['album_id'];
    			$albuns.= 'foto
	    		<div class="gallery-item col-lg-3 col-md-4 col-sm-6 col-xs-12" '.$style.'">
                    <div class="inner-box">
                        <div class="image"><img src="'.$img.'" alt="'.$res['album_nome'].'">
                            <!--Overlay Box-->
                            <div class="overlay-box">
                                <div class="content">
                                    <a class="lightbox-image" href="'.$img.'" title="'.$res['album_nome'].'" data-fancybox-group="gallery'.$res['album_id'].'"><span class="icon fa fa-plus"></span></a>
                                    <p>'.$res['album_nome'].'</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                $foto++;
    		}

    		return $albuns;
    	}
    	catch( Exception $e )
    	{
    		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    	}
    }



    static function getFotos()
	{
		try
		{
            if(! isset($_GET['album']))
            {
                $_GET['album']=1;
            }
			$condicao = " WHERE foto_deletada=0 AND foto_ativa=1 AND album_id=".(int)$_GET['album'];

			$retorno = '';

			$sql = "SELECT * FROM foto {$condicao}";

            $classcss = "";
            $count = 0;
			$result = Sql::_fetchall($sql);


			foreach ($result as $res)
			{
                $img = HTTP.'/imagens/colecoes/albuns/1/'.$res['album_id'].'/'.$res['foto_id'].'_1.'.$res['foto_extensao'];
                $retorno.='
                <div class="col-xs-12 col-sm-6 col-md-3 project-item interior gardening">
					<div class="project-img">
						<img class="" src="'.$img.'" alt="interior" />
						<div  class="project-hover img-popup"  href="'.$img.'" >
							<div class="project-meta">
								<h4>
									<a href="#">'.$res['foto_nome'].'</a>
								</h4>
							</div>
							<div class="project-zoom">
								<a class="img-popup" href="'.$img.'" title="'.$res['foto_nome'].'"><i class="fa fa-search"></i></a>
							</div>
						</div>
					</div>
				</div>';

                // $count++;
                // if($count >= 4)
                // {
                //     $classcss = "morefotos";
                // }
			}


			return U::clearStr($retorno);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

    static function getFotosInstagram()
    {
        try
        {
            $layout = '
            <div id="instagram-feed">'.Layout::loading().'</div>';

                $GLOBALS['Xjs'][]="
                $.instagramFeed({
                    'username': 'techdoor.aruja',
                    'container': '#instagram-feed',
                    'display_profile': false,
                    'display_biography': false,
                    'display_gallery': true,
                    'callback': null,
                    'styling': false,
                    'items': 6,
                    'items_per_row': 3,
                    'margin': 10
                });";


            $GLOBALS['XjsScriptsAdicionais'][] = '<script src="'.HTTP.'/xpainel/js/vendor/instagramFeed/jquery.instagramFeed.min.js"></script>';

             return $layout;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getFotosCarrossel()
    {
        try
        {
            $loops = 8;
            $ret=array('','','','');
            $sql = "SELECT *,album_nome,album_campo_adicional1 FROM foto f INNER JOIN album a ON a.album_id=f.album_id WHERE foto_deletada=0 AND foto_ativa=1";


            $result = Sql::_fetchAll($sql);
            $linhas=count($result);
            $prefixo = '<div class="item active">';
            $sulfixo = '</div>';
            $idcolum = 'foto_id';
            $bt = '<li data-target="#myCarousel" data-slide-to="0" class="active"></li>';
            $null = '';
            $contador=0;
             $ret[0] = $result[0]['album_nome'];
             $ret[1] = $result[0]['album_campo_adicional1'];
            for($i=0;$i < $linhas; $i+=$loops)
            {
                $ret[3].=$prefixo;
                $ret[2].=$bt;

                for($lis=0;$lis < $loops ; $lis++)
                {
                    if(isset($result[$i+$lis][$idcolum]))
                    {
                        $res = $result[$i+$lis];
                        $img = HTTP.'/imagens/colecoes/albuns/1/'.$result[$i+$lis]['album_id'].'/'.$result[$i+$lis]['foto_id'].'_1.'.$result[$i+$lis]['foto_extensao'];
                        $link='';

                            $ret[3].= '
                            <div class="single-project col-md-3 col-lg-3 pad-0 col-sm-6 col-xs-12 corporate">
                                <div class="single-project-details">
                                    <div class="project-img">
                                        <img src="'.$img.'" alt="">
                                    </div>
                                    <div class="project-details">
                                        <div class="project-view-details">
                                            <a class="project-big-thumb" href="'.$img.'" data-effect="mfp-zoom-in"><i class="fa fa-search"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>';

                    }
                    else
                    {
                        $ret[3].= $null;
                    }


                }

            $ret[3].=$sulfixo;
            $prefixo = '<div class="item">';
            $bt = '<li data-target="#myCarousel" data-slide-to="'.++$contador.'" class=""></li>';
        }
            return $ret;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

}