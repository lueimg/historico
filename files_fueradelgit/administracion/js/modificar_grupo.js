/**
 * Created by lmori on 20/10/14.
 */

var url_ajax = "modificar_grupo_ajax.php";
var url = "modificar_grupo.php";
var formCreateEdit = "modalFormCreateEdit";
$().ready(function(){
    //Uso de templates de underscore
    //  _.templateSettings = {interpolate: /\{\{(.+?)\}\}/g, evaluate: /\{!(.+?)!\}/g};
    window.templates = {};

    //LEVANTA LA VENTANA DE CREAER USUARIO
    $("#popupADD").click(function(e){
        e.preventDefault();
        //todos los campos a estado inicial
        limpiarCampos("#"+formCreateEdit);
        //LEVANTA POPUP
        $("#"+formCreateEdit).dialog({
            modal : true,            width:'auto',height:'auto',            title : 'Crear Grupo'
        });
    });

    //MODAL CERRAR
    $("#"+formCreateEdit+" #btn_salir").click(function(){
        $("#"+formCreateEdit).dialog("close");
    });

    //GUARDAR CREAR USUARIO
    $("#btn_guardar").click(function(){
        //validar inputs Y SELECTS
        var validacion = validarCampos("#"+formCreateEdit);
        if(validacion){  guardarData();  }

    });

    //EDITAR USUARIO
    $(".popupEDIT").click(function(e){
        e.preventDefault();

        var idExistente = $(this).attr("idExistente");

        //CARGAR DATA
        $.ajax({
            type: "POST",
            url: url_ajax,
            data: {
                accion:"getDataRegistro",
                idExistente:idExistente
            },
            dataType: "Json",
            success: function (obj) {
                if(obj.error == 0 ){
                    limpiarCampos("#"+formCreateEdit);

                    //CARGAR DATOS
                    $("#idExistente").val(obj.data.id);
                    $("#grupo").val(obj.data.grupo);
                    $("#estado").val(obj.data.estado);

                    //LEVANTA POPUP
                    $("#"+formCreateEdit).dialog({
                        modal : true,  width:'auto',height:'auto',    title : 'Editar persona'
                    });

                }else{
                    alert(obj.msj);
                }
            },
            error: function () {
                alert("Error al cargar el grupo, " +
                "Por favor actualize su pagina y vuelva a intentar");
            }
        });


    });





});



guardarData = function(){

    //OBTENER DATOS
    var id              = $("#idExistente").val();
    var grupo          = $("#grupo").val();
    var estado          = $("#estado").val();




    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {
            accion:"InsertarRegistro",
            id:id,
            grupo:grupo,
            estado:estado

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
            alert("Error al cargar al guardar el grupo, " +
            "Por favor actualize la pagina y vuelva a intentar");
        }
    });
}

validarCampos= function(idparent){
    var error = 0;
    //$(idparent + " input").css
    $(idparent + " input").each(function(i,el){

        //si no es un campo de empresa critica, si no es un checkbox
        if($(el).hasClass("cri") == false && $(el).attr("id") != "idExistente"){

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



    return true;

}

//limpiar campos
function limpiarCampos(idpadre){
    $(idpadre + " input").val("");
    $(idpadre + " input").each(function(i,el){
        $(el).css("border-color","");
    });

    $(idpadre + " select").val("");




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

cambiarEstado = function(idExistente,valor){

    $.ajax({
        type: "POST",
        url: url_ajax,
        data: {
            accion: "cambiarEstado",
            idExistente:idExistente,
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
