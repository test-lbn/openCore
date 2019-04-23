<?php
class cardPedidos extends mainModel {  
	                                                                    
	public function __construct()  
	{  
		$this->Conectarse();
		$this->ejecuta_query("SET ISOLATION TO DIRTY READ");
	}
	public function getPedidos(){
		$hist=$this->lee_todo("SELECT cod_cam, sum(nro_ped_fac)fac FROM szonas WHERE cod_cam>='{$this->campana_ant}' and cod_cam<'{$this->campana}' group by 1 order by 1");

		$m=$this->lee_uno("SELECT sum(case when estado in ('U','L','A','k') then 1 else 0 end) fac,sum(case when estado in ('C','B') then 1 else 0 end) ped,(SELECT max(pedidos) FROM ins_proyect WHERE campana='{$this->campana}' and tipocam='CAT') proyect FROM movih a WHERE transaccion='PED' and retencion='{$this->campana}' and estado not in ('E','W') and transportador!='CAMBIO'");
		$m->historico=$hist;
		return $m;
	}
}  
