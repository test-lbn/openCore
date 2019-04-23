var obj = function () {var o = this;$(document).ready(function () {o.initialize();});}
var cardPedidos = obj.prototype;

cardPedidos.initialize=function (){
  //this.info();
}
$(document).ready(function() {
  Core.CrearOffCanvas("offcanvasDetallePedidos","6","right"); 
  $(Core.paramWidget['cardPedidos']['destino']).append('<div class="col-md-3 col-sm-6" id="cardPedidos"></div>');
    //Core.CargarComponente("campana");
    var campana=Campana.TraerCampana("interna");

   $("#cardPedidos").load("static/widgets/cardPedidos/vista.html?23", function() {
    //materialadmin.AppOffcanvas.initialize();
    //$(".cardPedidos-campana").html(campana);
    $.ajax({type: "POST",url: "restWidget.php",dataType: 'json',async: true,data: { "modulo":"cardPedidos","metodo":"consultaPedidos","parametros":{"campana":campana}},
      success: function (data){
        var porcent=((data.fac/data.proyect)*100).toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

        $("#cardPedidos .facturados").html(formatNumber.new(data.fac));
        $("#cardPedidos .proceso").html(formatNumber.new(data.ped));
        $("#cardPedidos .project").html(formatNumber.new(data.proyect));
        $("#cardPedidos .project-percent").html(porcent+"%" );
        $("#cardPedidos .progress-bar").css("width",parseInt(porcent)+"%");
        var points=[];
        var point_names=[];
        $.each(data.historico, function(index, val) {
           points.push(val.fac);
           point_names.push(val.cod_cam);
        });
        var options = $('.sparkline-pedidos').data();
        options.type = 'line';
        options.width = '100%';
        options.height = $('.sparkline-pedidos').height() + 'px';
        options.fillColor = false;
        options.tooltipFormat= '<span style="color: {{color}}">&#9679;</span> {{offset:names}} Fac:{{prefix}}{{y}}{{suffix}}'
        options.tooltipValueLookups= {names: point_names};

        $('.sparkline-pedidos').sparkline(points, options);
        //console.log(points);

      }
    });
  });
});
