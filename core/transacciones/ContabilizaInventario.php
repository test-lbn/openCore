<?php
require_once(CORE."transacciones/Comprobante.php");

/**
 * clase para contabilizar transacciones de inventario
 *
 * @author       Alvaro Pulgarin <aepulgarin@lebon.com.co>
 * @copyright    Alvaro Pulgarin 2014-03-20
 * @category     Core
 * @package      Clases
 * @version      1.0
 */

//constantes temporales
define("TOLERANCIA_COSTO",40); // 50%


class ContabilizaInventario extends Comprobante{
    public $bod_ori;    //integer
    public $nit;        //string
    public $tip_ori;    //string
    public $nro_ori=0;    //integer
    public $bod_des;    //integer
    public $ubi_des;    //string
    public $item_res;   //string
    public $cant_res;   //float
    public $centro;    //string
    public $tip_doc;    //string
    public $nro_doc=0;    //integer

    public $f_pago=1;
    public $iva=0;
    public $descuento=0;
    public $retencion=0;
    public $solicita=1;
    public $aprueba=1;
    public $nota='N';
    public $estado='';
    public $transportador='';
    public $vehiculo='';
    public $empresa='';
    public $moneda='';
    public $usuario;    //string
    
    public $ubi_des_req;        //almacen requiere ubicacion (S-N)
    public $ubi_des_valida;     //ubicacion valida (S-N)
    public $ubi_ori_req;        //almacen requiere ubicacion (S-N)
    public $ubi_ori_valida;     //ubicacion valida (S-N)
    public $ValidarUbicacion='S'; //variable para excepciones ubicaciones virtuales
    public $observacion;   
    public $transacBloqueo= array('DVP','AJV','ENS');   
    public $transacNoInve= array('PED');   
    
