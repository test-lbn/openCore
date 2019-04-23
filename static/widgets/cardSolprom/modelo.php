<?php
class cardSolprom extends mainModel {  
	                                                                    
	public function __construct()  
	{  
		$this->Conectarse();
		$this->ejecuta_query("SET ISOLATION TO DIRTY READ");
	}
	public function getSolprom(){
		$hist=$this->lee_todo("SELECT cod_cam, ((sum(vr_fac_siva)/sum(vr_ped_pof))*100)::decimal(6,1) nsv FROM v_informe_comercial WHERE cod_cam >= '{$this->campana_ant}' and cod_cam<'$this->campana' group by 1 order by 1");

		$m=$this->lee_uno("SELECT (sum(vr_neto)/sum(nro_ped_fac))::decimal(15,0)spf ,(sum(vr_neto_dem)/sum(nro_ped_fac))::decimal(15,0)spd, ((sum(vr_fac_siva)/sum(vr_ped_pof))*100)::decimal(6,1) nsv FROM v_informe_comercial WHERE cod_cam='{$this->campana}'");
		$m->historico=$hist;
		return $m;
	}
}  
