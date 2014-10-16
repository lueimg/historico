$(document).ready(function(){

  if(window.parent.window.page == "visorHistorico"){
      $(".agendarTecnico").hide()
      $(".agendarCelula").hide()
  }
  
/* --- Para Gestion de Críticos --- */
  $("#motivo_registro").change(function(){
    motivo = $("#motivo_registro").val()
       if(motivo==1){
          $("#segun_motivo #horario").removeClass("ocultar")
       }else{
          $("#segun_motivo #horario").addClass("ocultar")
       }
  })

  $("#ck_penalizable").change(function(){
       if($("#ck_penalizable"). attr("checked")){
          $("#penalizable").removeAttr("disabled")
       }else{
          $("#penalizable").attr("disabled","disabled")
       }
  })

  $("#motivo").change(function(){
       var id = $("#motivo").val();
       $("#tec_movimiento").css("display","none")
       $("#tec_movimiento").html()
       $("#h_submotivo").val("")

       $.ajax({
        type: "POST",
            url: "controladorHistorico/historicoController.php",
            data: {id:id,tipo:"submotivo"},
            dataType: "html",
            dataType: "Json",
            success: function (data) {

              if(data.error){
                submotivo  = '';
              }else{
                submotivo  = '<select name="submotivo" id="submotivo">';
                submotivo += '<option value="">Seleccione Submotivo</option>';
                if(id==3){
                  $.each(data, function (i) {
                    if(this.id==3){
                      submotivo += '<option value="' + this.id + '">' + this.submotivo + '</option>';
                    }
                  })  
                }else{
                  $.each(data, function (i) {
                    submotivo += '<option value="' + this.id + '">' + this.submotivo + '</option>';
                  }) 
                }
                
              }

              $(".submotivo").html("");
              $(".submotivo").html(submotivo);

                $(".estado").html("");
                $(".tecnico").html("");
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

                $("#liquidaciones").addClass("liquidado")//para mantenimiento liquidados
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
    var id_empresa = $("#id_empresa").val();
    var id_gestion = $("#id_gestion").val();
    $(".help-inline").html("")

    //si en caso eligen horario y se equivocan limpio las cajas
    $("#fecha_agenda").val("")
    $("#horario_agenda").val("")
    $("#dia_agenda").val("")
    //submotivo en hidden para pluglin validate
    $("#h_submotivo").val($(this).val())

    $(".tecnico").html("");

    if(id_submotivo==1 || id_submotivo==2){
      $("#mostrar_tecnicos").removeClass("ocultar")
    }else{
      $("#mostrar_tecnicos").addClass("ocultar")
    }

       $.ajax({
        type: "POST",
            url: "controladorHistorico/historicoController.php",
            data: {id_motivo:id_motivo,id_submotivo:id_submotivo,id_gestion:id_gestion,id_empresa:id_empresa,tipo:"estado"},
            dataType: "Json",
            success: function (data) {

                $(".estado").html("")
                $(".tecnico").html("")

                if(data.error){
                  //si no trae datos de estado
                  estado  = '';
                  $(".tabla_horarios").css("display","none")
                    $("#horario td").each(function(){
                      color = $(this).css("background-color")
                      color = hexcolor(color)
                      if(color=="#008000"){
                        $(this).css({"background":"","color":""})
                      }
                    })

                    $("#btn_gestion_critico").removeClass("btn_mover_critico")
                    $(".tmovimientos").removeClass("moverxhorario")
                    $("#liquidaciones").addClass("liquidado")
                    $("#tec_movimiento").css("display","none")
                    $("#tec_movimiento").html()
                }else{

                  estado  = '<select name="estado" id="estado">';
                  $.each(data.estado, function (i) {
                    if(id_motivo!=5 && this.id==8){//ya q puede ser observaciones y haya un registro anterior sin tecnico 
                         //
                    }else{
                      estado += '<option value="' + this.id + '">' + this.estado + '</option>';
                    }
                       //para la data basica de contacto si esta vacio el nombre y demas
                      //alert(this.id+"-"+$("#nombre_critico").val())
                      if((this.id==1 && $("#nombre_critico").val()=="") || (this.id==8 && $("#nombre_critico").val()=="") ||
                         (this.id==2 && $("#nombre_critico").val()=="")){//para pendiente cuando esta vacio los datos
                          $(".datos .datos_contacto").removeClass("ocultar")//para que lo llenen si quieren
                      }else{
                          $(".datos .datos_contacto").addClass("ocultar")
                      }

                       //submotivo en hidden para plugkin validate
                       tec = $("#tecnico_movimiento").val()
                       if(this.id==9 || this.id==10 || this.id==3 || this.id==19){
                          //$("span#tec_movimiento").html("111")
                          $("#tec_movimiento").css("display","inline")
                          $("#tec_movimiento").html(tec)
                       }else{
                          $("#tec_movimiento").css("display","none")
                          $("#tec_movimiento").html()
                       }
                  })

                  //Para Técnicos y Horarios en vez de validar por estado agendado valido por data traida en el json
                  tecnico = '';
                  if(data.tecnico!=""){
                    tecnico = '<select name="tecnico" id="tecnico">';
                     $.each(data.tecnico, function (i) {
                       tecnico += '<option value="' + this.nombre_tecnico + '">' + this.nombre_tecnico + '</option>';
                     })
                    tecnico += '</select>';
                    tecnico += '<span class="checkbox" ><input type="checkbox" value="si" name="flag_tecnico" id="flag_tecnico" style="margin:0 5px 0 5px">Tecnico Entregado</span>';

                      //configuración para el estado Agendado
                      if(id_motivo==1 && id_submotivo==1){
                        //horario_reservado()
                        $(".tabla_horarios").css("display","block")
                        $("#btn_gestion_critico").addClass("btn_mover_critico")
                        $(".tmovimientos").addClass("moverxhorario")
                        $("#liquidaciones").addClass("liquidado")//Escondiendo campos de liquidación
                      }



                  }else{

                    //Si el estado es diferente de Agendado:
                    //Si marcaron un horario y cambian de estado tiene que limpiarse el color
                    $(".tabla_horarios").css("display","none")
                    $("#horario td").each(function(){
                      color = $(this).css("background-color")
                      color = hexcolor(color)
                      if(color=="#008000"){
                        $(this).css({"background":"","color":""})
                      }
                    })

                    $("#btn_gestion_critico").removeClass("btn_mover_critico")
                    $(".tmovimientos").removeClass("moverxhorario")
                     $("#liquidaciones").addClass("liquidado")
                    //Si es liquidado mostrar los campos de Liquidación

                    $.each(data.estado, function (i) {
                       if(this.id==3 || this.id==19){//para el mantenimiento de liquidados
                          $("#liquidaciones").removeClass("liquidado")

                          $("#cr_nombre").removeAttr("disabled")
                          $("#cr_telefono").removeAttr("disabled")
                          $("#cr_celular").removeAttr("disabled")
                          $(".datos .datos_contacto").addClass("ocultar")
                       }else if(this.id==9 || this.id==10 || this.id==20){
                        $(".datos .datos_contacto").addClass("ocultar")
                       }
                    })
                  }
                  $(".estado").html(estado);
                  $(".tecnico").html(tecnico);
                }
            },
            error: function () {
                alert("Error");
            }
        });
  });  
  
    $(document).on('change',"#estado", function(){
       var id_motivo = $("#motivo").val();
       var id_submotivo = $("#submotivo").val();
       var id_estado = $("#estado").val();
       var id_empresa = $("#id_empresa").val();

       //submotivo en hidden para plugkin validate
       //$("#h_submotivo").val("")
      //if(id_motivo==1 && id_submotivo==1 && id_estado==1){
      if(id_estado==1){
         $.ajax({
          type: "POST",
              url: "controladorHistorico/historicoController.php",
              data: {id_motivo:id_motivo,id_submotivo:id_submotivo,id_estado:id_estado,id_empresa:id_empresa,
                tipo:"tecnico"},
              dataType: "html",
              success: function (data) {
                  $(".tecnico").html("");
                  $(".tecnico").html(data);
              },
              error: function () {
                  alert("Error");
              }
          });

         $(".registro_clientes .liquidado").css("display","none")
       }else if(id_estado==3 || id_estado==19){
        $(".registro_clientes .liquidado").css("display","block")
       }else{
          $(".tecnico").html("");
          $(".registro_clientes .liquidado").css("display","none")
       }

    });

  //Para el flag del Tecnico
  //$("#flag_tecnico").attr("disabled","disabled")
  $("#tecnico").change(function(){

    var tecnico = $("#tecnico option:selected").val()
    if(tecnico!=""){
      $("#flag_tecnico").removeAttr("disabled")
    }else{
      $("#flag_tecnico").attr("disabled","disabled")
    }
    
  })


  //Para el Registro de Clientes Críticos
  $("#btn_gestion_critico").click(function(){
    alerta="";
    //alert($("#mostrar_tecnicos").css("display"));
    estado_id = $("#estado option:selected").val();
   if(estado_id==9 || estado_id==10 || estado_id==20){

      $("#frm_gestion_critico").validate({
        errorClass:'help-inline',
        errorElement:'div',
        ignore: 'hidden',
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
              }
              else if($("#mostrar_tecnicos").css("display")!='none'){
                  if($("#slct_cedula").val()==''){
                    alert('Seleccione Celula');
                    alerta="ok";
                    $("#slct_cedula").focus();
                  }
                  else if($("#tecnico").val()==''){
                    alert('Seleccione Tecnico');
                    alerta="ok";
                    $("#tecnico").focus();
                  }
                  else{
                    registrarMovimientos()
                    return false
                  }
              }
              else if($('#liquidaciones').css("display")!='none'){
                if($("#slct_cedula2").val()==''){
                  alert('Seleccione Celula');
                  alerta="ok";
                  $("#slct_cedula2").focus();
                }
                else if($("#tecnico_movimiento").val()==''){
                  alert('Seleccione Tecnico');
                  alerta="ok";
                  $("#tecnico_movimiento").focus();
                }
                else{
                  registrarMovimientos()
                  return false
                }
              }
              else{
                  $(".fecha_error").html("").css("display","none")
                  registrarMovimientos()
                  return false
              }
          }
          else if($("#mostrar_tecnicos").css("display")!='none'){
              if($("#slct_cedula").val()==''){
                alert('Seleccione Cedula');
                alerta="ok";
                $("#slct_cedula").focus();
              }
              else if($("#tecnico").val()==''){
                alert('Seleccione Tecnico');
                alerta="ok";
                $("#tecnico").focus();
              }
              else{
                registrarMovimientos()
                return false
              }
          }
          else if($('#liquidaciones').css("display")!='none'){
                if($("#slct_cedula2").val()==''){
                  alert('Seleccione Celula');
                  alerta="ok";
                  $("#slct_cedula2").focus();
                }
                else if($("#tecnico_movimiento").val()==''){
                  alert('Seleccione Tecnico');
                  alerta="ok";
                  $("#tecnico_movimiento").focus();
                }
                else{
                  registrarMovimientos()
                  return false
                }
          }
          else{
              registrarMovimientos()
              return false
          }
        }
      });
	   
      if(alerta==""){
		      if($(".tabla_horarios").css("display")=="block"){
              if($("#fecha_agenda").val()==""){
                  $(".fecha_error").html("Seleccione un horario").css({"display":"block","margin":"-14px 0 0 130px"})
              }
              else if($("#mostrar_tecnicos").css("display")!='none'){
                  if($("#slct_cedula").val()==''){
                    alert('Seleccione Celula');
                    $("#slct_cedula").focus();
                  }
                  else if($("#tecnico").val()==''){
                    alert('Seleccione Tecnico');
                    $("#tecnico").focus();
                  }
                  else{
                    registrarMovimientos()
                    return false
                  }
              }
              else if($('#liquidaciones').css("display")!='none'){
                if($("#slct_cedula2").val()==''){
                  alert('Seleccione Celula');
                  $("#slct_cedula2").focus();
                }
                else if($("#tecnico_movimiento").val()==''){
                  alert('Seleccione Tecnico');
                  $("#tecnico_movimiento").focus();
                }
                else{
                  registrarMovimientos()
                  return false
                }
              }
              else{
                  $(".fecha_error").html("").css("display","none")
                  registrarMovimientos()
                  return false
              }
          }
          else if($("#mostrar_tecnicos").css("display")!='none'){
              if($("#slct_cedula").val()==''){
                alert('Seleccione Celula');
                $("#slct_cedula").focus();
              }
              else if($("#tecnico").val()==''){
                alert('Seleccione Tecnico');
                $("#tecnico").focus();
              }
              else{
                registrarMovimientos()
                return false
              }
          }
          else if($('#liquidaciones').css("display")!='none'){
            if($("#slct_cedula2").val()==''){
              alert('Seleccione Celula');
              $("#slct_cedula2").focus();
            }
            else if($("#tecnico_movimiento").val()==''){
              alert('Seleccione Tecnico');
              $("#tecnico_movimiento").focus();
            }
            else{
              registrarMovimientos()
              return false
            }
          }else{
              registrarMovimientos()
              return false
          }
      }

    }
    else{
      $("#frm_gestion_critico").validate({
      errorClass:'help-inline',
      errorElement:'div',
      ignore: 'hidden',
      highlight: function (element, errorClass) {
       $(element).removeClass(errorClass);
      },
      rules: {
        cr_observacion: {
          required: true
        },
        motivo: {
          required: true
        },
        h_submotivo: {
          required: true
        }
      },
      messages: {
        cr_observacion:{
          required: "Ingrese una observaci&oacute;n"
        },
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
            }
            else if($("#mostrar_tecnicos").css("display")!='none'){
                if($("#slct_cedula").val()==''){
                  alert('Seleccione Celula');
                  $("#slct_cedula").focus();
                }
                else if($("#tecnico").val()==''){
                  alert('Seleccione Tecnico');
                  $("#tecnico").focus();
                }
                else{
                  registrarMovimientos()
                  return false
                }
            }
            else if($('#liquidaciones').css("display")!='none'){
                if($("#slct_cedula2").val()==''){
                  alert('Seleccione Celula');
                  $("#slct_cedula2").focus();
                }
                else if($("#tecnico_movimiento").val()==''){
                  alert('Seleccione Tecnico');
                  $("#tecnico_movimiento").focus();
                }
                else{
                  registrarMovimientos()
                  return false
                }
            }
            else{
                $(".fecha_error").html("").css("display","none")
                registrarMovimientos()
                return false
            }
        }
        else if($("#mostrar_tecnicos").css("display")!='none'){
            if($("#slct_cedula").val()==''){
              alert('Seleccione Cedula');
              $("#slct_cedula").focus();
            }
            else if($("#tecnico").val()==''){
              alert('Seleccione Tecnico');
              $("#tecnico").focus();
            }
            else{
              registrarMovimientos()
              return false
            }
        }
        else if($('#liquidaciones').css("display")!='none'){
            if($("#slct_cedula2").val()==''){
              alert('Seleccione Celula');
              $("#slct_cedula2").focus();
            }
            else if($("#tecnico_movimiento").val()==''){
              alert('Seleccione Tecnico');
              $("#tecnico_movimiento").focus();
            }
            else{
              registrarMovimientos()
              return false
            }
        }
        else{          
            registrarMovimientos()
            return false
        }
      }
    });
		
    }
       
  })

