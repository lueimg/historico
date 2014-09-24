$(document).ready(function(){

  
	
/* --- Para Gestion de Críticos --- */
  $("#motivo").change(function(){
       var id = $("#motivo").val();
       //submotivo en hidden para plugkin validate
       $("#h_submotivo").val("")
       $.ajax({
        type: "POST",
            url: "controladorHistorico/historicoController.php",
            data: {id:id,tipo:"submotivo"},
            dataType: "html",
            success: function (data) {
                $(".submotivo").html(data);
                $(".estado").html("");
                //para horarios
                $(".tabla_horarios").css("display","none")
                //si marcaron un horario tiene que limpiarse el color
                $("#horario td").each(function(){
                  color = $(this).css("background-color")
                  color = hexcolor(color)
                  if(color=="#008000"){
                    $(this).css({"background":"","color":""})
                  }
                })


                $(".tmovimientos").removeClass("moverxhorario")
                $("#btn_gestion_critico").removeClass("btn_mover_critico")
                //si en caso eligen horario y se equivocan limpio las cajas
                $("#fecha_agenda").val("")
                $("#horario_agenda").val("")
                $("#dia_agenda").val("")
            },
            error: function () {
                alert("Error");
            }
        });
    });


  $(document).on('change',"#submotivo", function(){
    var id_motivo = $("#motivo").val();
    var id_submotivo = $("#submotivo").val();
    $(".help-inline").html("")

    //si en caso eligen horario y se equivocan limpio las cajas
    $("#fecha_agenda").val("")
    $("#horario_agenda").val("")
    $("#dia_agenda").val("")
    //submotivo en hidden para pluglin validate
    $("#h_submotivo").val($(this).val())

       $.ajax({
        type: "POST",
            url: "controladorHistorico/historicoController.php",
            data: {id_motivo:id_motivo,id_submotivo:id_submotivo,tipo:"estado"},
            dataType: "html",
            success: function (data) {
                $(".estado").html(data);
                if(id_motivo==1 && id_submotivo==1){
                  //pintando horario reservado
                  horario_reservado()
                  $(".tabla_horarios").css("display","block")

                  $("#btn_gestion_critico").addClass("btn_mover_critico")
                  $(".tmovimientos").addClass("moverxhorario")
                }else{
                  $(".tabla_horarios").css("display","none")
                  //si marcaron un horario tiene que limpiarse el color
                  $("#horario td").each(function(){
                    color = $(this).css("background-color")
                    color = hexcolor(color)
                    if(color=="#008000"){
                      $(this).css({"background":"","color":""})
                    }
                  })

                  $("#btn_gestion_critico").removeClass("btn_mover_critico")
                  $(".tmovimientos").removeClass("moverxhorario")
                }
            },
            error: function () {
                alert("Error");
            }
        });
  });  

  //Para el Registro de Clientes Críticos
  $("#btn_gestion_critico").click(function(){
   
    $("#frm_gestion_critico").validate({
      errorClass:'help-inline',
      errorElement:'div',
      highlight: function (element, errorClass) { 
       $(element).removeClass(errorClass);
      },
      rules: {
        motivo: {
          required: true
        },
        h_submotivo: {
          required: true
        }
      },
      messages: {
        motivo:{
          required: "Seleccione un motivo",
        },
        h_submotivo:{
          required: "Seleccione un submotivo"
        }
      },
      submitHandler: function(form) {
        if($(".tabla_horarios").css("display")=="block"){
            if($("#fecha_agenda").val()==""){
                $(".fecha_error").html("Seleccione un horario").css({"display":"block","margin":"-14px 0 0 130px"})
            }else{
                $(".fecha_error").html("").css("display","none")
                registrarMovimientos()
                return false
            }
        }else{
            registrarMovimientos()
            return false
        }
      }
    });
       
  })

/* --- Para Registro clientes Críticos --- */
	$("form:not(.filter) :input:visible:enabled:first").css({'background-color' : '#c4e0f2'}).focus()

	$('#cr_nombre').filter_input({regex:"[a-zA-Z\ áéíóúñÑ]"});
	$('#cr_telefono').filter_input({regex:'[0-9]'});
	$('#cr_celular').filter_input({regex:'[*#0-9]'});
	$('#cr_observacion').filter_input({regex:'[0-9-#a-zA-Z\ áéíóúñÑ.,]'});

	
	$("#horario td").click(function(){

		color = $(this).css("background-color")
		color = hexcolor(color)
    total = $(this).attr("data-total")

		if(color!="#ff0000" && color!="#ffff00" && color!="#c4e0f2" && total>0){

			$("#horario td").each(function(){
				color = $(this).css("background-color")
				color = hexcolor(color)
				if(color!="#ff0000" && color!="#ffff00" && color!="#c4e0f2"){
					$(this).css({"background":"","color":""})
				}
			})

			$(this).css({"background":"green","color":"#fff"})
			$("#fecha_agenda").val($(this).attr("data-fec"))
			$("#horario_agenda").val($(this).attr("data-horario"))
			$("#dia_agenda").val($(this).attr("data-dia"))
      $("#hora_agenda").val($(this).attr("data-hora"))
			$(".horario .help-inline").css("display","none")
      $(".fecha_error").html("").css("display","none")

		}
	})

  //Para el Registro de Clientes Críticos
  $("#btn_registro").click(function(){
    $("#frm_criticos").validate({
      errorClass:'help-inline',
      errorElement:'div',
      highlight: function (element, errorClass) { 
       $(element).removeClass(errorClass);
      },
      rules: {
        cr_nombre: {
          required: true,
        },
        cr_telefono: {
          required: true
        },
        cr_observacion: {
          required: true
        },
        fecha_agenda: {
          required: true
        }
      },
      messages: {
        cr_nombre:{
          required: "Ingrese el nombre del cliente",
        },
        cr_telefono:{
          required: "Ingrese su n&uacute;mero Telef&oacute;nico"
        },
        fecha_agenda:{
          required: "Seleccione un horario a agendar"
        },
        cr_observacion: "Ingrese una observaci&oacute;n",
      },
      submitHandler: function(form) {
        registrarCriticos()
        return false
      }
    });
       
  })

	$('#cr_nombre').focus(
    function(){
        $(this).css({'background-color' : '#c4e0f2'});
    });

  $('#cr_nombre').blur(
    function(){
        $(this).css({'background-color' : ''});
    });

  $('#cr_telefono').focus(
    function(){
        $(this).css({'background-color' : '#c4e0f2'});
    })

  $('#cr_telefono').blur(
    function(){
        $(this).css({'background-color' : ''});
    });

  $('#cr_celular').focus(
    function(){
        $(this).css({'background-color' : '#c4e0f2'});
    })

  $('#cr_celular').blur(
    function(){
        $(this).css({'background-color' : ''});
    });

  $('#cr_observacion').focus(
    function(){
        $(this).css({'background-color' : '#c4e0f2'});
    })

  $('#cr_observacion').blur(
    function(){
        $(this).css({'background-color' : ''});
    });

  

})

