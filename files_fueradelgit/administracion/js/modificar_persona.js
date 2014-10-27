/**
 * Created by lmori on 20/10/14.
 */

var url_ajax = "modificar_persona_ajax.php";
var url = "modificar_persona.php";

$().ready(function(){
    //Uso de templates de underscore
    //  _.templateSettings = {interpolate: /\{\{(.+?)\}\}/g, evaluate: /\{!(.+?)!\}/g};
    window.templates = {};
    templates.numContactHTML = _.template($("#NumeroContactoPersona").html());

    //CARGA LOS MUDLOS Y LOS GUARDA EN UNA VARIABLE GLOBAL
    //cargarSubmodulos();
    $("#grupo").multiselect({});
    $("#grupo option").prop("selected",false)
    $("#grupo").multiselect("refresh");


    //LEVANTA LA VENTANA DE CREAER USUARIO
    $("#crearUsuario").click(function(e){
        e.preventDefault();

        //VALORES POR DEFAUL

        //todos los campos a estado inicial
        limpiarCampos("#modalFormCrearPersona");


        //LEVANTA POPUP
        $("#modalFormCrearPersona").dialog({
            modal : true,
            width:'auto',height:'auto',
            title : 'Crear Persona'
        });

    });




    //MODAL CERRAR
    $("#modalFormCrearPersona #btn_salir").click(function(){
        $("#modalFormCrearPersona").dialog("close");
    });


    //GUARDAR CREAR USUARIO
    $("#btn_guardar").click(function(){
        //validar inputs Y SELECTS
        var validacion = validarCampos("#modalFormCrearPersona");
        if(validacion){  guardarUsuario();  }

    });

    //EDITAR USUARIO
    $(".editUser").click(function(e){
        e.preventDefault();

        var idusuario = $(this).attr("idusuario");

        //CARGAR DATA
        $.ajax({
            type: "POST",
            url: url_ajax,
            data: {
                accion:"getDataUsuarioAll",
                idusuario:idusuario
            },
            dataType: "Json",
            success: function (obj) {
                if(obj.error == 0 ){
                    limpiarCampos("#modalFormCrearPersona");

                    //CARGAR DATOS
                    $("#idpersona").val(obj.data.id);
                    $("#nom").val(obj.data.nombre);
                    $("#ape_pa").val(obj.data.apellido_p);
                    $("#ape_ma").val(obj.data.apellido_m);
                    $("#dni").val(obj.data.dni);
                    $("#empresa_principal").val(obj.data.id_eecc);
                    $("#estado").val(obj.data.estado);

                    //GRUPO
                    //$("#grupo").val(obj.data.id_grupo);
                    debugger
                    if(obj.data.grupos != null){
                        $("#grupo option").attr("selected",false)

                        var grupo = obj.data.grupos.split(",");
                        $.each(grupo,function(i,e){
                            $("#grupo option[value="+e+"]").attr("selected","selected");
                        })
                        $("#grupo").multiselect("refresh");
                    }


                    //AGREGAR NUMERO
                    if(obj.data.numeros != "" && obj.data.numeros != null ){
                        var numeros = obj.data.numeros.split("|")
                        numeros.forEach(function(i){
                            var data = i.split("-");
                            agregarNumeroContacto(data[0],data[1]);
                        });
                    }

                    //LEVANTA POPUP
                    $("#modalFormCrearPersona").dialog({
                        modal : true,
                        width:'auto',height:'auto',
                        title : 'Editar persona'
                    });

                }else{
                    alert(obj.msj);
                }
            },
            error: function () {
                alert("Error al cargar la persona, " +
                "Por favor actualize su pagina y vuelva a intentar");
            }
        });


    });


    $("#agregarNumContact").click(function(){
        var numero = "";
        agregarNumeroContacto(numero,0);


    });



    ActivarFiltros();
    ColocarFiltrosSeccionados();


});



guardarUsuario = function(){

    //OBTENER DATOS
    var id              = $("#idpersona").val();
    var nombre          = $("#nom").val();
    var ape_pa        = $("#ape_pa").val();
    var ape_ma        = $("#ape_ma").val();
    var dni             = $("#dni").val();

    var ideecc          = $("#empresa_principal").val()
    var estado          = $("#estado").val();

    var grupo = "";
    if($("#grupo").val() != null){
        grupo = $("#grupo").val().join()
    }
    //numero de contacto
    var numerosContacto = $(".numContact-row").map(function(){
        var num = $(this).find(".numcontact").val();
        var selected = $(this).find(".numcontactrb:checked").val();
        if(!selected){
            selected = 0
        }
        return num+ "-" +selected

    })



    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {
            accion:"crearPersona",
            id:id,
            nombre:nombre,
            ape_pa:ape_pa,
            ape_ma:ape_ma,
            dni:dni,
            grupo:grupo,
            estado:estado,
            ideecc:ideecc,
            numeros:numerosContacto.get().join()
        },
        dataType: "Json",
        success: function (obj) {

            if(obj != null){
                if(obj.error == 0 ){
                    alert(obj.msj);
                    // window.location.reload();
                    window.location.reload()
                }else{
                    alert(obj.msj);
                }
            }else{
                this.error()
            }
        },
        error: function () {
            alert("Error al cargar al guardar la persona, " +
            "Por favor actualize la pagina y vuelva a intentar");
        }
    });
}

