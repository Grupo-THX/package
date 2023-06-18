<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class E
{


	static function email($destino,$nome,$assunto, $mensagem, $anexo=false, $copiaToAdmin = true)
	{
		try
		{
			$dados=X::getParametros('email');
			$corpo=str_replace(
								array('{titulo}','{mensagem}','{logomarca}', '{HTTP}'),
								array($assunto,$mensagem,$dados['logomarca'], HTTP),
								$dados['htmlEmail']
							   );

			if(defined('XSETOR'))
			{
				$dados = self::setDepartamento($dados);
			}

			$mail = new PHPMailer();

			if(DEBUG === true)
			{
				$mail->SMTPDebug  = 2;
			}

			$mail->IsSMTP();
			$mail->CharSet = 'UTF-8';
			$mail->Port = $dados['SmtpPorta'];
			$mail->Host = $dados['ServerSmtp'];
			$mail->SMTPAuth = true;
			$mail->Username = $dados['UserName'];
			$mail->Password = $dados['ServerEmailSenha']; // senha
			$mail->From = $dados['UserName'];
			$mail->FromName = $dados['FromName'];//FromName
			$mail->WordWrap = 50;
			$mail->IsHTML(true);

			$mail->AddAddress($destino,$nome);

			if(EMAIL_LOG && $destino != EMAIL_LOG)
			{
				$mail->AddBcc(EMAIL_LOG, APP_NAME.' EMAIL_LOG:');
			}

			if($copiaToAdmin && $destino != $copiaToAdmin)
			{
				$mail->AddBcc($dados['EmailFrom'],$nome);
			}


			if(isset($dados['email_bcc']))
			{
				$copias=$dados['email_bcc'];
				if ($copias && $copias != '')
				{
					 $copias= explode(';',$copias);
					 if (is_array($copias))
					 {
					 	foreach ($copias as $copia)
					 	{
							$mail->AddBcc($copia,$nome);
					 	}
					 }
					 else
					 {
					 	$mail->AddBcc($copias,$nome);
					 }
				}
			}


			if($anexo)
			{
				if (is_array($anexo))
				{
					foreach($anexo as $file)
					{
						if(is_array($file))
						{
							$mail->AddAttachment($file[0], $file[1]);
						}
						else
						{
							$mail->AddAttachment($file);
						}
					}
				}
				else
				{
					$mail->AddAttachment($anexo);
				}
			}

			$mail->AddReplyTo($destino,$nome);
			$mail->Subject = $assunto;
			$mail->Body = $corpo;

			if(! $mail->Send())
			{
				if(DEBUG)
				{
					echo $mail->ErrorInfo;
				}
				return false;
			}

			return $corpo;

		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function emailLog($acao, $log, $anexo = false)
	{
		try
		{
			if(! EMAIL_LOG)
			{
				return;
			}

			$log = '
			<br /><strong>Data:</strong> '.date('d/m/Y G::i').'
			<br /><strong>Ação:</strong> '.$acao.',
			<br /><strong>APP:</strong> '.APP_NAME.',
			<br /><br /><strong>Log:</strong>
			<pre style="padding: 15px;background-color: #444;color: #f8f8f8;border: 0;">'.print_r($log, 1).'</pre>';

			return self::email(EMAIL_LOG, APP_NAME, APP_NAME.' ['.$acao.']', $log, $anexo, false);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function setDepartamento($dados)
	{
		try
		{
			$sql = "SELECT * FROM  dept_email WHERE dept_email_id=".XSETOR;
			$result = Sql::_fetch($sql);

			if($result)
			{
				$dados['EmailFrom'] = $result['dept_email_email'];
				$dados['FromName'].= ' '.$result['dept_email_nome'];

			}
			return U::clearStr($dados);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function setSetor($id)
	{
		try
		{
			return '<input type="'.TYPE.'" name="xsetor" value="'.$id.'" >';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getDado($id, $coluna = false)
	{
		try
		{
			$dados = Sql::_fetch("SELECT * FROM dept_email WHERE dept_email_id =".$id);
			if(! $dados)
			{
				return false;
			}
			return ($coluna && isset($dados[$coluna])) ? $dados[$coluna] : $dados;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getDepartamento()
	{
		try
		{
			if(isset($_POST['xsetor']))
			{
				if($_POST['xsetor'] > 0)
				{
					if(Sql::checaTabelaExists('dept_email'))
					{
						define('XSETOR', (int)$_POST['xsetor']);
					}
				}

				unset($_POST['xsetor']);
			}
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function departamento($departamento = false)
	{
		try
		{
			if($departamento)
			{
				return '<input type="'.TYPE.'" name="xsetor" value="'.$departamento.'">';
			}

			return;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

	static function getDepartamentos()
	{
		try
		{

			$ret = '<option value="">Selecione o departamento</option>';
			$sql = "SELECT * FROM  dept_email WHERE dept_email_deletado=0 AND dept_email_ativo=1   ORDER BY dept_email_ordem";
			$result = Sql::_fetchAll($sql);
			if(! $result)
			{
				$ret.='<option value="0">Geral</option>';
			}

			foreach($result as $res)
			{
				$ret.='<option value="'.$res['dept_email_id'].'">'.$res['dept_email_setor'].'</option>';
			}

			return U::clearStr($ret);
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}
