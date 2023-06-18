<?php
class Cliente
{

    static function enviaEmailParaRecuperarSenha($email)
	{
		try
		{
                //Seguranca::checkCaptcha();
                echo $sql = "SELECT * FROM clientes WHERE clientes_email='{$email}'";
				$check = Sql::_fetch($sql);

				if($check)
				{
                    $token = U::getToken(50);
					$sql = "UPDATE clientes SET clientes_hash = '{$token}' WHERE clientes_email='{$email}'";
	            	$update = Sql::_query($sql);
	            	if($update)
	            	{
                        $link = HTTP.'/login?tk_pwd='.$token;
	            		$mensagem = '
	            		<h4>Recuperação de senha</h4>
	            		<p>
                            Olá <strong>'.U::firstName($check['clientes_nome']).'</strong>;
                            <br />
                            Conforme sua solicitação, segue um link para que possa alterar sua senha <strong>:)</strong>
	            		    <hr />
	            		    <a href="'.$link.'">Clique aqui para recuperar sua senha.</a>
                            <br /><br />
                            Caso o link acima esteja bloqueado, copie o endereço abaixo e cole em seu navegador:
                            <br /> '.$link.'
                            <br /><br />
                            <strong>Atenção:</strong>
                            <br />
                            1) Esse link tem validade de 24hs e só pode ser usado uma vez.
                            <br />
                            2) Apenas o último link enviado é valido.
	            		';

	            		E::email($check['clientes_email'],'Usuário', APP_NAME.' : Recuperação de Senha', $mensagem);
	            	}
				}

                return X::alert('Foi enviado um link para redefinir sua senha no e-mail informado.', HTTP, false);

		}
		catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
	}

	static function resenha($id)
	{
		try
		{
			if($_POST)
			{
				$token = U::getToken(50);
				$_POST['resenha'] = md5($_POST['resenha']);
				$sql = "UPDATE clientes SET clientes_senha = '{$_POST['resenha']}', clientes_hash = '{$token}'  WHERE clientes_hash='{$_POST['hash']}'";

            	$result = Sql::_query($sql);
            	if($result)
            	{
            		return Cliente::setLoginById($id);
            	}
			}
		}
		catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
	}


    static function validaNomeCompleto($retornoBoleano = false)
    {
        try
        {
            $nome = trim($_REQUEST['clientes_nome']);
            if($nome != '')
            {
                if (strstr($nome, ' '))
                {
                    return true;
                }
            }

            if($retornoBoleano)
            {
                return false;
            }
            die(X::alert('Informe o seu nome completo com <strong>NOME</strong> e <strong>SOBRENOME</strong>', false, true));
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }


    static function setCadastro()
    {
        try {
            $values = self::getValues(1);

            if ($_POST) {
                $acesso = $_POST['clientes_senha'];
                $_POST['clientes_senha'] = md5($_POST['clientes_senha']);

                if (!strstr($_POST['clientes_nome'], ' ')) {
                    return X::alert('Digite o seu nome completo com NOME e SOBRENOME', false, true);
                }

                foreach ($values as $campo => $valor) {

                    if ($valor['form_required'] == 'required') {
                        if (!isset($_POST[$valor['form_chave']]) || $_POST[$valor['form_chave']] == '') {
                            return X::alert($valor['form_aviso_erro'], false, true);
                        }

                        $updates[] = @" {$valor['form_chave']} = '{$_POST[$valor['form_chave']]}'";
                        $fields[] = $valor['form_chave'];
                        $contents[] = @"'{$_POST[$valor['form_chave']]}'";
                    }
                }

                Cliente::validaNomeCompleto();

                if (U::checkDuplicate('clientes', 'clientes_email', $_POST['clientes_email'])) {
                    return X::alert("E-mail já cadastrado", false, true);
                }

                $sql = 'INSERT INTO clientes (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $contents) . ')';
                $result = Sql::_query($sql);

                if ($result) {
                    return Cliente::setLoginById($result);
                } else {
                    return X::alert("Erro ao efetuar seu cadastro");
                }
            }
            return X::alert("No Thanks");
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }
    static function alterCadastro()
    {
        try {

            $values = self::getValues(1);

            if ($_POST)
            {

                if (isset($_POST['clientes_senha'][1]))
                {
                    $_POST['clientes_senha'] = md5($_POST['clientes_senha']);
                }
                else
                {
                    unset($_POST['clientes_senha']);
                }

                foreach ($values as $campo => $valor)
                {

                    if ($valor['form_required'] == 'required')
                    {
                        if ((!isset($_POST[$valor['form_chave']]) || $_POST[$valor['form_chave']] == '') && $campo != 'clientes_senha')
                        {
                            return X::alert($valor['form_aviso_erro'] . '', false, true);
                        }
                    }

                    if(isset($_POST[$valor['form_chave']]))
                    {
                        $updates[] = " {$valor['form_chave']} = '{$_POST[$valor['form_chave']]}'";
                    }
                }

                Cliente::validaNomeCompleto();

                if (Sql::checkDuplicate('clientes', 'clientes_email', $_POST['clientes_email'], 'clientes_id', Cliente::getDado('clientes_id'))) {
                    return X::alert("E-mail já cadastrado", false, true);
                }


                $sql = 'UPDATE clientes SET ' . implode(', ', $updates) . ' WHERE clientes_id=' . Cliente::getDado('clientes_id');

                $result = Sql::_query($sql);

                if ($result)
                {
                    $ck = Sql::_fetch("SELECT * FROM clientes WHERE clientes_id=".Cliente::getDado('clientes_id'));
                    if($ck)
                    {
                        $_SESSION[X.X] = $ck;
                    }
                    return X::alert("Seu Cadastro foi Alterado", false, true);
                }

                return X::alert("Erro ao efetuar seu cadastro");
            }
            return X::alert("No Thanks");
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }

    static function setLoginById($id)
    {
        try {

            $ck = Sql::_fetch("SELECT * FROM clientes WHERE clientes_id=" . $id);
            if ($ck) {

                $_SESSION[X.X] = $ck;
                $redirect = HTTP . '/minha-conta';
                if (Cart::getDado()) {
                    U::goHome(HTTP . '/carrinho');
                }

                if(isset($_SESSION['compra_de_curso']))
                {
                    U::goHome($_SESSION['compra_de_curso']['curso_url']);
                }

                return X::alert(false, $redirect, false);
            }
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }

    static function forgotPassword()
    {
        try {
            return Js::xSetHtml('
            <h4 id="tituloFormX">Recuperar Senha</h4>
            <div class="tm-form-inner">
                <div class="tm-form-field">
                    <label for="clientes_email">Informe seu e-mail*</label>
                    <input type="email" id="clientes_email" name="clientes_email" required />
                </div>
                <div id="xSetHtml" class="my-3"></div>
                <div class="tm-form-field">
                    <input type="'.TYPE.'" name="enviaEmailParaRecuperarSenha" value="'.U::getToken(10).'" >
                    <button type="submit" class="tm-button">Solicitar nova senha <b></b></button>
                </div>
                <div class="tm-form-field  text-right">
                    <a ' . Form::linkAction('loginForm') . '>Retornar para o Login</a>
                </div>
                <div class="tm-form-field">
                    <!--Seguranca::getCaptcha()-->
                </div>

            </div>', '#formLoginX');
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }

    static function loginForm()
    {
        try {
            if(isset($_GET['tk_pwd']))
            {
                return 'aha';
            }
            return Js::xSetHtml('
            <div class="tm-form-inner">
                <div class="tm-form-field">
                    <label for="clientes_email">Informe seu melhor e-mail*</label>
                    <input type="email" id="clientes_email"  name="clientes_email" onChange="checkIsClient()" required />
                    <small id="clientes_email_validate" style="color: red; font-weight: bold;"></small>
                </div>
                <div id="xSetHtml" class="my-3">
                    <div class="tm-form-field">
                        <button type="button" onclick="checkIsClient()" class="tm-button">Enviar <b></b></button>
                    </div>
                </div>
            </div>', '#formLoginX');
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }

    static function redefineSenha()
	{
		try
		{
            if(! isset($_POST['token_redefinicao_de_senha'][20]))
            {
                return X::alert('Token de recuperação de senha inválido!<br />Verifique seu e-mail.');
            }

            if(! isset($_POST['clientes_senha']) || ! isset($_POST['clientes_senha2']))
            {
                return X::alert('Informe a senha e a confirmação de senha!');
            }

            if($_POST['clientes_senha'] != $_POST['clientes_senha2'])
            {
                return X::alert('As senhas não são iguais!');
            }

            $sql = "SELECT * FROM clientes WHERE clientes_hash='{$_POST['token_redefinicao_de_senha']}'";
            $check = Sql::_fetch($sql);

            if(! $check)
            {
                return X::alert('Token de recuperação de senha <strong>inválido</strong>!<br />Verifique o link enviado.');
            }

            $novaSenha = md5($_POST['clientes_senha']);
            $token = U::getToken(50);
            $sql = "UPDATE clientes SET clientes_senha = '{$novaSenha}', clientes_hash = '{$token}'  WHERE clientes_hash='{$_POST['token_redefinicao_de_senha']}'";

            $result = Sql::_query($sql);

            if($result)
            {
                return Cliente::setLoginById($check['clientes_id']);
            }

            return X::alert('Houve um erro na recuperação de senha!<br />Tente mais tarde.');

		} catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
	}

    static function redefinicaoDeSenha()
    {
        try {
            return Js::xSetHtml('
            <div class="tm-form-inner">
                <div class="tm-form-field">
                    <label for="clientes_senha">Nova Senha*</label>
                    <input type="password" id="clientes_senha"  name="clientes_senha" required />
                </div>
                <div class="tm-form-field">
                    <label for="clientes_senha2">Confirme a Nova Senha*</label>
                    <input type="password" id="clientes_senha2"  name="clientes_senha2" required />
                </div>
                <div id="xSetHtml" class="my-3">
                    <div class="tm-form-field">
                        <button type="submit" class="tm-button">Redefinir Senha</button>
                    </div>
                </div>
                <input type="'.TYPE.'" name="token_redefinicao_de_senha"  value="'.$_GET['tk_pwd'].'" >
            </div>', '#formLoginX');
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }


    static function checkIsClient()
    {
        try
        {
                if(! filter_var($_REQUEST['clientes_email'], FILTER_VALIDATE_EMAIL))
                {
                    die('emailinvalido');
                }

                $result = Sql::_fetch("SELECT * FROM clientes WHERE clientes_email='{$_REQUEST['clientes_email']}'");

                if ($result)
                {

                    if(isset($_POST['forgotPassword']))
                    {
                        return Cliente::setCadastro();
                    }


                    if(isset($_POST['clientes_senha']))
                    {
                        return Cliente::setLogin();
                    }


                    return Js::xSetHtml('
                    <div class="tm-form-field">
                        <label for="clientes_senha">Informe sua Senha*</label>
                        <input type="password" id="clientes_senha" name="clientes_senha" required />
                    </div>
                    <div class="tm-form-field">
                        <button type="submit" class="tm-button">Entrar <b></b></button>
                    </div>
                    <div class="tm-form-field text-right">
                        <a ' . Form::linkAction('forgotPassword') . '>Esqueceu sua Senha?</a>
                    </div>');
                }

                $campos = '';
                $values = self::getValues(1);//^[-\w]{2,}+(?:\W+[-\w]{2,}+){1,2}\W*$

                foreach ($values as $campo => $valor)
                {

                    if($valor['form_chave'] == 'clientes_email')
                    {
                        continue;
                    }
                    $pattern = $valor['form_validacao_js'] ? ' pattern="'.$valor['form_validacao_js'].'" ' : '';
                    $asterisco = $valor['form_required'] ? '*' : '';


                    $campos.='
                    <div class="tm-form-field">
                        <label for="'.$valor['form_chave'].'">'.$valor['form_label'].$asterisco.'</label>
                        <input title="'.$valor['form_dica'].'" maxlenght="'.$valor['form_maxlength'].'" type="'.$valor['form_type'].'" '.$pattern.' id="'.$valor['form_chave'].'" name="'.$campo.'" '.$valor['form_required'].' />
                    </div>';
                }

                $campos.='
                    <div class="tm-form-field">
                        <button type="submit" class="tm-button">Cadastrar <b></b></button>
                    </div>
                    <div class="tm-form-field text-right">
                        <a href="javascript:resetLoginX()" >Voltar para o Login</a>
                    </div>';

                return Js::xSetHtml($campos);


        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }

    static function getCamposCadastro(array $camposIgnorados = array(), array $requiredsIgnorados = array())
    {
        try
        {
            $campos = '';
            $values = self::getValues(1);

            foreach ($values as $campo => $valor)
            {
                $value = isset($_SESSION[X.X][$valor['form_chave']]) ? $_SESSION[X.X][$valor['form_chave']] : '';

                if(in_array($valor['form_chave'], $camposIgnorados))
                {
                    continue;
                }


                $pattern = $valor['form_validacao_js'] ? ' pattern="'.$valor['form_validacao_js'].'" ' : '';

                if(in_array($valor['form_chave'], $requiredsIgnorados))
                {
                    $valor['form_required'] = '';
                    $value = '';
                }


                $campos.='
                <div class="col-md-6 form-group">
                    <label for="'.$valor['form_chave'].'">'.$valor['form_label'].'</label>
                    <input title="'.$valor['form_dica'].'" maxlenght="'.$valor['form_maxlength'].'" type="'.$valor['form_type'].'" '.$pattern.' id="'.$valor['form_chave'].'" name="'.$campo.'" value="'.$value.'" '.$valor['form_required'].' />
                </div>';
            }

            return $campos;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function setLogin()
    {
        try {

            if(isset($_POST['clientes_nome']))
            {
                return Cliente::setCadastro();
            }

            if(isset($_POST['token_redefinicao_de_senha']))
            {
                return Cliente::redefineSenha();
            }

            if(isset($_POST['enviaEmailParaRecuperarSenha']))
            {
                if(! isset($_POST['clientes_email'][1]))
                {
                    return X::alert('Informe seu email', false, true);
                }
                return Cliente::enviaEmailParaRecuperarSenha($_POST['clientes_email']);
            }

            if (!isset($_POST['clientes_email'][2]) || !isset($_POST['clientes_senha'][0])) {
                return X::alert('Dados inválidos', false, true);
            }

            $_POST['clientes_email'] = addslashes($_POST['clientes_email']);
            $_POST['clientes_senha'] =  md5($_POST['clientes_senha']);
            $result = Sql::_fetch("SELECT * FROM clientes
                        WHERE clientes_ativo=1 AND clientes_email='{$_POST['clientes_email']}' AND clientes_senha='{$_POST['clientes_senha']}'", array($_POST['clientes_email']), $_POST['clientes_senha']);

            if ($result) {
                return Cliente::setLoginById($result['clientes_id']);
            } else {
                echo X::alert('Dados Inválidos', false, true);
            }
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }


    static function getValues($id)
    {
        try {
            $result = Sql::_fetchall("SELECT * FROM forms WHERE form_categoria_id=" . $id);
            foreach ($result as $campo => $valor) {
                $value[$valor['form_chave']] = $valor;
            }

            return $value;
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }
    static function logado($ret = false)
    {
        try {
            if (isset($_SESSION[X.X]) || isset($_SESSION['adm'])) {
                if ($ret) {
                    return $ret;
                }
                return true;
            }

            U::goHome(HTTP . '/login');
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }
    static function getLogin()
    {
        if (isset($_GET['logoff']) && isset($_SESSION[X.X])) {
            unset($_SESSION[X.X]);
            U::goHome();
        }

        if (isset($_SESSION[X.X])) {
            return '
                <li class="tm-navigation-dropdown"><a class="" href="#"><i class="fa fa-user"></i></a>
                    <ul>
                        <li class=""><a href="'.HTTP.'/minha-conta">Olá <strong>' . U::firstName(Cliente::getDado('clientes_nome')) . '</strong></a></li>
                        <li class=""><a href="'.HTTP.'/minha-conta?open=meus-pedidos"><i class="fa fa-list"></i> Meus Pedidos</a></li>

                        <li class=""><a href="'.HTTP.'/minha-conta?open=alterar-dados"><i class="fa fa-user"></i> Alterar Dados</a></li>

                        <li><a href="'.HTTP.'?logoff=true" title="Sair" class="header-loginformtrigger"  style="border:none"><i class="fa fa-sign-out"></i> Sair</a></li>
                    </ul>
                </li>';
        }

        return '
        <li class="tm-navigation-dropdown"><a class="" href="#"><i class="fa fa-user"></i></a>
                    <ul>
                        <li class=""><a href="'.HTTP.'/minha-conta">Olá <strong> visitante</strong></a></li>

                        <li class=""><a href="'.HTTP.'/login" title="Login" class="header-loginformtrigger"  style="border:none"><i class="fa fa-user"></i> Entrar</a></li>
                    </ul>
                </li>';
    }
    static function checkLogin($url = HTTP . '/login')
    {
        try {
            if (!isset($_SESSION[X.X]['clientes_id'])) {
                U::goHome($url);
            }
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }
    static function getDado($key = false)
    {
        try {
            if (!isset($_SESSION[X.X]) || !isset($_SESSION[X.X][$key])) {
                return;
            }
            if ($key) {
                return $_SESSION[X.X][$key];
            }
            return $_SESSION[X.X];
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }


    static function setDado($array)
    {
        try {
            $_SESSION[X.X] = $array;
            return;
        } catch (Exception $e) {
            X::sendErrors($e->getMessage(), __CLASS__ . '>' . __FUNCTION__ . '>' . __LINE__);
        }
    }
}