/* --- Para Registro clientes Críticos --- */
  $("form:not(.filter) :input:visible:enabled:first").css({'background-color' : '#c4e0f2'}).focus()

  $('#cr_nombre').filter_input({regex:"[a-zA-Z\ áéíóúñÑ]"});
  $('#cr_telefono').filter_input({regex:'[0-9]'});
  $('#cr_celular').filter_input({regex:'[*#0-9]'});
  $('#cr_observacion').filter_input({regex:'[0-9-#a-zA-Z\ áéíóúñÑ.,]'});

  
  //$("#horario td").click(function(){
  //
  //  color = $(this).css("background-color")
  //  //para IE8 ya que toma el color como lo pone
  //    if(color.indexOf('#')!=-1){
  //      color = color
  //    }else{
  //      color = hexcolor(color)
  //    }
  //
  //  id_celda = $(this).attr("title")
  //
  //  horario_celda = document.getElementById("horario").getElementsByTagName("td")[id_celda]
  //  totales = horario_celda.getAttribute("data-total");
  //  //total = $(this).attr("data-total")
  //
  //  if(color!="#ff0000" && color!="#ffff00" && color!="#c4e0f2" && totales>0){
  //
  //    $("#horario td").each(function(){
  //      color = $(this).css("background-color")
  //      if(color.indexOf('#')!=-1){
  //        color = color
  //      }else{
  //        color = hexcolor(color)
  //      }
  //      if(color!="#ff0000" && color!="#ffff00" && color!="#c4e0f2"){
  //        $(this).css({"background":"","color":""})
  //      }
  //    })
  //
  //    $(this).css({"background":"green","color":"#fff"})
  //    /*$("#fecha_agenda").val($(this).attr("data-fec"))
  //    $("#horario_agenda").val($(this).attr("data-horario"))
  //    $("#dia_agenda").val($(this).attr("data-dia"))
  //    $("#hora_agenda").val($(this).attr("data-hora"))*/
  //    $("#fecha_agenda").val(horario_celda.getAttribute("data-fec"))
  //    $("#horario_agenda").val(horario_celda.getAttribute("data-horario"))
  //    $("#dia_agenda").val(horario_celda.getAttribute("data-dia"))
  //    $("#hora_agenda").val(horario_celda.getAttribute("data-hora"))
  //
  //    $(".horario .help-inline").css("display","none")
  //    $(".fecha_error").html("").css("display","none")
  //
  //  }
  //})

  //Para el Registro de Clientes Críticos

    //AL DAR CLICK EN REGISTRAR AGENDAR
    $("#btn_registro").click(function(){


    motivo = $("#motivo_registro").val()
    if(motivo==1){
      $("#frm_criticos").validate({
        errorClass:'help-inline',
        errorElement:'div',
        ignore: 'hidden',
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

        var page_parent = window.parent.window.page
        if(page_parent == "visorHistorico"){
            registrarCriticos()
            return false
        }else if($("#slct_cedula").val()==''){
        alert('Seleccione Cedula');
        $("#slct_cedula").focus();
          }
          else if($("#tecnico").val()==''){
            alert('Seleccione Tecnico');
            $("#tecnico").focus();
          }
          else{
            registrarCriticos()
            return false  
          }
        }
      });
    }else{
        $("#frm_criticos").validate({
        errorClass:'help-inline',
        errorElement:'div',
        ignore: 'hidden',
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
          }
        },
        messages: {
          cr_nombre:{
            required: "Ingrese el nombre del cliente",
          },
          cr_telefono:{
            required: "Ingrese su n&uacute;mero Telef&oacute;nico"
          },
          cr_observacion: "Ingrese una observaci&oacute;n",
        },
        submitHandler: function(form) {



            var page_parent = window.parent.window.page
            if(page_parent == "visorHistorico"){
                registrarCriticos()
                return false
            }else if($("#slct_cedula").val()==''){
            alert('Seleccione Cedula');
            $("#slct_cedula").focus();
          }
          else if($("#tecnico").val()==''){
            alert('Seleccione Tecnico');
            $("#tecnico").focus();
          }
          else{
            registrarCriticos()
            return false
          }
        }
      });
    }


       
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


    //OFFICETRACK
    $("#tecnico").change(function(){

        var idtecnico =  $(this).val();
        getTecnicoOfficeTrack(idtecnico,'labeltecoffice');


    });

    $("#tecnico_movimiento").change(function(){

        var idtecnico =  $(this).val();
        getTecnicoOfficeTrack(idtecnico,'labeltecoffice2');


    });
    




})


