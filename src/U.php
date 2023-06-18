<?php

namespace GrupoThx;

class U{
    static function getToken($tamanho = 10)
    {
        $hash="";
        $letra=array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','y','x','z');
        for ($i=0;$i<$tamanho;$i++)
        {
            $caracter=rand(1,3);
            $numero = rand(1,24);
            $numero_de_um_digito = rand(0,9);
            if($caracter==1)
                $hash=$hash.$numero_de_um_digito;
            if($caracter==2)
            {
                $letra[$numero]=strtoupper($letra[$numero]);
                $hash=$hash.$letra[$numero];
            }
            if($caracter==3)
            {
                $hash=$hash.$letra[$numero];
            }
        }
        return($hash);
    }

    static function valorPorExtenso($valor, $bolExibirMoeda = true, $bolPalavraFeminina = false)
	{
		
        $valor = number_format($valor, 2, ".", ".");
        $singular = null;
        $plural = null;

        if ( $bolExibirMoeda )
        {
                $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
                $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
        }
        else
        {
                $singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
                $plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
        }

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezessete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");


        if ( $bolPalavraFeminina )
        {

                if ($valor == 1)
                {
                    $u = array("", "uma", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
                }
                else
                {
                    $u = array("", "um", "duas", "três", "quatro", "cinco", "seis","sete", "oito", "nove");
                }


                $c = array("", "cem", "duzentas", "trezentas", "quatrocentas","quinhentas", "seiscentas", "setecentas", "oitocentas", "novecentas");


        }


        $z = 0;

        $inteiro = explode( ".", $valor );

        for ( $i = 0; $i < count( $inteiro ); $i++ )
        {
                for ( $ii = mb_strlen( $inteiro[$i] ); $ii < 3; $ii++ )
                {
                    $inteiro[$i] = "0" . $inteiro[$i];
                }
        }

        // $fim identifica onde que deve se dar junção de centenas por "e" ou por "," ;)
        $rt = null;
        $fim = count( $inteiro ) - ($inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2);
        for ( $i = 0; $i < count( $inteiro ); $i++ )
        {
                $valor = $inteiro[$i];
                $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
                $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
                $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

                $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
                $t = count( $inteiro ) - 1 - $i;
                $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
                if ( $valor == "000")
                    $z++;
                elseif ( $z > 0 )
                    $z--;

                if ( ($t == 1) && ($z > 0) && ($inteiro[0] > 0) )
                    $r .= ( ($z > 1) ? " de " : "") . $plural[$t];

                if ( $r )
                    $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        $rt = mb_substr( $rt, 1 );

        return($rt ? trim( $rt ) : "zero");

	}

    static function urlAtual($remove_GET = false)
	{
        $protocolo =  isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? "https://" : 'http://';
        $retorno = $protocolo.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if($remove_GET)
        {
            $urlParts = explode('?', $retorno);
            return $urlParts[0];
        }
        return $retorno;
	}
}