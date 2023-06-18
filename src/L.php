<?php
class L
{



	static function getEstoqueQtd($produto)
    {
        try
        {


            if(isset($produto['cartKey']))
            {
                return '
                <form '.Form::setAction('cartAction').' class="estoqueX" id="linhaCarrinho'.$produto['cartKey'].'" >
                    <input type="'.TYPE.'" name="acao_carrinho" value="alterar">
                    <input type="'.TYPE.'" name="cartKey" value="'.$produto['cartKey'].'">
                    <div id="produtoQuantidade">
                        <label>
                            <div class="containerSetas">
                                <input type="number" readonly  name="produto_qtd" max="'.$produto['estoque_grade_estoque'].'" id="produto_quantidade'.$produto['cartKey'].'" class="output" value="'.$produto['produto_qtd'].'">
                                <div class="setas">
                                    <span class="fa fa-chevron-up" onclick="setQtd(1, \''.$produto['cartKey'].'\')"></span>
                                    <br />
                                    <span class="fa fa-chevron-down" onclick="setQtd(0, \''.$produto['cartKey'].'\')"></span>
                                </div>
                            </div>
                        </label>
                    </div>
                </form>';
            }


            return '
			<div id="estoqueGradeX">
				<div id="produtoQuantidade">
					<label>Quantidade (Temos '.$produto['estoque_grade_estoque'].'x '.$produto['estoque_grade_linha_primaria'].' '.$produto['estoque_grade_linha_secundaria'].')<br />
						<div class="containerSetas">
							<input type="number" readonly  name="produto_qtd" max="'.$produto['estoque_grade_estoque'].'" id="produto_quantidade" class="output" value="1">
							<div class="setas">
								<span class="fa fa-chevron-up" onclick="setQtd(1)"></span>
								<br />
								<span class="fa fa-chevron-down" onclick="setQtd(0)"></span>
							</div>
						</div>
					</label>
				</div>
				<button type="submit" class="tm-button estoqueXSubmit" >Adicionar ao Carrinho</button>
			</div>
			';
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }



	static function getDadosProdutoLoja(int $produto_id, $groupBy = false, $estoque_grade_linha_primaria = false, $estoque_grade_linha_secundaria = false)
	{
		try
		{
			$condicao = " WHERE p.produto_id={$produto_id} AND p.produto_deletado=0 AND p.produto_ativo=1 ";

			if($estoque_grade_linha_primaria)
			{
				$condicao.=" AND estoque_grade_linha_primaria='{$estoque_grade_linha_primaria}' ";
			}

			if($estoque_grade_linha_secundaria)
			{
				$condicao.=" AND estoque_grade_linha_secundaria='{$estoque_grade_linha_secundaria}' ";
			}

			if($groupBy)
			{
				$groupBy = " GROUP BY {$groupBy}";
			}


			$sql = "SELECT * FROM produto p
						INNER JOIN subcategoria s ON s.subcategoria_id=p.subcategoria_id
							INNER JOIN categoria c ON c.categoria_id=s.categoria_id
								INNER JOIN estoque_grade e ON e.estoque_grade_produto=p.produto_id
									{$condicao}
										{$groupBy}
											ORDER BY estoque_grade_linha_primaria, estoque_grade_linha_primaria";

			$result = Sql::_fetchAll($sql);
			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

    static function getPedidosQtd()
    {
        try
        {
           L::checkLogin();

           $ct = Sql::_fetch("SELECT count(*) as pedidos FROM pedidos WHERE clientes_id=?", array(Cliente::getDado('clientes_id')));
           return $ct['pedidos'];
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
    static function getPedido()
    {
        try
        {
            $result = Sql::_fetchAll("SELECT ped.*,pedi.*,prod.*,

                                        date_format(pedidos_criacao, '%d/%m/%Y %T') AS pedidos_criacao

                                            FROM pedidos ped

                                                INNER JOIN pedido_itens pedi ON ped.pedidos_id=pedi.pedidos_id

                                                    INNER JOIN produto prod ON pedi.produto_id=prod.produto_id

                                                        AND ped.pedidos_id={$_GET['pedido']} AND ped.clientes_id=".Cliente::getDado('clientes_id'));


             $pedido = '
             <h4>Pedido '.$result[0]['pedidos_id'].' - Criado em  '.$result[0]['pedidos_criacao'].'</h4>
             <div class="cart-table table-responsive mb-40"><table>
                                <thead>
                                    <tr>
                                        <th class="pro-thumbnail"></th>
                                        <th class="pro-title">Produto</th>
                                    </tr>
                                </thead>
                                <tbody>';

            foreach ($result as $item)
            {
            	$pedido.= '
                <tr>
                    <td class="pro-thumbnail">
                        <a href="javascript:void(0)">
                            <img style="max-width:120px;" src="'.U::getImg('imagens/produtos/'.$item['produto_id'].'_1_1.'.$item['produto_extensao1']).'" title="'.$item['produto_nome'].'" alt="'.$item['produto_nome'].'" />
                        </a>
                    </td>
                    <td class="pro-title text-left">
                        <a title="'.$item['produto_nome'].'" href="javascript:void(0)">'.$item['produto_nome'].' - <strong>'.$item['pedido_itens_parametro1'].'/'.$item['pedido_itens_parametro2'].'</strong>
                        </a><br />
                        '.intval($item['pedido_itens_quantidade']).' <strong>(x)</strong> <span class="amount">R$ '.U::moeda($item['pedido_itens_valor']).'</span> - Subtotal:  <span class="amount">R$ '.U::moeda($item['pedido_itens_valor']*$item['pedido_itens_quantidade']).'</span><br/>

                    </td>
                </tr>';
          }
          $pedido.= '
            </tbody>
                            </table>
                        </div>';
          $pagar = '';

          $link_pagamento = $item['pedidos_link_pagamento'];
          if($item['pedidos_gateway'] == 'pagseguro')
          {
            $link_pagamento = L::setLightBoxPagSeguro($link_pagamento);
          }

          if($item['pedidos_status'] == 'Aguardando Pagamento' || $item['pedidos_status'] == 'Pedido Realizado')
          {
              $pagar = ' - <a href="'.$link_pagamento.'" style="color: #f00;"><img title="Clique aqui para pagar" alt="Clique aqui para pagar" src="'.HTTP.'/xpainel/gateway/'.$item['pedidos_gateway'].'/'.$item['pedidos_gateway'].'.png" class="img-responsive" /></a>';
          }

         $pedido.='
            <blockquote class="blockquote" style="max-width:100%">
            	<h3>Endereço de Entrega</h3>
                <p>'.$item['pedidos_entrega_endereco'].', N'.$item['pedidos_entrega_numero'].' '.$item['pedidos_entrega_complemento'].' - '.$item['pedidos_entrega_cidade'].'/'.$item['pedidos_entrega_estado'].' - '.$item['pedidos_entrega_cep'].'<br />Destinatário: '.$item['pedidos_entrega_destinatario'].'</p>
                <span>Pedido '.$result[0]['pedidos_id'].' - Criado em  '.$result[0]['pedidos_criacao'].'</span>
            </blockquote>
            <div class="mb-40">
				<h4 class="checkout-title">+ INFORMAÇÕES</h4>
				<div class="checkout-cart-total">

					<ul>
                    	<li><strong>Forma de Pagamento: </strong><span>'.L::getGateway($item['pedidos_gateway'],'gateway_nome').'</span></li>
                    	<li><strong>Status: </strong> <span>'.$item['pedidos_status'].'</span></li>
                    	<li><strong>Frete: </strong> <span>R$ '.$item['pedidos_frete'].' ('.$item['pedidos_forma_entrega'].')</span></li>
                    	<li><strong>Total: </strong> <span>R$ '.$item['pedidos_valor'].'</span></li>
                    </ul>
            		<h4><a href="javascript:getTodosPedidos()" >Ver todos Meus Pedidos</a></h4>
				</div>
			</div>
            <div clas="col-sm-12 text-center" style="text-align: center;">
                '.$pagar.'
            </div>';

            return $pedido;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function styleClass($indice)
    {
        try
        {
            $indice = U::setUrlAmigavel($indice);
            $indices['pedido-realizado'] = 'primary';
            return isset($indices[$indice]) ? $indices[$indice] : 'default '.$indice;


        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getPedidos($listaInteira = false)
    {
        try
        {
           Cliente::checkLogin();

            $listaPedidos = '
            <div class="row">
                <h4 class="titulo_tabela_pedidos_x text-center col-md-1"></h4>
                <h4 class="titulo_tabela_pedidos_x col-md-4 text-center">Data</h4>
                <h4 class="titulo_tabela_pedidos_x text-center col-md-3">Status</h4>
                <h4 class="titulo_tabela_pedidos_x col-md-2 text-center">Valor</h4>
                <h4 class="titulo_tabela_pedidos_x col-md-2 text-center"></h4>
            </div>';
            if($listaInteira)
            {
                $limit = '';
            }
            $result = Sql::_fetchAll("SELECT * FROM pedidos WHERE clientes_id=".Cliente::getDado('clientes_id')." ORDER BY pedidos_id DESC LIMIT 10");



            if(! $result)
            {
                return U::ocultaX('<h4>Nenhum Pedido localizado!</h4>');
            }

            foreach($result as $pedido)
            {
                $acoes = '<a class="dropdown-item" href="javascript:getPedido('.$pedido['pedidos_id'].')">Ver Pedido</a>';
                $link_pagamento = $pedido['pedidos_link_pagamento'];
                if($pedido['pedidos_gateway'] == 'pagseguro')
                {
                  $link_pagamento = L::setLightBoxPagSeguro($link_pagamento);
                }

                if($pedido['pedidos_status'] == 'Aguardando Pagamento' || $pedido['pedidos_status'] == 'Pedido Realizado')
                {
                    $acoes.= '
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="'.$link_pagamento.'">Pagar Pedido</a>';

                }
                $acoes= '
                <div class="col-sm-2 text-center pb-2">
                    <div class="btn-group">
                        <button class="btn btn-sm dropdown-toggle btn-xpaineltheme-dark" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Ações
                        </button>
                        <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 31px, 0px); top: 0px; left: 0px; will-change: transform;">
                            '.$acoes.'
                        </div>
                    </div>
                </div>';

                $listaPedidos.='
                <hr>
                <div class="row">
                    <p class="col-md-1 text-center">
                        <strong>
                            '.$pedido['pedidos_id'].'
                        </strong>
                    </p>
                    <p class="col-md-4 text-center">'.U::formataData($pedido['pedidos_criacao'], '%d/%m/%Y <br /> %H:%M').'</p>
                    <p class="col-md-3 text-center">
                        <span type="button" class="btn btn-sm btn-outline-'.L::styleClass($pedido['pedidos_status']).' btn-sm">'.$pedido['pedidos_status'].'</span>
                    </p>
                    <p class="col-md-2 text-center">R$'.$pedido['pedidos_valor'].'</p>
                    '.$acoes.'


                </div>';
            }

            return $listaPedidos;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function getEstoqueProduto($produto_id)
    {
        try
        {
         	return L::getEstoqueQtd($produto_id);
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function getEstoqueGradeSimples()
    {
        try
        {
           $qtd = '';
           $estoque = 1;
           $sql = "SELECT * FROM estoque_grade WHERE estoque_grade_produto = {$_GET['produto_id']} AND estoque_grade_estoque > 0";
           $result = Sql::_fetchall($sql);
            if(! $result)
            {
                return  L::produtoIndisponivel();
            }

            $caracteristica = $result[0]['estoque_grade_linha_primaria'];
            $caracteristicasPrimarias = '<option value="">Escolha</option>';

            foreach ($result as $res)
            {
                $caracteristicasPrimarias.='<option  value="'.$res['estoque_grade_linha_secundaria'].'">'.$res['estoque_grade_linha_secundaria'].'</option>';
                if(! empty($_GET['caracteristicasPrimarias']) && $_GET['caracteristicasPrimarias'] == $res['estoque_grade_linha_secundaria'])
                {
                    $estoque = $res['estoque_grade_estoque'];

                    /*RETIRAR NO FINAL*/echo $estoque;/*RETIRAR NO FINAL*/
                }
            }



            if(! empty($_GET['caracteristicasPrimarias']))
            {
                $caracteristicasPrimarias = str_replace('value="'.$_GET['caracteristicasPrimarias'].'"', 'selected value="'.$_GET['caracteristicasPrimarias'].'"',$caracteristicasPrimarias);
                $produto = array('cartKey' => '', 'estoque_grade_estoque' => $estoque, 'produto_qtd' => 1);
                $qtd = Cart::getEstoqueQtd($produto);
            }

            return '
                <form method="post" action="carrinho.php">
                    <label>'.$caracteristica.'<br />
                        <select onchange="getEstoque('.$_GET['produto_id'].', this.value)" name="caracteristicasPrimarias">'.$caracteristicasPrimarias.'</select>
                    </label>
                    '.$qtd.'
                    <input type="'.TYPE.'" name="produto_id" value="'.$_GET['produto_id'].'" >
                    <input type="'.TYPE.'" name="acao_carrinho" value="add" >
                </form>
                    ';
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function getEstoqueGradeDupla($produto_id)
    {
        try
        {
           return 'Pra que esse ? '.__LINE__;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	// VERIFICADAS COMEÇO
	static function getDescontoPedido()
    {
      try
      {
        return;
      }
      catch( Exception $e )
      {
        X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
      }
    }

	static function getDadosPedido($token = false)
    {
        try
        {
			if(! isset($_GET['token']))
			{
				if(! $token)
				{
					U::goHome();
				}
			}
			else
			{
				$token = $_GET['token'];
			}

            $result = Sql::_fetch("SELECT * FROM pedidos 
                                    INNER JOIN clientes ON clientes.clientes_id=pedidos.clientes_id
                                        WHERE pedidos.pedidos_token = '{$token}' ");

            if(! $result)
            {
                U::goHome();
            }
            return $result;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function rollbackPedido($pedidoIdOrToken)
    {
        try
        {
            return X::alert('Hiuve um erro em seu pedido e ele foi cancelado. Tente novamente mais tarde');
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function msgCompra($gateway, $pedido_id, $link_pagamento)
    {
        try
        {
            $link_pagamento = '
            <a  title="Clique para pagar Agora"   href="'.$link_pagamento.'">
                <img class="img-fluid" src="'.APP_URL.'/xpainel/gateway/'.$gateway.'/'.$gateway.'.png" title="Clique aqui para pagar" alt="Clique aqui para pagar Agora" />
            </a>';

            echo Js::xSetHtml('
			<div class="msgCompra">
            	<div>
                    '.$link_pagamento.'
                    <br /><br />
                </div>
                <div>
                    <h3>Parabéns pela compra! :)
					<br /><br />
					Seu pedido será processado após confirmação do pagamento!</h3>
                    <br />
                    <h5>Número do pedido: <strong>'.$pedido_id.'</strong></h5>
                    <br /><br />
                </div>
			</div>', '#formCompraX');
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function getPedidoEmail($pedido,$msg,$cliente)
    {
        try
        {
            $result = Sql::_fetchall("SELECT ped.*,pedi.*,prod.*,

            date_format(pedidos_criacao, '%d/%m/%Y %T') AS pedidos_criacao

            FROM pedidos ped

            INNER JOIN pedido_itens pedi ON ped.pedidos_id=pedi.pedidos_id

            INNER JOIN produto prod ON pedi.produto_id=prod.produto_id

            AND ped.pedidos_id={$pedido} AND ped.clientes_id={$cliente}");



            if(! $result)
            {
                return 'Erro ao capturar produtos do pedido';
            }

            $pedido ='



                    <table  style="float:left; border-collapse:collapse; border:solid #CCC 1px; width:100%;" cellpadding="5" cellspacing="5" class="table">';


            foreach ($result as $item)
            {

                $pedido.='

                <tr>

                <th style="border:solid #EEE 1px; width: 90px;">
                <img style="max-height:75px; max-width: 60px;" src="'.U::getImg('/imagens/produtos/'.$item['produto_id'].'_1_1.'.$item['produto_extensao1']).'" />

                </th>

                <th style="border:solid #EEE 1px; text-align: left;">'.$item['produto_nome'].'<br />'.$item['pedido_itens_parametro1'].'<br />'.$item['pedido_itens_parametro2'].'</th>

                <th style="border:solid #EEE 1px;">R$ '.$item['pedido_itens_valor'].'</th>

                </tr>

                ';

            }

                    $pedido.='

                    <tr>
                        <th style="border:solid #EEE 1px;"></th>

                        <th style="border:solid #EEE 1px; text-align:right">Frete: </th>

                        <th colspan="2" style="border:solid #EEE 1px;white-space: nowrap;">R$ '.$result[0]['pedidos_frete'].'</th>

                    </tr>

                    <tr>
                        <th style="border:solid #EEE 1px;"></th>

                        <th style="border:solid #EEE 1px; text-align:right">Total: </th>

                        <th style="border:solid #EEE 1px;white-space: nowrap;">R$ '.$result[0]['pedidos_valor'].'</th>

                    </tr>';

                $pedido.='</table>';

            return $pedido;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function getRetornaProdutoEstoque($pedidoToken)
    {
        try
        {
           $result = Sql::_fetchAll("SELECT * FROM pedidos ped
                                        INNER JOIN pedido_itens pedi ON ped.pedidos_id=pedi.pedidos_id
                                            INNER JOIN produto prod ON pedi.produto_id=prod.produto_id
                                                WHERE pedidos_token='$pedidoToken'");

            foreach($result as $res)
            {
                $checkExists = Sql::_fetch("SELECT estoque_grade_id FROM estoque_grade
                    WHERE estoque_grade_linha_primaria='{$res['pedido_itens_parametro1']}'
                        AND estoque_grade_linha_secundaria='{$res['pedido_itens_parametro2']}'
                            AND estoque_grade_produto={$res['produto_id']}");

                if($checkExists)
                {
                    $sql = "UPDATE estoque_grade SET estoque_grade_atualizada=NOW(),
                                estoque_grade_estoque=estoque_grade_estoque+{$res['pedido_itens_quantidade']}
                                    WHERE estoque_grade_linha_primaria='{$res['pedido_itens_parametro1']}'
                                        AND estoque_grade_linha_secundaria='{$res['pedido_itens_parametro2']}'
                                            AND estoque_grade_produto={$res['produto_id']}";
                }
                else
                {
                    $sql = "INSERT INTO estoque_grade
                        (
                            estoque_grade_atualizada,
                            estoque_grade_linha_primaria,
                            estoque_grade_linha_secundaria,
                            estoque_grade_estoque,
                            estoque_grade_produto
                        )
                        VALUES
                        (
                            NOW(),
                            '{$res['pedido_itens_parametro1']}',
                            '{$res['pedido_itens_parametro2']}',
                            {$res['pedido_itens_quantidade']},
                            {$res['produto_id']}
                        )";
                }

                Sql::_query($sql);
            }

            return;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function sendStatusOrder($token)
    {
        try
        {
            if(isset($_REQUEST['status']) && $_REQUEST['status'] == 'Cancelado')
            {
                L::getRetornaProdutoEstoque($_REQUEST['token']);
            }
            $produtos='';
            $result = Sql::_fetch("SELECT p.*, c.* FROM pedidos p INNER JOIN clientes c ON c.clientes_id=p.clientes_id WHERE pedidos_token='{$token}'");

            if(! $result)
            {
                return false;
            }


            $forma_de_pagamento = L::getGateway($result['pedidos_gateway']);
            $mensagem = '
            <div style="width:100%;float:left;padding:10px; text-align:center;">
                <h1 style="letter-spacing: -2px;margin: 0;font-size: 28px;text-align: center;">Olá <strong>'.U::firstName($result['clientes_nome']).'</strong></h1>

                <p style="letter-spacing: -2px;margin: 0;font-size: 28px;text-align: center;">Seu pedido número <strong>'.$result['pedidos_id'].'</strong> teve o status alterado.</p>
            </div>
            <div style="width:100%;float:left;">
                <div style="width:45%; float:left; padding:10px;line-height: 30px; text-align:center;">

                    <h3 style="font-size: 25px;letter-spacing: -1.5px;margin: 0;">Status de seu pedido</h3>
                     <p style="margin: 0;font-size: 21px;font-weight: 700;">'.$result['pedidos_status'].'</p>
                </div>
                <div style="width:45%; float:right; padding:10px;line-height: 30px; text-align:center;">

                   <h3 style="font-size: 25px;letter-spacing: -1.5px;margin: 0;"> Forma de Pagamento</h3>
                   <p style="margin: 0;font-size: 21px;font-weight: 700;">'.$forma_de_pagamento['gateway_nome'].'</p>
                </div>

            </div>


            <hr />';

            if($result['pedidos_status'] == 'Enviado' && $result['pedidos_rastreamento'] != '')

            $mensagem.= '<h3 style="font-size:25px;letter-spacing:-1.5px;margin:0;text-align: center;">Link de Rastreamento</h3>
            <p style="text-align: center"><a href="'.$result['pedidos_rastreamento'].'">Clique aqui para rastrear seu pedido.</a>
                <br />
                Caso seu programa de email bloqueie o link acima, copie o endereço abaixo<br />
                <small>'.$result['pedidos_rastreamento'].'</small>
            </p>
            <hr />';



            $link = $result['pedidos_link_pagamento'];

            if($result['pedidos_status'] == 'Aguardando Pagamento' || $result['pedidos_status'] == 'Pedido Realizado')
            {
                $mensagem.=
                '<div style="text-align: center">
                    '.$result['pedidos_html'].'
                    <hr />
                    <a href="'.$link.'">
                        <img style="max-width: 80%;margin: 10px auto;" src="'.APP_URL.'/xpainel/gateway/'.$result['pedidos_gateway'].'/'.$result['pedidos_gateway'].'.png" style="margion: 0 auto;" title="Clique aqui para pagar" alt="Clique aqui para pagar Agora" />
                    </a>
                </div>';
            }

            if($result['pedidos_entrega_endereco'] != '')
            {
                $mensagem.='
                <hr />
					<h3 style="font-size:25px;letter-spacing:-1.5px;margin:0;text-align: center;">Dados da Entrega</h3>
                    <p style="text-align: center;font-size: 21px;">
                    '.$result['pedidos_entrega_endereco'].', Nº: '.$result['pedidos_entrega_numero'].' '.$result['pedidos_entrega_complemento'].' - '.$result['pedidos_entrega_bairro'].' - '.$result['pedidos_entrega_cidade'].' / '.$result['pedidos_entrega_estado'].' - '.$result['pedidos_entrega_cep'].'<br />

                    Destinatário: '.$result['pedidos_entrega_destinatario'].' <br />

                    </p>';
            }

            $mensagem.= '

                    <div style="width:100%;float:left;">
		                <div style="width:100%; float:left; padding:10px;line-height: 30px; text-align:center;">

		                    <h3 style="font-size: 25px;letter-spacing: -1.5px;margin: 0;">Forma de Envio</h3>
		                     <p style="margin: 0;font-size: 21px;font-weight: 700;">'.$result['pedidos_forma_entrega'].'</p>
		                </div>
		            </div>
                    <hr />';
            $mensagem.=L::getPedidoEmail($result['pedidos_id'],$mensagem,$result['clientes_id']);

            return E::email($result['clientes_email'],$result['clientes_nome'],'Atualização do Pedido #ID - '.$result['pedidos_id'],$mensagem);

        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function setLinkPagamento($pedido_id, $url)
    {
        try
        {
			L::pedidoSetDado('pedidos_link_pagamento', $url, $pedido_id);
            return $url;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function pedidoSetDado($coluna, $valor, $pedido_id)
    {
        try
        {
            return  Sql::_query("UPDATE pedidos SET {$coluna} = '{$valor}' WHERE pedidos_id = '{$pedido_id}' ");
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function getGateway($gateway,$campo = '*')
    {
        try
        {
            $result = Sql::_fetch("SELECT {$campo} FROM gateway WHERE gateway_parametro = '{$gateway}'");
            if($campo != '*')
            {
                return $result[$campo];
            }
            return $result;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function setEstoque()
    {
        try
        {
            foreach ($_SESSION[X]['carrinho']['produtos'] as $retirar)
            {
                Sql::_query("UPDATE estoque_grade SET estoque_grade_estoque = estoque_grade_estoque - {$retirar['produto_qtd']} WHERE estoque_grade_id = {$retirar['estoque_grade_id']}");
            }
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function setValidarPedido()
    {
        try
        {
			Cart::checkCarrinho();
            Cliente::checkLogin();

            $obg = array(
              'clientes_endereco' => 'Informe o endereço para entrega.',
              'clientes_numero' => 'Informe o número para a entrega',
              'clientes_bairro' => 'Informe o bairro para entrega',
              'clientes_estado' => 'Informe o estado para entrega',
              'clientes_cidade' => 'Informe a cidade para entrega',
              'clientes_pagamento' => 'Escolha uma forma de pagamento',
              'frete_escolhido' => 'Escolha uma forma de entrega');

            foreach($obg as $name => $erro)
            {
              if(!isset($_POST[$name][0]))
              {
                echo "<script>parent.document.getElementsByName('{$name}')[0].focus();</script>";
                return X::alert($erro.$_POST[$name], false, true);
              }
            }

            $_SESSION[X]['sessao_cliente'] = $_POST;

            Cart::setDado('valor_frete', Frete::getFrete('valor'));
            Cart::setDado('total', Frete::getFrete('valor')+Cart::getDado('subtotal'));

            return true;

        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function setLightBoxPagSeguro($url)
    {
        try
        {
            $code = explode('code=', $url);
            $code = end($code);
            //return 'https://pagseguro.uol.com.br/v2/checkout/payment.html?code='.$code;
            return "javascript:setPagSeguro('{$code}')";

        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

	static function setPedido()
    {
        try
        {
			L::setValidarPedido();
           // self::setEstoque();

            //Frete::checkEntrega();

            $produtos='';
            $token= U::getToken(30);
            $pedidos_valor = U::moeda(Cart::getDado('total'));
            $destinatarioOpt = $_SESSION[X]['sessao_cliente']['clientes_destinatario'] == '' ? $_SESSION[X.X]['clientes_nome'] : $_SESSION[X]['sessao_cliente']['clientes_destinatario'];

            $result = Sql::_query("INSERT INTO pedidos (
            pedidos_status,
            pedidos_criacao,
            clientes_id,
            pedidos_valor,
            pedidos_gateway,
            pedidos_html,
            pedidos_token,
            pedidos_frete,
            pedidos_entrega_cep,
            pedidos_entrega_numero,
            pedidos_entrega_complemento,
            pedidos_entrega_endereco,
            pedidos_entrega_cidade,
            pedidos_entrega_estado,
            pedidos_entrega_bairro,
            pedidos_forma_entrega,
            pedidos_entrega_destinatario
            )
            VALUES (
            'Pedido Realizado',
            NOW(),
            {$_SESSION[X.X]['clientes_id']},
            '{$pedidos_valor}',
            '".$_SESSION[X]['sessao_cliente']['clientes_pagamento']."',
            '',
            '{$token}',
            '".Frete::getFrete('valor')."',
            '{$_SESSION[X]['sessao_cliente']['clientes_cep']}',
            '{$_SESSION[X]['sessao_cliente']['clientes_numero']}',
            '{$_SESSION[X]['sessao_cliente']['clientes_complemento']}',
            '{$_SESSION[X]['sessao_cliente']['clientes_endereco']}',
            '{$_SESSION[X]['sessao_cliente']['clientes_cidade']}',
            '{$_SESSION[X]['sessao_cliente']['clientes_estado']}',
            '{$_SESSION[X]['sessao_cliente']['clientes_bairro']}',
            '".Frete::getFrete('nome')."',
            '{$destinatarioOpt}'
            )");
            return self::setPedidoItens($result,$token);
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function setPedidoItens($pedidoId,$token)
    {

        try
        {

            $produtos='';
            $array_produtos='';
            foreach($_SESSION[X]['carrinho']['produtos'] as $linha)
            {
                $subtotal = U::moeda($linha['produto_qtd']*$linha['produto_preco']);
                $produtos.="
                ({$pedidoId},
                '{$linha['produto_qtd']}',
                '{$linha['produto_preco']}',
                '{$subtotal}',
                '{$linha['produto_id']}',
                '{$linha['estoque_grade_linha_primaria']}',
                '{$linha['estoque_grade_linha_secundaria']}'),";
                $array_produtos.=$linha['produto_id'].', ';
            }

            $array_produtos = trim($array_produtos,',');
            $produtos=substr($produtos,0,-1);

            Sql::_query("INSERT INTO pedido_itens(pedidos_id,pedido_itens_quantidade, pedido_itens_valor, pedido_itens_total,produto_id,pedido_itens_parametro1,pedido_itens_parametro2) VALUES {$produtos}");



            self::setEstoque();

            return self::enviaGateway($pedidoId,$token);
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }

    }

	static function enviaGateway($pedido_id,$token)
    {
        $funcao = ucfirst($_SESSION[X]['sessao_cliente']['clientes_pagamento']);
        $script = ROOT.'/xpainel/gateway/'.$_SESSION[X]['sessao_cliente']['clientes_pagamento'].'/setPedido'.$funcao.'.php';

        if(file_exists($script))
        {
            //die($script);
            require_once($script);
            call_user_func('setOrder'.$funcao,$pedido_id,$token);
        }
        else
        {
            return X::alert('<br /><br /><br /><br /><br />

                    <div id="agradecimento">

                        <p style="text-align: center;font-size: 20px;  font-weight: bold;">

                            <span>

                                Ooops :(

                            </span>

                            <br />

                            Encontramos um erro no processamento do seu pedido.

                            <br/>

                            <br/>

                            <a title="Retornar para o site" href="'.HTTP.'">Retornar ao Site</a>

                        </p>

                    </div>');
        }

    }

	static function getCaracteristicasPrimarias($produto_id, $tagSelect = true)
	{
		try
		{
			$produtoLoja = L::getDadosProdutoLoja($produto_id, 'estoque_grade_linha_primaria');


			$grade = '';
			$default = false;


			if(! $produtoLoja)
			{
				return L::produtoIndisponivel();
			}

			foreach($produtoLoja as $linha)
			{
					$selected = '';
					if(! $default)
					{
						$default = $linha;
						$selected = ' selected ';
					}
					$grade.='<option '.$selected.' value="'.$linha['estoque_grade_linha_primaria'].'">'.$linha['estoque_grade_linha_primaria'].'</option>';
			}

			if($tagSelect)
			{
				return '
				<form '.Form::setAction('cartAction').' class="estoqueX" >
					<input type="'.TYPE.'" name="acao_carrinho" value="add">
					<input type="'.TYPE.'" name="produto_id" value="'.$produto_id.'">
					<div id="caracteristicaPrimaria">
						<select name="estoque_grade_linha_primaria" onChange="getCaracteristicasSecundarias('.$produto_id.', this.value)" required >
							'.$grade.'
						</select>
					</div>
					'.L::getCaracteristicasSecundarias($produto_id, $default['estoque_grade_linha_primaria']).'
				</form>
				';
			}

			return 'BBBBBB'.$grade;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getCaracteristicasSecundarias($produto_id, $estoque_grade_linha_primaria)
	{
		try
		{
			$caracteristicasSecundarias = L::getDadosProdutoLoja($produto_id, false, $estoque_grade_linha_primaria);

			$grade='';

			$checked = ' checked';

			$default = false;

			foreach($caracteristicasSecundarias as $linha)
			{
				if(! $default)
				{
					$default = $linha;
				}

				$grade.='
				<div class="getCaracteristicasSecundariasContainerRadios">
					<input type="radio" '.$checked.' onChange="getEstoqueQtd('.$produto_id.', \''.$linha['estoque_grade_linha_primaria'].'\', this.value)" id="getCaracteristicasSecundarias'.$linha['estoque_grade_id'].'" name="estoque_grade_linha_secundaria" value="'.$linha['estoque_grade_linha_secundaria'].'" required >
					<label for="getCaracteristicasSecundarias'.$linha['estoque_grade_id'].'" class="labelCaracteristicaSecundaria">'.$linha['estoque_grade_linha_secundaria'].'</label>
				</div>';

				$checked = ' ';
			}

			return '
			<div id="caracteristicaSecundaria">
				<div class="estoqueXradios">
					<small>Escolha uma das opções</small><br />
						'.$grade.'
				</div>
			</div>
			'.L::getEstoqueQtd($default);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	// VERIFICADAS FIM

	static function produtoIndisponivel()
	{
		try
		{
			return 'produtoIndisponivel';
			$ret = '';
			$sql = "";
			$result = Sql::_fetchAll($sql);

			foreach($result as $res)
			{
				$img = U::getImg('');
				$link = '';
				$ret.='';
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}