validarCampos= function(idparent){
    var error = 0;
    //$(idparent + " input").css
    $(idparent + " input").each(function(i,el){


        //si no es un campo de empresa critica, si no es un checkbox
        if($(el).hasClass("cri") == false && $(el).attr("id") != "idpersona"){

            var value  = $(el).val();
            if(value == ""){
                $(el).css("border-color","red");
                alert("Debe llenar todos los datos");
                console.log($(el))
                error++;
                return false;
            }else{
                $(el).css("border-color","");
            }
        }
    });
    if(error > 0 ){
        return false;
    }

    if($("#grupo").val() == null ){
        alert("Debe seleccionar al menos un grupo");
        return false
    }

    return true;

}

//limpiar campos
function limpiarCampos(idpadre){
    $(idpadre + " input").val("");
    $(idpadre + " input").each(function(i,el){
        $(el).css("border-color","");
    });
    $(idpadre + " select").val("");

    $(".numContact-row").remove();

}

RemoverRow = function(){
    //REMOVE SUBMODULO-ROW
    $(this).parent().parent().remove();

    //VALIDANDO SELECCION AUTOMATICA
    if($(".numcontactrb:checked").length == 0){
        $(".numcontactrb").eq(0).prop("checked","checked")
    }

}


ActivarFiltros = function(){

    $("#filtros").change(function(){
        var widget_filter = "";
        var element = $(this).val();
        var tipo = $(this).find("option:selected").attr("tipo");
        var html_inner = "";
        var es_input = false;

        if(tipo == "input"){
            widget_filter = "<input type='text' id='filter-value' >";
            html_inner= ""
            es_input = true;
            var placehoder= $(this).find("option:selected").attr("ph")

        }else if( element != ""){
            widget_filter = '<select name="select-filter" id="filter-value"></select>';
            html_inner = $("#"+element).html()

        }else{
            widget_filter= ""
            html_inner = ""
        }

        $(".filter-seleccionado").html(widget_filter);
        $("#filter-value").html(html_inner);
        if(es_input){
            $("#filter-value").attr("placeholder",placehoder);
        }

        $("#filter-value").keypress(function(e){
            if(e.which === 13){ $(".actionFiltrar").trigger("click") }
        });

    });

    $(".actionFiltrar").click(function(){

        var element = $("#filtros").val();

        if(element == ""){
            alert("Debe seleccionar un filtro");
        }else{
            var filter = element;
            var value =$("#filter-value").val();

            $.ajax({
                type: "POST",
                url: url_ajax,
                data: {
                    accion: "registrarFiltros",
                    filter: filter,
                    value:value
                },
                success: function (response) {
                    window.location.href = url
                },
                error: function () {
                    alert("Hubo un problema, por favor actualize su pagina. Gracias.")
                }
            });


        }

    });


    $(".actionReiniciar").click(function(){


        $.ajax({
            type: "POST",
            url: url_ajax,
            data: {
                accion: "reiniciarFiltros"
            },
            success: function (response) {
                window.location.href = url
            },
            error: function () {
                alert("Hubo un problema, por favor actualize su pagina. Gracias.")
            }
        });

    });


}


ColocarFiltrosSeccionados = function(){

    if(filtrosactivos != null){
        $("#filtros").val(filtrosactivos.filter);
        $("#filtros").trigger("change");
        $("#filter-value").val(filtrosactivos.value);
    }
}

cambiarEstado = function(idpersona,valor){

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {
            accion: "cambiarEstado",
            idpersona:idpersona,
            valor:valor
        },
        success: function (response) {
           if(response == "1"){
               location.reload();
           }else{
               alert(response)
           }
        },
        error: function () {
            alert("Hubo un problema, por favor actualize su pagina. Gracias.")
        }
    });

}

var num_row = 0;
agregarNumeroContacto = function(numero,checked){

    num_row++;

    if(checked == 1){
        checked = "checked"
    }else{
        checked = ""

    }
    var html = templates.numContactHTML({numero:numero,checked:checked,num_row:num_row});
    $("#tableContactoPersona").append(html);

    $(".numContact-row").css("background","#fff");
    $(".remove-row").click(RemoverRow);


    //SELECCIONO AL PRIMERO AUTOMATICAMENTE
    if($(".numContact-row").length == 1){
        $(".numContact-row .numcontactrb").prop("checked","checked")
    }



}