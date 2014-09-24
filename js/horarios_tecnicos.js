/**
 * Created by lmori on 18/09/14.
 */


function generarExcel() {

    var finicio=$("#fecha_ini").val();
    var ffin=$("#fecha_fin").val();
    var quiebre = $(".slct_quiebre").val().join("','");
    var empresa = $(".slct_empresa").val()
    var cedula = $(".slct_celula").val()
    //alert($(".slct_tecnico").val());
    if($(".slct_tecnico").val()!= "" && $(".slct_tecnico").val() != null ){
        var tecnico = $(".slct_tecnico").val().join(",");
    }else{
        var tecnico = "";
    }


    window.open("horarios_tecnicos_excel.php?" +
    "finicio="+finicio
    + "&ffin="+ffin
    + "&quiebre="+quiebre
    + "&empresa="+empresa
    + "&cedula="+cedula
    + "&tecnico="+tecnico
    );

}



$().ready(function(){



    //exportar excel
    $(".exportar_excel").click(function(){
        generarExcel();
    });


    $("#slct_empresa").change(function(i)
    {
        //console.log($(this).val());
        //CARGA LAS CEDULAS
        $.ajax({
            url : "horarios_tecnicos.ajax.php",
            type : 'POST',
            async:false,//no ejecuta otro ajax hasta q este termine
            dataType : 'json',
            data : {
                idempresa:$(this).val()
            },
            beforeSend : function ( ) {
            },
            success : function ( obj ) {
                //console.log(obj)
                //console.log("success");
                //quito los options anteriores si existieran
                $("#slct_celula .added").remove();
                //agrego los options por empresa
                if(obj){
                    $("#slct_celula").append(obj.html);
                }
            },
            error: function(){
                alert("Hubo un problema, por favor actualize su pagina.")

            }
        });

    });


    $("#slct_celula").change(function(i)
    {
        //console.log($(this).val());
        //CARGA LAS CEDULAS
        $.ajax({
            url : "horarios_tecnicos.ajax.php",
            type : 'POST',
            async:false,//no ejecuta otro ajax hasta q este termine
            dataType : 'json',
            data : {
                idcedula:$(this).val()
            },
            beforeSend : function ( ) {
            },
            success : function ( obj ) {

                //quito los options anteriores si existieran
                $("#slct_tecnico .added").remove();
                //agrego los options por empresa
                if(obj) {
                    $("#slct_tecnico").append(obj.html);
                }

            },
            error: function(){
                alert("Hubo un problema, por favor actualize su pagina.")
            }
        });

    });




});