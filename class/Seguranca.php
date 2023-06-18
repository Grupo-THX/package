<?php
class Seguranca
{
    static function encrypt($str)
    {
        try
        {
            return md5($str);
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
    static function getCaptchaV3($float = 'left', $cor = 'padrao', $size = 'padrao')
    {
        try
        {
            $cor = $cor == 'padrao' ? 'light' : 'dark';
            $size = $size == 'padrao' ? 'normal' : 'compact';

            $local = '<input type="hidden" name="xlocal" value="'.$_SERVER['PHP_SELF'].'">';

            $params = X::getParametros();
            echo '<pre>';
            print_r($params);
            die('oxi');

            // return'
            //     <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response">
            //     <script src="https://www.google.com/recaptcha/api.js?render='.CAPTCHA_SITE_KEY.'"></script>
            //     <script>
            //         grecaptcha.ready(function() {
            //             grecaptcha.execute(\''.CAPTCHA_SITE_KEY.'\', {action: \'login\'}).then(function(token) {
            //                document.getElementById(\'g-recaptcha-response\').value=token;
            //             });
            //         });
            //     </script>'.$local;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
    static function getCaptcha($float = 'left')
    {
        try
        {
            $data = X::getParametros('reCaptchaV2');

            if(LOCAL_MODE || MODE_DEVELOPER || count($data) == 0)
            {
                $data['recaptcha_v2_theme'] = 'light';
                $data['recaptcha_v2_size'] = '200';
                $data['recaptcha_v2_public_key'] = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
            }

            $local = '<input type="hidden" name="xlocal" value="'.$_SERVER['PHP_SELF'].'">';

            return'
            <div id="recaptchaX">
                <div class="g-recaptcha" data-theme="'.$data['recaptcha_v2_theme'].'" data-size="'.$data['recaptcha_v2_size'].'" data-sitekey="'.$data['recaptcha_v2_public_key'].'" style="float: '.$float.';"></div>
            </div>'.$local;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function getHCaptcha($float = 'left')
    {
        try
        {

            $data = X::getParametros('hcaptcha');

            if(LOCAL_MODE || MODE_DEVELOPER || count($data) == 0)
            {
                $data['recaptcha_v2_theme'] = 'light';
                $data['recaptcha_v2_size'] = '200';
                $data['recaptcha_v2_public_key'] = '20000000-ffff-ffff-ffff-000000000002';
            }

            //Array ( [hcaptcha_message] => [hcaptcha_secret_key] => 0xD903297bAA42308B341Ddc2584FB5595d5Bc22A1 [hcaptcha_public_key] => 3b2e5253-7ba6-4f9b-9d84-5840f18a32f6 )

            $local = '<input type="hidden" name="xlocal" value="'.$_SERVER['PHP_SELF'].'">';

            return'
            <div id="recaptchaX">
                <script src="https://www.hCaptcha.com/1/api.js?explicit&hl='.Traducao::setHtmlLang().'" async defer></script>
                <div class="h-captcha" data-sitekey="'.$data['hcaptcha_public_key'].'" style="float: '.$float.';"></div>
            </div>'.$local;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function checkHCaptcha($retornoBoleano = false)
    {
        try
        {
            (print_r($_POST));
            $data = X::getParametros('hcaptcha');

            if(LOCAL_MODE || MODE_DEVELOPER || count($data) == 0)
            {
                $data['hcaptcha_secret_key'] = '0x0000000000000000000000000000000000000000';
            }

            if($data['hcaptcha_message'] == '')
            {
                $data['hcaptcha_message'] = 'Robôs são bloqueados. <br /> Prove que você não é um robô.';
            }

            $retorno = false;
            if(isset($_POST['h-captcha-response']))
            {
                $check = array(
                    'secret' => $data['hcaptcha_secret_key'],
                    'response' => $_POST['h-captcha-response']
                );

                $verify = curl_init();
                curl_setopt($verify, CURLOPT_URL, "https://hcaptcha.com/siteverify");
                curl_setopt($verify, CURLOPT_POST, true);
                curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($check));
                curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($verify);

                $responseData = json_decode($response);
                var_dump($responseData);
                if($responseData->success) {
                    $retorno = true;
                }
                unset($_POST['h-captcha-response']);
            }

            if($retornoBoleano)
            {
                return $retorno;
            }

            if(! $retorno)
            {
                echo '<script>parent.hcaptcha.reset();</script>';
                die(Js::alert($data['hcaptcha_message']));
            }

        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }

    static function checkCaptcha($retornoBoleano = false)
    {
        try
        {
            $data = X::getParametros('reCaptchaV2');

            if(LOCAL_MODE || MODE_DEVELOPER || count($data) == 0)
            {
                $data['recaptcha_v2_secret_key'] = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
            }

            if($data['recaptcha_v2_message'] == '')
            {
                $data['recaptcha_v2_message'] = 'Robôs são bloqueados. <br /> Prove que você não é um robô.';
            }

            $retorno = false;

            if(isset($_POST['g-recaptcha-response']))
            {

                $recaptcha = json_decode(File::file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$data['recaptcha_v2_secret_key']."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']));

                $retorno = $recaptcha->success;

                unset($_POST['g-recaptcha-response']);
            }

            if($retornoBoleano)
            {
                return $retorno;
            }

            if(! $retorno)
            {
                echo '<script>parent.grecaptcha.reset();</script>';
                die(Js::alert('Robôs são bloqueados. <br /> Prove que você não é um robô.'));
            }

        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
}
