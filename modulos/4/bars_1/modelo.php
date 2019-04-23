<?php
class bars_1Model extends mainModel {
	public $atributo1="Valor por defecto ejemplo";  
	public $atributo2;  
	                                                                    
	public function __construct($parametro="")  
	{  
		$this->atributo2 = strtoupper($parametro);
		$this->Conectarse();
	}
	public function funcionModelo($atributo){
		return $this->atributo1." ".$atributo;
	}
}  
