var obj = function () {var o = this;$(document).ready(function () {o.initialize();});}
var cardSolprom = obj.prototype;

cardSolprom.initialize=function (){
  //this.info();
}
$(document).ready(function() {
  Core.CrearOffCanvas("offcanvasDetallePedidos","6","right"); 
  $(Core.paramWidget['cardSolprom']['destino']).append('<div class="col-md-3 col-sm-6" id="cardSolprom"></div>');
    //Core.CargarComponente("campana");
    var campana=Campana.TraerCampana("interna");

   $("#cardSolprom").load("static/widgets/cardSolprom/vista.html?14", function() {
    //materialadmin.AppOffcanvas.initialize();
    //$(".cardSolprom-campana").html(campana);
    $.ajax({type: "POST",url: "restWidget.php",dataType: 'json',async: true,data: { "modulo":"cardSolprom","metodo":"consultaSolprom","parametros":{"campana":campana}},
      success: function (data){
        var porcent=((data.fac/data.proyect)*100).toFixed(1).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

        $("#cardSolprom .spd").html(formatNumber.new(data.spd));
        $("#cardSolprom .spf").html(formatNumber.new(data.spf));
        $("#cardSolprom .nsv").html(formatNumber.new(data.nsv));
        $("#cardSolprom .progress-bar").css("width",parseInt(data.nsv)+"%");
        var points=[];
        var point_names=[];
        $.each(data.historico, function(index, val) {
           points.push(val.nsv);
           point_names.push(val.cod_cam);
        });
        var parent = $('.sparkline-solprom').closest('.card-body');
        var barWidth=10;
        var spacing = (parent.width() - (points.length * barWidth)) / points.length;
        var options = $('.sparkline-solprom').data();
        options.type = 'bar';
        options.barWidth = barWidth;
        options.barSpacing = spacing;
        options.height = $('.sparkline-solprom').height() + 'px';
        options.fillColor = false;

        options.tooltipFormat= '<span style="color: {{color}}">&#9679;</span> {{offset:names}} NS:{{value}}%'
        options.tooltipValueLookups= {names: point_names};

        $('.sparkline-solprom').sparkline(points, options);
        //console.log(points);

      }
    });
  });
});
