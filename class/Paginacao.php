<?php
class Paginacao
{
    static function layout($indice, $chave = false, $valor = false)
    {
        try
        {
            $layout=array();
            $layout['open'] = '
            <div class="tm-pagination mt-50">
                <ul>';
            $layout['setaE'] = '<li class="previous"><a href="{url}"><i class="fa fa-angle-double-left"></i></a></li>';
            $layout['setaD'] = '<li class="next"><a href="{url}"><i class="fa fa-angle-double-right"></i></a></li>';
            $layout['pagina'] = '<li><a href="{url}">{pag}</a></li>';
            $layout['paginaAtual'] = '<li class="is-active"><a href="javascript:void(0);">{pag}</a></li>';
            $layout['close'] = '
                </ul>
            </div>';

            if(defined('X_ADMIN'))
            {
                $layout['open'] = '
                <div class="container">
                    <div class="row  mb-5">
                        <nav aria-label="Paginação Admin">
                            <ul class="pagination pagination-sm justify-content-center">';
                $layout['setaE'] = '<li class="page-item disabled"><a class="page-link" href="{url}" tabindex="-1"><i class="fa fa-angle-double-left"></i></a></li>';
                $layout['setaD'] = '<li class="page-item"><a class="page-link" href="{url}"><i class="fa fa-angle-double-right"></i></a></li>';
                $layout['pagina'] = '<li class="page-item"><a class="page-link" href="{url}">{pag}</a></li>';
                $layout['paginaAtual'] = '<li class="page-item active" aria-current="page"><a class="page-link" href="javascript:void(0);">{pag} <span class="visually-hidden">(current)</span></a>
                </li>';
                $layout['close'] = '
                            </ul>
                        </nav>
                    </div>
                </div>';
            }

            if($chave &&  $valor)
            {
               return str_replace($chave, $valor, $layout[$indice]);
            }

            return $layout[$indice];

        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
    static function getPaginacao($query,$final=12, $setas = true, $limitePaginas = 13)
    {
        try
        {
             $paginacao = '';
            $_GET['pag'] = isset($_GET['pag']) && is_numeric($_GET['pag']) ? $_GET['pag'] : 1;

            if(is_array($query))
            {
                $setSql = str_replace($query[1], ' count(*) ',$query[0]); //echo"$setSql";
                $result = Sql::_fetch($setSql);
                $query = $query[0];
                $total_de_linhas = $result['count(*)'];
            }
            else
            {
                $setSql = "SELECT count(*) as paginacaototal FROM ({$query}) as paginacaototal"; //echo"$setSql";
                $result = Sql::_fetch($setSql);
                $query = $query;
                $total_de_linhas = $result['paginacaototal'];

            }

            $total_de_paginas = ceil ($total_de_linhas/$final);
            $inicio = ($_GET['pag']-1) * $final;

            $inicioing_no = $inicio + 1;

            if ($total_de_linhas - $inicio < $final)
            {
                $ultima_pagina = $total_de_linhas;
            }
            else if($total_de_linhas - $inicio >= $final)
            {
                $ultima_pagina = $inicio + $final;
            }

            if($total_de_linhas > $final)
            {
                if($total_de_linhas>0)
                {
                    if ($total_de_linhas - $ultima_pagina > $final)
                    {
                        $var2 = $final;
                    }
                    else if ($total_de_linhas - $ultima_pagina <= $final)
                    {
                        $var2 = $total_de_linhas - $ultima_pagina;
                    }


                    $setaesq = $setadir = '';
                    if($setas)
                    {
                        $bkpPage=$_GET['pag'];
                        if($_GET['pag'] != 1 && $total_de_paginas > 1)
                        {
                            $_GET['pag']--;
                            $parametros = strtok($_SERVER['REQUEST_URI'],'?').'?'.http_build_query($_GET);
                            $setaesq = self::layout('setaE', '{url}', $parametros);
                        }

                        $_GET['pag'] = $bkpPage;
                        $setadir = '';
                        if($_GET['pag'] != $total_de_paginas)
                        {
                            $_GET['pag']++;
                            $parametros = strtok($_SERVER['REQUEST_URI'],'?').'?'.http_build_query($_GET);

                            $setadir = self::layout('setaD', '{url}', $parametros);
                        }
                        $_GET['pag'] = $bkpPage;
                    }

                    $variavel = str_replace("{pag}", $_GET['pag'], self::layout('open'));
                    $paginacao.=$variavel.$setaesq;



                    $ultimoItemDaPagina = $final*$_GET['pag'] > $total_de_linhas ? $total_de_linhas : $final*$_GET['pag'];
                    $paginacao = str_replace(array('{totalItens}', '{primeiroItem}', '{ultimoItem}'), array($total_de_linhas, ($final*$_GET['pag']-($final-1)), $ultimoItemDaPagina), $paginacao);



                    $paginaAtual=$_GET['pag'];
                    for ($i=1; $i<=$total_de_paginas; $i++)
                    {
                        if($i==$paginaAtual)
                        {
                            $paginacao.=self::layout('paginaAtual', '{pag}', $i);
                        }
                        else
                        {
                             $_GET['pag']=$i;
                             $parametros = strtok($_SERVER['REQUEST_URI'],'?').'?'.http_build_query($_GET);

                            if($limitePaginas > 0)
                            {
                                if($i+$limitePaginas > $paginaAtual && $i-$limitePaginas < $paginaAtual)
                                {
                                    $paginacao.=self::layout('pagina', array('{url}', '{pag}'), array($parametros, $i));
                                }
                            }
                            else
                            {
                                $paginacao.=self::layout('pagina', array('{url}', '{pag}'), array($parametros, $i));
                            }
                        }

                    }

                    $paginacao.=$setadir.self::layout('close');
                }

            }
            $queryLimit=$query." LIMIT {$inicio},{$final}";
            $GLOBALS['paginacao'] = array('paginacao' => $paginacao, 'query' => $queryLimit);
            return $queryLimit;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
    static function getPaginacaoAjax($query, $limitePaginas = 8)
    {
        try
        {
            $_GET['pag'] = isset($_GET['pag']) && is_numeric($_GET['pag']) ? $_GET['pag']+$limitePaginas : 0;

            $bt='
            <div class="col-md-12 align-center btpaginacaoAjax">
                <a href="javascript:paginacaoAjaxFotos('.$_GET['pag'].')" class="ff_button">Carregar mais</a>
            </div>
            <script>
            recarregaLightBox();
            </script>';


            $query.=" LIMIT {$_GET['pag']},{$limitePaginas}";

            return array('query' => $query, 'paginacao' => $bt);
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
    static function getPaginas()
    {
        try
        {
            if(! isset($GLOBALS['paginacao']['paginacao']))
            {
                return;
            }
            $ret = $GLOBALS['paginacao']['paginacao'];
            $GLOBALS['paginacao']['paginacao']='';
            return $ret;
        }
        catch( Exception $e )
        {
            X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
        }
    }
}
