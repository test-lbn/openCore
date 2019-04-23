/**
 * 
 * @autor xxx xxxx (xxxxxxx@lebon.com.co)
 * @fecha   
 * @version 
 */
(function(document, window, $, Core) {
    (function() {

        return bars_2 = {

            Estacion: 0,
            items: [],
            mascosas: "",
            


            /*
            ┏━╸╻ ╻┏┓╻┏━╸╻┏━┓┏┓╻
            ┣╸ ┃ ┃┃┗┫┃  ┃┃ ┃┃┗┫
            ╹  ┗━┛╹ ╹┗━╸╹┗━┛╹ ╹
            ┏━┓╻ ╻┏━╸   ╻┏┓╻┏━╸╻┏━┓╻  ╻╺━┓┏━┓
            ┃┓┃┃ ┃┣╸    ┃┃┗┫┃  ┃┣━┫┃  ┃┏━┛┣━┫
            ┗┻┛┗━┛┗━╸   ╹╹ ╹┗━╸╹╹ ╹┗━╸╹┗━╸╹ ╹
            ┏━╸┏━┓┏┳┓┏━┓┏━┓┏┓╻┏━╸┏┓╻╺┳╸┏━╸┏━┓
            ┃  ┃ ┃┃┃┃┣━┛┃ ┃┃┗┫┣╸ ┃┗┫ ┃ ┣╸ ┗━┓
            ┗━╸┗━┛╹ ╹╹  ┗━┛╹ ╹┗━╸╹ ╹ ╹ ┗━╸┗━┛

            */

            Initialize: function() {
                var self = this;

                //id de la tabla en la vista
                target_tabla = $("#admin_costos_est-datatable tbody");
                template_tabla = target_tabla.find("tr.tr").clone(true);

                //forma de asignar un click a un boton
                $("#myBoton").on("click", function(event) {
                    self.funcionRest();
                });




            },

            /*
            ┏━┓┏━╸╺┳╸╻┏━╸╻┏━┓┏┓╻   
            ┣━┛┣╸  ┃ ┃┃  ┃┃ ┃┃┗┫   
            ╹  ┗━╸ ╹ ╹┗━╸╹┗━┛╹ ╹
            ┏━┓ ┏┓┏━┓╻ ╻
            ┣━┫  ┃┣━┫┏╋┛
            ╹ ╹┗━┛╹ ╹╹ ╹
            */

            ajaxRequest: function(data, type, method) {
                return $.ajax({
                    url: 'rest.php',
                    data: data,
                    type: type,
                    dataType: 'json',
                    async: true,
                    data: {
                        "modulo": "bars_2",
                        "metodo": method,
                        "token": getToken(),
                        "parametros": data
                    }
                })
            },

            //funcion de ejemplo
            funcionRest: function() {
                var self = this;
                self.ajaxRequest({
                            //"item": $('#txt_item').val(),
                            //"tipob": $("input[name='rd_tipo']:checked").val()
                        },
                        'post', 'funcionRest')
                    .done(function(response) {
                        toastr.info("Funcion ejecutada");
                        $('#myInput').val(response.campo1).change();
                        
                    });
            },

            //ejemplo de llenado de una tabla en la vista -- SE PUEDE BORRAR
            detalleCostosE: function() {
                var self = this;
                self.ajaxRequest({
                            "item": $('#txt_item').val(),
                            "tipob": $("input[name='rd_tipo']:checked").val()
                        },
                        'post', 'detalleCostosE')
                    .done(function(response) {
                        target_tabla.html(null);

                        $.each(response, function(id, value) {
                            var cloned_linea = template_tabla.clone(true);
                            cloned_linea.attr("id", "tr-" + id);
                            value.item = value.item.trim();
                            cloned_linea.find("td.item").html(value.item);
                            cloned_linea.find("td.descip").html(value.descripcion);
                            cloned_linea.find("td.refe").html(value.referencia);
                            cloned_linea.find("td.tipo_comp").html(value.tipo_compra);
                            cloned_linea.find("td.tipoinv").html(value.tipo_inv);
                            cloned_linea.find("td.costest").html(value.costo_est);
                            cloned_linea.find("td.costolist").html(value.costo_lista);
                            cloned_linea.find("td.unidad").html(value.unidad);
                            cloned_linea.find("td.cambio .inputcambio").attr('id', 'cambio_' + id);
                            cloned_linea.find("td.cambio .inputcambio").attr('data-item', value.item);
                            cloned_linea.find("td.cambio .inputcambio").attr('data-index', id);

                            //boton en la tabla
                            cloned_linea.find("td.histo i").attr('data-item', value.item);
                            cloned_linea.find("td.histo i").on("click", function(event) {
                                self.consultaHist($(event.srcElement).data('item'));
                            });

                            cloned_linea.find("td.observa .inputobserva").attr('id', 'observa_' + id);
                            cloned_linea.find("td.observa .inputobserva").attr('data-index', id);


                            target_tabla.append(cloned_linea);
                        });
                    });
            },






        }
    })()
    bars_2.Initialize()
})(document, window, jQuery, Core)

