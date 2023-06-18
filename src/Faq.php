<?php
class Faq
{
    static function getFaqPerguntas()
    {
    	try
    	{
    		$faq = '';
	    	$sql = "SELECT * FROM faq WHERE faq_deletada=0 AND faq_ativa=1 AND faq_resposta != '' ORDER BY faq_cliques DESC";
	    	$result = Sql::_fetchAll($sql);

	    	foreach($result as $res)
	    	{
	    		$class = '';
	    		if(! isset($_GET['pergunta']))
	    		{
	    			$_GET['pergunta'] = $res['faq_id'];
	    		}

	    		$class = $_GET['pergunta'] == $res['faq_id'] ? 'active' : '';

	    		$faq.= '
	    		<li><a href="javascript:addClickFaq('.$res['faq_id'].', \'faq.php?pergunta='.$res['faq_id'].'\');" class="'.$class.'">'.$res['faq_pergunta'].'</a></li>';
        	}

        	return U::clearStr($faq);
    	}
    	catch( Exception $e )
    	{
    		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    	}
    }

    static function getFaqPergunta()
    {
    	try
    	{

	    	$sql = "SELECT faq_pergunta FROM faq WHERE faq_deletada=0 AND faq_ativa=1 AND faq_resposta != '' AND faq_id=".(int)$_GET['pergunta'];
	    	$result = Sql::_fetch($sql);

        	return U::clearStr($result['faq_pergunta']);
    	}
    	catch( Exception $e )
    	{
    		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    	}
    }

    static function getFaqResposta()
    {
    	try
    	{

	    	$sql = "SELECT faq_resposta FROM faq WHERE faq_deletada=0 AND faq_ativa=1 AND faq_resposta != '' AND faq_id=".(int)$_GET['pergunta'];
	    	$result = Sql::_fetch($sql);

        	return U::clearStr($result['faq_resposta']);
    	}
    	catch( Exception $e )
    	{
    		X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
    	}
    }

}