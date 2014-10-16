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


    $( "#asistenciaFecha" ).datepicker();
    $( "#asistenciaFecha" ).datepicker("option", "dateFormat", "dd/mm/yy");
    $("#asistenciaFecha").val(fecha.diaHoy);

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

        if(ids_tecnicos != null ){

            $.ajax({
                type: "POST",
                url: url_ajax,
                data: {
                    accion:"MostrarAsistencia",
                    ids_tecnicos: ids_tecnicos.join(),
                    fecha:$("#asistenciaFecha").val()
                }
                ,
                success: function (obj) {

                    if(obj == null){
                        this.error();
                    }else {
                        MostrarAsistenciaHTML(obj);
                    }

                },
                error: function () {
                    alert("Error al cargar al guardar la asistencia, " +
                    "Por favor actualize su pagina y vuelva a intentar");
                }
            });


        }else{
            alert("Debe seleccionar al menos 1 tecnico para mostrar los registros de asistencia")
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


MostrarAsistenciaHTML = function(data){

    //AGREGAR LOS DETALLES
    $("#asistenciaTecnicos").html(data)

}