function getTecnicoOfficeTrack(idtecnico,idlabel)
{
    $.ajax({
        type: "POST",
        url: "controladorHistorico/historicoController.php",
        data:  {
            accion:"getTecnicoOfficeTrack",
            idtecnico:idtecnico
        },

        success: function (data) {
            $("#tec_officetrack").val(data);
            if(data == "1" || data == 1){
                $("."+idlabel).text("Es tecnico officetrack")
            }else{
                $("."+idlabel).text("No es tecnico officetrack")

            }
        },

        error: function () {
           // alert("Error al obtener OfficeTrack estado");
        }
    });
}

function hexcolor(colorval) {
    var parts = colorval.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
    if(parts){
      delete(parts[0]);

      for (var i = 1; i <= 3; ++i) {
          parts[i] = parseInt(parts[i]).toString(16);
          if (parts[i].length == 1) parts[i] = '0' + parts[i];
      }
      color = '#' + parts.join('');
      return color
    }
}

function horario_reservado(){
  //enfocando la fecha agenda ya reservada
  fec_ini = $("#fecha_agenda_ini").val()
  horario_ini = $("#horario_agenda_ini").val()

  if(fec_ini!="" && horario_ini!=""){

    $("#horario td").each(function(i){

        horario_celda = document.getElementById("horario").getElementsByTagName("td")[i]
        fec = horario_celda.getAttribute("data-fec")
        hora = horario_celda.getAttribute("data-horario")
        if(fec_ini==fec && horario_ini==hora){
            $(this).css({"background":"#c4e0f2"})
        }

    })

  }
}

