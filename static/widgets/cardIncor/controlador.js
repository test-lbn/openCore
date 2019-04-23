var obj = function () {var o = this;$(document).ready(function () {o.initialize();});}
var cardIncor = obj.prototype;

cardIncor.initialize=function (){
  //this.info();
}
$(document).ready(function() {
  $(Core.paramWidget['cardIncor']['destino']).append('<div class="col-md-3 col-sm-6" id="cardIncor"></div>');
    //Core.CargarComponente("campana");
    var campana=Campana.TraerCampana("interna");

   $("#cardIncor").load("static/widgets/cardIncor/vista.html?15", function() {
    //materialadmin.AppOffcanvas.initialize();
    //$(".cardIncor-campana").html(campana);
    $.ajax({type: "POST",url: "restWidget.php",dataType: 'json',async: true,data: { "modulo":"cardIncor","metodo":"consultaIncor","parametros":{"campana":campana}},
      success: function (data){
        $(".knob .incorporaciones")
          .val(data.por_incor)
          .attr("data-original-title","Facturado:"+formatNumber.new(data.incor)+"\nProject:"+formatNumber.new(data.incor_pro))
          .tooltip();
        $(".knob .ventas")
          .val(data.por_venta)
          .attr("data-original-title","Facturado:"+formatNumber.new(data.venta)+"\nProject:"+formatNumber.new(data.venta_pro))
          .tooltip();
        $(".knob .margen").val(data.margen);

        //$(".knob .incorporaciones").popover({html:true,placement: 'right',content: "Facturado: "+data.incor+"<br>Project: "+data.incor_pro});
        //$(".knob .ventas").popover({html:true,placement: 'right',content: "Facturado: "+data.venta+"<br>Project: "+data.venta_pro});
        
        $('.dial').each(function () {
          var options = materialadmin.App.getKnobStyle($(this));
          options.height=60;
          //console.log(options);
          $(this).knob(options);
        });
      }
    });
  });
});
