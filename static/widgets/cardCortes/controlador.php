<?php
if(!class_exists("servicio")){
	class servicio extends mainController{
		function consultaCortes($param){
			$campana=$param['campana'];
			$modelo= new cardCortes();
			$modelo->campana=$campana;
			$m=$modelo->getCortes();
			$this->response($m);
		}function detalleCorte($param){
			$campana=$param['campana'];
			$corte=$param['corte'];
			$modelo= new cardCortes();
			$modelo->campana=$campana;
			$modelo->corte=$corte;
			$m=$modelo->getDetalleCorte();
			$this->response($m);
		}
	}	
}
