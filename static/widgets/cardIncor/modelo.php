<?php
class cardIncor extends mainModel {  
	                                                                    
	public function __construct()  
	{  
		$this->Conectarse();
		$this->ejecuta_query("SET ISOLATION TO DIRTY READ");
	}
	public function getIncor(){
		$m=$this->lee_uno("SELECT *, ((venta/venta_pro)*100)::integer por_venta, ((incor/incor_pro)*100)::integer por_incor FROM (
		SELECT sum(incorp+reincorporacion) as incor ,sum(vr_neto/1000)::integer venta ,b.incor as incor_pro, b.venta::integer as venta_pro
		FROM v_informe_comercial a, ins_proyect b WHERE cod_cam='{$this->campana}' and a.cod_cam=b.campana and b.tipocam='CAT'
		group by 3,4
		)");
		$mar=$this->lee_uno("SELECT case when sum(neto)!=0 then ((sum(utilidad)/sum(neto))*100)::integer else 0 end margen
        FROM ins_tmp_infoprono2
        WHERE campana ='{$this->campana}' and canal in ('CAT','EXT')");
        $m->margen=$mar->margen;
		return $m;
	}
}  
