<?php
class A
{
	static function setRevisarPedido()
	{
		try
		{
			die(L::setRevisarPedido());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}



	static function upFile()
	{
		try
		{
			if(! S::isMaster())
			{
				die(X::alert('Sessão Expirada', HTTP.'/login', false));
			}

			if(! isset($_SESSION[X.X]['clientes_id']))
			{
				die(X::alert('Sessão Expirada', HTTP.'/login', false));
			}

			$path = ROOT.'/arquivosProjetos/'.$_SESSION[X.X]['clientes_id'];

			if(! file_exists($path))
			{
				if(! mkdir($path))
				{
					die(X::alert('Erro ao criar diretório do cliente.', false, true));
				}
			}

			$path = ROOT.'/arquivosProjetos/'.Cliente::getDado('clientes_id').'/'.$_POST['subcategoria'];

			if(! file_exists($path))
			{
				if(! mkdir($path))
				{
					die(X::alert('Erro ao criar diretório do cliente.', false, true));
				}
			}

			$toName = $_FILES['arquivo']['name'];
			$ext = U::getExtensao($toName);
			$pureName = str_replace('.'.$ext, '', $toName);
			$newName = U::setUrlAmigavel($pureName).'.'.$ext;



			if(move_uploaded_file($_FILES['arquivo']['tmp_name'], $path.'/'.$newName))
			{
				//die(X::alert(false, HTTP.'/perfil', false));


				$idLinha = U::getToken(5);
				$newLine.='
				<div id="subX'.$id.'">
					<div class="row cborda" id="'.$idLinha.'">
						<div class="col-md-10">
							<a href="#"><span>'.$newName.'</span></a>
						</div>
						<div class="col-md-2">
							<a title="Apagar Arquivo" target="xgetDados" href="xpainel/lib/ajax.php?function=deleteFile&sub='.$_POST['subcategoria'].'&file='.$newName.'&idLinha='.$idLinha.'"><i class="fas fa-trash" style="color: red"></i></a>
							<a title="Baixar Arquivo" target="xgetDados"  href="xpainel/lib/ajax.php?function=downloadFile&sub='.$_POST['subcategoria'].'&file='.$newName.'&idLinha='.$idLinha.'"><i class="fas fa-download"></i></a>

						</div>
					</div>
				</div>';

				$newLine = U::strToJs($newLine);




				die("<script>parent.addLine({$_POST['subcategoria']}, '{$newLine}')</script>");
			}

			die(X::alert('Erro ao enviar arquivo.', false, true));

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function downloadFile()
	{
		try
		{
			if(! isset($_SESSION[X.X]['clientes_id']))
			{
				die(X::alert('Sessão Expirada', HTTP.'/login', false));
			}


			$file_url = ROOT.'/arquivosProjetos/'.Cliente::getDado('clientes_id').'/'.$_GET['sub'].'/'.$_GET['file'];

			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary");
			header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
			readfile($file_url);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	static function deleteFile()
	{
		try
		{


			if(! S::isMaster())
			{
				die(X::alert('Sessão Expirada', HTTP.'/login', false));
			}


			if(! isset($_SESSION[X.X]['clientes_id']))
			{
				die(X::alert('Sessão Expirada', HTTP.'/login', false));
			}


			$file_url = ROOT.'/arquivosProjetos/'.Cliente::getDado('clientes_id').'/'.$_GET['sub'].'/'.$_GET['file'];

			unlink($file_url);

			die("<script>parent.removeLine('{$_GET['idLinha']}');</script>");
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function setIdioma()
	{
		try
		{
			return Traducao::setIdioma();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function loginById()
	{
		try
		{
			if(! S::isMaster())
			{
				die(X::alert('Sessão Expirada', HTTP.'/login', false));
			}
			return Cliente::setLoginById($_POST['id']);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function modalContents()
	{
		try
		{
			if(! isset($_GET['context']))
			{
				die('No Context - No Thanks');
			}
			die(Layout::ModalX($_GET['context']));
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function alterCadastro()
	{
		try
		{
			die(Cliente::alterCadastro());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function ajaxFotos()
	{
		try
		{
			die(Galeria::getFotos());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function setContato()
	{
		try
		{
			die(X::setContato());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function setLogin()
	{
		try
		{
			die(Cliente::setLogin());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function redefinicaoDeSenha()
	{
		try
		{
			return Cliente::redefinicaoDeSenha();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function checkIsClient()
	{
		try
		{
			die(Cliente::checkIsClient());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function cartActionCurso()
	{
		try
		{
			return Custom::cartActionCurso();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function loginForm()
	{
		try
		{
			die(Cliente::loginForm());
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
			return L::setPedido();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function mensagemPerito()
	{
		try
		{
			die(Custom::mensagemPerito());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function filtraPeritos()
	{
		try
		{
			return Custom::filtraPeritos();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function forgotPassword()
	{
		try
		{
			die(Cliente::forgotPassword());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function setCadastro()
	{
		try
		{
			die(Cliente::setCadastro());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function newsletter()
	{
		try
		{
			return X::newsletter();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getFretes()
	{
		try
		{
			die(Frete::getFretes($_GET['cep']));
		}
		catch( Exception $e )
		{
			X::sendErrors($e->aMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function avisaPagamentoMensalidade()
	{
		try
		{
			if(! Cliente::getDado())
			{
				die('Pagamento iniciado :)');
			}
			$msg= "";
			$msg.=	"<strong>Nome: </strong>: ".Cliente::getDado('clientes_nome')." <br />";
			$msg.=	"<strong>Email: </strong>: ".Cliente::getDado('clientes_email')." <br />";
			$msg.=	"<strong>Id de Usuário: </strong>: ".Cliente::getDado('clientes_id')." <br />";
			$msg.=	"<strong>Observações: </strong>: Olá ".Cliente::getDado('primeiro_nome').", o pagamento de sua mensalidade foi iniciado. <br />Assim que for concluído, seu Cadastro será atualizado.<br />";
			$msg.= '<br /><br /><h2 style="color:red; font-size:18px">Departamento:</h2><hr />';

			$msg.=	"<strong>Departamento: </strong>: ".E::getDado(1, 'dept_email_setor')." <br />";
			$msg.=	"<strong>Aos cuidados de: </strong>: ".E::getDado(1, 'dept_email_nome')." <br />";

			define('XSETOR', 1);

			E::email(Cliente::getDado('clientes_email'),'Loja Estopim','Pagamento de Mensalidade', $msg);



			die('Pagamento iniciado :)');
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function cartAction()
	{
		try
		{
			return Cart::action();
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getEndereco()
	{
		try
		{
			$retorno = (File::file_get_contents('https://api.xpainel.com.br/cep/?json=true&cep='.$_GET['cep']));

			$retorno = json_decode($retorno, true);
			
			$keys = array(
			'endereco' => '',
			'bairro' => '',
			'cidade' => '',
			'estado' => '',
			'logradouro' => '',
			'rua' => '',
			'status' => '');


			$i = 0;

			foreach($keys as $indice => $valor)
			{
				//if(isset($keys[$indice]) && isset($retorno[$indice]))
				{
					$keys[$indice] = $retorno[$i];
				}
				$i++;
			}

			if(isset($_SESSION[X]['sessao_cliente']))
			{
				foreach($_SESSION[X]['sessao_cliente'] as $indice => $valor)
				{
					$keys[$indice] = $valor;
				}
			}
			
			return json_encode($keys, JSON_FORCE_OBJECT);

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
			die(L::getPedido());
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getEnderecoRepublicaVirtual()
	{
		try
		{
			$resultado = (File::file_get_contents('http://republicavirtual.com.br/web_cep.php?cep='.urlencode($_GET['cep']).'&formato=query_string'));

			if(!$resultado)
			{
				return false;
		  	}

			parse_str($resultado, $retorno);

			$_SESSION[X]['sessao_cliente']['cep'] = $_GET['cep'];
			$_SESSION[X]['sessao_cliente']['endereco'] = utf8_encode($retorno['logradouro']);
			$_SESSION[X]['sessao_cliente']['bairro'] = utf8_encode($retorno['bairro']);
			$_SESSION[X]['sessao_cliente']['cidade'] = utf8_encode($retorno['cidade']);
			$_SESSION[X]['sessao_cliente']['estado'] = $retorno['uf'];
			$_SESSION[X]['sessao_cliente']['logradouro'] = utf8_encode($retorno['tipo_logradouro']);
			$_SESSION[X]['sessao_cliente']['rua'] = utf8_encode($retorno['logradouro']);


			foreach($_SESSION[X]['sessao_cliente'] as $indice => $valor)
			{
				$data[$indice] = $valor;
			}

			//$data = array_map('utf8_encode', $data);
			return json_encode($data, JSON_FORCE_OBJECT);

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function setSessionEndereco()
	{
		try
		{
			if($_POST)
			{

				 if($_SESSION[X]['endereco'] = $_POST)
				 {
				 	return 'OK';
				 }
				 return 'NOT';
			}
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getCaracteristicasSecundarias()
	{
		try
		{
			return L::getCaracteristicasSecundarias($_REQUEST['produto_id'], $_REQUEST['estoque_grade_linha_primaria']);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function addClickFaq()
	{
		try
		{
			Sql::_query("UPDATE faq SET faq_cliques = faq_cliques + 1 WHERE faq_id = ".(int)$_GET['faq_id']);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function addAcessoPaginaPouso()
	{
		try
		{
			LandPage::addAcessoPaginaPouso((int)$_GET['id']);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getCaracteristicasPrimarias()
	{
		try
		{
			return L::getCaracteristicasPrimarias($_GET['produto_id']);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	static function getGateways()
    {
        try
        {
            $retorno = '';
            $result = Sql::_fetchall('SELECT * FROM gateway WHERE gateway_ativo = 1');
            foreach ($result as $gtw)
            {
                $checado = '';
                if ((isset($_SESSION[X]['sessao_cliente']['clientes_pagamento']) && $_SESSION[X]['sessao_cliente']['clientes_pagamento']  == $gtw['gateway_parametro']) || count($result) == 1)
                {
                    $checado = 'checked';
                }
                $retorno.='
				<div class="xlabelContainerRadios">
					<input '.$checado.' type="radio" id="gateway'.$gtw['gateway_parametro'].'" name="clientes_pagamento" value="'.trim($gtw['gateway_parametro']).'" required />
					<label for="gateway'.$gtw['gateway_parametro'].'">'.$gtw['gateway_nome'].'</label>
				</div>';
            }
            return $retorno;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
	static function getEstoqueQtd()
	{
		try
		{
			$dadosProduto = L::getDadosProdutoLoja($_REQUEST['produto_id'], false, $_REQUEST['estoque_grade_linha_primaria'], $_REQUEST['estoque_grade_linha_secundaria']);
			return L::getEstoqueQtd($dadosProduto[0]);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getEstoque()
	{
		try
		{

			return L::getEstoqueProduto($_GET['estoque_grade_id']);



		$result = Sql::_fetch(" SELECT * FROM estoque_grade WHERE estoque_grade_produto = ? AND estoque_grade_linha_secundaria = ? AND estoque_grade_estoque > 0", array($_GET['produto'], $_GET['caracteristica'] ));

		if(!$result)
		{
			return 'Produto Não Disponível em Estoque <br /> <a href="contato.php">Me avise quando chegar</a>';
		}


		$qtdDisponivel = $result['estoque_grade_estoque'];


		if(isset($_SESSION['carrinho']['produtos'][$result['estoque_grade_produto'].$result['estoque_grade_linha_secundaria']]))
		{
			$qtdDisponivel = $qtdDisponivel - $_SESSION['carrinho']['produtos'][$result['estoque_grade_produto'].$result['estoque_grade_linha_secundaria']]['produto_qtd'];
		}



		return 'Quantidade
		<div class="quantity buttons_added">
          <div onClick="setQuantidade2(false,'.$qtdDisponivel.')" class="minus"><i class="fa fa-minus"></i></div>
          <input style="float:left;" type="text" readonly id="prod_qtd2" size="4" class="qty text form-control" title="Quantidade" value="1"  step="1">
		  <div onClick="setQuantidade2(true,'.$qtdDisponivel.')" class="plus"><i class="fa fa-plus"></i></div>
		  </div>
		<p>&ensp;&ensp;Disponivel em Estoque: <strong>'.$qtdDisponivel.' Unidades</strong></p>
		<div align="right"><input type="button" class="btn btn-primary btn-lg" id="add-to-cart" value="Comprar" onClick="EnviaCarrinho()"></div>';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getEstoqueDisponivel()
	{
		try
		{
			$valorAtual = 1;
			$result = Sql::_fetch('SELECT * FROM estoque_grade WHERE estoque_grade_id = ?',array($_GET['estoque_grade_id']));
			if(! $result)
			{
				return L::produtoIndisponivel();
			}
			return '<div class="col-sm-6">
                                    <span class="label">Quantidade :</span>
                    </div>
                    <div class="col-sm-2">
                        <div class="cart-quantity">
                            <div class="quant-input">
                                <div class="arrows">
                                  <div class="arrow plus gradient" onClick="setQtd(1,'.$result['estoque_grade_id'].')"><span class="ir"><i class="icon fa fa-sort-asc"></i></span></div>
                                  <div class="arrow minus gradient" onClick="setQtd(0,'.$result['estoque_grade_id'].')"><span class="ir"><i class="icon fa fa-sort-desc"></i></span></div>
                                </div>
                                <input required title="Informe a quantidade" type="text" id="produto_quantidade" name="produto_quantidade" max="5" value="'.$valorAtual.'" readonly>
                          </div>
                        </div>
                    </div>
                                ';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}
