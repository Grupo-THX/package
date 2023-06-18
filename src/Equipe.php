<?php
class Equipe
{
	static function getEquipe()
	{
		try
		{
			$equipe = '';
			$result = Sql::_fetchAll('SELECT * FROM equipe WHERE equipe_deletado=0 AND equipe_ativo=1 ORDER BY ordem');
			$class = 'active';
			foreach($result as $res)
			{
				$img = U::getImg('imagens/equipe/'.$res['equipe_id'].'_1_1.'.$res['equipe_extensao1']);
				$equipe.='
				<div class="col-md-3 col-sm-6 col-xs-12 margin-bottom">
		            <div class="ce-feature-box-8">
		              <div class="main-box">
		                <div class="img-box">
		                  <div class="overlay">
		                    <p class="small-text text-center">'.$res['equipe_descricao'].'</p>
		                    <br/>
		                    <ul class="sc-icons">
		                      '.Layout::display('<li><a href="#"><i class="fa fa-twitter"></i></a></li>',$res['equipe_campo_adicional2']).'
		                      '.Layout::display('<li><a href="#"><i class="fa fa-facebook"></i></a></li>',$res['equipe_campo_adicional3']).'
		                      '.Layout::display('<li><a href="#"><i class="fa fa-instagram"></i></a></li>',$res['equipe_campo_adicional3']).'
		                      '.Layout::display('<li><a href="#"><i class="fa fa-linkedin"></i></a></li>',$res['equipe_campo_adicional4']).'
		                    </ul>
		                  </div>
		                  <img src="'.$img.'" alt="" class="img-responsive"/> </div>
		                <div class="text-box text-center">
		                  <h5 class="nopadding title">'.$res['equipe_nome'].'</h5>
		                  <p class="subtext">'.$res['equipe_campo_adicional1'].'</p>
		                </div>
		              </div>
		            </div>
		        </div>';
	            $class = '';
			}
			return $equipe;
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}