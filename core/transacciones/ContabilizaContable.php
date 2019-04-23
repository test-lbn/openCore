<?php
require_once(CORE."transacciones/Comprobante.php");

/**
 * clase para contabilizar transacciones contables
 *
 * @author       Alvaro Pulgarin <aepulgarin@lebon.com.co>
 * @copyright    Alvaro Pulgarin 2014-03-20
 * @category     Core
 * @package      Clases
 * @version      1.0
 * 
 * TODO:
 * 1. moneda extrangera
 * 2. como validar cuenta_t
 */

class ContabilizaContable extends Comprobante{
     public $comprob; 
     public $numero; 
     public $fecha="today"; 
     public $cuenta; 
     public $nit; 
     public $centro; 
     public $valor; 
     public $deb_cre; 
     public $descripcion=''; 
     public $tipo_doc=''; 
     public $numero_doc='0'; 
     public $conciliado=''; 
     public $cuenta_t=''; 
     public $base=0; 
     public $fecha_grab="today"; 
     public $nota=''; 
     public $f_vence="today"; 
     public $concepto=''; 
     public $activi='';
    
    public $errores = array();
    function __construct($comprob='',$numero=0){
        parent::__construct();
        $this->comprob=trim($comprob);
        $this->numero=$numero;
        
        if($comprob==''){
            $this->agregarError("COMPROB","comprobante vacio");
        }

        if($comprob!=''){
            $consulta="SELECT comprob from comprobante where comprob='$comprob'";
            $mval=$this->lee_uno($consulta);
            if($mval->comprob=='') $this->agregarError("COMPROB","comprobante no existe ({$this->comprob})");
        }
        
        $this->grabador = $_SESSION['usuario'];
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

    }
    function contabilizar(){
        

        if($this->numero==0 || $this->numero==''){
            $this->NuevoConsecutivo();
            $this->ejecuta_query("UPDATE TMPmovi set numero={$this->numero}");    
        }
        $this->Validar();

        $consulta="SELECT distinct numero from TMPmovi";
        $mnumeros=$this->lee_todo($consulta);

        for ($i=0; $i <count($mnumeros) ; $i++) { 
            $numeros[]=$mnumeros[$i]->numero;
            
        }
        $this->numeros=implode(",",$numeros);
        
        
        $this->ejecuta_query("UPDATE TMPmovi set numero_doc=case when numero_doc=0 then {$this->numero} else numero_doc end");
        $this->ejecuta_query("UPDATE TMPmovi set tipo_doc=case when nvl(tipo_doc,'')='' then '{$this->comprob}' else tipo_doc end");

        $this->ejecuta_query("INSERT into movi (comprob,numero,fecha,cuenta,nit,centro,deb_cre,descripcion,tipo_doc,numero_doc,conciliado,cuenta_t,base,fecha_grab,grabador,nota,f_vence,concepto,activi,valor) SELECT comprob,numero,fecha,cuenta,nit,centro,deb_cre,descripcion,tipo_doc,numero_doc,conciliado,cuenta_t,base,fecha_grab,grabador,nota,f_vence,concepto,activi,sum(valor) from TMPmovi group by 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19");

        #SCUENTAS
        $m=$this->lee_todo("SELECT a.cuenta,a.nit,b.pide_nit,sum(case when deb_cre='D' then valor else -valor end) as valor,sum(base) as base
        FROM TMPmovi a, cuentas b  WHERE a.cuenta=b.cuenta group by 1,2,3");
        for ($i=0; $i <count($m) ; $i++) { 
            $pide_nit=trim($m[$i]->pide_nit);
            $cuenta=$m[$i]->cuenta;
            $nit=$m[$i]->nit;
            $valor=$m[$i]->valor;
            $base=$m[$i]->base;
            $mval=$this->lee_todo("SELECT nit from scuentas where nit='$nit' and ano_mes='0' and cuenta='$cuenta'");
            if(count($mval)>0){
                $this->ejecuta_query("UPDATE scuentas set saldo_hoy=saldo_hoy+$valor where nit='$nit' and ano_mes='0' and cuenta='$cuenta'");
            }else if($pide_nit=='S'){
                $this->ejecuta_query("INSERT INTO scuentas (cuenta,nit,ano_mes,base,saldo_hoy) values ('$cuenta','$nit','0','$base','$valor')");
            }
            
            $mval=$this->lee_todo("SELECT nit from scuentas where nit='0' and ano_mes='0' and cuenta='$cuenta'");
            if(count($mval)>0){
                $this->ejecuta_query("UPDATE scuentas set saldo_hoy=saldo_hoy+$valor where nit='0' and ano_mes='0' and cuenta='$cuenta'");
            }else{
                $this->ejecuta_query("INSERT INTO scuentas (cuenta,nit,ano_mes,base,saldo_hoy) values ('$cuenta','0','0','$base','$valor')");
            }


        }
        
        #DOCS
        $m=$this->lee_todo("SELECT a.tipo_doc,a.numero_doc ,a.nit ,a.centro,a.cuenta,fecha as f_emision,f_vence,sum(case when deb_cre='D' then valor else -valor end) as valor 
        FROM TMPmovi a, cuentas b  WHERE a.cuenta=b.cuenta and b.pide_fact='S' group by 1,2,3,4,5,6,7");
        for ($i=0; $i <count($m) ; $i++) { 
            $tipo_doc=$m[$i]->tipo_doc;
            $numero_doc=$m[$i]->numero_doc;
            $nit=$m[$i]->nit;
            $centro=$m[$i]->centro;
            $cuenta=$m[$i]->cuenta;
            $f_emision=$m[$i]->f_emision;
            $f_vence=$m[$i]->f_vence;
            $valor=$m[$i]->valor;
            $mval=$this->lee_todo("SELECT nit from docs where tipo_doc='$tipo_doc' and numero_doc='$numero_doc' and nit='$nit' and cuenta='$cuenta'");
            if(count($mval)>0){
                $this->ejecuta_query("UPDATE docs set saldo_doc=saldo_doc+$valor where tipo_doc='$tipo_doc' and numero_doc='$numero_doc' and nit='$nit' and cuenta='$cuenta'");
            }else{
                $this->ejecuta_query("INSERT INTO docs (cuenta, nit, tipo_doc, numero_doc,v_inicial, saldo_doc,v_retencion,numero_tercero,centro, f_emision, f_vence,vendedor) values ('$cuenta','$nit','$tipo_doc','$numero_doc','$valor','$valor',0,0,'$centro','$f_emision','$f_vence',1)");
            }
        }
        
        #SCUENTAP
        $m=$this->lee_todo("SELECT a.cuenta,a.centro ,sum(case when deb_cre='D' then valor else -valor end) as valor    
        FROM TMPmovi a, cuentas b  WHERE a.cuenta=b.cuenta and b.pide_centro='S' group by 1,2");
        for ($i=0; $i <count($m) ; $i++) { 
            $cuenta=$m[$i]->cuenta;
            $centro=$m[$i]->centro;
            $valor=$m[$i]->valor;
            $mval=$this->lee_todo("SELECT cuenta from scuentap where cuenta='$cuenta' and centro='$centro' and ano_mesp='0'");
            if(count($mval)>0){
                $this->ejecuta_query("UPDATE scuentap set saldo_hoyp=saldo_hoyp+$valor where cuenta='$cuenta' and centro='$centro' and ano_mesp='0'");
            }else{
                $this->ejecuta_query("INSERT INTO scuentap (cuenta, centro, ano_mesp, saldo_hoyp) values ('$cuenta','$centro','0','$valor')");
            }
        }

        if(count($mValida)>0){
               print_r($mValida);
            die("Genero negativos, proceso cancelado, intente  nuevamente");
        }

        $this->ejecuta_query("drop table TMPmovi");
        $this->ejecuta_sp("execute procedure spactualizasaldo_doc_gio2('{$this->comprob}','{$this->numero}','{$this->numero}')");
        

    }

    function validar(){
        $consulta="SELECT numero,sum(case when deb_cre='D' then valor else -valor end) as balanceo from TMPmovi group by 1";
        $mval=$this->lee_todo($consulta);
        for ($i=0; $i <count($mval) ; $i++) { 
            $numero=$mval[$i]->numero;
            $balanceo=$mval[$i]->balanceo;
            if($balanceo<>0)
                $this->agregarError("BALANCE $numero","comprobante desbalanceado (".number_format($balanceo).")");
        }
        if(count($mval)==0){
            $this->agregarError("DATOS","NADA QUE PROCESAR..".print_r($this,true));   
        }
        


        #VALIDACION DE ERRORES
        if(count($this->error)>0){
            foreach ($this->error as $key => $value) {
                $error.=$key.":".$value.PHP_EOL;
            }
            die("PROBLEMA CON LA TRANSACCION: $this->comprob $this->bod_ori $this->bod_des <pre>".PHP_EOL.$error);
        }
    }
    function Agregar(){
        if($this->fecha!="today") $this->fecha="'".$this->fecha."'";
        if($this->fecha_grab!="today") $this->fecha_grab="'".$this->fecha_grab."'";
        if($this->f_vence!="today") $this->f_vence="'".$this->f_vence."'";

        if($this->nit=='') $this->agregarError("NIT","campo requerido para contabilizar");
        if($this->cuenta=='') $this->agregarError("CUENTA","campo requerido para contabilizar");
        if($this->centro=='') $this->agregarError("CENTRO","campo requerido para contabilizar");
        if($this->deb_cre=='' || ($this->deb_cre<>'D' && $this->deb_cre<>'C')) $this->agregarError("DEB_CRE","debito o credito errado ({$this->deb_cre})");
        if(floatval($this->valor)<=0) $this->agregarError("VALOR","debe ser valores positivos >0  ({$this->valor})");

        if($this->numero!=''){
            $consulta="SELECT numero from movi where comprob='$this->comprob' and numero=$this->numero";
            $mval=$this->lee_uno($consulta);
            if($mval->numero>0) $this->agregarError("NUMERO","numero de transaccion ya existe ({$this->numero})");
        }
        #Valida NIT
        $consulta="SELECT nit from nits where nit={$this->nit}";
        $mval=$this->lee_uno($consulta);
        if($mval->nit=='') $this->agregarError("NIT","nit no existe ({$this->nit})");

        #Valida  CENTRO
        $consulta="SELECT centro from centros where centro='{$this->centro}'";
        $mval=$this->lee_uno($consulta);
        if($mval->centro=='') $this->agregarError("CENTRO","centro no existe ({$this->centro})");

        #Valida CUENTA
        $consulta="SELECT cuenta,pide_base from cuentas where cuenta='{$this->cuenta}' and subdivide='N'";
        $mval=$this->lee_uno($consulta);
        if($mval->cuenta=='') $this->agregarError("CUENTA","cuenta no existe o no es cuenta final ({$this->cuenta})");
        if($mval->pide_base=='S' && $this->base==0){
            $this->agregarError("BASE","la cuenta ({$this->cuenta}) requiere base");
        }

        #Valida fecha
        $consulta="SELECT 'N' as valido FROM iden where ultimo_cierre>={$this->fecha}";
        $mval=$this->lee_uno($consulta);
        if($mval->valido=='N') $this->agregarError("FECHA","Perido cerrado");        
        
        #Inserta en temporal
        $this->ejecuta_query("insert into TMPmovi (comprob,numero,fecha,cuenta,nit,centro,valor,deb_cre,descripcion,tipo_doc,numero_doc,conciliado,cuenta_t,base,fecha_grab,grabador,nota,f_vence,concepto,activi) values 
                ('{$this->comprob}','{$this->numero}',{$this->fecha},trim('{$this->cuenta}'),'{$this->nit}',trim('{$this->centro}'),'{$this->valor}','{$this->deb_cre}','{$this->descripcion}',trim('{$this->tipo_doc}'),'{$this->numero_doc}','{$this->conciliado}','{$this->cuenta_t}','{$this->base}',{$this->fecha_grab},'{$this->grabador}','{$this->nota}',{$this->f_vence},'{$this->concepto}','{$this->activi}')");
    }

    function agregarError($tipo,$detalle){
        $this->error[$tipo] = $detalle;
    }

}