    public $errores = array();
    function __construct(){
        parent::__construct();

         $this->usuario = $_SESSION['usuario'];

        // TODO: Crear una clase para tablas temporales
        //Tabla temporal base para contabilizar
        $this->ejecuta_query("CREATE temp TABLE TMPmovi ( 
            comprob char(3),
            numero integer default 0,
            fecha   date default today,    
            cuenta  VARCHAR(12),    
            nit decimal(15,0),
            centro varchar(8),    
            valor decimal(16,2),    
            deb_cre char(1),    
            descripcion varchar(40),
            tipo_doc varchar(3),    
            numero_doc decimal(12,0) default 0,    
            conciliado char(1) default '',
            cuenta_t varchar(12) default '',
            base decimal(16,2) default 0,
            fecha_grab   date default today,    
            grabador varchar(8),
            nota char(1) default '',    
            f_vence date default today,
            concepto varchar(14) default '0',
            activi varchar(20) default ''
        )");

        $this->ejecuta_query("CREATE temp TABLE TMPmovil ( 
            transaccion         varCHAR(3),
            almacen             SMALLINT,
            numero              INTEGER default 0,
            origen              SMALLINT,
            fecha               DATE default today,
            almacen2            SMALLINT,
            item                varCHAR(15) not null,
            cantidad            DECIMAL(14,5),
            valor               DECIMAL(14,2) default 0,
            cantidad_rec        DECIMAL(14,2) default 0,
            cantidad_cargada    DECIMAL(14,2) default 0,
            costo_tr            DECIMAL(14,2) default 0,
            nota                varCHAR(1) default '',
            unidad              varCHAR(3),
            prioridad           varCHAR(1),
            iva                 DECIMAL(8,2),
            descuento           DECIMAL(5,2) default 0,
            cod_ubica           varchar(12) default ''
        )");
    }
    function contabilizar(){
        $this->Validar();

        $this->NuevoConsecutivo();
        $this->ejecuta_query("update TMPmovil set numero={$this->numero} where transaccion='{$this->comprob}'");
        $this->ejecuta_query("update TMPmovi set numero={$this->numero}, numero_doc=case when numero_doc=0 then {$this->numero} else numero_doc end where comprob='{$this->comprob}'");

        #INSERTA MOVIH
        $inserta="INSERT INTO movih(transaccion, almacen, numero, fecha, nit, centro, t_pedido, n_pedido, f_vence, f_entrega, f_pago, almacen_destino, iva, descuento, retencion, t_factura_c, factura_c, solicita, aprueba, nota, estado, v_total, transportador, vehiculo, empresa, fecha_grab, grabador, moneda)
            SELECT transaccion,almacen, numero, fecha, {$this->nit},{$this->centro},'{$this->tip_ori}',{$this->nro_ori},fecha, fecha,'{$this->f_pago}',{$this->bod_des},{$this->iva},{$this->descuento},{$this->retencion},'{$this->tip_doc}',{$this->nro_doc},{$this->solicita},{$this->aprueba},'{$this->nota}','{$this->estado}',sum(valor),'{$this->transportador}','{$this->vehiculo}','{$this->empresa}',current,'{$this->usuario}','{$this->moneda}' 
            FROM TMPmovil --where prioridad=8
            group by 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,23,24,25,26,27,28";
        $this->ejecuta_query($inserta);

        #INSERTA OBERVACION DE LA TRANSACCION
        if($this->observacion!=''){
            $inserta="insert into notas (transaccion, almacen, numero, origen, item, nota) values ('{$this->comprob}','{$this->bod_ori}','{$this->numero}','0','','{$this->observacion}')";
            $this->ejecuta_query($inserta);
        }

        #INSERTA MOVIL
        $inserta="INSERT INTO movil(transaccion, almacen, numero, origen, fecha, almacen2, item, cantidad, valor, cantidad_rec, cantidad_cargada, costo_tr, nota, unidad, prioridad, iva, descuento) 
            SELECT transaccion, almacen, numero, origen, fecha, almacen2, item, cantidad, valor, cantidad_rec, cantidad_cargada, costo_tr, nota, unidad, prioridad, iva, descuento FROM TMPmovil";
        $this->ejecuta_query($inserta);
        
        if($this->ubi_des_req=='S'){
            #INSERTA MUBICA
            $inserta="INSERT INTO mubica (almacen, transaccion, numero, origen, item, cod_ubica, cant_ubi) SELECT almacen2, transaccion, numero, origen, item, cod_ubica, cantidad FROM TMPmovil where nvl(cod_ubica,'')!=''";
            $this->ejecuta_query($inserta);

            #CREA SUBICALM SI NO EXISTE ITEM EN ALMACEN DESTINO Y UBICACION DESTINO
            $consulta="SELECT item from subicalm where almacen='{$this->bod_des}' and item='{$this->item_res}' and cod_ubica='{$this->ubi_des}'";
            $mval=$this->lee_todo($consulta);
            if(count($mval)==0 && $this->ubi_des!='' && $this->item_res != ""){
                $this->ejecuta_query("insert into subicalm (almacen,cod_ubica,item,saldo_ubica) values ('{$this->bod_des}','{$this->ubi_des}','{$this->item_res}',0)");
            }
            if ($this->ubi_des_req=='S' && !in_array($this->comprob, $this->transacNoInve) ){
                #CREA UBICACIONES SI NO EXISTE EN ARREGLO TMPmovil
                $this->ejecuta_query("insert into subicalm (almacen,cod_ubica,item,saldo_ubica)
                SELECT distinct almacen2, cod_ubica,item,0 FROM TMPmovil where cantidad>0 and
                cod_ubica not in (select cod_ubica from subicalm where almacen='{$this->bod_des}' and item=TMPMovil.item)");
                
                $this->ejecuta_query("update subicalm set saldo_ubica=saldo_ubica+(select sum(cantidad) from TMPMovil where item=subicalm.item and cantidad>0 and cod_ubica=subicalm.cod_ubica) WHERE almacen='{$this->bod_des}' and item in (
                select item from TMPmovil where cantidad>0)
                and  trim(item)||trim(cod_ubica) in (select trim(item)||trim(cod_ubica) from TMPmovil where cantidad>0)");
            }
        }
        #Resta ubicacion
         if($this->ubi_ori_req=='S'){
         $this->ejecuta_query("update subicalm set saldo_ubica=saldo_ubica+(select sum(cantidad) from TMPMovil where item=subicalm.item and cantidad<0 and cod_ubica=subicalm.cod_ubica) WHERE almacen='{$this->bod_ori}' and item in (
    select item from TMPmovil where cantidad<0)
and trim(item)||trim(cod_ubica) in (select trim(item)||trim(cod_ubica) from TMPmovil where cantidad<0)");
        }

        #INSERTA MOVI
        $inserta="INSERT INTO infse.movi(comprob, numero, fecha, cuenta, nit, centro, valor, deb_cre, descripcion, tipo_doc, numero_doc, conciliado, cuenta_t, base, fecha_grab, grabador, nota, f_vence, concepto, activi) select comprob, numero, fecha, cuenta, nit, centro, valor, deb_cre, descripcion, tipo_doc, numero_doc, conciliado, cuenta_t, base, fecha_grab, grabador, nota, f_vence, concepto, activi from TMPmovi";
        $this->ejecuta_query($inserta);

        #CREA SINVE SI NO EXISTE ITEM EN ALMACEN DESTINO
        $this->ejecuta_query("insert into sinve (item, almacen, ano_mesi, saldo, costo, en_transaccion,v_ajustado, costo_std ) 
                SELECT distinct item,almacen2,'0',0,0,0,0,0 FROM TMPMovil where prioridad in (1) and item not in (select item from sinve where ano_mesi=0 and almacen='{$this->bod_des}' and item=TMPMovil.item)");
        

        #CREA SINVE SI NO EXISTE ITEM EN ALMACEN 0
        $this->ejecuta_query("insert into sinve (item, almacen, ano_mesi, saldo, costo, en_transaccion,v_ajustado, costo_std ) 
                SELECT distinct item,0,'0',0,0,0,0,0 FROM TMPMovil where prioridad in (1) and item not in (select item from sinve where ano_mesi=0 and almacen=0 and item=TMPMovil.item)");

        #ACTUALIZA ALMACEN DESTINO
        $this->ejecuta_query("update sinve set saldo=saldo+(select sum(cantidad) from TMPMovil where item=sinve.item and cantidad>0) 
        ,costo=costo+(select sum(costo_tr) from TMPMovil where item=sinve.item and cantidad>0) 
         WHERE ano_mesi=0 and almacen='{$this->bod_des}' and item in (select item from TMPmovil where cantidad>0)");

        #ACTUALIZA ALMACEN ORIGEN
        $this->ejecuta_query("update sinve set saldo=saldo+(select sum(cantidad) from TMPMovil where item=sinve.item and cantidad<0) 
        , costo=costo+(select sum(costo_tr) from TMPMovil where item=sinve.item and cantidad<0) 
         WHERE ano_mesi=0 and almacen='{$this->bod_ori}' and item in (select item from TMPmovil where cantidad<0)");

        #ACTUALIZA 0 DESTINO
        $this->ejecuta_query("update sinve set saldo=saldo+(select sum(cantidad) from TMPMovil where item=sinve.item and cantidad>0) 
        ,costo=costo+(select sum(costo_tr) from TMPMovil where item=sinve.item and cantidad>0) 
         WHERE ano_mesi=0 and almacen=0 and item in (select item from TMPmovil where cantidad>0)");

        #ACTUALIZA 0 ORIGEN
        $this->ejecuta_query("update sinve set saldo=saldo+(select sum(cantidad) from TMPMovil where item=sinve.item and cantidad<0) 
        , costo=costo+(select sum(costo_tr) from TMPMovil where item=sinve.item and cantidad<0) 
         WHERE ano_mesi=0 and almacen=0 and item in (select item from TMPmovil where cantidad<0)");

        $consulta="select item, saldo from sinve where ano_mesi=0 and almacen='{$this->bod_ori}' and item in (select item from TMPmovil where cantidad<0) and saldo<0";
        //die($consulta);
        $mValida=$this->lee_todo($consulta);
     

        if(count($mValida)>0){
               print_r($mValida);
            die("Genero negativos, proceso cancelado, intente  nuevamente");
        }

        #VALIDACION DE NOTIFICACIONES
        if(count($this->notificaciones)>0){
            for ($n=0; $n < count($this->notificaciones) ; $n++) { 
                  //$notificaciones.=implode(":",$value).PHP_EOL;
                $data=$this->notificaciones[$n][1];
                $sql_inserta_log_error="INSERT INTO ins_log_errores_costos_inventarios( comprob, numero, borigen, bdestino, item,cantidad, costotrans, preciocompra, fechaerror) 
                                VALUES( '{$data[comprob]}', '$this->numero', '$this->bod_ori', '$this->bod_des', '{$data[item]}','{$data[cantidad]}', '{$data[costotrans]}', '{$data[preciocompra]}', current)";
                $this->ejecuta_query($sql_inserta_log_error);
                //die("PROBLEMA CON LA TRANSACCION: $this->comprob $this->bod_ori $this->bod_des <pre>".PHP_EOL.$notificaciones);
             } 
            //die("PROBLEMA CON LA TRANSACCION: $this->comprob $this->bod_ori $this->bod_des <pre>".PHP_EOL.$notificaciones);
             $this->logerrornobloquea($this->notificaciones);
        }


        $this->ejecuta_query("drop table TMPmovi;drop table TMPmovil;");
        

    }

    function validar(){
        #------------------VALIDACIONES------------------
        #ALMACEN DESTINO
        $consulta="SELECT a.almacen as des,b.almacen as ori,a.ubicacion as ubicacion_destino,a1.cod_ubica as ubica_des, b.ubicacion as ubicacion_origen,b1.cod_ubica as ubica_ori
        FROM almacenes a,outer ubicalm a1,almacenes b,outer ubicalm b1
        WHERE a.almacen={$this->bod_des} and b.almacen={$this->bod_ori}
        and a.almacen=a1.almacen and a1.cod_ubica='{$this->ubi_des}'
        and b.almacen=b1.almacen and b1.cod_ubica='{$this->ubi_ori}'
        ";
        $mUbica=$this->lee_uno($consulta);
      //  print_r($mUbica);

        $this->ubi_des_req = trim($mUbica->ubicacion_destino);
        $this->ubi_des_valida = ($this->ubi_des_req=='S' && $mUbica->ubica_des=='')?'N':'S';
        
        if($this->ValidarUbicacion=='N'){
            $this->ubi_des_req='N';
             $this->ubi_des='';
             //die('no deber');
        }

        $this->ubi_ori_req = trim($mUbica->ubicacion_origen);
        $this->ubi_ori_valida = ($this->ubi_ori_req=='S' && $mUbica->ubica_ori=='')?'N':'S';

        $consulta="SELECT nit FROM nits WHERE nit = '{$this->nit}'";
        $mValida=$this->lee_todo($consulta);

        if(count($mValida)==0){
            $this->agregarError("NIT","Nit {$this->nit} no existe");
        }

        if($this->ubi_des_req=='S' && $this->ubi_des_valida=='N' && $this->ubi_des!=''){
            $this->agregarError("UBICACION","ubicacion Destino:{$this->ubi_des} en almacen:{$this->bod_des} no existe");
        }

        if($this->bod_ori==''){
            $this->agregarError("BODEGA","Falta bodega origen");
        }

        #ITEMS DEL ARREGLO ORIGEN
        $consulta="SELECT 
            a.item, a.cod_ubica
            ,a1.precio_compra, a1.tipo_inv
            ,abs(a.cantidad) as cantidad
            ,case when a.cantidad>1 then abs(a.costo_tr/a.cantidad)::decimal(16,2) else abs(costo_tr) end as costo_movil
            ,b.saldo as saldo_sinve
            ,case when b.saldo<>0 then (b.costo/b.saldo)::decimal(16,2) else 0 end as costo_sinve
            ,c.saldo_ubica,(SELECT avg(p_compra) FROM sinvnit WHERE item=a.item and ano_mes=0 and almacen=0 and nvl(p_compra,0)>0) precio_sinvnit
        FROM TMPmovil a 
            left join inve a1 on (a.item=a1.item) 
            left join sinve b on (a.almacen=b.almacen and a.item=b.item and b.ano_mesi=0)
            left join subicalm c on (a.almacen=c.almacen and a.item=c.item and a.cod_ubica=c.cod_ubica)
        WHERE prioridad=8";
        $mValida=$this->lee_todo($consulta);

        // print_r($mValida);
        // die();

        for ($i=0; $i <count($mValida) ; $i++) { 
            $item = $mValida[$i]->item;
            $precio_sinvnit = (float) $mValida[$i]->precio_sinvnit;
            $precio_compra = (float) $mValida[$i]->precio_compra;
            $tipo_inv = (float) $mValida[$i]->tipo_inv;
            $cantidad = $mValida[$i]->cantidad;
            $costo_movil = $mValida[$i]->costo_movil;
            $saldo_sinve = $mValida[$i]->saldo_sinve;
            $costo_sinve = $mValida[$i]->costo_sinve;
            $saldo_ubica = $mValida[$i]->saldo_ubica;
            $cod_ubica = $mValida[$i]->cod_ubica;


            #AJUSTA PRECIO DE COMPRA SI LAS UNIDADES SON DECIMALES. aepv 29/04/2014
            //if($cantidad>0 && $cantidad<1){
                $precio_compra = round($precio_compra*$cantidad);
            //}

            #TOLERANCIA COSTO MOVIL
            if(round(@(abs($precio_compra-$costo_movil)/$precio_compra)*100)>TOLERANCIA_COSTO) {
                if(round(@(abs($precio_sinvnit-$costo_movil)/$precio_sinvnit)*100)>TOLERANCIA_COSTO) {
                    if($tipo_inv!=6){//No aplica para los servicios
                        if (in_array($this->comprob, $this->transacBloqueo)) {
                            $this->agregarError("COSTO TRANSAC","item $item, transaccion: $costo_movil, compra: $precio_compra >".TOLERANCIA_COSTO."% Tolerancia.");
                        }else{
                            $this->agregarNotificacion("COSTO TRANSAC", array('item' => $item, 'cantidad' => $cantidad, 'comprob' => $this->comprob,'costotrans' => $costo_movil,'preciocompra' => $precio_compra)); 
                                //"item $item, transaccion: $costo_movil, compra: $precio_compra >".TOLERANCIA_COSTO."% Tolerancia."
                        }
                    
                    }
                }
            }
            #PRECIO COMPRA SIN DEFINIR
            if($precio_compra==0){
                if($precio_sinvnit==0){
                    $this->agregarError("PRECIO COMPRA","item $item, costo sinve: $costo_sinve, costo compra: $precio_compra.");
                }
            }
            #CANTIDAD
            if($cantidad==0){
                $this->agregarError("CANTIDAD","item $item, cantidad: $cantidad.");
            }
            #SALDO SINVE
            if($cantidad>$saldo_sinve){
                $this->agregarError("SALDO SINVE","item $item, cantidad: $cantidad, saldo: $saldo_sinve");
            }
            #SALDO SUBICALM
            if($this->ubi_ori_req=='S' && $cantidad>$saldo_ubica){
                $this->agregarError("SALDO SUBICALM","item $item, ubicacion:$cod_ubica, cantidad: $cantidad, saldo: $saldo_ubica");
            }
        }

        #VALIDACION DE ERRORES
        if(count($this->error)>0){
            foreach ($this->error as $key => $value) {
                $error.=implode(":",$value).PHP_EOL;
            }
            die("PROBLEMA CON LA TRANSACCION: $this->comprob $this->bod_ori $this->bod_des <pre>".PHP_EOL.$error);
        }
    }
    
    function agregarError($tipo,$detalle){
        $this->error[] = array($tipo,$detalle);
    }

    function agregarNotificacion($tipo,$detalle){
        $this->notificaciones[] = array($tipo,$detalle);
    }

    /**
     * guarda log de la transaccion sin bloquear el proceso.
     */
    function logerrornobloquea($data){
 
        include_once("funciones/EnviarMailAuth.php");
            $correos=array('aepulgarin@lebon.com.co','lrivera@lebon.com.co','jmgonzalez@lebon.com.co');
            // print_r($data);
            // die();

            $mens="<span style='font-family:Arial;'>Se ha generado un error de movimientos de inventario. <br>Comprobante: {$data[0][1][comprob]} - Numero: $this->numero</span><br><br>";
                for ($m=0; $m <count($data) ; $m++) { 
                   $mens.="
                    <span style='font-family:Arial;'>
                        <table width='600' style='border: solid 1px #000000;'>
                         <tr>
                            <td>Item: </td>
                            <td><b>{$data[$m][1][item]}</b></td>
                        </tr>
                        <tr>
                            <td>Cantidad: </td>
                            <td><b>".$data[$m][1][cantidad]."</b></td>
                        </tr>
                        <tr>
                            <td>Bod ORI - DES: </td>
                            <td><b>$this->bod_ori - $this->bod_des</b></td>
                        </tr>
                        <tr>
                            <td>Costo Uni TRANS - Uni PRECIO COMPRA: </td>
                            <td><b>".number_format($data[$m][1][costotrans] / $data[$m][1][cantidad])." - ".number_format($data[$m][1][preciocompra] / $data[$m][1][cantidad])."</b></td>
                        </tr>
                        <tr>
                            <td>Costo TRANS - PRECIO COMPRA: </td>
                            <td><b>".number_format($data[$m][1][costotrans])." - ".number_format($data[$m][1][preciocompra])."</b></td>
                        </tr>
                    </table>
                    </span><br>";
                }

            

                    $mens.="<br><br>
            <span style='font-family:Arial;'>Este correo fue generado automáticamente por el Sistema.<br><b>Por favor no responder este mensaje.</b><br>INSCRA S.A - Le Bon</span>";
            
            EnviarMailAuth($correos, "SISTEMA DE ALERTA - PROBLEMA CON TRANSACCIONES", $mens, "Sistema Le Bon - Inscra S.A", $nom_destinatarios="");

    }

}
