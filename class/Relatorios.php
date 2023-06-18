<?php
class Relatorios
{
	static function getRelatoriosAll()
	{
		try
		{
			if(! isset($_GET['relatorio']))
			{
				$_GET['relatorio'] = false;
			}

			switch ($_GET['relatorio'])
			{
				case 'social':
					return self::getShareSocial();
				break;

				case 'erros':
					return self::getRelatorioErros();
				break;

				default:
					return self::getAlteracoes();
			}
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getAlteracoes()
	{
		try
		{
			$relatorio='
			<div class="row logoCLIENTE" style="text-align:center">
	            <div class="md-12">
	                <h1>Relatório de Tarefas Realizadas</h1>
	            </div>
	        </div>';

			$result = Sql::_fetchALL('SELECT * FROM relatorio_cliente ORDER BY relatorio_data DESC,relatorio_id DESC');
			if(! $result)
			{
				$relatorio.='
				<div class="row logoCLIENTE" style="text-align:center">
		            <div class="md-12">
		                <h3>Nenhuma alteração Encontrada</h3>
		            </div>
		        </div>';
				return $relatorio;
			}
			$nomeClaturaArquivo = '';
			foreach($result as $relat)
			{
				$arquivos = '';
				for($i = 1; $i <= 3; $i++)
				{
					if(isset ($relat['arquivo_extensao'.$i]))
					{
						$ckarq = '/xpainel/relatorios/arquivos/'.$relat['relatorio_id'].'_'.$i.'.'.$relat['arquivo_extensao'.$i];
						if(file_exists($_SERVER['DOCUMENT_ROOT'].$ckarq))
						{
							$arquivos.= '<a  href="javascript:openWindow(\''.HTTP.$ckarq.'\')"><i class="fa fa-external-link" aria-hidden="true"></i> '.$relat['relatorio_nome_arquivo'.$i].'</a> | ';
						}
					}

					if(isset ($relat['imagem_extensao'.$i]))
					{
						$ckimg = '/xpainel/relatorios/imagens/'.$relat['relatorio_id'].'_'.$i.'_1.'.$relat['imagem_extensao'.$i];
						if(file_exists($_SERVER['DOCUMENT_ROOT'].$ckimg))
						{
							$arquivos.= ' <a  href="javascript:openWindow(\''.HTTP.$ckimg.'\')"><i class="fa fa-external-link" aria-hidden="true"></i> '.$relat['relatorio_nome_imagem'.$i].'</a> | ';
						}
					}
				}

				$arquivos = $arquivos != '' ? '<p><strong>Arquivos</strong> | '.$arquivos.'</p>' : '';


				$relatorio_link = $relat['relatorio_link'] != ''
				? '<a href="'.$relat['relatorio_link'].'" target="_blank" title="Clique aqui para visualizar a tarefa" type="button" class="btn btn-primary linkver">Ver<br />Alteração</a>': "";
				$relatorio.='
				<div class="row relLinhas">
						<div class="col-md-1" style="text-align:center"><h4>'.utf8_encode(strftime('%d <br /> %B <br /> %Y', strtotime($relat['relatorio_data']))).'</h4></div>
						<div class="col-md-10"><strong>Alteração</strong><p>'.$relat['relatorio_tarefa'].'</p>'.$arquivos.'</div>
						<div class="col-md-1">'.$relatorio_link.'</div>
				</div>';
			}

			return $relatorio;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getShareSocial()
	{
		$relatorio='
			<div class="row logoCLIENTE" style="text-align:center">
	            <div class="md-12">
	                <h1>Relatório de Social Share</h1>
	            </div>
	        </div>';
			$arquivos = false;
			$result = Sql::_fetchall('SELECT * FROM rede_social  WHERE social_deletada=0 AND social_ativa=1');
			if(! $result)
			{
				$relatorio.='
				<div class="row logoCLIENTE" style="text-align:center">
		            <div class="md-12">
		                <h3>Nenhuma Rede Social Encontrada </h3>
		                <h1 style="color:#F00;color: #F00;font-size: 90px;">:(</h1>
		            </div>
		        </div>';
				return $relatorio;
			}
			foreach($result as $res)
			{
				$ico = '<i class="'.$res['social_chave'].' fa-3x" title="'.$res['social_titulo'].'"></i>';

				if(file_exists(ROOT.'/imagens/redes/'.$res['social_chave'].'.'.$res['social_extensao']))
				{
					$ico = '<img src="'.HTTP.'/imagens/redes/'.$res['social_chave'].'.'.$res['social_extensao'].'" alt="'.$res['social_titulo'].'" title="'.$res['social_titulo'].'">';
				}


				$relatorio.='
				<div class="row relLinhas" style="cursor:pointer" onClick="openSocial(this, \''.$res['social_url'].'\')">
					<div class="col-md-1">'.$ico.'</div>
					<div class="col-md-11">'.$res['social_titulo'].'</div>
				</div>';
				$arquivos = '';
			}

			return $relatorio;
	}
	static function getRelatorioErros()
	{
		try
		{
			$relatorio='
			<div class="row logoCLIENTE" style="text-align:center">
	            <div class="md-12">
	                <h1>Relatório de Erros</h1>
	            </div>
	        </div>';
			$arquivos = false;
			$result = Sql::_fetchALL('SELECT * FROM relatorio_erros ORDER BY relatorio_erros_data DESC,relatorio_erros_cont DESC');
			if(! $result)
			{
				$relatorio.='
				<div class="row logoCLIENTE" style="text-align:center">
		            <div class="md-12">
		                <h3>Todos os erros já foram corrigidos </h3>
		                <h1 style="color:#F00;color: #F00;font-size: 90px;">:)</h1>
		            </div>
		        </div>';
				return $relatorio;
			}
			foreach($result as $relat)
			{
				$relatorio.='
				<div class="row relLinhas">
					<div class="col-md-1">Último em '.utf8_encode(strftime('<br />%d de %B %Y <br /> %H:%M', strtotime($relat['relatorio_erros_data']))).'</div>
					<div class="col-md-1">'.$relat['relatorio_erros_cod'].'</div>
					<div class="col-md-8">'.$relat['relatorio_erros_erro'].'</div>
					<div class="col-md-1">'.$relat['relatorio_erros_cont'].'</div>
				</div>';
				$arquivos = '';
			}

			return $relatorio;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function setRelatorioErros($errno, $errstr, $errfile, $errline)
	{
		try
		{
			$errstr = addslashes($errstr);
			if(! Sql::_rowCount("SELECT * FROM relatorio_erros WHERE relatorio_erros_arquivo = '{$errfile}' AND relatorio_erros_linha =".$errline))
			{
				echo $sql = "INSERT INTO relatorio_erros (relatorio_erros_cod,relatorio_erros_erro, relatorio_erros_arquivo, relatorio_erros_linha)
    												VALUES ({$errno},'{$errstr}', '{$errfile}', {$errline})";
				Sql::_query($sql);
			}
			$sql = "UPDATE relatorio_erros SET relatorio_erros_cont = relatorio_erros_cont+1, relatorio_erros_data = NOW() WHERE relatorio_erros_arquivo = '{$errfile}' AND relatorio_erros_linha =".$errline;
			Sql::_query($sql);

    		return true;

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}