/**
 * Created by lmori on 09/10/14.
 */
$().ready(function(){
    //Uso de templates de underscore
  //  _.templateSettings = {interpolate: /\{\{(.+?)\}\}/g, evaluate: /\{!(.+?)!\}/g};
    window.templates = {};
    templates.SubModuloTemplate = _.template($("#SubModuloTemplate").html());

    //CARGA LOS MUDLOS Y LOS GUARDA EN UNA VARIABLE GLOBAL
    cargarSubmodulos();


    //LEVANTA LA VENTANA DE CREAER USUARIO
    $("#crearUsuario").click(function(e){
        e.preventDefault();

        //VALORES POR DEFAUL

        //todos los campos a estado inicial
        limpiarCampos("#modalFormCrearUsuario");


        //LEVANTA POPUP
        $("#modalFormCrearUsuario").dialog({
            modal : true,
            width:'auto',height:'600',
            title : 'Crear usuario'
        });

    });


    //GENERAR PASS DENTRO DEL DIALOG CREAR USUARIO
    $("#generarPass").click(function(){  $("#pass").val(randompass(6));    });

    //MODAL CERRAR
    $("#modalFormCrearUsuario #btn_salir").click(function(){
        $("#modalFormCrearUsuario").dialog("close");
    });


    //GUARDAR CREAR USUARIO
    $("#btn_guardar").click(function(){
        //validar inputs Y SELECTS
        var validacion = validarCampos("#modalFormCrearUsuario");
        if(validacion){  guardarUsuario();  }

    });


    //multiselect
    $("#empresas").multiselect({});
    $("#quiebres").multiselect({});
    //agrega submodulos en el dialog crear usuario
    $("#agregarModulo").click(agregarSubmodulos);


    $(".cri.emp.id").click(function(){
       var checked= $(this).is(":checked") ;
        if(checked){
            $("#cri_vis_"+$(this).attr("emp")).attr("disabled",false);
            $("#cri_vis_"+$(this).attr("emp")).prop('checked', true);

        }else{
            $("#cri_vis_"+$(this).attr("emp")).prop('checked', false);
            $("#cri_vis_"+$(this).attr("emp")).attr("disabled",true)
        }
    });


    //EDITAR USUARIO
    $(".editUser").click(function(e){
        e.preventDefault();

        var idusuario = $(this).attr("idusuario");

        //CARGAR DATA
        $.ajax({
            type: "POST",
            url: "modificar_usuarios_ajax.php",
            data: {
                accion:"getDataUsuarioAll",
                idusuario:idusuario
            },
            dataType: "Json",
            success: function (obj) {

                if(obj.error == 0 ){

                    limpiarCampos("#modalFormCrearUsuario");
                    $(".table-empresas-criticas input").prop("checked",false)
                    //CARGAR DATOS
                    //info

                    $("#idusuario").val(obj.data.info.id);
                    $("#nom").val(obj.data.info.nombre);
                    $("#ape").val(obj.data.info.apellido);
                    $("#login").val(obj.data.info.usuario);
                    $("#pass").val("******");
                    $("#dni").val(obj.data.info.dni);
                    $("#online").val(obj.data.info.online);
                    $("#perfil").val(obj.data.info.id_perfil);
                    $("#area").val(obj.data.info.id_area);
                    $("#estado").val(obj.data.info.status);
                    $("#empresa_principal").val(obj.data.info.ideecc)
                    //OTRAS EMPRESAS
                    window.usuariodata = obj.data;

                    if(obj.data.empresas.empresas != null){
                        $("#empresas option").attr("selected",false)

                        var empresas = obj.data.empresas.empresas.split(",");
                        $.each(empresas,function(i,e){
                            $("#empresas option[value="+e+"]").attr("selected","selected");
                        })
                        $("#empresas").multiselect("refresh");
                    }


                    // QUIEBRES
                    if(obj.data.quiebres.quiebres != null){
                        $("#quiebres option").attr("selected",false)

                        var quiebres = obj.data.quiebres.quiebres.split(",");
                        $.each(quiebres,function(i,e){
                            $("#quiebres option[value="+e+"]").attr("selected","selected");
                        })
                        $("#quiebres").multiselect("refresh");
                    }


                    //USUARIO CONTRATA CRITICO
                    if(obj.data.empcri.data != null){
                        $(".table-empresas-criticas input").prop("checked",false);
                        $(".table-empresas-criticas .cri.emp.vis ").attr("disabled",true);
                        var empcri = obj.data.empcri.data.split("|");
                        $.each(empcri,function(i,e){
                            var data = e.split("-");

                            $("#cri_emp_"+data[0]).prop("checked",true);
                            $("#cri_vis_"+data[0]).attr("disabled",false);
                            if(data[1]== "1"){
                                $("#cri_vis_"+data[0]).prop("checked",true);
                            }


                        });

                    }else{
                        $(".table-empresas-criticas input").prop("checked",true);
                    }


                    //CARGAR SUBMODULOS
                    if(obj.data.ususub.data != null){

                        var modulos = obj.data.ususub.data.split("|");
                        $.each(modulos, function(i,modulo){
                            //OBTENGO EL MODULO Y LOS SUBMODULOS REGISTRADOS
                            var data = modulo.split("-");
                            //AGREGO EL MODULO EN HTML
                            agregarSubmoduloHTML(data[0],$("#modulos option[value=" +data[0] +"]").text());
                            //DESELECCIONO TODOS LOS SUBMODULOS DE ESE MODULO
                            $("#submodulo_"+data[0]+" option").attr("selected",false);
                            //OBTENDO LOS SUBMODULOS REGISTRADOS EN ARRAY
                            var submodulos = data[1].split(",");
                            $.each(submodulos,function(i,e){
                                //SELECCIONO LOS SUBMODULOS
                                $("#submodulo_"+data[0]+" option[value="+ e+"]").attr("selected",true);
                            });
                            //REFRESCO EL MULTISELECT
                            $("#submodulo_"+data[0]).multiselect("refresh");
                        });

                    }


                    //LEVANTA POPUP
                    $("#modalFormCrearUsuario").dialog({
                        modal : true,
                        width:'auto',height:'600',
                        title : 'Editar Usuario'
                    });

                }else{
                    alert("Hubo un problema al cargar la data del usuario ," +
                    " por favor actualize su pagina e intente de nuevo");
                }

            },
            error: function () {
                alert("Error al cargar el usuario, " +
                "Por favor actualize su pagina y vuelva a intentar");
            }
        });


    })


    ActivarFiltros();
    ColocarFiltrosSeccionados();


});



