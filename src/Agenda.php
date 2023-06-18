<?php
class Agenda
{
	static function getAgenda()
	{
		try
		{
			$agenda = '';
			$class = 'first';
			$sql = "SELECT * FROM agenda WHERE agenda_deletada=0 AND agenda_ativa = 1 ORDER BY agenda_data";
			$result = Sql::_fetchAll($sql);

			foreach($result as $res)
			{
				$eventos = '';
				if(true)
				{
					$eventos.='
					<h5 class="sub-title alignleft"><a href="#"> '.$res['agenda_titulo2'].' </a></h5>
                    <p> '.$res['agenda_texto2'].' </p>
                    <h5 class="sub-title alignleft"><a href="#"> '.$res['agenda_titulo3'].' </a></h5>
                    <p> '.$res['agenda_texto3'].' </p>';
				}
				$agenda.='
				<div class="column dt-sc-one-fifth '.$class.'">
					<div class="dt-sc-hr-invisible-toosmall"></div>
					<h3 class="sub-title alignleft"> '.$res['agenda_titulo'].' </h3>
					<div class="dt-sc-four-third column first">
						'.$eventos.'
					</div>
				</div>';

				$class = '';
			}

			return $agenda;
			return '
			<div class="column dt-sc-one-fifth first">
    	                    <div class="dt-sc-hr-invisible-toosmall"></div>
    						<h3 class="sub-title alignleft"> Sexta </h3>
    	                    <div class="dt-sc-four-third column first">


    	                            <h5 class="sub-title alignleft"><a target="_blank" href="#"> Recepção </a></h5>
    	                            <p> Festa Disco Voador</p>

    	                    </div>



                        </div>

                        <div class="column dt-sc-one-fifth">
    	                    <div class="dt-sc-hr-invisible-toosmall"></div>
    						<h3 class="sub-title alignleft"> Sábado </h3>
    	                    <div class="dt-sc-four-third column first">


    	                            <h5 class="sub-title alignleft"><a target="_blank" href="#"> Tarde </a></h5>
    	                            <p> Festa da Espuma </p>

    	                            <h5 class="sub-title alignleft"><a target="_blank" href="#"> Noite </a></h5>
    	                            <p> Festa à Fantasia </p>

    	                    </div>



                        </div>';
		}
		catch( Exception $e )
		{
			X::sendErrors($e->getMessage(), __CLASS__.'>'.__FUNCTION__.'>'.__LINE__);
		}
	}
}