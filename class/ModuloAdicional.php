<?php
class ModuloAdicional
{
	static function get($moduloNumber)
	{
		try
		{
			$sql = Paginacao::getPaginacao("SELECT * FROM moduloadicional{$moduloNumber} WHERE moduloadicional{$moduloNumber}_deletado=0 AND moduloadicional{$moduloNumber}_ativo=1");
			$result = Sql::_fetchAll($sql);

			foreach($result as $key => $res)
			{
				$result[$key]['img'] = U::getImg("imagens/moduloadicional{$moduloNumber}/{$res['moduloadicional'.$moduloNumber.'_id']}_1_1.{$res['imagem_extensao1']}");
				$result[$key]['link'] = '';
				$active = '';
			}
			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function tabelaHall()
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM moduloadicional1 WHERE moduloadicional1_deletado=0 AND moduloadicional1_ativo = 1 ";
			$result = Sql::_fetchAll($sql);
			foreach($result as $res)
			{
				$ret.='
				<div class="cell" id="'.$res['moduloadicional1_titulo1'].'">
					<div class="cell-inner"><a class="content cell-1" style="background-color: '.$res['moduloadicional1_titulo1'].';"><span
								style="color:'.$res['moduloadicional1_titulo2'].'">'.$res['moduloadicional1_titulo'].'</span></a></div>
				</div>';
			}
			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getModuloAdicional2($cortexto = '')
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM moduloadicional2 WHERE moduloadicional2_deletado=0 AND moduloadicional2_ativo = 1 AND checkbox0=1";
			$result = Sql::_fetchAll($sql);
			foreach($result as $res)
			{
				$img = U::getImg('imagens/moduloadicional2/'.$res['moduloadicional2_id'].'_1_1.'.$res['imagem_extensao1']);
				$link = '';
				$ret.='
				<div class="vantagens_div1">
		          <div class="vantagens_div2 w-clearfix"><img src="'.$img.'" class="vantagens_icone">
		            <div class="vantagens_titulo">'.$res['moduloadicional2_titulo'].'</div>
		          </div>
		          <div class="vantagens_texto" style="color:'.$cortexto.'">'.$res['moduloadicional2_texto'].'</div>
		        </div>';
			}
			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getModuloAdicional2Vantagens()
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM moduloadicional2 WHERE moduloadicional2_deletado=0 AND moduloadicional2_ativo = 1 AND checkbox0=0 AND checkbox1=0";
			$result = Sql::_fetchAll($sql);
			$loops = 0;
			foreach($result as $res)
			{
				$img = U::getImg('imagens/moduloadicional2/'.$res['moduloadicional2_id'].'_2_1.'.$res['imagem_extensao2']);
				$link = '';
				$ret.='
				<div class="vantagens_column w-col w-col-4">
		          <div class="vantagens_div4">
		            <div class="vantagens_div2 w-clearfix"><img src="'.$img.'" width="40" height="40" class="vantagens_icone">
		              <div class="vantagens_titulo white">'.$res['moduloadicional2_titulo'].'</div>
		            </div>
		            <div class="vantagens_texto white">'.$res['moduloadicional2_texto'].'</div>
		          </div>
		        </div>';
		        $loops++;
		        if($loops%3==0)
		        {
		        	$ret.='<div class="col-md-12 w-clearfix"></div>';
		        }
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function getModuloAdicional2Destaques()
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM moduloadicional2 WHERE moduloadicional2_deletado=0 AND moduloadicional2_ativo = 1 AND checkbox1=1";
			$result = Sql::_fetchAll($sql);
			$reverse = 'reverse';
			foreach($result as $res)
			{
				$img = U::getImg('imagens/moduloadicional2/'.$res['moduloadicional2_id'].'_3_1.'.$res['imagem_extensao3']);
				$link = '';
				$reverse = $reverse == 'reverse' ? '' : 'reverse';
				$ret.='
				<div class="conteudo '.$reverse.' w-clearfix">
					<div class="conteudo_imagem vantagens1" style="background-image: url('.$img.');"></div>
					<div class="conteudo_div2">
					  <h3 class="conteudo_titulo2">'.$res['moduloadicional2_titulo'].'</h3>
					  <h4 class="conteudo_texto2">'.$res['moduloadicional2_texto'].'</h4>
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

	static function getModuloAdicional1()
	{
		try
		{
			$ret= '';
			$sql = "SELECT * FROM moduloadicional1 WHERE moduloadicional1_deletado=0 AND moduloadicional1_ativo=1";
			$result = Sql::_fetchAll($sql);
			foreach ($result as $res)
			{
				 //$img = U::getImg('imagens/moduloadicional1/'.$res['moduloadicional1_id'].'_1_1.'.$res['imagem_extensao1']);
				 //$link = $res['moduloadicional1_titulo1'];
				 $ret.='
				 <div class="col-sm-4">
					<div class="branch">
						<div class="branch-icon"></div>
						<div class="branch-name">
							'.$res['moduloadicional1_titulo'].'
						</div>
						<p>'.$res['moduloadicional1_texto'].'</p>
					</div><!-- /.branch -->
				</div>';
			}
			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	static function getModuloAdicional4()
	{
		try
		{
			$ret = '';
			$sql = "SELECT * FROM moduloadicional4 WHERE moduloadicional4_deletado=0 AND moduloadicional4_ativo=1";
			$result = Sql::_fetchAll($sql);
			$id = 1;
			foreach($result as $res)
			{
				$img = U::getImg('imagens/moduloadicional4/'.$res['moduloadicional4_id'].'_1_1.'.$res['imagem_extensao1']);
				$ret.='
				<div class="slide-'.$id.' w-clearfix w-slide">
		          <div class="slide_div_imagem" style="background-image: url('.$img.');"></div>
		          <div class="slide_div_texto"><img src="images/seta_1.svg" class="promocao_seta">
		            <div class="promocao_titulo">'.$res['moduloadicional4_titulo'].'</div>
		            <div class="promocao_texto">'.$res['moduloadicional4_texto'].'</div><a href="'.$res['moduloadicional4_titulo2'].'" class="button_vazado w-button">'.$res['moduloadicional4_titulo1'].'</a></div>
		        </div>';
        		$id++;
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}