function registrarCriticos() {

  $("#nombretecnico").val($("#tecnico option:selected").text());

    var tecnicooffcetrack = $("#tec_officetrack").val();

  if($('#frm_criticos #motivo_registro').val()=="1"
      && tecnico!=''
      && $("#frm_criticos #flag_tecnico").attr("checked")
      &&
      //( $("#frm_criticos #quiebre").val()=="R9-REIT-CATV" || $("#actividad").val() === "Provision" )
      $("#siva_nova").val()=="SiVa"
      &&
          //SI ES TECNICO OFFICETRACK
      ( tecnicooffcetrack == 1 )

        ){

      var parametros = $("#frm_criticos").serialize();

      $.ajax({
    type: "POST",
        url: "controladorHistorico/eventoController.php",
        data:  parametros,
        dataType: "Json",
        beforeSend: function(){
    // Handle the beforeSend event
        $('.modalPop').show();  
        },

        complete: function(){
        // Handle the complete event
        
        },
        success: function (data) {
          finalizarEnvioRegistro(data,'0');
        },

        error: function () {
            $('.modalPop').hide();
            alert("Error: No se realizo el registro,por favor intente nuevamente; Si persiste el error favor de comunicarse con sistemas");
        }
    });

   }
   else{

      if($('#frm_criticos #motivo_registro').val()=="1"
          && tecnico!=''
          && $("#frm_criticos #flag_tecnico").attr("checked")
          &&
          //( $("#frm_criticos #quiebre").val()=="R9-REIT-CATV" || $("#actividad").val() === "Provision" )
          $("#siva_nova").val()=="SiVa"
      ){
          if(
          confirm("No he ha enviando a officetrack , " +
          "ya que el tecnico no cumple con el estado Officetrack" +
          " Desea registrar de todas maneras sin officetrack ?")
          ){
              finalizarRegistroCritico('','0');
          }
      }else{
          //REGISTRO PARA CUANDO ES PENDIENTE
          // O AGENDAMIENTO SIN TECNICO ENTREGADO
          finalizarRegistroCritico('','0');
      }



   }
}

