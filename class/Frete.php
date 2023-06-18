<?php
class Frete
{
  static function getTransportadoras()
  {
    try
    {
        unset($_SESSION[X]['fretes']);
        return Sql::_fetchAll("SELECT * FROM transportadora WHERE transportadora_ativa = 1");
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }
  static function getFretes($cep, $produto = false)
  {
    try
    {
        $transportadoras = Frete::getTransportadoras();

        $lista ='';
        foreach($transportadoras as $t)
        {

            $funcao = $t['transportadora_parametro'];
            //echo '<br />'.$funcao;
            //if(function_exists('Frete::'.$funcao))
            {
              $lista.=call_user_func('Frete::'.$funcao, $cep, $t, $produto);
            }
        }

        return self::tabela_frete();
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }

  static function dadosProdutosParaFrete($produto)
  {
    try
    {
      if(! $produto)
      {
        Cart::checkCarrinho();
        $produtos = $_SESSION[X]['carrinho']['produtos'];
        //$produtos['subtotal'] = $_SESSION[X]['carrinho']['subtotal'];
      }
      else
      {
        $produtos[] = array($produto);
        //$produtos['subtotal'] = $produto['valor1'];
      }

      return $produtos;
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }


  static function getServicosTransportadora($t)
  {
    try
    {
        if($t['transportadora_servicos'] == '')
        {
          return false;
        }

        $services = array();

        $servicesAtivos = explode(',', $t['transportadora_servicos']);

        foreach($servicesAtivos as $servico)
        {
          $separaId = explode('#', $servico);
          $services[]=$separaId[0];
        }

        if(count($services) == 0)
        {
           return false;
        }

        return  implode(',', $services);
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }


  static function melhorenvio($cep, $t, $produto)
  {
  	try
  	{

        $services = Frete::getServicosTransportadora($t);

        if(! $services)
        {
          return;
        }


      $produtosCarrinho = Frete::dadosProdutosParaFrete($produto);

  		$url = $t['transportadora_adicional1'];

  		$token = $t['transportadora_token'];

  		$produtos = array();

  		foreach($produtosCarrinho as $linha)
      {
          $produtos[] = array (
              'id' => $linha['cartKey'],
              'width' => 11,
              'height' => 17,
              'length' => 11,
              'weight' => $linha['produto_peso'],
              'insurance_value' => $linha['produto_preco'],
              'quantity' => $linha['produto_qtd'],
            );
      }



        $curl = curl_init();

        $postFields = array (
          'from' =>
          array (
            'postal_code' => $t['transportadora_cep_origem'],
          ),
          'to' =>
          array (
            'postal_code' => $cep,
          ),
          'options' =>
          array (
            'receipt' => false,
            'own_hand' => false,
          ),
          'services' => $services,
          'products' =>	$produtos,
        );

        //X::dd($postFields);

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($postFields),
          CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Content-Type: application/json",
            "Authorization: Bearer ".$token
          ),
		    ));

        $response = curl_exec($curl);
        curl_close($curl);


        $response = json_decode($response);
        //X::dd($response);
        if(isset($response->message) && $response->message == 'Unauthenticated.')
        {
          echo 'Aguardando Homologação Melhor Envio<br />Para Disponibilizar Serviços';
          return;
        }

      foreach($response as $res)
      {
        //X::dd($response);
        if(! isset($res->error))
        {
          Self::addFrete($res->company->name.' '.$res->name, $res->price, $res->company->name.' '.$res->name, $res->delivery_time.' úteis para entrega' );
        }
      }
  	}
  	catch( Exception $e )
  	{
  		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
  	}
  }


  static function porregioes($cep, $t, $produto)
  {
    $_GET['cep'] = $cep;
    A::getEndereco();
    if(DEBUG)
    {
      echo 'in por regioes<br />';
    }
    $produtos = Frete::dadosProdutosParaFrete($produto);

    if( !isset($_SESSION[X]['sessao_cliente']['estado'][1]) || !isset($_SESSION[X]['sessao_cliente']['cidade'][1]))
    {
      //die('<pre>'.print_r($_SESSION[X]['sessao_cliente'],1).'</pre>');
      return self::addFrete('disabled', false, false);
    }





    $sql = "SELECT * FROM cidades c
    INNER JOIN estados e ON e.estado_id=c.estado_id
      WHERE estado_ativo=1 AND cidade_ativa=1
        AND c.cidade_nome = '{$_SESSION[X]['sessao_cliente']['cidade']}' AND e.estado_uf ='{$_SESSION[X]['sessao_cliente']['estado']}'";



    $result = Sql::_fetch($sql);
    if(!$result)
    {
      return self::addFrete('disabled', false, "{$_SESSION[X]['sessao_cliente']['estado']}/{$_SESSION[X]['sessao_cliente']['cidade']}");
    }

    $valorCarrinho = $produtos['subtotal'];


    if($valorCarrinho < $t['transportadora_adicional2'])
    {
      return self::addFrete('disabled', false, 'Valor mínimo para o carrinho: '.$t['transportadora_adicional2']);
    }

    if($valorCarrinho < $result['estado_taxa_minima'])
    {
      return self::addFrete('disabled', false, $_SESSION[X]['sessao_cliente']['estado']);
    }

    if($valorCarrinho < $result['cidade_taxa_minima'])
    {
      return self::addFrete('disabled', false, $_SESSION[X]['sessao_cliente']['cidade']);
    }

    $prazo = $t['transportadora_adicional1'];

    if($result['estado_prazo'] != '')
    {
      $prazo = $result['estado_prazo'];
    }

    if($result['cidade_prazo'] != '')
    {
      $prazo = $result['cidade_prazo'];
    }


    $valorDoFrete = $result['cidade_taxa'] > $result['estado_taxa'] ? $result['cidade_taxa'] : $result['estado_taxa'];

    return self::addFrete('Por Regiões', $valorDoFrete, 'Frete expresso para '.$result['cidade_nome'].'/'.$result['cidade_uf'], $prazo);
  }
  static function correios($cep, $t)
  {

     $cep_destino = eregi_replace("([^0-9])",'',$cep_destino);
     $rotulo = array();
     $servicos = explode(',',$t['transportadora_servicos']);
     foreach($servicos as $servico)
     {
        $s = explode('#', $servico);
        $rotulo[$s[0]] = $s[1];
     }

     $webservice = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx?WSDL';
     $codigo_correios = implode(',',array_keys($rotulo));
     $pesominimo = 0.300;
     $parms = new stdClass;
     $parms->nCdServico = $codigo_correios;//41106,40010,40290,40215 ->  PAC, SEDEX E ESEDEX (TODOS COM CONTRATO) - se vc precisar de mais tipos adicione aqui
     $parms->nCdEmpresa = $t['transportadora_usuario'];// <- LOGIN DO CADASTRO NO CORREIOS (OPCIONAL)
     $parms->sDsSenha = $t['transportadora_pwd'];// <- SENHA DO CADASTRO NO CORREIOS (OPCIONAL)
     $parms->StrRetorno = 'xml';
     // DADOS DINAMICOS
     $parms->sCepDestino = $cep;// CEP CLIENTE
     $parms->sCepOrigem = $t['transportadora_cep_origem'];// CEP DA LOJA (BD)
     $parms->nVlPeso = $_SESSION[X]['carrinho']['peso_total'] >= $pesominimo ? $_SESSION[X]['carrinho']['peso_total'] : $pesominimo;
     // VALORES MINIMOS DO PAC (SE VC PRECISAR ESPECIFICAR OUTROS FAÇA ISSO AQUI)
     $parms->nVlComprimento = '16';
     $parms->nVlDiametro = 0;
     $parms->nVlAltura = 2;
     $parms->nVlLargura = 11;
     // OUTROS OBRIGATORIOS (MESMO VAZIO)
     $parms->nCdFormato = 1;
     $parms->sCdMaoPropria = 'N';
     $parms->nVlValorDeclarado = 0;
     $parms->sCdAvisoRecebimento = 'N';
     // Inicializa o cliente SOAP
     $soap = @new SoapClient($webservice, array(
             'trace' => true,
             'exceptions' => true,
             'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
             'connection_timeout' => 1000
     ));
     // Resgata o valor calculado
     $resposta = $soap->CalcPrecoPrazo($parms);
     $objeto = $resposta->CalcPrecoPrazoResult->Servicos->cServico;
     $array = array();
     $tabela = '';
     $valor = 0;

     foreach($objeto as $obj)
     {

      $tipo = isset($rotulo[$obj->Codigo]) ? strtolower($rotulo[$obj->Codigo]) : '';
      if($tipo != '')
      {

        $retorno[$tipo] = array('tipo'=>$tipo,'valor'=>str_replace(',','.',$obj->Valor),'prazo'=>$obj->PrazoEntrega,'erro'=>$obj->Erro,'msg'=>$obj->MsgErro);

        if($retorno[$tipo]['erro'] == 0)
        {
          self::addFrete($retorno[$tipo]['tipo'], $retorno[$tipo]['valor'], $rotulo[$obj->Codigo], ($retorno[$tipo]['prazo']+$t['transportadora_adicional1']).' dias para entrega.');
        }
      }
    }
      //self::addFrete('retira', 0, 'Retirar no Local', 'Disponível até 30 dias para retirada');
      //self::impressoModico($peso);
  }
  static function checkEntrega($url = 'entrega.php')
  {
      try
      {
          if(! isset($_SESSION[X]['carrinho']['valor_frete']))
          {
              U::goHome($url);
          }
      }
      catch( Exception $e )
      {
          X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
      }
  }
  static function retira($cep, $t)
  {
    try
    {
       return  self::addFrete('retira', U::moeda($t['transportadora_adicional2']), $t['transportadora_adicional1'], $t['transportadora_adicional3']);
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }
  static function personalizada1($cep, $t)
  {
    try
    {
       return  self::addFrete(__FUNCTION__, U::moeda($t['transportadora_adicional2']), $t['transportadora_adicional1'], $t['transportadora_adicional3']);
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }
  static function personalizada2($cep, $t)
  {
    try
    {
       return  self::addFrete(__FUNCTION__, U::moeda($t['transportadora_adicional2']), $t['transportadora_adicional1'], $t['transportadora_adicional3']);
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }

  static function tabela_frete()
  {
    try
    {
        $tabela ='';
        foreach($_SESSION[X]['fretes'] as $tipo => $valor)
        {
          $ck = isset($_SESSION[X]['sessao_cliente']['frete_escolhido']) && ($_SESSION[X]['sessao_cliente']['frete_escolhido'] == $tipo || count($_SESSION[X]['fretes']) == 1)? ' checked ' : '';
          $preco = $valor['valor'] > 0 ? 'R$ '.$valor['valor'] : 'GRÁTIS';
          $tabela.='
          <div class="xlabelContainerRadios">
            <input '.$ck.' type="radio" id="transportadora'.$tipo.'" name="frete_escolhido" value="'.$tipo.'" required />
            <label for="transportadora'.$tipo.'">'.$preco.' - '.$valor['nome'].' ('.$valor['prazo'].')</label>
          </div>';
        }




        return $tabela;
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }
  static function addFrete($tipo, $valor, $nome, $prazo)
  {
    try
    {
       $_SESSION[X]['fretes'][$tipo] = array('valor' => $valor, 'nome' => $nome, 'prazo' => $prazo, 'tipo' => $tipo);
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }
  static function getFrete($valor = false)
  {
    try
    {
      if($valor)
      {
        return  $_SESSION[X]['fretes'][$_SESSION[X]['sessao_cliente']['frete_escolhido']][$valor];
      }
        return  $_SESSION[X]['fretes'][$_SESSION[X]['sessao_cliente']['frete_escolhido']];
    }
    catch( Exception $e )
    {
      X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    }
  }
}
