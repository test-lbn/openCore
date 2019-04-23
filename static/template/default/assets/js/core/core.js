//compatibilidad contains para navegadores
String.prototype.contains = function(it) { return this.indexOf(it) != -1; };

//configuraciones generales para los mensajes
toastr.options.progressBar = true;


$(document).ajaxSend(function(event, request, settings) {
  $('#loading-indicator').show();
});

$(document).ajaxComplete(function(event, request, settings) {
  $('#loading-indicator').hide();
});

$(document).ajaxError(function(event,xhr, ajaxOptions, thrownError) {
  if(ajaxOptions.url.contains("rest.php")){//errores solo del servicio local
    var obj = jQuery.parseJSON( xhr.responseText);
    Core.ErrorSistema("<h3>Error:"+obj.codigo+"</h3><p>"+obj.mensaje+"<br>Archivo: "+obj.archivo+"<br>Linea: "+obj.linea+"</p>");
    if(xhr.status=='401'){
      setTimeout(function(){ document.location='index.php?modulo=login'; }, 1000);
    }
  }

});

var objCore = function () {var o = this; $(document).ready(function () { o.initialize();});}
var Core = objCore.prototype;

Core.initialize=function (){
this.paramWidget;
this.CargarComponente(compo);
this.CargarWidget(widget,params);
this.FormToJSON(selector);
this.ErrorSistema(mensaje);
this.GetPermiso(programa, opcion);
this.GetPermisos(programa);
this.GetUrlParameter(sParam);
this.CrearOffCanvas(id,ancho,lr);
}
Core.paramWidget={};
Core.CargarComponente= function(compo){
  $.getScript("core/componentes/"+compo+"/controlador.js");
}

Core.TraerCampanaActual = function(tipo,callback){
  $.ajax({
    type:"POST",
    url: "rest.php",
    dataType: 'json',
    async: true,
    data:{"tipo":"CORE","modulo":"campana","metodo":"traerCampana","token":getToken(),"parametros":{"tipo":tipo}}
  }).then( function (data) {
    callback(null, data.campana)
  }).fail( function (error) {
    callback(error, null)
  })
}

Core.CargarWidget= function(widget,param){
  this.paramWidget[widget]=param;
  $.getScript("static/widgets/"+widget+"/controlador.js",function(){
    //do something
  });
}
Core.FormToJSON = function ( selector ){
 var form = {};
 $(selector).find(':input[name]:checked').each( function() {
   var self = $(this);
   var name = self.attr('name');
   if (form[name]) {
    form[name] = form[name] + ',' + self.val();
  }
  else {
    form[name] = self.val();
  }
});

 var TXTinputs =$(selector).find('input[type=text],input[type=email],input[type=password],input[type=hidden],input[type=number],select').filter(function() {
   return this.value!='';
 });

 TXTinputs.each( function() {
   var self = $(this);
   var name = self.attr('name');
   if (form[name]) {
    form[name] = form[name] + ',' + self.val();
  }
  else {
    form[name] = self.val();
  }
});
 return form;
}

Core.ErrorSistema= function (mensaje){
  toastr.options.positionClass = "toast-bottom-left";
  toastr.options.closeButton = true;
  toastr.options.timeOut = 0;
  toastr.options.extendedTimeOut = 0;
  toastr.options.onclick = true;
  toastr.error(mensaje);
}

Core.GetPermiso= function(programa,opcion){
 var permiso='';
 $.ajax({type: "POST",url: "rest.php",dataType: 'json',async: false,data: { "modulo":"programa","metodo":"getPermiso","token":getToken(),"parametros":{"programa":programa,"opcion":opcion}},success: function (data){permiso= data.permiso;}});
 return permiso;
}
Core.GetPermisos= function(programa){
 var permisos={};
 $.ajax({type: "POST",url: "rest.php",dataType: 'json',async: false,data: { "modulo":"programa","metodo":"getPermiso","token":getToken(),"parametros":{"programa":programa}},success: function (data){permisos= data.listado;}});
 return permisos;
}

