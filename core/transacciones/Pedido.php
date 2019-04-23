<?php
@include_once(CORE."transacciones/ContabilizaInventario.php");
/**
 * Explicacion corta del contenido de archivos y funciones
 *
 * @author       Alvaro Pulgarin <aepulgarin@lebon.com.co>
 * @copyright    Alvaro Pulgarin Y-M-D
 * @category     Area
 * @package      Modulo
 * @subpackage   SubModulo
 * @version         Version
 */

class Pedido extends ContabilizaInventario {
    public $tipoPedido='';
    public $detalle_items = array(); //array

    function __construct (){
        parent::__construct();
        $this->f_pago='1';
    }

    function AgregarItem($item,$cantidad, $valor){
        $line=count($this->detalle_items);
        $costoLinea=0;
        $cantidad = (float) $cantidad;
        $this->detalle_items[$line]['item']=$item;
        
        $consulta="select iva, unidad from inve where item='$item'";
        $dItem=$this->lee_uno($consulta);
        #INSERTA ITEM SALE
        $this->ejecuta_query("insert into TMPmovil (transaccion,almacen,origen,almacen2,item,cantidad,valor,costo_tr,unidad,prioridad,iva,cod_ubica) values 
                ('{$this->comprob}','1',".($line+1).",'0','$item','$cantidad','$valor','$costoLinea','{$dItem->unidad}',0,'{$dItem->iva}','{$ubicacion_ori}')");
    }

    function Ejecutar(){
        if($this->nit=='' && $this->tip_ori!=''){
            $consulta="SELECT nit,almacen,centro FROM movih WHERE transaccion = '{$this->tip_ori}' AND numero = '{$this->nro_ori}'";
            $datOri=$this->lee_todo($consulta);
            $this->nit =$datOri[0][nit];

            if($this->nit==''){
                $this->agregarError("DOCUMENTO","Documento:{$this->tip_ori} numero {$this->nro_ori}, No existe");
            }
       }
       if($this->tipoPedido==''){
            $this->agregarError("DOCUMENTO","TipoPedido (solped,web,pediplan,etc...)");
        }
        
        $this->contabilizar();

        $this->Moviped($this->tipoPedido,'');
        $this->Movipedl();
    }

    function Moviped($programa,$numsp){
        $sql_insercionp = "INSERT into moviped (usuario, transaccion, numero, fecha, fecha_grab, programa, cod_cam,numsol) values ('{$this->usuario}','{$this->comprob}','{$this->numero}',today,current,'$programa','{$this->retencion}','$numsp');";
        $this->ejecuta_query($sql_insercionp);
    }

    function Movipedl(){
        $inserta2="INSERT into movipedl (transaccion, numero, origen, item, cantidad_po, cantidad_pof, valor, cantidad_pt, pcontado) SELECT transaccion, numero, origen,item,cantidad, cantidad, valor, cantidad,'' from movil where transaccion='{$this->comprob}' and numero='{$this->numero}'";
        $ejecuta=$this->ejecuta_query($inserta2);
    }

    function getCosto($almacen,$item){
        $cos_="select case when saldo<>0 then costo/saldo else 0 end costo from sinve where ano_mesi =0 and almacen ='$almacen' and item ='$item'";
        $mat_c=$this->lee_todo($cos_);
        return $mat_c[0]->costo;
    }

}
