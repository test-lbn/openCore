<?php
class informeModel extends mainModel {
	                                                                    
	public function __construct($parametro=""){  
		$this->Conectarse($parametro);
	}

	public function getInformacion(){

		//$query = "SELECT * FROM allus_gestiones LIMIT 10";
		
		$query = "SELECT a.id ,a.date_entered, a.modified_user_id, a.created_by, a.deleted, COUNT(*) cantidad
					FROM allus_dataimportlog a
					WHERE a.dalo_daim_id='c20f48fb-cf71-0433-18a3-5c896f086096'
					GROUP BY 1";

		$resultado = $this->lee_todo($query);

		return $resultado;
	}
}  