Core.GetUrlParameter= function (sParam){
  var sPageURL = window.location.search.substring(1);
  var sURLVariables = sPageURL.split('&');
  for (var i = 0; i < sURLVariables.length; i++){
    var sParameterName = sURLVariables[i].split('=');
    if (sParameterName[0] == sParam){
      return sParameterName[1];
    }
  }
}
Core.CrearOffCanvas= function(id,ancho,lr){
  $(".offcanvas-"+lr).append('<div class="offcanvas-pane width-'+ancho+'" id="'+id+'">'+
                '<div class="offcanvas-head">'+
                  '<header></header>'+
                  '<div class="offcanvas-tools">'+
                    '<a class="btn btn-icon-toggle pull-right" data-dismiss="offcanvas">'+
                      '<i class="md md-close"></i>'+
                    '</a>'+
                  '</div>'+
                '</div>'+
                '<div class="offcanvas-body">'+
                  'Offcanvas body content ~'+
                '</div>'+
            '</div>');
}

$(document).ready(function() {
  cargarInfoCore();
});

function getToken(){
  var info=$.parseJSON(localStorage.getItem('usuario'));
  if(info!=null)
    return info.token;
}

function cargarInfoCore(){
  var info=$.parseJSON(localStorage.getItem('usuario'));
  if(info==null){

  }else{
    $(".profile-info").html('<span class="hidden-xs">'+info.nombre.toLowerCase()+" "+info.apellidos.toLowerCase()+"</span><small>"+info.usuario+"</small>");
    $(".profile-img").attr("style","background-image: url(static/images_usuarios/"+info.usuario+".png)");
    var menu=localStorage.getItem('menu');
    if(menu==null){
      menuProgramas();
    }else{
      $("#main-menu").html(menu);
    }
  }
}

function logOut(){
 localStorage.removeItem('menu');
 localStorage.removeItem('usuario');
 $.ajax({
  type: "POST",
  url: "rest.php",
  dataType: 'json',
  async: true,
  data: { "modulo":"login",
  "metodo":"logOut"
},
success: function (data){
  document.location='index.php';
}
});
}

//CARGA DE WIDGETS DEL SISTEMA
Core.CargarWidget("frmChangePassword");
Core.CargarWidget("notificaciones");
//Core.CargarWidget("GenerarCat");

/*$( "#headerSearch" ).change(function() {

  var cant_li = $(".dropdown-menu").children("li").size();
  console.log('-> '+cant_li);
  
  setTimeout(function(){ 
    $( '.navbar-search' ).removeClass( "open" );
  }, 100);
});*/

//Busqueda de aplicaciones
$(".navbar-search input").keyup(function(event) {
  var defecto="";
  var nuevo="";
  var busqueda=$(this).val();
  var programas=JSON.parse(localStorage.getItem('programas'));

  /*var resu=jQuery.grep(programas, function( n, i ) {
    console.log(n.programa+' / '+busqueda);
    return ( n.programa.contains(busqueda) ||  n.descripcion.contains(busqueda));
  });*/

  var resu = programas.filter(function (v) {
      if(v.programa.toUpperCase().indexOf(busqueda.toUpperCase()) != -1 || v.descripcion.toUpperCase().indexOf(busqueda.toUpperCase()) != -1){
        return v;
      }      
  });

  var link="";
  $(".navbar-search .dropdown-menu").html("");
  $(".navbar-search").removeClass("open");
  
  if(busqueda!=''){
    $.each(resu, function(index, val) {

      link='index.php?modulo='+val.programa;
      if(defecto==""){defecto=link;}

       $(".navbar-search .dropdown-menu").append('<li class="tile"><a href="'+link+'">'+val.descripcion+' <span class="badge">'+val.programa+'</span></a></li>');
    });
  }
  if(defecto!=''){
    $(".navbar-search").addClass("open");
  }
  if(event.which == 13) {
    event.preventDefault();
  }
}).keydown(function( event ) {
  if ( event.which == 13 ) {
    event.preventDefault();
    var url = $(".navbar-search .dropdown-menu li a").prop("href");
    if(url != ""){
      document.location=url;
    } else {
      document.location=$(this).val().toLowerCase().replace(".php", "")+".php";
    }
    event.stopPropagation();
    return false;
  }
}).change(function(){
  var cant_li = $(".dropdown-menu").children("li").size()-4;

  setTimeout(function(){
    if (parseInt(cant_li)>0) {
      //console.log('entra -> '+parseInt(cant_li));
      $( '.navbar-search' ).removeClass( "open" );
    }
  }, 500); 
});


