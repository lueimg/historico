/**
 * Created by lmori on 15/10/14.
 */
var url_ajax = "asistenciaTecnicos_ajax.php";
var url = "asistenciatecnicos.php";
$().ready(function(){


    //CARGAR MULTISELECT TECNICO
    agregarTecnicos(tecnicosOT);
    $slct_tecnico = $("#tecnicos_ot").multiselect().multiselectfilter();
    $slct_tecnico.multiselect("checkAll");


    $( "#asistenciaFecha" ).datepicker({maxDate: "+0D"});
    $( "#asistenciaFecha" ).datepicker("option", "dateFormat", "dd/mm/yy");
    $("#asistenciaFecha").val(fecha.diaHoy);

    $( "#asisFechaFin" ).datepicker({maxDate: "+0D"});
    $( "#asisFechaFin" ).datepicker("option", "dateFormat", "dd/mm/yy");
    $("#asisFechaFin").val(fecha.diaHoy);

    //FILTRO EMPRESA
    $("#fil_empresa").change(function () {
        var idempresa = $(this).val();
        $("#fil_celula option").hide();
        $("#fil_celula option").attr("selected",false);
        if(idempresa != ""){
            $("#fil_celula option[idemp="+idempresa+"]").show();
            $("#fil_celula option").eq(0).show()
            $("#fil_celula option").eq(0).text("Todos " + $("#fil_empresa option:selected").text());
            //actualizamos tecnicos
            $("#tecnicos_ot option").remove();
            agregarTecnicos(_.where(tecnicosOT,{id_empresa:idempresa}));
        }else{
            $("#fil_celula option").eq(0).text(" -- Todos --");
            $("#fil_celula option").show();

            //actualizamos tecnicos
            $("#tecnicos_ot option").remove();
            agregarTecnicos(tecnicosOT);
        }
    });


    $("#fil_celula").change(function () {
        var idcelula = $(this).val();

        if(idcelula!= ""){
            //removemos todos los tecnicos
            $("#tecnicos_ot option").remove();
            //agregamos los tecnicos correspondientes
            agregarTecnicos(_.where(tecnicosOT,{idcedula:idcelula}));
        }else{
            //revisamos si empresaa estaba seleccionado
            var idempresa = $("#fil_empresa").val();
            if(idempresa != ""){
                agregarTecnicos(_.where(tecnicosOT,{id_empresa:idempresa}));
            }else{
                agregarTecnicos(tecnicosOT);
            }

        }

    });

    $("#reiniciarFiltros").click(function(){
        $("#fil_empresa").val("")
        $("#fil_empresa").trigger("change");
        $("#asistenciaFecha").val(fecha.diaHoy);
        $("#asistenciaTecnicos").html("")
    });



    $("#mostrarAsistencia").click(function(){

        var ids_tecnicos = $("#tecnicos_ot").val();
        var tipo_repo = $(".reporteTipo:checked").val();

        if(ids_tecnicos != null ){
            if(tipo_repo == "repo_dia"){
                var accion = "MostrarAsistencia"
            }else{
                var accion = "MostrarAsistenciaRangoFechas"
            }

            $.ajax({
                type: "POST",
                url: url_ajax,
                data: {
                    accion:accion,
                    ids_tecnicos: ids_tecnicos.join(),
                    fecha:$("#asistenciaFecha").val(),
                    fechaFin:$("#asisFechaFin").val()
                },
                success: function (obj) {

                    if(obj == null){
                        this.error();
                    }else if(obj == "1"){
                        alert("La fecha Final no puede ser menor que la fecha de Inicio")
                    }
                    else {
                        $("#asistenciaTecnicos").html(obj);
                    }
                },
                error: function () {
                    alert("Error al cargar al guardar la asistencia, " +  "Por favor actualize su pagina y vuelva a intentar");
                }
            });


        }else{
            alert("Debe seleccionar al menos 1 tecnico para mostrar los registros de asistencia")
        }

    });


    $("#tipo-reporte").click(function(){

        var tipo_repo = $(".reporteTipo:checked").val();
        if(tipo_repo == "repo_dia"){
            $(".fechaFin").hide("fast");
        }else{
            $(".fechaFin").show("fast");
            $("#asisFechaFin").val(fecha.diaHoy);
        }
    });

    $("#AsisExportExcel").click(function(){
       //EXPORTAR EXCEL
        //DATOS A ENVIAR

        var tipo_repo = $(".reporteTipo:checked").val();
        var ids_tecnicos = $("#tecnicos_ot").val();
        var fecha = $("#asistenciaFecha").val();
        var fechaFin = $("#asisFechaFin").val();


        if(ids_tecnicos != null ) {
            if (tipo_repo == "repo_dia") {
                var accion = "MostrarAsistencia"
            } else {
                var accion = "MostrarAsistenciaRangoFechas"
            }

            window.open("asistenciaTecnicos_ajax.php?" +
                "accion="+accion
                + "&excel=1"
                + "&ids_tecnicos="+ids_tecnicos.join()
                + "&fecha="+fecha
                + "&fechaFin="+fechaFin
                + "&empresa="+$("#fil_empresa option:selected").text()
                + "&celula="+$("#fil_celula option:selected").text()
            );

        }else{
            alert("Debe seleccionar al menos 1 Tecnico");
        }




    });



//tpls
    window.templates = {};
    templates.TecnicoDetalleAsistencia = _.template($("#TecnicoDetalleAsistencia").html());
 });

agregarTecnicos = function(lista){
    _.each(lista,function(item){
        var option = "<option value='" +  item.id + "'>" +  item.nombre_tecnico + "</option>";
        $("#tecnicos_ot").append(option);
    });

    $("#tecnicos_ot").multiselect("refresh")
    $("#tecnicos_ot").multiselect("checkAll")

}



