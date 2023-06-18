<?php
class Social
{
	static function getCommentsFacebook($url=false, $width = '100%', $maxPosts = 5)
	{
		try
		{
			if($url)
			{
				$url = HTTP.$_SERVER['REQUEST_URI'];
			}
			return '
			<div id="fb-root"></div>
            <script>(function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id;
              js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.6&appId=1529123514084914";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, \'script\', \'facebook-jssdk\'));</script>
            <div class="fb-comments" data-href="'.$url.'" data-width="'.$width.'" data-numposts="'.$maxPosts.'"></div>';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
	static function get()
	{
		try
		{
			$result = Sql::_fetchall('SELECT * FROM rede_social  WHERE social_deletada=0 AND social_ativa=1');
			foreach($result as $key => $res)
			{
				$result[$key]['img'] = U::getImg('imagens/redes/'.$res['social_id'].'.'.$res['social_extensao']);
			}
			return $result;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}


	static function share($rede, $url = false)
	{
		try
		{
			if(! $url)
			{
				$url = U::urlAtual();
			}
			switch ($rede)
			{
				case 'facebook':
					return 'https://www.facebook.com/sharer.php?p[url]='.$url;
				break;

				case 'whatsapp':
					return 'https://wa.me?text='.$url;
				break;

				case 'twitter':
					return 'https://twitter.com/share?url='.$url;
				break;

				case 'linkedin':
					return 'https://www.linkedin.com/sharing/share-offsite/?url='.$url;
				break;

				default:
					return '#';
				break;
			}
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}

}