//MENU PRINCIPAL
var MainMenu='';
var ArrayProgramas=[];

function menuProgramas(){
  $.ajax({
    type: "POST",
    url: "rest.php",
    dataType: 'json',
    async: true,
    data: { "modulo":"programa",
    "metodo":"getMenuProgramas",
    "token":getToken()
  },
  success: function (data){
    menuProgramaSub(data,0);
    localStorage.setItem('menu',MainMenu);
    localStorage.setItem('programas',JSON.stringify(ArrayProgramas));
    $("#main-menu").html(MainMenu);
  }
});
}

function MaysPrimera(string){
  return string.charAt(0).toUpperCase() + string.slice(1);
}

function menuProgramaSub(data,nivel){
  $.each(data, function(index, vmenu) {
    MainMenu+='<li class="gui-folder">'+
    '<a href="javascript:void(0);">';
    if(nivel==0){MainMenu+='<div class="gui-icon"><i class="fa '+vmenu.icono+'"></i></div>';}
    MainMenu+='<span class="title">'+MaysPrimera(vmenu.nombre.toLowerCase())+'</span>'+
    '</a>';
    MainMenu+='<ul>';
    menuProgramaSub(vmenu.sub,1);

    MainMenu+='</ul>';
    MainMenu+='<ul>';
    $.each(vmenu.progs, function(index, vprog) {
      var link='';
      link='index.php?modulo='+vprog.programa.toLowerCase();
      MainMenu+='<li>'+
      '<a href="'+link+'"><span class="title">'+MaysPrimera(vprog.descripcion.toLowerCase())+'</span></a>'+
      '</li>';
      ArrayProgramas.push({"programa":MaysPrimera(vprog.programa.toLowerCase()),"descripcion":MaysPrimera(vprog.descripcion.toLowerCase()),"nuevo":vprog.nuevo});
    });
    MainMenu+='</ul>';
    MainMenu+='</li>';
  });
}

var formatNumber = {
   separador: ".", // separador para los miles
   sepDecimal: ',', // separador para los decimales
   formatear:function (num){
   num +='';
   var splitStr = num.split('.');
   var splitLeft = splitStr[0];
   var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
   var regx = /(\d+)(\d{3})/;
   while (regx.test(splitLeft)) {
   splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
   }
   return this.simbol + splitLeft +splitRight;
   },
   new:function(num, simbol){
   this.simbol = simbol ||'';
   return this.formatear(num);
   }
}

//funcion para agregar formato mm/dd/aaaa al objeto Date FBP
Date.prototype.mmddyyyy = function() {
  var yyyy = this.getFullYear();
   var mm = this.getMonth() < 9 ? "0" + (this.getMonth() + 1) : (this.getMonth() + 1); // getMonth() is zero-based
   var dd  = this.getDate() < 10 ? "0" + this.getDate() : this.getDate();
   return "".concat(mm).concat("/").concat(dd).concat("/").concat(yyyy);
};


/**
 * Number.prototype.format(n, x, s, c) FBP 06/12/16
 * 
 * @param integer n: Tamaño del decimal
 * @param integer x: Tamaño de la parte entera
 * @param mixed   s: Separador de miles
 * @param mixed   c: Separador decimal
 */
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};



