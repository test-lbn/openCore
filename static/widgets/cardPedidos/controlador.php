<?php
if(!class_exists("servicio")){
	class servicio extends mainController{
		function consultaPedidos($param){
			$campana=$param['campana'];
			$modelo= new cardPedidos();
			$modelo->campana=$campana;
			$Campana=$this->cargarComponente("campana");
			$Campana->restarcampanas(array("campana"=>$campana,"cant"=>18));
			$modelo->campana_ant=$Campana->response->campana;
			$m=$modelo->getPedidos();
			$this->response($m);
		}
	}	
}
