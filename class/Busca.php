<?php
class Busca
{
	static function getBusca($minimoCaracteres = 3)
	{
		try
		{
			$ret='';
			if(isset($_GET['busca'][$minimoCaracteres-1]))
			{
				return self::layout('Resultados para sua  busca: <strong style="color: red">'.$_GET['busca'].'<strong>. <a class="btn btn-default" href="'.$_SERVER['PHP_SELF'].'" >Limpar Busca</a>');
			}
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function layout($str, $tipo='success')
	{
		try
		{
			return '
			<h5  class="xclearFix">'.$str.'</h5>';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function hr($str, $contents, $linhas)
	{
		try
		{
			if($contents == '')
			{
				return '<hr /><br />'.$str.'(0)' ;
			}
			return '
			<hr /><h1 class="xclearFix">'.$str.' ('.$linhas.')</h1>'.$contents;
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
        	$ret='';
        	$like = Sql::toLike($_GET['busca']);
        	$sql = "SELECT * FROM produto WHERE produto_deletado=0 AND produto_ativo=1 AND produto_nome LIKE '{$like}' ORDER BY produto_nome";
			$result = Sql::_fetchAll($sql);
			$delay = 1;
			foreach($result as $res)
			{
				$link = 'javascript:orcar('.$res['produto_id'].',\'produto\')';
				$img = U::getImg('/imagens/produtos/'.$res['produto_id'].'_1_1.'.$res['produto_extensao1']);
				$ret.='
				<div class="service-item col-xs-6 col-sm-4 col-md-4 col-lg-4 wow zoomIn" data-wow-delay="0.'.$delay++.'s">
					<img class="full-width" src="'.$img.'"  title="'.$res['produto_nome'].'" alt="'.$res['produto_nome'].'"  />
					<h4>'.$res['produto_nome'].'</h4>
					<p>'.$res['produto_descricao'].'</p>
					<a class="btn btn-success btn-sm"  href="'.$link.'">Solicite Orçamento</a>
				</div>';
				$ret.=U::clearFix(++$loop, 3);
			}

			return self::hr('Produtos', $ret, count($result));
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getServicos()
	{
		try
		{
			$ret = '';
			$like = Sql::toLike($_GET['busca']);
        	$sql = "SELECT * FROM servicos WHERE servico_deletado=0 AND servico_ativo=1 AND servico_titulo LIKE '{$like}' ORDER BY ordem";
			$result = Sql::_fetchall($sql);
			$delay = 1;
			$class="first";
			foreach ($result as $res)
			{
				$img = U::getImg('imagens/servicos/'.$res['servico_id'].'_1_1.'.$res['servico_extensao1']);
				$link = 'javascript:orcar('.$res['servico_id'].',\'servico\')';
				 	$ret.='
				 	<div class="service-item col-xs-6 col-sm-4 col-md-4 col-lg-4 wow zoomIn" data-wow-delay="0.'.$delay++.'s">
						<img class="full-width" src="'.$img.'"   title="'.$res['servico_titulo'].'" alt="'.$res['servico_titulo'].'"  />
						<h4>'.$res['servico_titulo'].'</h4>
						<p>'.$res['servico_texto'].'</p>
						<a class="btn btn-success btn-sm"  href="'.$link.'">Solicite Orçamento</a>
					</div>';

					$ret.=U::clearFix(++$loop, 3);
				$class="";

			}
			return self::hr('Serviços', $ret, count($result));
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}