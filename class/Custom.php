<?php
class Custom
{

   static function getCursos()
	{
		try
		{
         $moduloNumber = 3;
			$sql = Paginacao::getPaginacao("SELECT * FROM moduloadicional{$moduloNumber} WHERE moduloadicional{$moduloNumber}_deletado=0 AND moduloadicional{$moduloNumber}_ativo=1");

			$result = Sql::_fetchAll($sql);

			foreach($result as $key => $res)
			{
				$result[$key]['img'] = U::getImg("imagens/moduloadicional{$moduloNumber}/{$res['moduloadicional'.$moduloNumber.'_id']}_1_1.{$res['imagem_extensao1']}");
				$result[$key]['link'] = HTTP.'/curso/'.$res['moduloadicional3_id'].'/'.U::setUrlAmigavel($res['moduloadicional3_titulo']);
				$active = '';
			}
			return $result;
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
         Cart::limpaCarrinho(true);

         $_GET['id'] = $_POST['curso_id'];
         $dadosCurso = Custom::getCurso();

         $_SESSION['compra_de_curso'] = $dadosCurso;

         $_REQUEST['acao_carrinho'] = 'add';
         $_REQUEST['produto_qtd'] = 1;
         $_REQUEST['estoque_grade_linha_primaria'] = U::formataData($dadosCurso['data']);
         $_REQUEST['estoque_grade_linha_secundaria'] = $dadosCurso['data_amigavel'];
         $_REQUEST['clientes_pagamento'] = $_POST['clientes_pagamento'];



         Cliente::checkLogin();


         $valor = $dadosCurso['valor'];

            if(Cliente::getDado('checkbox0') == 1)
            {
               $_REQUEST['estoque_grade_linha_primaria'] = "Curso :: AssociadoAssociado";
               $valor = $dadosCurso['valor_associado'];
            }

            if(Cliente::getDado('checkbox1') == 1)
            {
               $_REQUEST['estoque_grade_linha_primaria'] = "Curso :: Estudante/Parceiro";
               $valor = $dadosCurso['valor_estudantes'];
            }

            $_REQUEST['produto_preco'] = $valor;

            $sql = "INSERT INTO produto
               (
                  produto_nome,
                  categoria_id,
                  subcategoria_id,
                  valor1,
                  valor2,
                  produto_deletado,
                  produto_ativo,
                  produto_extensao1,
                  curso
               )
               VALUES
               (
                  'Curso: {$dadosCurso['moduloadicional3_titulo']}',
                  2,
                  3,
                  '{$_REQUEST['produto_preco']}',
                  '0.00',
                  0,
                  1,
                  '{$dadosCurso['imagem_extensao1']}',
                  {$dadosCurso['moduloadicional3_id']}
               )";

            $criaProdutoFake = Sql::_query($sql);
            if(! $criaProdutoFake)
            {
               die(X::alert('Houve um erro na compra do curso. <br />TEnte novamente mais tarde'));
            }



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
               '{$_REQUEST['estoque_grade_linha_primaria']}',
               '{$_REQUEST['estoque_grade_linha_secundaria']}',
               1,
               {$criaProdutoFake}
            )";

            $criaGradeFake = Sql::_query($sql);
            $_REQUEST['produto_id'] = $criaProdutoFake;

            Frete::addFrete('digital', 0.00, 'digital', 'Imediato');

            Cart::action($redirectPosAction = false);


            $entrega = array(
               'clientes_endereco' => '',
               'clientes_numero' => '',
               'clientes_bairro' => '',
               'clientes_estado' => '',
               'clientes_cidade' => '',
               'clientes_cep' => '',
               'clientes_complemento' => '',
               'clientes_destinatario' => '',
               'clientes_pagamento' => $_REQUEST['clientes_pagamento'],
               'frete_escolhido' => 'digital');

             $_POST = $entrega;

            $_SESSION[X]['sessao_cliente'] = $entrega;

            unset($_SESSION[X]['fretes']);

            Frete::addFrete('digital', 0.00, 'digital', 'Imediato');

            L::setPedido();

      }
      catch( Exception $e )
      {
         X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
      }
   }



   static function getCurso()
	{
		try
		{
         if(empty($_GET['id']))
         {
            U::goHome(HTTP.'/cursos');
         }

         $moduloNumber = 3;
			$sql = "SELECT * FROM moduloadicional{$moduloNumber} WHERE moduloadicional{$moduloNumber}_deletado=0 AND moduloadicional{$moduloNumber}_ativo=1 AND moduloadicional{$moduloNumber}_id={$_GET['id']}";

			$result = Sql::_fetch($sql);

         if(! $result)
         {
            U::goHome(HTTP.'/cursos');
         }

         $result['img'] = U::getImg("imagens/moduloadicional{$moduloNumber}/{$result['moduloadicional'.$moduloNumber.'_id']}_1_1.{$result['imagem_extensao1']}");

         $result['data'] = U::formataData($result['data']);


			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

   static function getPeritos()
	{
		try
		{
			$condicao = " WHERE moduloadicional2_id > 0 ";
         if(! empty($_GET['filtro_tipo']) && $_GET['filtro_tipo'] == 'area-de-atuacao')
         {
            if(! empty($_GET['filtro_categoria_ou_texto']))
            {
               $condicao.=" AND perito.xserialize_areas_de_atuacao LIKE CONCAT('%', '{$_GET['filtro_categoria_ou_texto']}','%') ";
            }
         }


         if(! empty($_GET['nome']))
         {
            $condicao.=" AND perito.moduloadicional2_titulo LIKE '%{$_GET['nome']}%' ";
         }

			if(! empty($_GET['filtro_uf']))
			{
				$condicao.=" AND perito.estado = '{$_GET['filtro_uf']}' ";
			}


			$sql = "
					SELECT perito.registro,perito.imagem_extensao1,moduloadicional2_id, perito.moduloadicional2_titulo FROM moduloadicional2 perito
						INNER JOIN moduloadicional1 area ON perito.xserialize_areas_de_atuacao LIKE CONCAT('%', area.moduloadicional1_id,'%')
	                  {$condicao}
                        GROUP BY moduloadicional2_id
	                        ORDER BY moduloadicional1_titulo ";

			$sql = Paginacao::getPaginacao($sql);
			$result = Sql::_fetchAll($sql);

			foreach($result as $key => $res)
			{
				$result[$key]['img'] = U::getImg("imagens/moduloadicional2/{$res['moduloadicional2_id']}_1_1.{$res['imagem_extensao1']}");
				$result[$key]['link'] = HTTP.'/perito/'.$res['moduloadicional2_id'].'/'.U::setUrlAmigavel($res['moduloadicional2_titulo']);
            $result[$key]['divlink'] = U::divLink($result[$key]['link']);
				$active = '';
			}
			return $result;
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
			Seguranca::checkCaptcha();

			if(empty($_POST['assunto']))
			{
				return X::alert('Assunto Inválido');
			}

			if(empty($_POST['mensagem']))
			{
				return X::alert('Mensagem Inválida');
			}

			$p = Custom::getPerito();

			$msg = '
		<div class="col-md-6">
			Olá <strong> '.U::firstName($_SESSION['lead']['newsletter_nome']).'</strong>,
			<br />
			Sua mensagem foi enviada para <strong>'.U::firstName($p['moduloadicional2_titulo']).'</strong>
			<br /><br />
			<strong>Seguem os dados de contato dele para contatos futuros:</strong>
			<br /><hr />
			<h2>Nº de Registro: '.$p['registro'].'</h2>
			<ul class="dados-perito-ul">
				<li><h4>Dados de contato</h4></li>
					<li>
							<a target="_blank" href="'.Link::mapa("{$p['rua']}, {$p['numero']} {$p['bairro']} {$p['cidade']} {$p['estado']} {$p['cep']}").'">
							Endereço: '.$p['rua'].', '.$p['numero'].'
							<br />
							'.$p['bairro'].'
							<br />
							'.$p['cidade'].'/'.$p['estado'].
							' '.$p['cep'].'</a>
					</li>
					<li>
						<a target="_blank"  href="'.Link::telefone($p['celular']).'">Telefone: '.$p['celular'].'</a>
					</li>
					<li>
						<a  target="_blank" href="'.Link::email($p['email']).'">E-mail: '.$p['email'].'</a>
					</li>
			</ul>
		</div>
		<div class="col-md-6">
			<ul class="areas-de-atuacao-perito-ul">
				<li><h4>Áreas de Atuação de <strong>'.U::firstName($p['moduloadicional2_titulo']).'</strong>:</h4></li>
				'.$p['areas'].'
			</ul>
		</div>';

      $mensagemParaPerito = '
      Olá <strong>'.U::firstName($p['moduloadicional2_titulo']).'</strong>,
      Seus dados de acesso foram solicitados por <strong> '.U::firstName($_SESSION['lead']['newsletter_nome']).'</strong> em '.APP_NAME.'
      <br />
      Segue a mensagem e os dados de contato de <strong> '.U::firstName($_SESSION['lead']['newsletter_nome']).'</strong>:
      <br /><br />

      Nome: <strong>'.$_SESSION['lead']['newsletter_nome'].'</strong><br />
      E-mail: <strong>'.$_SESSION['lead']['newsletter_email'].'</strong><br />
      Telefone: <strong>'.$_SESSION['lead']['newsletter_telefone'].'</strong><br />
      <br /><br />
      <strong>Assunto:</strong> '.$_POST['assunto'].'
      <br />
      <strong>Mensagem:</strong> '.$_POST['mensagem'].'
      <br />
      ';

			if(

            E::email($_SESSION['lead']['newsletter_email'], $_SESSION['lead']['newsletter_nome'], 'Sua mensagem para '.U::firstName($p['moduloadicional2_titulo']).' foi enviada com sucesso', $msg)

            &&

            E::email('deljdl@gmail.com', U::firstName($p['moduloadicional2_titulo']), 'Nova mensagem '.APP_NAME, $mensagemParaPerito)
         )
			{
				return X::alert('Mensagem enviada com sucesso!', HTTP, false);
			}

			return X::alert('Erro ao enviar sua mensagem<br /> Tente mais tarde!', HTTP, true);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

   static function getPerito()
   {
      try
      {
         if(empty($_REQUEST['id']))
         {
            return U::goHome(HTTP.'/buscar-peritos');
         }

         $ret = '';
         $sql = "SELECT * FROM moduloadicional2 WHERE moduloadicional2_id={$_REQUEST['id']}";
         $result = Sql::_fetch($sql);

         if(! $result)
         {
            return U::goHome(HTTP.'/buscar-peritos');
         }

         $result['img'] = U::getImg("imagens/moduloadicional2/{$result['moduloadicional2_id']}_1_1.{$result['imagem_extensao1']}");
         $result['areas'] = '';
         $in = implode(',', unserialize($result['xserialize_areas_de_atuacao']));
         $sql = "SELECT * FROM moduloadicional1 WHERE moduloadicional1_id IN ({$in}) ORDER BY moduloadicional1_titulo";
         $result2 = Sql::_fetchAll($sql);

         foreach($result2 as $a)
         {
            $result['areas'].= '<li><i class="fa fa-check-square-o"></i> '.$a['moduloadicional1_titulo'].'</li>';
         }



         return U::clearStr($result);
      }
      catch( Exception $e )
      {
         X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
      }
   }


   static function getEstadosPeritos()
   {
      try
      {
         $ret = '';
         $sql = "SELECT DISTINCT(estado) FROM moduloadicional2 ORDER BY estado";
         $result = Sql::_fetchAll($sql);

         foreach($result as $res)
         {
            $img = U::getImg('');
            $link = '';
            $ret.='<option value="'.$res['estado'].'">'.$res['estado'].'</option>';
         }

         if(! empty($_GET['filtro_uf']))
         {
            $ret = str_replace('value="'.$_GET['filtro_uf'].'"', 'value="'.$_GET['filtro_uf'].'" selected ', $ret);
         }

         return U::clearStr($ret);
      }
      catch( Exception $e )
      {
         X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
      }
   }

   static function getCategoriasComPeritos($estado = false)
   {
      try
      {
         $condicao = '';
         if($estado)
         {
            $condicao.=" WHERE estado = '{$estado}'";
         }
         $ret = '';
         $sql = "SELECT * FROM moduloadicional1 a
         INNER JOIN moduloadicional2 p ON p.xserialize_areas_de_atuacao LIKE CONCAT('%', moduloadicional1_id,'%')
         {$condicao}
         GROUP BY moduloadicional1_titulo
         ORDER BY moduloadicional1_titulo";
         $result = Sql::_fetchAll($sql);

         foreach($result as $res)
         {
            $selected = ! empty($_GET['filtro_categoria']) && $_GET['filtro_categoria'] == $res['moduloadicional1_id'] ? 'selected' : '';
            $ret.='<option '.$selected.' value="'.$res['moduloadicional1_id'].'">'.$res['moduloadicional1_titulo'].'</option>';
         }

         return U::clearStr($ret);
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


            // if($_GET['valor'] == 'area-de-atuacao')
            // {
            //    $ufs = "SELECT DISTINCT(estado) FROM moduloadicional2 ORDER BY moduloadicional2";
            //    $ret = [
            //       'teste' => true
            //    ];
            //    die(json_encode($ret));
            // }

            // = 'xpainel/lib/ajax.php?function=

            // filtro_tipo='+filtro_tipo+'
            // filtro_categoria_ou_texto='+filtro_categoria_ou_texto+'
            // filtro_uf='+filtro_uf;



            if($_GET['filtro_tipo'] == 'area-de-atuacao')
            {
                  die
                  (
                     json_encode
                     (
                        [
                           'filtro_select_or_text' => '
                           <select name="filtro_categoria" id="filtro_categoria" class="form-control">
                                 <option value="">Área de Atuação</option>
                                 '.Custom::getCategoriasComPeritos($_GET['filtro_uf']).'
                           </select>',
                        ]
                     )
                  );
            }
            else
            {
               die
               (
                  json_encode
                  (
                     [
                        'filtro_select_or_text' => '
                        <input type="search" name="nome" id="filtro_nome" class="form-control buscatextinput" required placeholder="Digite o nome aqui" >',
                     ]
                  )
               );
            }

     die(json_encode(array('error' => 'Houve um erro, tente mais tarde <pre>'.print_r($_GET,1).'</pre>')));
   }
   catch( Exception $e )
   {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
   }
  }
}