//sha1
var _0xb80a=["","\x6C\x65\x6E\x67\x74\x68","\x63\x68\x61\x72\x43\x6F\x64\x65\x41\x74","\x70\x75\x73\x68","\x74\x6F\x4C\x6F\x77\x65\x72\x43\x61\x73\x65"];function sha1(_0x78e0x2){var _0x78e0x3=function(_0x78e0x4,_0x78e0x5){var _0x78e0x6=(_0x78e0x4<<_0x78e0x5)|(_0x78e0x4>>>(32-_0x78e0x5));return _0x78e0x6;};var _0x78e0x7=function(_0x78e0x8){var _0x78e0x2=_0xb80a[0];var _0x78e0x9;var _0x78e0xa;for(_0x78e0x9=7;_0x78e0x9>=0;_0x78e0x9--){_0x78e0xa=(_0x78e0x8>>>(_0x78e0x9*4))&0x0f;_0x78e0x2+=_0x78e0xa.toString(16);};return _0x78e0x2;};var _0x78e0xb;var _0x78e0x9,_0x78e0xc;var _0x78e0xd= new Array(80);var _0x78e0xe=0x67452301;var _0x78e0xf=0xEFCDAB89;var _0x78e0x10=0x98BADCFE;var _0x78e0x11=0x10325476;var _0x78e0x12=0xC3D2E1F0;var _0x78e0x13,_0x78e0x14,_0x78e0x15,_0x78e0x16,_0x78e0x17;var _0x78e0x18;_0x78e0x2=unescape(encodeURIComponent(_0x78e0x2));var _0x78e0x19=_0x78e0x2[_0xb80a[1]];var _0x78e0x1a=[];for(_0x78e0x9=0;_0x78e0x9<_0x78e0x19-3;_0x78e0x9+=4){_0x78e0xc=_0x78e0x2[_0xb80a[2]](_0x78e0x9)<<24|_0x78e0x2[_0xb80a[2]](_0x78e0x9+1)<<16|_0x78e0x2[_0xb80a[2]](_0x78e0x9+2)<<8|_0x78e0x2[_0xb80a[2]](_0x78e0x9+3);_0x78e0x1a[_0xb80a[3]](_0x78e0xc);};switch(_0x78e0x19%4){case 0:_0x78e0x9=0x080000000;break ;;case 1:_0x78e0x9=_0x78e0x2[_0xb80a[2]](_0x78e0x19-1)<<24|0x0800000;break ;;case 2:_0x78e0x9=_0x78e0x2[_0xb80a[2]](_0x78e0x19-2)<<24|_0x78e0x2[_0xb80a[2]](_0x78e0x19-1)<<16|0x08000;break ;;case 3:_0x78e0x9=_0x78e0x2[_0xb80a[2]](_0x78e0x19-3)<<24|_0x78e0x2[_0xb80a[2]](_0x78e0x19-2)<<16|_0x78e0x2[_0xb80a[2]](_0x78e0x19-1)<<8|0x80;break ;;};_0x78e0x1a[_0xb80a[3]](_0x78e0x9);while((_0x78e0x1a[_0xb80a[1]]%16)!=14){_0x78e0x1a[_0xb80a[3]](0)};_0x78e0x1a[_0xb80a[3]](_0x78e0x19>>>29);_0x78e0x1a[_0xb80a[3]]((_0x78e0x19<<3)&0x0ffffffff);for(_0x78e0xb=0;_0x78e0xb<_0x78e0x1a[_0xb80a[1]];_0x78e0xb+=16){for(_0x78e0x9=0;_0x78e0x9<16;_0x78e0x9++){_0x78e0xd[_0x78e0x9]=_0x78e0x1a[_0x78e0xb+_0x78e0x9]};for(_0x78e0x9=16;_0x78e0x9<=79;_0x78e0x9++){_0x78e0xd[_0x78e0x9]=_0x78e0x3(_0x78e0xd[_0x78e0x9-3]^_0x78e0xd[_0x78e0x9-8]^_0x78e0xd[_0x78e0x9-14]^_0x78e0xd[_0x78e0x9-16],1)};_0x78e0x13=_0x78e0xe;_0x78e0x14=_0x78e0xf;_0x78e0x15=_0x78e0x10;_0x78e0x16=_0x78e0x11;_0x78e0x17=_0x78e0x12;for(_0x78e0x9=0;_0x78e0x9<=19;_0x78e0x9++){_0x78e0x18=(_0x78e0x3(_0x78e0x13,5)+((_0x78e0x14&_0x78e0x15)|(~_0x78e0x14&_0x78e0x16))+_0x78e0x17+_0x78e0xd[_0x78e0x9]+0x5A827999)&0x0ffffffff;_0x78e0x17=_0x78e0x16;_0x78e0x16=_0x78e0x15;_0x78e0x15=_0x78e0x3(_0x78e0x14,30);_0x78e0x14=_0x78e0x13;_0x78e0x13=_0x78e0x18;};for(_0x78e0x9=20;_0x78e0x9<=39;_0x78e0x9++){_0x78e0x18=(_0x78e0x3(_0x78e0x13,5)+(_0x78e0x14^_0x78e0x15^_0x78e0x16)+_0x78e0x17+_0x78e0xd[_0x78e0x9]+0x6ED9EBA1)&0x0ffffffff;_0x78e0x17=_0x78e0x16;_0x78e0x16=_0x78e0x15;_0x78e0x15=_0x78e0x3(_0x78e0x14,30);_0x78e0x14=_0x78e0x13;_0x78e0x13=_0x78e0x18;};for(_0x78e0x9=40;_0x78e0x9<=59;_0x78e0x9++){_0x78e0x18=(_0x78e0x3(_0x78e0x13,5)+((_0x78e0x14&_0x78e0x15)|(_0x78e0x14&_0x78e0x16)|(_0x78e0x15&_0x78e0x16))+_0x78e0x17+_0x78e0xd[_0x78e0x9]+0x8F1BBCDC)&0x0ffffffff;_0x78e0x17=_0x78e0x16;_0x78e0x16=_0x78e0x15;_0x78e0x15=_0x78e0x3(_0x78e0x14,30);_0x78e0x14=_0x78e0x13;_0x78e0x13=_0x78e0x18;};for(_0x78e0x9=60;_0x78e0x9<=79;_0x78e0x9++){_0x78e0x18=(_0x78e0x3(_0x78e0x13,5)+(_0x78e0x14^_0x78e0x15^_0x78e0x16)+_0x78e0x17+_0x78e0xd[_0x78e0x9]+0xCA62C1D6)&0x0ffffffff;_0x78e0x17=_0x78e0x16;_0x78e0x16=_0x78e0x15;_0x78e0x15=_0x78e0x3(_0x78e0x14,30);_0x78e0x14=_0x78e0x13;_0x78e0x13=_0x78e0x18;};_0x78e0xe=(_0x78e0xe+_0x78e0x13)&0x0ffffffff;_0x78e0xf=(_0x78e0xf+_0x78e0x14)&0x0ffffffff;_0x78e0x10=(_0x78e0x10+_0x78e0x15)&0x0ffffffff;_0x78e0x11=(_0x78e0x11+_0x78e0x16)&0x0ffffffff;_0x78e0x12=(_0x78e0x12+_0x78e0x17)&0x0ffffffff;};_0x78e0x18=_0x78e0x7(_0x78e0xe)+_0x78e0x7(_0x78e0xf)+_0x78e0x7(_0x78e0x10)+_0x78e0x7(_0x78e0x11)+_0x78e0x7(_0x78e0x12);return _0x78e0x18[_0xb80a[4]]();}