guardarUsuario = function(){

    //OBTENER DATOS
    var id              = $("#idusuario").val();
    var nombre          = $("#nom").val();
    var apellido        = $("#ape").val();
    var usuario         = $("#login").val();
    var password        = $("#pass").val();
    var dni             = $("#dni").val();
    var online          = $("#online").val();
    var id_perfil       = $("#perfil").val();
    var id_area         = $("#area").val();
    var estado          = $("#estado").val();
    var ideecc          = $("#empresa_principal").val()
    var empresas        = $("#empresas").val().join();
    var quiebres = "";

    if($("#quiebres").val() != null){
        quiebres = $("#quiebres").val().join()
    }
    //EMPRESA CRITICOS
    var empresas_criticos= [];
    $(".table-empresas-criticas tr.cri-empresas").each(function(i,el){
        var emp = "";
        var vis = 0;
        if($(el).find(".cri.emp.id").is(':checked') == true){
            emp = $(el).find(".cri.emp.id").attr("emp");
            if($(el).find(".cri.emp.vis").is(':checked') == true){
                vis = 1
            }
            empresas_criticos.push(  emp + "-" + vis );
        }
    })

    var emp_cri = empresas_criticos.join("|");

    //OBTENIENDO SUBMODULOS
    var modulos = []
    $("#listModulosSeleccionados .modulo.selected").each(function(){
        var id = $(this).val()
        var submodulos =  $(this).parent().parent().find("#submodulo_"+id).val()
        if(submodulos != null ){
            modulos.push( id + "-" + submodulos.join(","));
        }

    })
    var usu_submodulos = modulos.join("|");


    $.ajax({
        type: "POST",
        url: "modificar_usuarios_ajax.php",
        data: {
            accion:"crearUsuario",
            id:id,
            nombre:nombre,
            apellido:apellido,
            usuario:usuario,
            password:password,
            dni:dni,
            online:online,
            id_perfil:id_perfil,
            id_area:id_area,
            estado:estado,
            ideecc:ideecc,
            quiebres:quiebres,
            empresas:empresas,
            emp_cri:emp_cri,
            usu_sub:usu_submodulos
        },
        dataType: "Json",
        success: function (obj) {

            if(obj.error == 0 ){

                alert(obj.msj);
               // window.location.reload();
                window.location.reload()

            }else{
                alert(obj.msj);
            }

        },
        error: function () {
            alert("Error al cargar al guardar el usuario, " +
            "Por favor actualize su pagina y vuelva a intentar");
        }
    });
}

