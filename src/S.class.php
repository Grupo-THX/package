<?php
class S
{
	static function isMaster()
	{
		try
		{
			return isset($_SESSION['adm']);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getClientesToLogin()
	{
		try
		{
			if(! S::isMaster())
			{
				return;
			}
			$ret = '';
			$sql = "SELECT * FROM clientes WHERE clientes_deletado=0";
			$result = Sql::_fetchAll($sql);

			foreach($result as $res)
			{
				$ret.='<option value="'.$res['clientes_id'].'">'.$res['clientes_nome'].'</option>';
			}

			if(isset($_SESSION[X.X]['clientes_id']))
			{
				$ret = str_replace('value="'.$_SESSION[X.X]['clientes_id'].'"', 'value="'.$_SESSION[X.X]['clientes_id'].'" selected', $ret);
			}

			$ret='
			<form '.Form::setAction('loginById').'>
				<select name="id" onChange="if(this.value != \'\'){ loadingX(); this.form.submit();}" class="contact-input placeholder1"><option value="">Escolha um Cliente</option>'.$ret.'</select>
			</form>';

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getArquivos()
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM subcategoria WHERE subcategoria_deletada=0 ORDER BY subcategoria_ordem";
			$result = Sql::_fetchAll($sql);


			foreach($result as $res)
			{
				$ret.='';

				$ret.=self::getArquivosDownload($res);


				if(S::isMaster())
				{
					$ret.= '
					<form '.Form::setAction('upFile').'>
						<input type="hidden" name="subcategoria" value="'.$res['subcategoria_id'].'" />
						<div class="row cborda">
							<div class="col-md-10">
								<input type="file" name="arquivo" required />
							</div>
							<div class="col-md-2">
								<button type="submit" class="btup"><i class="fas fa-upload"></i></button>
							</div>
						</div>
					</form>';
				}


				$ret.='';
			}



			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getArquivosDownload($res)
	{
		try
		{
			$ret = '';
			$path = ROOT.'/arquivosProjetos/'.Cliente::getDado('clientes_id').'/'.$res['subcategoria_id'].'/';
			foreach (glob($path."*") as $arquivo)
			{
				$arquivoName = str_replace($path,'',$arquivo);
				$idLinha = U::getToken(5);

				$apagar = '';

				if(S::isMaster())
				{
					$apagar = '<a title="Apagar Arquivo" target="xgetDados" href="xpainel/lib/ajax.php?function=deleteFile&sub='.$res['subcategoria_id'].'&file='.$arquivoName.'&idLinha='.$idLinha.'"><i class="fas fa-trash" style="color: red"></i></a>';
				}
				$ret.='

					<div class="row cborda" id="'.$idLinha.'">
						<div class="col-md-10">
							<a href="#"><span>'.$arquivoName.'</span></a>
						</div>
						<div class="col-md-2">
							'.$apagar.'
							<a title="Baixar Arquivo" target="xgetDados"  href="xpainel/lib/ajax.php?function=downloadFile&sub='.$res['subcategoria_id'].'&file='.$arquivoName.'&idLinha='.$idLinha.'"><i class="fas fa-download"></i></a>

						</div>
					</div>
				';
			}
			if($ret != '' || S::isMaster())
			{
				$ret='<hr /><br /><strong>'.$res['subcategoria_nome'].'</strong><br />'.$ret;
			}
			$ret='<div id="subX'.$res['subcategoria_id'].'">'.$ret.'</div>';
			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}

