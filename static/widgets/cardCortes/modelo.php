<?php
class cardCortes extends mainModel {  
	                                                                    
	public function __construct()  
	{  
		$this->Conectarse();
		$this->ejecuta_query("SET ISOLATION TO DIRTY READ");
	}
	public function getCortes(){
		return $this->lee_todo("SELECT trim(a.corte) as corte, count(a.zona) zonas, count(b.zona) cerradas FROM zonas a, outer szonas b WHERE a.zona=b.zona and length(a.zona)=5 and b.cod_cam='{$this->campana}' and b.cierre='D' and a.corte!='Z' and a.regional not in (99,97,88) group by 1");
	}
	public function getDetalleCorte(){
		return $this->lee_todo("SELECT trim(a.zona) as zona, decode(nvl(b.cierre,'A'),'A','Abierta','D','Cerrada definitiva','P','Cerrada parcial','','Reabierta',b.cierre)estado FROM zonas a, outer szonas b WHERE a.zona=b.zona and length(a.zona)=5 and b.cod_cam='{$this->campana}' and a.corte='{$this->corte}' and a.corte!='Z' and a.regional not in (99,97,88) order by 1");
	}

}  