validarCampos= function(idparent){
    var error = 0;
    $(idparent + " input").css
    $(idparent + " input").each(function(i,el){


        //si no es un campo de empresa critica, si no es un checkbox
        if($(el).hasClass("cri") == false && $(el).attr("id") != "idusuario"){

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


    if($("#empresas").val() == null ){
        alert("Debe seleccionar al menos una empresa secundaria");
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

    $(".submodulo-row").remove();

    //todos los quiebres por defecto
    $("#quiebres").multiselect("checkAll");
    $("#empresas").multiselect("checkAll");

    //TODAS LAS EMPRESAS CRITICAS ACTIVADAS POR DEFECTO
    $(".table-empresas-criticas input").prop('checked', true);
    $(".table-empresas-criticas input").attr("disabled",false)

}

function randompass(length){
    var letras = new Array();
    letras[1]="a";
    letras[2]="b";
    letras[3]="c";
    letras[4]="d";
    letras[5]="e";
    letras[6]="f";
    letras[7]="g";
    letras[8]="h";
    letras[9]="i";
    letras[10]="j";
    letras[11]="k";
    letras[12]="l";
    letras[13]="m";
    letras[14]="n";
    letras[15]="o";
    letras[16]="p";
    letras[17]="q";
    letras[18]="r";
    letras[19]="s";
    letras[20]="t";
    letras[21]="u";
    letras[22]="w";
    var pass = "";
    var numero = 0;
    for(var i=1;i<length;i++){
        numero = Math.floor(Math.random()*(22-1))+1;
        if(i==1){
            pass+=numero;
        }else{
            pass+=letras[numero];
        }
    }
    numero = Math.floor(Math.random()*(22-1))+1;
    pass+=numero;
    return pass;
}


function cargarSubmodulos(){



    $.ajax({
        type: "POST",
        url: "modificar_usuarios_ajax.php",
        data: {
            accion:"getSubmodulosJSON"
        },
        dataType: "Json",
        success: function (obj) {
            window.webpsi = {}
            webpsi.submodulos = obj
            webpsi.submByModulos = _.groupBy(webpsi.submodulos,function(obj){  return obj.idmodulo })

        },
        error: function () {
            alert("Error al cargar selectes");
        }
    });
}

agregarSubmodulos = function(e){
    e.preventDefault();

    var modulo = $("#modulos").val();
    var submodulo = $("#submodulo_"+modulo).length;

    if(modulo != null && submodulo == 0 )
    {
        //var items = webpsi.submByModulos[$("#modulos").val()]
        //
        //var html = templates.SubModuloTemplate({id:$("#modulos option:selected").val(),
        //nombre:$("#modulos option:selected").text(),
        //items:items})
        //
        //$("#listModulosSeleccionados").append(html);
        //$("#submodulo_"+$("#modulos option:selected").val()).multiselect({});
        //$("#submodulo_"+$("#modulos option:selected").val()).multiselect("checkAll");
        agregarSubmoduloHTML($("#modulos option:selected").val() , $("#modulos option:selected").text() )

    }


}

function agregarSubmoduloHTML(id , text ){
    var items = webpsi.submByModulos[id]

    var html = templates.SubModuloTemplate({id:id,
        nombre:text,
        items:items})

    $("#listModulosSeleccionados").append(html);
    $("#submodulo_"+id).multiselect({position: { my: 'left bottom',at: 'left top'}});
    $("#submodulo_"+id).multiselect("checkAll");

    $(".submodulo-row").css("background","#fff");
    $(".remove-row").click(RemoverRow);
}


RemoverRow = function(){
    //REMOVE SUBMODULO-ROW
    $(this).parent().parent().remove();

}


ActivarFiltros = function(){

    /*
    * <select name="filtros" id="filtros">
     <option value="">Seleccione un Filtro</option>
     <option value="ape">Apellidos y nombre</option>
     <option value="perfil">Por Perfil</option>
     <option value="area">Por area</option>
     <option value="empresa">Por Empresa</option>
     </select>
     */



    $("#filtros").change(function(){
        var widget_filter = "";
        var element = $(this).val();
        var html_inner = "";
        var es_input = false
        if(element == "ape" || element == "dni"){
            widget_filter = "<input type='text' id='filter-value' >";
            html_inner= ""
            es_input = true;
            if(element == "ape"){
                var placehoder= "Ingrese Apellido y/o nombre..."
            }else{
                var placehoder= "Ingrese numero de dni..."

            }
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
            var url_ajax = "modificar_usuarios_ajax.php";
            var url = "modificar_usuarios.php";
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

        var url_ajax = "modificar_usuarios_ajax.php";
        var url = "modificar_usuarios.php";
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