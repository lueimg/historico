var esCritico = false;
var rm_telefono, 
    rm_inscripcion, 
    rm_codcliente,
    rm_nombre,
    rm_apaterno, 
    rm_amaterno,
    rm_segmento,
    rm_zonal,
    rm_mdf,
    rm_averia;

$(document).ready(function(){
	
	/*$(".empresa").multiselect();
	$(".empresa").multiselect("checkAll");*/
	/*$(".zonales").multiselect();
	$(".zonales").multiselect("checkAll");
	$(".negocio").multiselect();
	$(".negocio").multiselect("checkAll");
	$(".mdf").multiselect();
	$(".mdf").multiselect("checkAll");
	$(".nodo").multiselect();
	$(".nodo").multiselect("checkAll");
	$(".estados").multiselect();
	$(".estados").multiselect("checkAll");
	$(".actividades").multiselect();
	$(".actividades").multiselect("checkAll");
	$(".quiebre").multiselect();
	$(".quiebre").multiselect("checkAll");
	
	$(".filtro_tecnico").multiselect();
	$(".filtro_tecnico").multiselect("checkAll");*/
	$("#btn_historico").click(buscarCliente);
	$("#btn_limpiar").click(limpiaForm);

	$(function(){

       var  telefonoCliente = $('#telefonoCliente');
       var  codigoClienteATIS = $('#codigoClienteATIS');
       var  codigoServicioCMS = $('#codigoServicioCMS');
       var  codigoClienteCMS = $('#codigoClienteCMS');
       var code =null;

        telefonoCliente.keypress(function(e)
        {
            code= (e.keyCode ? e.keyCode : e.which);
            if (code == 13) 
        		buscarCliente();
        });

        codigoClienteATIS.keypress(function(e)
        {
            code= (e.keyCode ? e.keyCode : e.which);
            if (code == 13) 
        		buscarCliente();
        });

        codigoServicioCMS.keypress(function(e)
        {
            code= (e.keyCode ? e.keyCode : e.which);
            if (code == 13) 
        		buscarCliente();
        });

        codigoClienteCMS.keypress(function(e)
        {
            code= (e.keyCode ? e.keyCode : e.which);
            if (code == 13) 
        		buscarCliente();
        });
    });

	function buscarCliente(){
		
		var fono = $("#telefonoCliente").val();
		var codATIS = $("#codigoClienteATIS").val();
		var codServCMS = $("#codigoServicioCMS").val();
		var codCliCMS = $("#codigoClienteCMS").val();
		
		if (fono.length==0 && codATIS.length==0 && codServCMS.length==0 && codCliCMS.length==0)
		{
			alert("Debe ingresar al menos 1 valor de busqueda.");
			return false;
		}
		if (fono.length>0 && codATIS.length>0 && codServCMS.length>0 && codCliCMS.length>0)
		{
			alert("Debe ingresar solo 1 valor de busqueda.");
			return false;
		}
		else if (fono.length>0 && codATIS.length>0 )
		{
			alert("Debe ingresar solo 1 valor de busqueda.");
			return false;
		}		
		else if (fono.length>0 && codServCMS.length>0 )
		{
			alert("Debe ingresar solo 1 valor de busqueda.");
			return false;
		}	
		else if (fono.length>0 && codCliCMS.length>0 )
		{
			alert("Debe ingresar solo 1 valor de busqueda.");
			return false;
		}	
		else if (codATIS.length>0 && codServCMS.length>0 )
		{
			alert("Debe ingresar solo 1 valor de busqueda.");
			return false;
		}	
		else if (codATIS.length>0 && codCliCMS.length>0 )
		{
			alert("Debe ingresar solo 1 valor de busqueda.");
			return false;
		}	
		else if (codServCMS.length>0 && codCliCMS.length>0 )
		{
			alert("Debe ingresar solo 1 valor de busqueda.");
			return false;
		}	
		
		var spinner = new Spinner(opts).spin(target);
		$.ajax({
			url : 'buscaCliente.php',
			type : 'POST',
			data : {
				telefonoCliente : $("#telefonoCliente").val(),
				codigoClienteATIS : $("#codigoClienteATIS").val(),
				codigoServicioCMS : $("#codigoServicioCMS").val(),
				codigoClienteCMS : $("#codigoClienteCMS").val()
			},
			dataType: 'json',
			success : function(obj){
				//alert("Hola!");
				//var cadenajson = data;
				//var obj = $.parseJSON(response);
				//alert(obj[0].posibleCritico);
				
                                
                                $.each(obj, function (){console.log(this.telefono);
                                    rm_telefono = this.telefono;
                                    rm_inscripcion = this.inscripcio;
                                    rm_codcliente = this.codclie;
                                    rm_apaterno = this.appater;
                                    rm_amaterno = this.apmater;
                                    rm_segmento = this.segest;
                                    rm_zonal = this.zonal;
                                    rm_mdf = this.mdf;
                                    rm_nombre = this.nombre;
                                });
                                
				var cantidad = obj.length;
				if (obj[0].encontrado=="0")  {
					spinner.stop();
					alert("No se encontraron coincidencias segun criterio de busqueda.");
					
					return false;
				}
				if(cantidad==1){
					$("#r_inscrip").html(obj[0].inscripcio);
					$("#r_telefono").html(obj[0].telefono);
					$("#r_codclie").html(obj[0].codclie);
					$("#r_codclicms").html(obj[0].codclicms);
					$("#r_codsercms").html(obj[0].codservcms);
					if(obj[0].tipopaq!=''){
						$("#r_paquete").html(obj[0].tipopaq);
					} else {
						$("#r_paquete").html("MONO");
					}
					$("#r_nombre").html(obj[0].nombre);
					$("#r_paterno").html(obj[0].appater);
					$("#r_materno").html(obj[0].apmater);
					$("#r_direcc").html(obj[0].tipocalle+" "+obj[0].nomcalle+" "+obj[0].numcalle);
					$("#r_segmento").html(obj[0].segest+" - "+obj[0].desseg);
					$("#r_modalidad").html(obj[0].modalidad);
					$("#r_velocidad").html(obj[0].veloc);
					$("#r_tasa").html(obj[0].tasa);
					$("#r_tecno").html(obj[0].tecnologia);

                    //RESUELVE SI MOSTRAR AGENDAR O NO
                    // c1 telefono
                    // c2 inscripcion

					if(obj[0].c1!=null || obj[0].c2!=null || obj[0].c1_tmp!=null){
						//$("#mensaje").html(obj[0].c1);
						 //alert("Critico");
						 $("#critico").html("Cliente Cr&iacute;tico");
						 $("#nocritico").html("");
						 $("#agendarVisita").css("display","block");
                                                 
                         esCritico = true;
						 
					} else {
						$("#nocritico").html("Cliente no cr&iacute;tico");
						$("#critico").html("");
						$("#agendarVisita").css("display","none");
                                                
                        esCritico = false;
					}

					if(obj[0].posibleCritico=="1"){
						//$("#mensaje").html(obj[0].c1);
						 //alert("Critico");
						 $("#critico2").html("Posible Cr&iacute;tico");
						 $("#nocritico2").html("");
						 
					} else {
						$("#nocritico2").html("");
						$("#critico2").html("");
					}
					
					
                                        
					//Obtenemos datos de averías
					listarAverias(obj[0].telefono,obj[0].codservcms,obj[0].codclicms);
					//Obtenemos datos de Provisión
					listarProvision(obj[0].telefono);
					//Obtenemos datos de llamadas
					listarLlamadas(obj[0].telefono);
					listarCriticos(obj[0].telefono);
					
					spinner.stop();
				} else {
					listado="";
					listado+="<span>Cliente : "+obj[0].nombre+" "+obj[0].appater+" "+obj[0].apmater+"</span>";
					listado+="<table width='100%' border='1'>";
					listado+="<tr><td>Tel&eacute;fono</td><td>Cod. Servicio CMS</td><td>Direcci&oacute;n</td><td>Cod. Cli ATIS</td><td>Cod. Cli. CMS</td></tr>";
					for (var i = 0; i < cantidad; i ++ ) {
						listado+="<tr><td><a href=\"#\" onclick=\"mostrarUnicoCliente('"+obj[i].telefono+"','"+obj[i].codclie+"','"+obj[i].codservcms+"','"+obj[i].codclicms+"')\">"+obj[i].telefono+"</a></td><td>"+obj[i].codservcms+"</td><td>"+obj[i].tipocalle+" "+obj[i].nomcalle+" "+obj[i].numcalle+"</td><td>"+obj[i].codclie+"</td><td>"+obj[i].codclicms+"</td></tr>";
						//listado+="<tr><td><a id=\"lnk_cliente\" href=\"#\">"+obj[i].telefono+"</a></td><td>"+obj[i].codservcms+"</td><td>"+obj[i].tipocalle+" "+obj[i].nomcalle+" "+obj[i].numcalle+"</td><td>"+obj[i].codclie+"</td><td>"+obj[i].codclicms+"</td></tr>";
					}
					//listado+="<tr><td>1</td><td>2</td><td>3</td></tr>";
					listado+="</table>";
					 $("#dialog-modal").html(listado);
					 $("#dialog-modal").dialog({
						width: 640,
						modal: true
						});
				}
			}
		});
	}

	function listarAverias(telefono,codservcms,codclicms){

		$.ajax({
			url : 'listarAverias.php',
			type : 'POST',
			data : {
				telefonoCliente : telefono,
				codigoServicioCMS : codservcms,
				codigoClienteCMS : codclicms,
                esCritico: esCritico
			},
			success : function(response){
				$("#tabs-averias").html(response);
                //Agregamos el evento click al boton de agendar averia
                AgendarAveriaEventoClick();
                AgendarProvisionEventoClick();

                                //Registro manual
                                $(".rmanual").click(function (event){
                                    event.preventDefault();
                                    rm_averia = $(this).attr("href");
                                    var tipo = $(this).attr("title");
                                    var url = "registro_manual_criticos_agenda.php?prio=" 
                                        + tipo 
                                        + '&rm_telefono=' + rm_telefono
                                        + '&rm_inscripcion=' + rm_inscripcion
                                        + '&rm_codcliente=' + rm_codcliente
                                        + '&rm_apaterno=' + rm_apaterno
                                        + '&rm_amaterno=' + rm_amaterno
                                        + '&rm_segmento=' + rm_segmento
                                        + '&rm_zonal=' + rm_zonal
                                        + '&rm_mdf=' + rm_mdf
                                        + '&rm_nombre=' + rm_nombre
                                        + '&rm_averia=' + rm_averia
                                        + '&tipo_actu=averia';
                                    var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
                                    $("#dialog-registro-manual").html(pagina);
                                    $("#dialog-registro-manual").dialog({
                                        autoOpen: false,
                                        modal: true,
                                        height: 450,
                                        width: 700
                                    });
                                    $("#dialog-registro-manual").dialog( "open" );
                                });
			}
		});
	}

	function listarLlamadas(telefono){
		$.ajax({
			url : 'listarLlamadas.php',
			type : 'POST',
			data : {
				telefonoCliente : telefono
			},
			success : function(response){
				$("#tabs-llamadas").html(response);
			}
		});
	}

	function listarProvision(telefono){

		$.ajax({
			url : 'listarProvision.php',
			type : 'POST',
			data : {
				telefonoCliente : telefono
			},
			success : function(response){
				$("#tabs-provision").html(response);
			}
		});
	}

	
	function listarCriticos(telefono){
		$.ajax({
			url : 'listarCriticos.php',
			type : 'POST',
			data : {
				telefonoCliente : telefono
			},
			success : function(response){
				$("#tabs-criticos").html(response);
			}
		});
	}
	
	
	function limpiaForm(){
		//Limpiamos Tabs
		$("#tabs-averias").html("");
		$("#tabs-llamadas").html("");
		$("#tabs-provision").html("");
		$("#tabs-criticos").html("");
		//Limpiamos Formulario
		$("#telefonoCliente").val("");
		$("#codigoClienteATIS").val("");
		$("#codigoServicioCMS").val("");
		$("#codigoClienteCMS").val("");
		//Listo para otra busqueda
		$("#telefonoCliente").focus();
		$("#mensaje").html("&nbsp;");
		$("#r_inscrip").html("&nbsp;");
		$("#r_telefono").html("&nbsp;");
		$("#r_codclie").html("&nbsp;");
		$("#r_codclicms").html("&nbsp;");
		$("#r_codsercms").html("&nbsp;");
		$("#r_paquete").html("&nbsp;");
		$("#r_segmento").html("&nbsp;");
		$("#r_direcc").html("&nbsp;");
		$("#r_nombre").html("&nbsp;");
		$("#r_paterno").html("&nbsp;");
		$("#r_materno").html("&nbsp;");
		$("#r_modalidad").html("&nbsp;");
		$("#r_velocidad").html("&nbsp;");
		$("#r_tasa").html("&nbsp;");
		$("#r_tecno").html("&nbsp;");
	}
	
	
	//Para agregar clientes críticos
	AgendarAveriaEventoClick = function(){
        $("#btn_cliente_critico").click(function(){

            var idAveria = $(this).parent().parent().parent().find("a").text();

            //var fonoBus = $("#telefonoCliente").val();
            var url = "registro_clientes_criticos.php?averia_ini="+idAveria;
            var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
            $("#dialog-criticos").html(pagina);
            $("#dialog-criticos").dialog({
                autoOpen: false,
                modal: true,
                height: 600,
                width: 700,
            });

            $("#dialog-criticos").dialog( "open" );

        });
    }
    AgendarProvisionEventoClick = function(){
        $("#btn_cliente_critico_provision").click(function(){

            var idAveria =$("#btn_cliente_critico_provision").parent().parent().attr("cod");
            var actividad  = "Provision"
            //var fonoBus = $("#telefonoCliente").val();
            var url = "registro_clientes_criticos_provision.php?averia_ini="+idAveria+"&actividad="+actividad;
            var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
            $("#dialog-criticos").html(pagina);
            $("#dialog-criticos").dialog({
                autoOpen: false,
                modal: true,
                height: 600,
                width: 700,
            });

            $("#dialog-criticos").dialog( "open" );

        });
    }


	var opts = {
	  lines: 13, // The number of lines to draw
	  length: 25, // The length of each line
	  width: 10, // The line thickness
	  radius: 41, // The radius of the inner circle
	  corners: 1, // Corner roundness (0..1)
	  rotate: 21, // The rotation offset
	  direction: 1, // 1: clockwise, -1: counterclockwise
	  color: '#000', // #rgb or #rrggbb or array of colors
	  speed: 1, // Rounds per second
	  trail: 60, // Afterglow percentage
	  shadow: false, // Whether to render a shadow
	  hwaccel: false, // Whether to use hardware acceleration
	  className: 'spinner', // The CSS class to assign to the spinner
	  zIndex: 2e9, // The z-index (defaults to 2000000000)
	  top: 'auto', // Top position relative to parent in px
	  left: 'auto' // Left position relative to parent in px
	};
	var target = document.getElementById('foo');
	//var spinner = new Spinner(opts).spin(target);

//multiselect listado criticos
/*
	$("#zonales").change(function(){

		var zonales = $("#zonales").val()
		if(zonales!==null){
			var tot_zonales = zonales.length
			var zonal=""
			if(tot_zonales>0){
				for(i=0;i<tot_zonales;i++){
					zonal += "'"+zonales[i]+"',"
				}
			}

			zonal = zonal.substr(0,zonal.length-1)

			var parametros = {zonal:zonal,filtro_mdf:"filtro_mdf"}
			$.ajax({
		    	type: "POST",
		        url: "controladorHistorico/historicoController.php",
		        data: parametros,
		        dataType: "Json",
		        success: function (data) {
		        	if(data!==null){
		        		$('#mdf').html("")
		   			    $("#mdf").multiselect("refresh")

		   			    $.each(data, function (index, nazriya) {
				            opt = $('<option />', {value: nazriya.MDF,text: nazriya.MDF});
		        			opt.appendTo($("#mdf").multiselect())
				        });

		   			    $("#mdf").multiselect("refresh")
		        	}else{
		        		$('#mdf').html("")
		   			    $("#mdf").multiselect("refresh")
		        	}
		            
		        },
		        error: function () {
		            //alert("Error no se realizo el registro");
		        }
		    });
		}else{

			$('#mdf').html("")
		    $("#mdf").multiselect("refresh")
		}
    });	
	*/
	
	
	
});

	function mostrarUnicoCliente(telefono,codcliatis,codsercms,codclicms){
	//function mostrarUnicoCliente(){
		//&alert("Hola");
		//var fono=telefono;
		$("#telefonoCliente").val(telefono);
		$("#codigoClienteATIS").val(codcliatis);
		$("#codigoServicioCMS").val(codsercms);
		$("#codigoClienteCMS").val(codclicms);
		$("#dialog-modal").dialog( "close" );
		$( "#btn_historico" ).trigger( "click" );
	}