function finalizarRegistroCritico(d,idcritico){
    $('#frm_criticos #datosfinal').val(d);
    var parametros = $("#frm_criticos").serialize();

    timestamp = new Date().getTime();
    $.ajax({
    type: "POST",
        url: "controladorHistorico/historicoController.php?timestamp="+timestamp+"&idcritico="+idcritico,
        data: parametros,
        dataType: "html",
        beforeSend: function(){
    // Handle the beforeSend event
        $('.modalPop').show();  
        },
        complete: function(){
        $('.modalPop').hide();
        // Handle the complete event
        
        },
        success: function (data) {
            window.parent.jQuery("#filtro_general").click();
            alert(data);

            window.parent.jQuery('#dialog-criticos').dialog('close');
            jQuery("#btn_historico").trigger("click");

            //si se hace click en registrar dentro del visor historioc
            //reactualiza la pagina
            if( window.parent.window.page == "visorHistorico"){
                window.parent.jQuery("#btn_historico").trigger("click");
            }
        },

        error: function () {
            $('.modalPop').hide();
            alert("Error no se realizo el registro");
        }
    });
}

function registrarMovimientos() {

   //para la fecha de consolidacion de liquidacion
   fecha_liquidacion = $("#fecha_liquidacion").val()
   $("#h_fecha_liquidacion").val(fecha_liquidacion)

   

   indice = $("#indice").val()
   idfila = $("#idfila").val()

   nombre = $("#cr_nombre").val()
   telefono = $("#cr_telefono").val()
   celular = $("#cr_celular").val()
   fecha_agenda = $("#fecha_agenda").val()
   hora = $("#hora_agenda").val()
   motivo = $("#motivo option:selected").text()
   id_motivo = $("#motivo option:selected").val()
   submotivo = $("#submotivo option:selected").text()
   estado = $("#estado option:selected").text()
   estado_id = $("#estado option:selected").val()
   codigo_estado = $("#estado option:selected").val()
   ult_flag_tecnico = $("#ult_flag_tecnico").val()

   $("#nombretecnico_movimiento").val($("#tecnico_movimiento option:selected").text());
   $("#nombretecnico").val($("#tecnico option:selected").text());

   sin_movimiento = ""
   if(estado_id==9 || estado_id==10 || estado_id==20 || estado_id==3 || estado_id==19 ||
      estado_id==7 || estado_id==11 || estado_id==12 || estado_id==13 || estado_id==14 || estado_id==15 || 
      estado_id==16 || estado_id==17 || id_motivo==5 || estado_id==26 || id_motivo==10 || id_motivo==9 || id_motivo==4 ){//id_estado 26 quiebre sistemas
      idtecnico = $("#tecnico_movimiento").val();
      tecnico=$("#tecnico_movimiento option:selected").text();
     sin_movimiento = "1"     
     if($.trim(idtecnico)==''){
      idtecnico=$("#IdTecnicoDefault").val();
      tecnico=$("#TecnicoDefault").val();
     }
   }else{
    if($("#tecnico").val()!=''){
        idtecnico = $("#tecnico").val();
        tecnico=$("#tecnico option:selected").text();
     }else{
        idtecnico= "";
        tecnico = "";        
     }
     
   }

   if(sin_movimiento!="1"){
     if(tecnico!="" && $("#flag_tecnico").attr("checked")){
         imgTecnicoEstado = '<img src="img/user-green.png" alt="Tecnico Entregado" title="Tecnico Entregado" />';
     }else if(tecnico!="" && !$("#flag_tecnico").attr("checked")){
         imgTecnicoEstado = '<img src="img/user-yellow.png" alt="Tecnico Asignado" title="Tecnico Asignado" />';
     }else if(tecnico==""){
         imgTecnicoEstado = '<img title="Sin tecnico" alt="Sin tecnico" src="img/user-clean.png">';
     }
   }else{
      if(ult_flag_tecnico=="Tecnico Entregado"){
        imgTecnicoEstado = '<img src="img/user-green.png" alt="Tecnico Entregado" title="Tecnico Entregado" />';
      }else if(ult_flag_tecnico=="Tecnico Asignado"){
        imgTecnicoEstado = '<img src="img/user-yellow.png" alt="Tecnico Asignado" title="Tecnico Asignado" />';
      }else{
        imgTecnicoEstado = '<img title="Sin tecnico" alt="Sin tecnico" src="img/user-clean.png">';
      }
   }

    var tecnicooffcetrack = $("#tec_officetrack").val();

   if(  //SI ES LA PRIMERA
       ( ($("#frm_gestion_critico #flag_tecnico").attr("checked") &&
            $("#frm_gestion_critico #motivo").val()=='1' &&
                $("#frm_gestion_critico #submotivo").val()=='1') ||
            //SI YA HABIA SIDO ENVIADO A OFFICETRACK
            $("#frm_gestion_critico #n_evento").val()=="1"
       ) &&
        //SI NO ES IGUAL A OBSERVACIONES
        $("#frm_gestion_critico #motivo").val()!='5'

       &&
           //Y SI ES ALGUNA R9 DE CUALQUIERA O CUALQUIER PROVISION
       // (  $("#frm_gestion_critico #quiebre").val()=="R9-REIT-CATV" || $("#actividad").val() === "Provision" )
          $("#siva_nova").val()=="SiVa"
       &&
           //SI ES TECNICO OFFICETRACK
       ( tecnicooffcetrack == 1 )

   )
   {
	
    if( ((estado_id==3 || estado_id==19) && $("#paso_final").val()=="0003-Cierre") || (estado_id!=3 && estado_id!=19) ){
	  var parametros = $("#frm_gestion_critico").serialize();
      if($("#frm_gestion_critico #n_evento").val()=="1"){

          if(confirm("Ya se realizo una transmision anteriormente, confirme para enviar la actualizacion.")){

              $.ajax({
            type: "POST",
                url: "controladorHistorico/eventoController.php",
                data:  parametros,
                dataType: "Json",
                beforeSend: function(){
            // Handle the beforeSend event
                $('.modalPop').show();  
                },

                complete: function(){
                // Handle the complete event
                
                },
                success: function (data) {
                  finalizarEnvioRegistro(data,'1');
                },

                error: function () {
                    $('.modalPop').hide();
                    alert("Error: No se realizo el registro,por favor intente nuevamente; Si persiste el error favor de comunicarse con sistemas");
                }
               });
            }

      }
  		else{
  			$.ajax({
            type: "POST",
                url: "controladorHistorico/eventoController.php",
                data:  parametros,
                dataType: "Json",
                beforeSend: function(){
            // Handle the beforeSend event
                $('.modalPop').show();  
                },

                complete: function(){
                // Handle the complete event
                
                },
                success: function (data) {
                  finalizarEnvioRegistro(data,'1');
                },

                error: function () {
                    $('.modalPop').hide();
                    alert("Error: No se realizo el registro,por favor intente nuevamente; Si persiste el error favor de comunicarse con sistemas");
                }
               });
  		}
    }
    else if( (estado_id==3 || estado_id==19) && $("#paso_final").val()!="0003-Cierre" ){
        alert("No se puede liquidar; Gestion aun no cuenta con cierre en officetrack.");
    }
   }
   else{


       if(
           ( ($("#frm_gestion_critico #flag_tecnico").attr("checked") &&
           $("#frm_gestion_critico #motivo").val()=='1' &&
           $("#frm_gestion_critico #submotivo").val()=='1') ||
               //SI YA HABIA SIDO ENVIADO A OFFICETRACK
           $("#frm_gestion_critico #n_evento").val()=="1"
           ) &&
               //SI NO ES IGUAL A OBSERVACIONES
           $("#frm_gestion_critico #motivo").val()!='5'

           &&
           //( $("#frm_gestion_critico #quiebre").val()=="R9-REIT-CATV" || $("#actividad").val() === "Provision" )
           $("#siva_nova").val()=="SiVa"

       ){
           if(
               confirm("No he ha enviando a officetrack , " +
               "ya que el tecnico no cumple con el estado Officetrack" +
               " Desea registrar de todas maneras sin officetrack ?")
           ){
               finalizarRegistroMovimiento('');
           }
       }else{
           //REGISTRO PARA CUANDO ES PENDIENTE
           // O AGENDAMIENTO SIN TECNICO ENTREGADO
           finalizarRegistroMovimiento('');
       }







    //finalizarRegistroMovimiento('');





   }

   
}

