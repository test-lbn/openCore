<?php
if(!class_exists("servicio")){
	class servicio extends mainController{
		function consultaIncor($param){
			$campana=$param['campana'];
			$modelo= new cardIncor();
			$modelo->campana=$campana;
			
			/*#resta campaña para rango historico
			$Campana=$this->cargarComponente("campana");
			$Campana->restarcampanas(array("campana"=>$campana,"cant"=>18));
			$modelo->campana_ant=$Campana->response->campana;
			*/
			$m=$modelo->getIncor();
			$this->response($m);
		}
	}	
}
