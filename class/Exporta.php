<?
class Exporta
{

	static function trataCelula($variavel)
	{
		try
		{
			$proibidos = array(
			"\r",
			"\n",
			"\r\n",
			"\t",
			';'

			);
			$variavel = U::utf8($variavel);
			$variavel = trim($variavel);
			$variavel = str_replace($proibidos,'', $variavel);
			$variavel = preg_replace("/(<br.*?>)/i"," ", $variavel);
			return $variavel;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function exportarListagem($sql_query,$formato='csv',$nome_arquivo='Não nomeado')
	{
		try
		{
			$titulo_do_arquivo = $nome_arquivo;
			$nome_arquivo.='.'.$formato;


			if($formato=='csv')
			{
			        $quebra_de_linha="";
					$inicio_de_linha ='';
					$final_de_linha ="\n";
			        $separar_com = ';';
			        $caracter_antes_do_valor ='';
					$caracter_depois_do_valor ='';
					$cabecalho='';
					$fechamento='';
			}
			if($formato=='xls')
			{
			        $quebra_de_linha='';
					$inicio_de_linha =$quebra_de_linha.'<tr>';
					$final_de_linha ='</tr>'.$quebra_de_linha;
			        $separar_com = "";
			        $caracter_antes_do_valor = '<td>';
					$caracter_depois_do_valor = '</td>';
					$cabecalho="<table border='1'>".$quebra_de_linha;
					$fechamento=$quebra_de_linha.'</table>';
			}

			if($formato=='doc')
			{
			        $quebra_de_linha='';
					$inicio_de_linha =$quebra_de_linha.'<tr>';
					$final_de_linha ='</tr>'.$quebra_de_linha;
			        $separar_com = "";
			        $caracter_antes_do_valor = '<td>';
					$caracter_depois_do_valor = '</td>';
					$cabecalho="<table border='1'>".$quebra_de_linha;
					$fechamento=$quebra_de_linha.'</table>';
			}

			if($formato=='html')
			{
			        $quebra_de_linha="\n";
					$inicio_de_linha =$quebra_de_linha.'<tr>';
					$final_de_linha ='</tr>'.$quebra_de_linha;
			        $separar_com = "";
			        $caracter_antes_do_valor = '<td style="padding: 5px">';
					$caracter_depois_do_valor = '</td>';
					$cabecalho="<html><head><title>{$titulo_do_arquivo}</title></head><body><table border='1' style='border-collapse: collapse;	margin: auto;'>".$quebra_de_linha;
					$fechamento=$quebra_de_linha.'</table></body>';
			}
			if($formato=='txt')
			{
					$quebra_de_linha="\r\n";
					$inicio_de_linha ='';
					$final_de_linha =$quebra_de_linha;
			        $separar_com = "\t";
			        $caracter_antes_do_valor = '';
					$caracter_depois_do_valor = '';
					$cabecalho="";
					$fechamento='';
			}




					$out=$cabecalho;
					$caracter_de_escape = "\\";
					$valorvazio='';
					$schema_insert = $inicio_de_linha;

					$result = Sql::_fetchAll($sql_query);

					if(! $result)
					{
						die("<script>alert('Nenhum resuldado para sua consulta'); window.close();</script>");
					}

					$fields_cnt = count($result[0]);

					$colunasNomeadas = array();
					foreach($result[0] as $key => $cabecalho)
					{
						$l = $caracter_antes_do_valor . str_replace($caracter_antes_do_valor, $caracter_de_escape . $caracter_depois_do_valor,
			                        stripslashes($key)) . $caracter_depois_do_valor;
			                $schema_insert .= $l;
			                $schema_insert .= $separar_com;

			            $colunasNomeadas[]=$key;


					}

					if($formato == 'xls')
					{
						$out .= trim($schema_insert);
						$out .= $quebra_de_linha;
					}

					if($formato == 'doc')
					{
						$out .= trim($schema_insert);
						$out .= $quebra_de_linha;
					}

					if($formato == 'csv')
					{
						$out .= trim(substr($schema_insert, 0, -1));
						$out .= $quebra_de_linha;
						$out.= $final_de_linha;
					}

					if($formato == 'html')
					{
						$out .= trim(substr($schema_insert, 0, -1));
						$out .= $quebra_de_linha;
						$out.= $final_de_linha;
					}




					// if($formato=='xls' || $formato=='doc')
					// 	$out .= trim($schema_insert);
					// else
					// 	$out .= trim(substr($schema_insert, 0, -1));

					// 	$out .= $quebra_de_linha;


					// if($formato == 'csv')
					// {
					// 	$out.= $final_de_linha;
					// }


			        foreach($result as $row)
			        {
			                $schema_insert = $inicio_de_linha;
			                for ($j = 0; $j < $fields_cnt; $j++)
			                {
								$row[$j] = self::trataCelula($row[$colunasNomeadas[$j]]);
			                        if ($row[$j] == '0' || $row[$j] != '')
			                        {
			                                if ($caracter_antes_do_valor == '')
			                                {
			                                        $schema_insert .= $row[$j];
			                                }
											else
			                                {
			                                        $schema_insert .= $caracter_antes_do_valor .
			                                        str_replace($caracter_antes_do_valor, $caracter_de_escape . $caracter_depois_do_valor, $row[$j]) . $caracter_depois_do_valor;
			                                }
			                        }
									else
			                        {
			                                $schema_insert .= $caracter_antes_do_valor.$valorvazio.$caracter_depois_do_valor.$quebra_de_linha;
			                        }
			                        if ($j < $fields_cnt - 1)
			                        {
			                                $schema_insert .= $separar_com;
			                        }
			                } // end for



							$schema_insert .= $final_de_linha;
			                $out .= $schema_insert;
			                if($formato != 'txt')
							{
								$out .= $quebra_de_linha;
							}
			        } // end while
			       $out.=$fechamento;

			header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
			header ("Cache-Control: no-cache, must-revalidate");
			header ("Pragma: no-cache");
			header ("Content-type: application/{$formato}; charset=UTF-8");
			header('Content-Disposition: attachment; filename="'.utf8_decode($nome_arquivo).'"');
			header ("Content-Description: X-Painel PHP Generated Data" );

			if($formato == 'csv')
			{
				die(mb_convert_encoding($out, 'UTF-16LE', 'UTF-8'));
			}
			die($out);

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}