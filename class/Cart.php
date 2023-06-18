<?php
class Cart
{
	static function getCarrinho()
    {
        try
        {

            self::setSessaoCart();

            if(isset($_REQUEST['acao_carrinho']))
            {
                $acao=$_REQUEST['acao_carrinho'];

                $cartKey =  Cart::getCartKey();

                if($acao =='add')
                {
                    $result = Cart::getProduto();
                    if($result)
                    {
                        $_SESSION[X]['carrinho']['produtos'][$cartKey] = $result;
                        $_SESSION[X]['carrinho']['produtos'][$cartKey]['produto_imagem'] = U::getImg('/imagens/produtos/'.$result['produto_id'].'_1_1.'.$result['produto_extensao1']);
                        $_SESSION[X]['carrinho']['produtos'][$cartKey]['produto_preco'] = Produto::getPreco($result);
                        $_SESSION[X]['carrinho']['produtos'][$cartKey]['produto_qtd'] = isset($_REQUEST['produto_qtd']) ? $_REQUEST['produto_qtd'] : 1;
                        $_SESSION[X]['carrinho']['produtos'][$cartKey]['produto_peso'] = $result['valor2'];
                        $_SESSION[X]['carrinho']['produtos'][$cartKey]['cartKey'] = $cartKey;
                    }
                }


                if($acao=='alterar')
                {

                    if($_REQUEST['produto_qtd'] <= 0)
                    {
                        unset($_SESSION[X]['carrinho']['produtos'][$cartKey]);
                    }
                    else
                    {
                        $_SESSION[X]['carrinho']['produtos'][$cartKey]['produto_qtd'] = $_REQUEST['produto_qtd'];
                    }
                }
            }

            $subTotalCarrinho = 0;
            if(isset($_SESSION[X]['carrinho']['produtos']))
            {
                foreach ($_SESSION[X]['carrinho']['produtos'] as $produto)
                {
                    $subTotalCarrinho+=($produto['produto_qtd'] * $produto['produto_preco']);
                }
            }

            Cart::setDado('subtotal', $subTotalCarrinho);

        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function action($redirect = true)
    {
        try
        {
            if($redirect)
            {
                die(X::alert(false, HTTP.'/carrinho', false));
            }
            Cart::getCarrinho();
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getCartKey()
    {
        try
        {
            if(isset($_REQUEST['cartKey']))
            {
                return $_REQUEST['cartKey'];
            }

            $cartKey = $_REQUEST['produto_id'];

            if(isset($_REQUEST['estoque_grade_linha_primaria']))
            {
                $cartKey.='-'.$_REQUEST['estoque_grade_linha_primaria'];
            }

            if(isset($_REQUEST['estoque_grade_linha_secundaria']))
            {
                $cartKey.='-'.$_REQUEST['estoque_grade_linha_secundaria'];
            }

            return U::setUrlAmigavel($cartKey);
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

            if(isset($_REQUEST['produto_id']) && isset($_REQUEST['estoque_grade_linha_primaria']) && isset($_REQUEST['estoque_grade_linha_secundaria']))
            {
                $dadosProduto = L::getDadosProdutoLoja($_REQUEST['produto_id'], false, $_REQUEST['estoque_grade_linha_primaria'], $_REQUEST['estoque_grade_linha_secundaria']);

                return $dadosProduto[0];
            }


            if(isset($_REQUEST['produto_id']) && isset($_REQUEST['estoque_grade_linha_primaria']))
            {
                $dadosProduto = L::getDadosProdutoLoja($_REQUEST['produto_id'], false, $_REQUEST['estoque_grade_linha_primaria']);

                return $dadosProduto[0];
            }

            if(isset($_REQUEST['produto_id']))
            {
                $dadosProduto = L::getDadosProdutoLoja($_REQUEST['produto_id'], false, $_REQUEST['estoque_grade_linha_primaria'], $_REQUEST['estoque_grade_linha_secundaria']);

                return $dadosProduto[0];
            }

            die(X::alert('Produto não encontrado', HTTP, false));
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function miniLista($topo = true)
    {
        try
        {
            $ret = '';
            $qtd = 0;
            $total;
            $produtos = '';
            foreach ($_SESSION[X]['carrinho']['produtos'] as $produto)
            {
                $qtd+=$produto['produto_qtd'];
                $total+=$produto['produto_qtd']*$produto['produto_preco'];
                $linkProduto = 'produto.php?produto_id='.$produto['produto_id'];

                if($topo)
                {
                    $produtos.='
                    <li class="item even">
                        <a class="product-image" href="'.$linkProduto.'" title="'.$produto['produto_nome'].'"><img alt="'.$produto['produto_nome'].'" src="'.$produto['produto_imagem'].'" width="80"></a>
                        <div class="detail-item">
                          <div class="product-details"> <a href="?acao_carrinho=alterar&produto_qtd=0&cartKey='.$produto['cartKey'].'" title="Remover" class="glyphicon glyphicon-remove">&nbsp;</a> <a class="glyphicon glyphicon-pencil" title="Editar item" href="carrinho.php">&nbsp;</a>
                            <p class="product-name"> <a href="'.$linkProduto.'" title="'.$produto['produto_nome'].'">'.$produto['produto_nome'].'</a> </p>
                          </div>
                          <div class="product-details-bottom"> <span class="price">R$ '.U::moeda($produto['produto_preco']).'</span> <span class="title-desc">Qtd:</span> <strong>'.$produto['produto_qtd'].'</strong> </div>
                        </div>
                    </li>';
                }
                else
                {
                    $produtos.='
                    <li class="item"> <a class="product-image" title="'.$produto['produto_nome'].'" href="'.$linkProduto.'"><img width="80" alt="'.$produto['produto_nome'].'" src="'.$produto['produto_imagem'].'"></a>
                      <div class="product-details">
                        <div class="access"> <a class="btn-remove1" title="Remover Item" href="?acao_carrinho=alterar&produto_qtd=0&cartKey='.$produto['cartKey'].'"> <span class="icon"></span> Remover </a> </div>
                        <p class="product-name"> <a href="'.$linkProduto.'">'.$produto['produto_nome'].'</a> </p>
                        <strong>'.$produto['produto_qtd'].'</strong> x <span class="price">R$ '.U::moeda($produto['produto_preco']).'</span> </div>
                    </li>';
                }
            }

            if($topo)
            {
                $ret='
                <div class="mini-cart">
                    <div data-toggle="dropdown" data-hover="dropdown" class="basket dropdown-toggle"> <a href="#"> <i class="glyphicon glyphicon-shopping-cart"></i>
                      <div class="cart-box"><!-- <span class="title">Carrinho</span> --><span id="cart-total">'.$qtd.' itens </span></div>
                      </a></div>
                    <div>
                      <div class="top-cart-content arrow_box">
                        <ul id="cart-sidebar" class="mini-products-list">
                          '.$produtos.'
                        </ul>
                        <div class="top-subtotal">Subtotal: <span class="price">R$ '.U::moeda($total).'</span></div>
                        <div class="actions">
                          <button class="btn-checkout" onClick="setLocation(\''.HTTP.'/entrega.php\')" type="button"><span>Comprar</span></button>
                          <button class="view-cart" onClick="setLocation(\''.HTTP.'/carrinho.php\')" type="button"><span>Ver Carrinho</span></button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>';
            }
            else
            {
                $ret='
                <div class="block-content">
                  <div class="summary">
                    <p class="amount">Você tem <a href="carrinho.php">'.$qtd.' itens</a> no carrinho.</p>
                    <p class="subtotal"> <span class="label">Subtotal:</span> <span class="price">R$ '.U::moeda($total).'</span> </p>
                  </div>
                  <ul>
                    '.$produtos.'
                  </ul>
                </div>';
            }


            return $ret;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function limpaCarrinho()
    {
        try
        {
            unset($_SESSION[X]['carrinho']['produtos']);
            unset($_SESSION[X]['carrinho']['qtd']);
            Cookie::delCookie('cookiecart');
            self::setSessaoCart();
            return;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function lista()
    {
    	try
    	{
    		if(! Cart::getDado())
            {
                return U::ocultaX('
                        <h1 class="border">OOOOPS, NENHUM PRODUTO EM SEU CARRINHO <span style="color:red">:(</span></h1>
                        <button onclick="location=\''.HTTP.'\'" class="button btn-return" title="Continar Comprando" type="button"><span><span>Continuar Comprando</span></span></button>');
            }


            $lista = '';
            $total = 0;

            foreach ($_SESSION[X]['carrinho']['produtos'] as $produto)
            {


                $subtotal = $produto['produto_qtd'] * $produto['produto_preco'];
                $linkProduto = HTTP.'/produto.php?produto_id='.$produto['produto_id'];

                $total +=  $subtotal;
    			$lista.= '

                <tr>
                    <td>
                        <a href="'.$linkProduto.'" class="tm-cart-productimage">
                            <img src="'.$produto['produto_imagem'].'" alt="'.$produto['produto_nome'].'">
                        </a>
                    </td>
                    <td>
                        <a href="'.$linkProduto.'" class="tm-cart-productname">
                            '.$produto['produto_nome'].'
                            <br />'.$produto['estoque_grade_linha_primaria'].'
                            <br />'.$produto['estoque_grade_linha_secundaria'].'
                        </a>
                    </td>
                    <td class="tm-cart-price">R$ '.U::moeda($produto['produto_preco']).'</td>
                    <td>
                        '.L::getEstoqueQtd($produto).'
                    </td>
                    <td>
                        <span class="tm-cart-totalprice">R$ '.U::moeda($subtotal).'</span>
                    </td>
                    <td>
                        <a class="tm-cart-removeproduct"  href="javascript:createIframe(\'xgetDados\', \''.HTTP.'/xpainel/lib/ajax.php?function=cartAction&acao_carrinho=alterar&produto_qtd=0&cartKey='.$produto['cartKey'].'\')"><i class="fa fa-times"></i></a>
                    </td>
                </tr>
    			';
                //variaveis
                // $produto['produto_nome']
                // $linkProduto.
                // $produto['produto_imagem']
                // $produto['estoque_grade_linha_primaria']
                // $produto['estoque_grade_linha_secundaria']
                // U::moeda($produto['produto_preco'])
                // Cart::getEstoqueQtd($produto)
                // U::moeda($subtotal).'</span> </span></td>
                // ?acao_carrinho=alterar&produto_qtd=0&cartKey='.$produto['cartKey'] botão excluir
            }

            $lista = '
            <div class="tm-cart-table table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th class="tm-cart-col-image" scope="col"></th>
                            <th class="tm-cart-col-productname" scope="col">Produto</th>
                            <th class="tm-cart-col-price" scope="col">Preço</th>
                            <th class="tm-cart-col-quantity" scope="col">Qtd</th>
                            <th class="tm-cart-col-total" scope="col">SubTotal</th>
                            <th class="tm-cart-col-remove" scope="col">Excluir</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$lista.'
                    </tbody>
                </table>
            </div>
            <div class="tm-cart-bottomarea">
                <div class="row">
                    <div class="col-lg-8 col-md-6">
                        <div class="tm-buttongroup">

                        </div>

                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="tm-cart-pricebox">

                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr class="tm-cart-pricebox-total">
                                            <td>SubTotal</td>
                                            <td>R$ '.U::moeda($total).'</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 text-left">
                        <a href="'.HTTP.'/produtos" class="tm-button">Continuar Comprando <b></b></a>
                    </div>
                    <div class="col-lg-6 col-md-6 text-right">
                        <a href="'.HTTP.'/finalizar-compra" class="tm-button">Finalizar Compra <b></b></a>
                    </div>
                </div>
            </div>
            ';


    		return U::clearStr($lista);
    	}
    	catch( Exception $e )
    	{
    		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    	}
    }

	static function setSessaoCart()
    {
        try
        {
            if(! isset($_SESSION[X]['carrinho']))
            {
                $_SESSION[X]['carrinho'] = array();
                $_SESSION[X]['carrinho']['produtos'] = array();
            }
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function checkCarrinho($url = 'carrinho.php')
    {
        try
        {
            if(! Cart::getDado())
            {
                U::goHome($url);
            }
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getProdutosRevisao()
    {
        try
        {
            $subtotal = $total = 0;
            $retorno = '';
            foreach ($_SESSION[X]['carrinho']['produtos'] as $produto)
            {
                $subtotal = $produto['produto_preco']*$produto['produto_qtd'];
                $total+=$subtotal;
                $retorno.='
                <tr class="first odd">
                  <td class="image"><a class="product-image" title="'.$produto['produto_nome'].'" href="'.$linkProduto.'"><img width="75" alt="'.$produto['produto_nome'].'" src="'.$produto['produto_imagem'].'"></a></td>
                  <td><h2 class="product-name"> <a href="#">'.$produto['produto_nome'].'<br />'.$produto['estoque_grade_linha_primaria'].': '.$produto['estoque_grade_linha_secundaria'].'</a> </h2></td>
                  <td class="a-right"><span class="cart-price"> <span class="price">R$ '.U::moeda($produto['produto_preco']).'</span> </span></td>
                  <td class="a-center movewishlist">'.$produto['produto_qtd'].'</td>
                  <td class="a-right movewishlist"><span class="cart-price"> <span class="price">R$ '.U::moeda($subtotal).'</span> </span></td>
                </tr>';

            }

            $destinatario = $_SESSION[X]['sessao_cliente']['clientes_destinatario'] == ''
            ? Cliente::getDado('clientes_nome')
            : $_SESSION[X]['sessao_cliente']['clientes_destinatario'];


            $retorno.='
            <tr class="first last">
                <td colspan="2">

                    <h4>Endereço de entrega:</h4>

                    CEP: '.$_SESSION[X]['sessao_cliente']['clientes_cep'].' <br />
                             '.$_SESSION[X]['sessao_cliente']['clientes_endereco'].', Nº: '.$_SESSION[X]['sessao_cliente']['clientes_numero'].' '.$_SESSION[X]['sessao_cliente']['clientes_complemento'].' - '.$_SESSION[X]['sessao_cliente']['clientes_bairro'].' - '.$_SESSION[X]['sessao_cliente']['clientes_cidade'].'/'.$_SESSION[X]['sessao_cliente']['clientes_estado'].'<br />
                            Destinatário: '.$destinatario.'
                    <br /><br /><a class="tt-btn-type1" href="entrega.php">Alterar Dados de Entrega</a>
                </td>
                <td></td>
                <td colspan="2">
                    <h4>Formade Entrega:</h4> '.Frete::getFrete('nome').' - R$ '.Frete::getFrete('valor').' ('.Frete::getFrete('prazo').')<br />
                    <a class="tt-btn-type1" href="entrega.php">Alterar Forma de Entrega</a>
                    <br /><br />

                    <h4>Forma de Pagamento:</h4> '.$_SESSION[X]['sessao_cliente']['clientes_pagamento'].'
                    <br /><a class="tt-btn-type1" href="entrega.php">Alterar Forma de Pagamento</a>
                </td>
            </tr>';

            $retorno.='
            <tr class="first odd">
              <td colspan="5" class="alinhar_direita_thx ">
              <h3 class="negrito_thx">Total: R$ '.U::moeda($total + Frete::getFrete('valor')).'</h3>
              </td>
            </tr>';

            $retorno='
            <table class="data-table cart-table" id="shopping-cart-table">
                  <thead>
                    <tr class="first last">
                      <th rowspan="1">&nbsp;</th>
                      <th rowspan="1"><span class="nobr">Produto</span></th>
                      <th colspan="1" class="a-center"><span class="nobr">Preço Unitário</span></th>
                      <th class="a-center" rowspan="1">Quantidade</th>
                      <th colspan="1" class="a-center">Subtotal</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr class="first last">
                      <td class="a-right last" colspan="7"><button onclick="location=\'carrinho.php\'" class="button btn-return" title="Revisar Pedido" type="button"><span><span>Revisar Pedido</span></span></button>
                        <button id="" class="button btn-next" title="Finalizar Compra" onclick="location=\'pedido-finalizado.php\'" name="update_cart_action"><span><span>Finalizar Compra</span></span></button></td>
                    </tr>
                  </tfoot>
                  <tbody>
                    '.$retorno.'
                  </tbody>
                </table>';

            return $retorno;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getDado($dado = false)
    {
        try
        {
            if(! isset($_SESSION[X]['carrinho']))
            {
                return;
            }

            if(! $dado)
            {
                if(isset($_SESSION[X]['carrinho']['produtos']))
                {
                    return count($_SESSION[X]['carrinho']['produtos']) > 0;
                }
            }

            if(isset($_SESSION[X]['carrinho'][$dado]))
            {
                return $_SESSION[X]['carrinho'][$dado];
            }

            return false;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function setDado($dado, $valor)
    {
        try
        {
            //echo "<br />Set: {$dado} => {$valor}";
            $_SESSION[X]['carrinho'][$dado] = $valor;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getRevisaPedido()
     {
       try
       {

          L::checkCarrinho();
          L::checkEntrega();
          L::checkLogin();
          if(! isset($_SESSION[X]['sessao_cliente']['clientes_destinatario'][1]));
          {
            $_SESSION[X]['sessao_cliente']['clientes_destinatario'] = Cliente::getDado('clientes_nome');
          }
          $dados = array();
          $dados['entrega'] = "
          <strong>Cep: </strong> {$_SESSION[X]['sessao_cliente']['clientes_cep']}<br />
          <strong>Endereço: </strong> {$_SESSION[X]['sessao_cliente']['clientes_endereco']}<br />
          <strong>Número: </strong> {$_SESSION[X]['sessao_cliente']['clientes_numero']}<br />
          <strong>Complemento: </strong> {$_SESSION[X]['sessao_cliente']['clientes_complemento']}<br />
          <strong>Bairro: </strong> {$_SESSION[X]['sessao_cliente']['clientes_bairro']}<br />
          <strong>Estado: </strong> {$_SESSION[X]['sessao_cliente']['clientes_estado']}<br />
          <strong>Cidade: </strong> {$_SESSION[X]['sessao_cliente']['clientes_cidade']}<br />
          <strong>Destinatário: </strong> {$_SESSION[X]['sessao_cliente']['clientes_destinatario']}<br />";


          $dados['lista'] = '';
          foreach ($_SESSION[X]['carrinho']['produtos'] as $produto)
          {
            $dados['lista'].='
            <tr>
                <td>
                   <div class="b-href-with-img">
                      <a class="c-primary" href="produto.php">
                         <img data-retina="" src="'.$produto['produto_ico'].'" alt="'.$produto['produto_nome'].'">
                         <p>
                            <span class="f-title-small ">'.$produto['produto_nome'].'</span>
                         </p>
                      </a>
                   </div>
                </td>
                <td><span class="f-primary-b  f-title-medium">R$<span class="j-product-price">'.$produto['produto_preco'].'</span></span></td>
                <td class="f-center">
                   <div class="b-product-card__info_count">
                      '.$produto['produto_qtd'].'
                   </div>
                </td>
                <td><span class="f-primary-b  f-title-medium">R$<span>'.U::moeda($produto['produto_qtd'] * $produto['produto_preco']).'</span></span></td>
             </tr>';
          }

          return $dados;
       }
       catch( Exception $e )
       {
         X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
       }
     }
}