function finalizarEnvioRegistro(d,ind){

  $.ajax({
    type: "POST",
        url: "http://190.232.39.142/test/integracion/office_track.php",
        data: 'cadena='+JSON.stringify(d),
        dataType: "text",
        beforeSend: function(){
    // Handle the beforeSend event
        $('.modalPop').show();  
        },

        complete: function(){
        // Handle the complete event
        //
        },
        success: function (datos) {          
          if(datos=="OK"){    
            if(ind=="1"){
              finalizarRegistroMovimiento(datos);
            } 
            else{
              finalizarRegistroCritico(datos,d.TaskNumber);
            }     
            
          }
          else{
            $('.modalPop').hide();
            alert("No se realizo el registro,por favor intente nuevamente; Si persiste el error favor de comunicarse con sistemas");
          }  
        },

        error: function () {
            $('.modalPop').hide();
            alert("Error no se realizo el registro");
        }
    });
}

function finalizarRegistroMovimiento(datos){
$('#frm_gestion_critico #datosfinal').val(datos);
var parametros = $("#frm_gestion_critico").serialize();

  $.ajax({
    type: "POST",
        url: "controladorHistorico/historicoController.php",
        data: parametros,
        dataType: "Json",
        beforeSend: function(){
    // Handle the beforeSend event
        $('.modalPop').show();  
        },

        complete: function(){
        // Handle the complete event
        $('.modalPop').hide();
        },
        success: function (data) {
          if(data.estado){
            if(estado!="Liquidado"){
              if(estado_id==9 || estado_id==10 || estado_id==20 || id_motivo==5 || id_motivo==6){
                  fecha_agenda = $("#fecha_agenda_ini").val()
                  hora = $("#horario_desc").val()
               }else{
                window.parent.jQuery('.flag_tecnico'+idfila).html(imgTecnicoEstado)//para q no cambie el flag
                //al registrar el mov con esos estados
               }
              
              fecha_agenda = fecha_agenda.substr(8,2)+"-"+fecha_agenda.substr(5,2)+"-"+fecha_agenda.substr(0,4);
              fechahora = fecha_agenda+"<br>"+hora
              var nmov = data.nmov
              window.parent.jQuery('.fagenda'+idfila).html(fechahora)
              window.parent.jQuery('.festado'+idfila).html(estado)

                if(estado_id!=9 && estado_id!=10 && estado_id !=20 ){
                     window.parent.jQuery('.ftecnico'+idfila).html(tecnico)}

                window.parent.jQuery('.nmov'+idfila).html("("+nmov+")")
              
              window.parent.jQuery('#seleccion_criticos'+indice).attr("data-estado",codigo_estado)
              window.parent.jQuery('#seleccion_criticos'+indice).attr("data-tecnico",tecnico)

              if(estado_id==21){
                window.parent.jQuery('.quitar_gestion'+idfila).html("")
                window.parent.jQuery('.quitar_gestion'+idfila).removeClass("gestion")
                window.parent.jQuery('.quitar_gestion'+idfila).removeClass("registro_criticos")
              }
            }else{
              window.parent.jQuery('#clienteCriticos tr[data-indice="'+indice+'"]').remove();
            }
          }

          if(datos=="OK"){
            window.parent.jQuery('.transmision'+idfila).html('<img src="img/cel1.png" alt="Transmision de informacion" title="Transmision de informacion" />');
          }

            alert(data.msg);
            window.parent.jQuery('#dialog-gestion-criticos').dialog('close');

        },

        error: function () {
            $('.modalPop').hide();
            alert("Error no se realizo el registro");
        }
    });

}


function cargarTecnico(slct,tec,cedu,idemp,quie){      
  if($('#'+cedu).val()==''){
    $('#'+slct).html('<option value="">-- Seleccione --</option>');
    $('#'+slct).val('');
  }
  else{
  var c=$('#'+cedu).val();
  var parametros = {cargarTecnico:"cargarTecnico",cedula:c,idempresa:idemp,quiebre:quie}
    $.ajax({
        type: "POST",
          url: "controladorHistorico/historicoController.php",
          data: parametros,
          dataType: "Json",
          success: function (obj) {
            var html='';
            if(obj!=null){
              $.each(obj,function(key,data){
                html+='<option value="'+data.id+'">'+data.nombre+'</option>';
              });
            }
            $('#'+slct).html('<option value="">-- Seleccione --</option>'+html);
            $('#'+slct).val(tec);
            idlabel="labeltecoffice";
              if(slct=="tecnico_movimiento"){
                idlabel="labeltecoffice2";
              }
              getTecnicoOfficeTrack(tec,idlabel);
          },
          error: function () {
              alert("Error no cargaron los tecnicos");
          }
      });
  }
}