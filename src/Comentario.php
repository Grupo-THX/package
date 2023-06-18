<?php
class Comentario
{
	static function getComentarios()
	{
		try
		{
			$ret='';
			$sql = "SELECT * FROM comentarios WHERE comentario_deletado=0 AND comentario_ativo=1";
			$result = Sql::_fetchAll($sql);


			foreach($result as $res)
			{
				$img = U::getImg('imagens/comentarios/'.$res['comentario_id'].'_1_1.'.$res['comentario_extensao1']);
				$ret.='
				<div class="testimonial-item">
					<div class="testimonial-content">
						<div class="testimonial-img">
							<i class="fa fa-quote-left"></i>
						</div>
						'.$res['comentario_textarea1'].'
					</div>
					<div class="testimonial-divider">
					</div>
					<div class="testimonial-meta">
						<strong>'.$res['comentario_nome'].'</strong>
					</div>
				</div>';
			}

			return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getComentariosImprensa()
	{
		try
		{
			$loops = 3;
			$ret='';
			$sql = "SELECT * FROM moduloadicional3 WHERE moduloadicional3_deletado=0 AND moduloadicional3_ativo=1 ORDER BY moduloadicional3_data DESC";
			$result = Sql::_fetchAll($sql);
			$linhas=count($result);
			$prefixo = '<div class="imprensa_linha">';
			$sulfixo = '</div>';
			$idcolum = 'moduloadicional3_id';
			$null = '';
			for($i=0;$i < $linhas; $i+=$loops)
			{
				$ret.=$prefixo;

				for($lis=0;$lis < $loops ; $lis++)
				{
					if(isset($result[$i+$lis][$idcolum]))
					{
						$res = $result[$i+$lis];
						$img = '';
						$link='';

                    		$ret.= '
                    		<div class="imprensa_div"><a target="_blank" href="'.$res['moduloadicional3_titulo1'].'" class="imprensa_link">'.$res['moduloadicional3_texto'].'</a>
					          <div class="imprensa_fonte">'.$res['moduloadicional3_titulo'].'<br>'.U::formataData($res['moduloadicional3_data']).'</div>
					        </div>';

					}
					else
					{
						$ret.= $null;
					}
				}

            $ret.=$sulfixo;
		}
			return $ret;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}