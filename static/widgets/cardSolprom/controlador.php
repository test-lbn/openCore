<?php
if(!class_exists("servicio")){
	class servicio extends mainController{
		function consultaSolprom($param){
			$campana=$param['campana'];
			$modelo= new cardSolprom();
			$modelo->campana=$campana;
			
			#resta campaña para rango historico
			$Campana=$this->cargarComponente("campana");
			$Campana->restarcampanas(array("campana"=>$campana,"cant"=>18));
			$modelo->campana_ant=$Campana->response->campana;

			$m=$modelo->getSolprom();
			$this->response($m);
		}
	}	
}
