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
}