<?php
class Produto
{
	static function get($filtro = '')
	{
		try
		{
			$condicao = '';
			if(isset($_GET['categoria']))
			{
				$condicao .= " AND c.categoria_nome ".Sql::toLike($_GET['categoria']);
			}

			if(isset($_GET['subcategoria']))
			{
				$condicao .= " AND s.subcategoria_nome ".Sql::toLike($_GET['subcategoria']);
			}

			$condicao.=$filtro;

			$sql = "SELECT * FROM produto p
						INNER JOIN subcategoria s ON s.subcategoria_id = p.subcategoria_id
							INNER JOIN categoria c on c.categoria_id = s.categoria_id
								WHERE p.produto_deletado=0
									AND p.produto_ativo=1
										{$condicao}";

			$sqlPaginada = Paginacao::getPaginacao($sql);

			$result = Sql::_fetchAll($sqlPaginada);


			foreach($result as $key => $res)
			{
				$result[$key]['img'] = U::getImg('imagens/produtos/'.$res['produto_id'].'_1_2.'.$res['produto_extensao2']);
				$result[$key]['valor3'] = $res['valor3'] == '' ? '' : '<del>R$ '.$res['valor3'].'</del>';
				$result[$key]['url'] = HTTP.'/produto/'.U::setUrlAmigavel($res['categoria_nome']).'/'.U::setUrlAmigavel($res['subcategoria_nome']).'/'.U::setUrlAmigavel($res['produto_nome']);
				$result[$key]['divlink'] = 	U::divLink($result[$key]['url']);

			}



			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function count()
	{
		try
		{
			$count = Sql::_fetch("SELECT COUNT(*) as linhas FROM produto WHERE produto_deletado=0 AND produto_ativo=1");
			return $count['linhas'];
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getProdutosHome()
	{
		try
		{
			return Produto::get(" AND p.checkbox0=1 ");
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getProdutosSelect()
	{
		try
		{

			$selects='';
			$qtd='<option value="1">1</option><option value="2">2</option><option value="3">3</option></select>';


			$ret = '';
			$sql = "SELECT * FROM produto p
						INNER JOIN subcategoria s
							ON s.subcategoria_id=p.subcategoria_id
								WHERE produto_deletado=0 AND produto_ativo=1
									ORDER BY subcategoria_ordem, ordem";
			$result = Sql::_fetchAll($sql);

			$i = 0;
			foreach($result as $res)
			{
				$selects.='<option valor_produto="'.$res['valor1'].'" descricaoproduto="'.U::clearStr($res['produto_descricao']).'" nome_produto="'.$res['produto_nome'].'" value="'.$res['produto_nome'].'">'.$res['produto_nome'].'</option>';
			}



			for($i=1;$i<=10;$i++)
			{
				$ret.='
				<tr>
					<td><select id="codigo'.$i.'" onChange="recalculaPreco('.$i.')"><option>Escolha</option>'.$selects.'</select></td>
					<td id="descricaoproduto'.$i.'"></td>
					<td><input type="text" id="largura_bobina'.$i.'"  onkeydown="Mascara(this,Integer); recalculaPreco('.$i.');" onkeypress="Mascara(this,Integer); recalculaPreco('.$i.');" onkeyup="Mascara(this,Integer); recalculaPreco('.$i.');" /></td></td>
					<td>500</td>
					<td><select  id="qtd'.$i.'" onChange="recalculaPreco('.$i.')">'.$qtd.'</select></td>
					<td>R$ <span id="result_linha'.$i.'"></span></td>
				</tr>

				<input type="hidden" name="codigodhs['.$i.']" id="codigodhs'.$i.'" value="" />
				<input type="hidden" name="descricao['.$i.']" id="descricao'.$i.'" value="" />
				<input type="hidden" name="largura['.$i.']" id="largura'.$i.'" value="" />
				<input type="hidden" name="comprimento['.$i.']" id="comprimento'.$i.'" value="" />
				<input type="hidden" name="qtd['.$i.']" id="qtdmail'.$i.'" value="" />
				<input type="hidden" name="valor['.$i.']" id="valor'.$i.'" value="" />';
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	static function getProdutosMenu()
	{
		try
		{

			$ret = '';
			$sql = "SELECT * FROM subcategoria WHERE subcategoria_deletada=0 AND subcategoria_ativa=1 AND checkbox0=1 ORDER BY subcategoria_ordem";
			$result = Sql::_fetchAll($sql);


			$i = 0;
			foreach($result as $res)
			{
				$link = 'produto.php?subcategoria='.$res['subcategoria_id'];
				$ret.='
				<li class="container3d relative">
					<a href="'.$link.'" class="d_block color_dark relative">'.$res['subcategoria_nome'].'
					</a>
				</li>';
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getSubcategoriasMenu()
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM subcategoria
			WHERE subcategoria_deletada=0 AND subcategoria_ativa=1";
			$result = Sql::_fetchAll($sql);

			foreach($result as $res)
			{
				$ret.='<li class="active" id="menu'.$res['subcategoria_id'].'"><a href="javascript:void(0)" onclick="displayCursos(\'subcurso'.$res['subcategoria_id'].'\')">'.$res['subcategoria_nome'].'</a></li>';
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getProduto()
	{
		try
		{
			if(! isset($_GET['produto']))
			{
				U::goHome(HTTP.'/produtos');
			}

			$condicao = " AND p.produto_nome ".Sql::toLike($_GET['produto']);
			$condicao .= " AND s.subcategoria_nome ".Sql::toLike($_GET['subcategoria']);
			$condicao .= " AND c.categoria_nome ".Sql::toLike($_GET['categoria']);

			$sql = "
				SELECT * FROM produto p
					INNER JOIN subcategoria s ON s.subcategoria_id = p.subcategoria_id
						INNER JOIN categoria c on c.categoria_id = s.categoria_id
							WHERE p.produto_deletado=0 AND p.produto_ativo=1 {$condicao}";

			$result = Sql::_fetch($sql);

			if(! $result)
			{
				U::goHome(HTTP.'/produtos');
			}

			$result['imgs'] = $result['thumbs'] = '';

			$result['valor3'] = $result['valor3'] == '' ? '' : '<del>R$ '.$result['valor3'].'</del>';

			$i=1;
			while(isset($result['produto_extensao'.$i]))
			{
				$img = U::getImg('imagens/produtos/'.$result['produto_id'].'_'.$i.'_2.'.$result['produto_extensao'.$i],true);
				$thumb = U::getImg('imagens/produtos/'.$result['produto_id'].'_'.$i.'_1.'.$result['produto_extensao'.$i],true);
				if($img && $thumb)
				{
					$result['img'.$i] = U::getImg('imagens/produtos/'.$result['produto_id'].'_'.$i.'_1.'.$result['produto_extensao'.$i],true);

					$result['imgs'].='
					<div class="tm-prodetails-largeimage">
						<img src="'.$img.'" alt="'.$result['produto_nome'].' Foto '.$i.'">
					</div>';

					$result['thumbs'].='
					<div class="tm-prodetails-thumbnail">
						<img src="'.$thumb.'"
							alt="'.$result['produto_nome'].' Foto '.$i.'">
					</div>';
				}
				$i++;
			}

			$i=1;
			while(isset($result['arquivo_extensao'.$i]))
			{
				$result['arquivo'.$i] = U::getFile('arquivos/'.$result['produto_id'].'_'.$i.'.'.$result['arquivo_extensao'+$i], true);
				$i++;
			}

			return U::clearStr($result);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getProdutos()
	{
		try
		{
			$servicos = '';
			$sql = "SELECT * FROM produto WHERE produto_deletado=0 AND produto_ativo=1 ";

			$result = Sql::_fetchAll($sql);


			foreach ($result as $res)
			{
				$link = 'produto.php?produto='.$res['produto_id'];
				$img1 = U::getImg('imagens/produtos/'.$res['produto_id'].'_1_1.'.$res['produto_extensao1']);

					$servicos.='
					<div class="col-lg-4 mb-3 col-md-6 col-sm-12">
                        <div class="service-box service-style-1">
                            <div class="service-thumbnail">
                                <div class="service-thumbnail-inner">
                                    <img src="'.$img1.'" class="img-fluid" alt="'.$res['produto_nome'].'">
                                </div>
                            </div>
                            <div class="service-content">
                                <div class="service-inner text-left" style="background: #EEE;margin: 3px 0 0 0;">
                                    <h3 class="service-box-title"><a href="'.$link.'">'.$res['produto_nome'].'</a></h3>
                                    <div class="service-desc">
                                        <p>'.$res['produto_descricao'].'</p>
                                    </div>
                                    <div class="link-btn">
                                        <a class="skincolor" href="'.$link.'">Conhecer<i class="fa fa-arrow-circle-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';


			}
			return $servicos;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getProdutosTabs()
    {
        try
        {
			$ret = '';
        	$sql = "SELECT * FROM subcategoria WHERE subcategoria_deletada=0 AND subcategoria_ativa=1";
			$result = Sql::_fetchAll($sql);


			foreach($result as $res)
			{
				$ret.=Produto::getProdutos($res['subcategoria_id']);
			}

			return  U::clearStr($ret);
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function getProdutosCheckbox($checkbox = 0)
    {
        try
        {
        	$produtos = '';


            $query = "SELECT *
                            FROM produto
                                WHERE produto_deletado=0 AND produto_ativo=1 AND checkbox{$checkbox}=1";


            $result = Sql::_fetchAll($query);

            if(! $result)
            {
            	U::ocultaX('produtosdestaque');
            }


            $loop = 0;
            foreach ($result as $res)
            {

                $img = U::getImg('/imagens/produtos/'.$res['produto_id'].'_1_1.'.$res['produto_extensao1']);
                $link = 'estoque_detalhe.php?id='.$res['produto_id'];

                $produtos.='
                <div class="col-md-12" '.U::divLink($link).'>
	                  <div class="ts-service-wrapper">
	                     <span class="service-img">
	                        <img class="img-fluid" src="'.$img.'" alt="'.$res['produto_nome'].'">
	                     </span>
	                     <div class="service-content">
	                        <div class="service-icon" style="line-height: 20px;">
	                           <i style="font-size: 15px;">ANO '.$res['valor3'].'</i>
	                        </div>
	                        <h3><a href="'.$link.'">'.$res['produto_nome'].'</a></h3>
	                        <p style="font-weight: 300;"><b>Marca: </b><span style="margin-right: 20px; color: #0270B3;">'.$res['valor2'].'</span><b>Ano: </b><span style="color: #0270B3;">'.$res['valor3'].'</span></p>
	                        <p style="font-weight: 300;"><b>Km: </b><span style="margin-right: 27px; color: #0270B3;">'.$res['valor4'].'</span><b>Câmbio: </b><span style="color: #0270B3;">'.$res['valor5'].'</span></p>
	                        <p>'.$res['produto_descricao2'].'</p>
	                        <a href="'.$link.'" class="readmore" style="color: #0270B3;">Saiba Mais<i class="fa fa-angle-double-right"></i></a>
	                     </div>
	                  </div>
	            </div>';

                $produtos.=U::clearFix(++$loop, 4);
            }


            return $produtos;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getProdutosRelacionados($p)
    {
        try
        {

        	$produtos = '';


            $query = "SELECT *
                            FROM produto
                            	WHERE produto_deletado=0 AND produto_ativo=1 AND produto_id != {$p['produto_id']} AND subcategoria_id = {$p['subcategoria_id']} ORDER BY RAND() LIMIT 10";




            $result = Sql::_fetchAll($query);

            foreach ($result as $res)
            {

                $img = U::getImg('/imagens/produtos/'.$res['produto_id'].'_1_1.'.$res['produto_extensao1']);
                $link = HTTP.'/curso/'.U::setUrlAmigavel($res['produto_nome']);

                $produtos.='
                <div class="item" '.U::divLink($link).'>

                    <div class="ce-feature-box-52 margin-bottom div_blog_noticias_x">

                      <div class="ce-post-img">

                        <a href="'.$link.'">

                          <div class="info-badge"><span class="icon-pencil icon"></span>'.$res['valor4'].' aulas</div>

                          <div class="overlay"><i class="fa fa-plus" aria-hidden="true"></i> </div>

                        </a> <img src="'.$img.'" alt="" class="img-responsive"> </div>

                      <div class="text-box padd-1 shadow">

                        <h5 class="less-mar-1 title titulo_blog_x"><a href="'.$link.'">'.$res['produto_nome'].'</a></h5>

                        <p class="des_blog_x">'.$res['produto_descricao2'].'</p>

                        <div class="date-info-box">



                          <div class="box-left">

                            <i class="fa fa-calendar" aria-hidden="true"></i>&nbsp; '.$res['valor5'].'

                          </div>



                          <div class="box-right">

                            '.$res['valor4'].' aulas

                          </div>



                        </div>

                      </div>

                    </div>

                  </div>';
            }


            return $produtos;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getProdutosMaisVendidos()
    {
        try
        {

        	$produtos = '';


            $query = "SELECT p.*, s.subcategoria_nome
                            FROM produto p
                                INNER JOIN subcategoria s ON s.subcategoria_id=p.subcategoria_id
                                        WHERE p.produto_deletado=0 AND produto_ativo=1 AND p.checkbox0=1 ORDER BY produto_nome";




            $result = Sql::_fetchAll($query);

            foreach ($result as $res)
            {

                $img = U::getImg('/imagens/produtos/'.$res['produto_id'].'_1_1.'.$res['produto_extensao1']);
                $link = 'produto.php?produto_id='.$res['produto_id'];

                $produtos.='
                <div class="item" '.U::divLink($link).'>
                  <div class="col-item">
                    <!-- <div class="sale-label sale-top-right">Sale</div> -->
                    <div class="product-image-area"> <a class="product-image" title="'.$res['produto_nome'].'" href="'.$link.'"> <img src="'.$img.'" class="img-responsive" alt="'.$res['produto_nome'].'" /> </a>
                    <div class="hover_fly">
                        <a class="exclusive ajax_add_to_cart_button" href="#" title="Adicionar ao Carrinho">
                        <div>
                          <i class="icon-shopping-cart"></i>
                          <span>Adicionar ao Carrinho</span>
                        </div>
                        </a>
                      </div>
                    </div>
                    <div class="info">
                      <div class="info-inner">
                        <div class="item-title"> <a title="'.$res['produto_nome'].'" href="'.$link.'">'.$res['produto_nome'].'</a> </div>
                        <!--item-title-->
                        <div class="item-content">
                          <!-- <div class="ratings">
                            <div class="rating-box">
                              <div class="rating"></div>
                            </div>
                          </div> -->
                          <div class="price-box">
                            <p class="special-price"> <span class="price"> R$ '.Produto::getPreco($res).' </span> </p>
                          </div>
                        </div>
                        <!--item-content-->
                      </div>
                      <!--info-inner-->
                      <div class="clearfix"> </div>
                    </div>
                  </div>
                </div>';
            }


            return $produtos;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }


    static function getProdutosDestaque()
    {
        try
        {

        	$produtos = '';


            $query = "SELECT p.*, s.subcategoria_nome
                            FROM produto p
                                INNER JOIN subcategoria s ON s.subcategoria_id=p.subcategoria_id
                                        WHERE p.produto_deletado=0 AND produto_ativo=1 AND p.checkbox1=1 ORDER BY produto_nome";




            $result = Sql::_fetchAll($query);

            foreach ($result as $res)
            {

                $img = U::getImg('/imagens/produtos/'.$res['produto_id'].'_1_1.'.$res['produto_extensao1']);
                $link = 'produto.php?produto_id='.$res['produto_id'];

                $produtos.='
                <div class="item" '.U::divLink($link).' >
		            <div class="col-item">
		              <!-- <div class="sale-label sale-top-right">Sale</div> -->
		              <div class="product-image-area"> <a class="product-image" title="'.$res['produto_nome'].'" href="'.$link.'"> <img src="'.$img.'" class="img-responsive" alt="'.$res['produto_nome'].'" /> </a>
		              </div>
		              <div class="info">
		                <div class="info-inner">
		                  <div class="item-title"> <a title=" '.$res['produto_nome'].'" href="'.$link.'"> '.$res['produto_nome'].' </a> </div>
		                  <!--item-title-->
		                  <div class="item-content">
		                      <div class="price-box">
		                      <p class="special-price"> <span class="price"> R$ '.Produto::getPreco($res).' </span> </p>
		                    </div>
		                  </div>
		                  <!--item-content-->
		                </div>
		                <!--info-inner-->
		                <div class="actions">
		                  <button type="button" title="Adicionar ao Carrinho" class="button btn-cart"><span>Adicionar ao Carrinho</span></button>
		                </div>
		                <!--actions-->
		                <div class="clearfix"> </div>
		              </div>
		            </div>
		         </div>';
            }


            return $produtos;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getPreco($p)
    {
    	try
    	{
   			return $p['valor1'];
    	}
    	catch( Exception $e )
    	{
    		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    	}
    }


	static function getProdutosRecentes()
	{
		try
		{
			$ret='';
			for ($i=0; $i < 10; $i++)
			{
				$ret.='
				<div class="item item-carousel">
		            <div class="products">
		                <div class="product">
		                    <div class="product-image">
		                        <div class="image">
		                            <a href="#"><img  src="assets/images/blank.gif" data-echo="assets/images/products/laco0.jpg" alt=""></a>
		                        </div><!-- /.image -->
		                    </div><!-- /.product-image -->
		                    <div class="product-info text-left">
		                        <h3 class="name"><a href="#">'.$i.'Laço de Onçinha</a></h3>
		                        <div class="description"></div>
		                        <div class="product-price">
		                            <span class="price">
		                                R$ 14,99
		                            </span>
		                        </div><!-- /.product-price -->
		                    </div><!-- /.product-info -->
		                    <div class="cart clearfix animate-effect">
		                        <div class="action">
		                            <ul class="list-unstyled">
		                                <li class="add-cart-button btn-group">
		                                    <button class="btn btn-primary icon" data-toggle="dropdown" type="button"><i class="fa fa-shopping-cart"></i></button><a href="produto-detalhado.php" class="btn btn-primary">Ver Detalhes</a>
		                                </li>
		                            </ul>
		                        </div><!-- /.action -->
		                    </div><!-- /.cart -->
		                </div><!-- /.product -->
		            </div><!-- /.products -->
		        </div>';
			}
			if($ret != '')
			{
				$ret='
				<h3 class="section-title2">Produtos Adicionados Recentemente</h3>
			    <div class="owl-carousel home-owl-carousel custom-carousel owl-theme outer-top-xs">
					'.$ret.'
			    </div>';
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getCategoriasHome()
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM categoria WHERE categoria_deletada=0 AND categoria_ativa=1";
			$result = Sql::_fetchAll($sql);

			foreach($result as $res)
			{
				$img = U::getImg('/imagens/categorias/'.$res['categoria_id'].'_1_1.'.$res['categoria_extensao1']);
				$link = HTTP.'/produtos/'.U::setUrlAmigavel($res['categoria_nome']);
				$ret.='
				<div class="col-sm-4" '.U::divLink($link).'>
					<div class="iconbox style3">
						<div class="iconbox-icon imgCategoria">
							<img src="'.$img.'"/>
						</div>
						<div class="iconbox-content">
							<h3 class="iconbox-title"><a href="'.$link.'">'.$res['categoria_nome'].'</a></h3>
							<div class="iconbox-desc">
								<a href="'.$link.'">'.$res['categoria_descricao'].'</a>
							</div>
						</div>
						<div class="clearfix">
						</div>
					</div>
				</div>';
			}
			return U::clearStr($ret);
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
			if(isset($_GET['categoria']))
			{
				return self::getSubcategorias();
			}
			$ret = '';
			$sql = "SELECT * FROM categoria WHERE categoria_deletada=0 AND categoria_ativa=1";
			$result = Sql::_fetchAll($sql);

			foreach($result as $res)
			{
				$img = U::getImg('/imagens/categorias/'.$res['categoria_id'].'_1_1.'.$res['categoria_extensao1']);
				$link = HTTP.'/produtos/'.U::setUrlAmigavel($res['categoria_nome']);
				$ret.='
				<div class="col-sm-4" '.U::divLink($link).'>
					<div class="imagebox style3">
						<div class="imagebox-image">
							<a href="'.$link.'" title="">
								<img src="'.$img.'" alt="">
								<i class="fa fa-link" aria-hidden="true"></i>
								<div class="overlay"></div>
							</a>
						</div>
						<div class="imagebox-header">
							<h3 class="imagebox-title">
								<a href="'.$link.'" title="">'.$res['categoria_nome'].'</a>
							</h3>
						</div>
						<div class="imagebox-content">
							<div class="imagebox-desc">
								'.$res['categoria_descricao'].'
							</div>
						</div>
					</div>
				</div>';
			}

			return array('titulo' => 'Produtos', 'conteudo' => U::clearStr($ret));
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

			$sql = "SELECT * FROM subcategoria s
							INNER JOIN categoria c ON s.categoria_id=c.categoria_id
								WHERE subcategoria_deletada=0 AND subcategoria_ativa=1 ORDER BY subcategoria_ordem";

			$result = Sql::_fetchAll($sql);

			foreach($result as $key => $res)
			{
				$result[$key]['img'] = U::getImg('/imagens/subcategorias/'.$res['subcategoria_id'].'_1_1.'.$res['subcategoria_extensao1']);
				$result[$key]['url'] = HTTP.'/produtos/'.U::setUrlAmigavel($res['categoria_nome']).'/'.U::setUrlAmigavel($res['subcategoria_nome']);
			}

			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


}