function hexcolor(colorval) {
    var parts = colorval.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    delete(parts[0]);
    for (var i = 1; i <= 3; ++i) {
        parts[i] = parseInt(parts[i]).toString(16);
        if (parts[i].length == 1) parts[i] = '0' + parts[i];
    }
    color = '#' + parts.join('');
    return color
}

function horario_reservado(){
  //enfocando la fecha agenda ya reservada
  fec_ini = $("#fecha_agenda_ini").val()
  horario_ini = $("#horario_agenda_ini").val()

  if(fec_ini!="" && horario_ini!=""){
    $("#horario td").each(function(){
        fec = $(this).attr("data-fec")
        horario = $(this).attr("data-horario")
        if(fec_ini==fec && horario_ini==horario){
            $(this).css({"background":"#c4e0f2"})
        }
    })

  }
}

function registrarCriticos() {

   var parametros = $("#frm_criticos").serialize();
   $.ajax({
    type: "POST",
        url: "controladorHistorico/historicoController.php",
        data: parametros,
        dataType: "html",
        beforeSend: function(){
    // Handle the beforeSend event
        $('.modalPop').show();  
        },

        complete: function(){
        // Handle the complete event
        $('.modalPop').hide();
        },

        success: function (data) {
            alert(data);
            window.parent.jQuery('#dialog-criticos').dialog('close');
        },

        error: function () {
            alert("Error no se realizo el registro");
        }
    });

}

function registrarMovimientos() {

   var parametros = $("#frm_gestion_critico").serialize();

   indice = $("#indice").val()
   idfila = $("#idfila").val()
   fecha_agenda = $("#fecha_agenda").val()
   hora = $("#hora_agenda").val()
   motivo = $("#motivo option:selected").text()
   submotivo = $("#submotivo option:selected").text()
   estado = $("#h_estado").val()
   $.ajax({
    type: "POST",
        url: "controladorHistorico/historicoController.php",
        data: parametros,
        dataType: "html",
        beforeSend: function(){
    // Handle the beforeSend event
        $('.modalPop').show();  
        },

        complete: function(){
        // Handle the complete event
        $('.modalPop').hide();
        },

        success: function (data) {
          if(estado!="Liquidado"){
            window.parent.jQuery('.fagenda'+idfila).html(fecha_agenda)
            window.parent.jQuery('.fhorario'+idfila).html(hora)
            window.parent.jQuery('.fmotivo'+idfila).html(motivo)
            window.parent.jQuery('.fsubmotivo'+idfila).html(submotivo)
            window.parent.jQuery('.festado'+idfila).html(estado)
          }else{
            window.parent.jQuery('#clienteCriticos tr[data-indice="'+indice+'"]').remove();
          }
            alert(data);
            window.parent.jQuery('#dialog-gestion-criticos').dialog('close');

        },

        error: function () {
            alert("Error no se realizo el registro");
        }
    });

}
