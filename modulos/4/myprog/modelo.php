<?php
class myprogModel extends mainModel {
	public $atributo1="Valor por defecto ejemplo";  
	public $atributo2;  
	public $atributo3="Nuevo ejemplo campo 2";  
	public $atributo4;  
	public $atributo5="Nuevo ejemplo campo 3";  
		                                                                    
	public function __construct($parametro="")  
	{  
		$this->exceptionMode="throw";
		$this->atributo2 = strtoupper($parametro);
		$this->atributo4 = strtoupper($parametro);
		$this->Conectarse();
	}
	public function funcionModelo($atributo){
		$this->atributo1.=" ".$atributo;
		$this->atributo3.=" ".$atributo;

		return $this;
	}
	public function funcionModelo2($id){
		try {
			return $this->lee_uno("SELECT descripcion, programa FROM core_programas WHERE id='$id'");
		} catch (Exception $e) {
			throw $e;
			
		}
		
	}
}  
