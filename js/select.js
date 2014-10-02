$(document).ready(function(){
	
	$('.empresa option.seleccion').attr('selected', 'selected')
	$(".empresa").multiselect();

	//$(".empresa").multiselect("checkAll");
	$(".zonales").multiselect();
	$(".zonales").multiselect("checkAll");
	$(".tecnico_nombre").multiselect();
	$(".tecnico_nombre").multiselect("checkAll");

	$('.lejano option').each(function(index, value) {
            if(this.value =="CRITICOS"){
            	$('.lejano option[value="CRITICOS"]').attr('selected', 'selected')
            }
    });
    $(".lejano").multiselect();

    $('.area2 option').each(function(index, value) {
            if(this.value =="CATV EN CAMPO"){
            	$('.area2 option[value="CATV EN CAMPO"]').attr('selected', 'selected')
            }else if(this.value =="EN CAMPO"){
            	$('.area2 option[value="EN CAMPO"]').attr('selected', 'selected')
            }else if(this.value ==""){
            	$('.area2 option[value=""]').attr('selected', 'selected')
            }
    });
    $(".area2").multiselect();

    $(".area_").multiselect();
	$(".area_").multiselect("checkAll");
	$(".movistar1").multiselect();
	$(".movistar1").multiselect("checkAll");
	$(".distrito").multiselect();
	$(".distrito").multiselect("checkAll");
	$(".esttransmision").multiselect();
	$(".esttransmision").multiselect("checkAll");

	$(".microzona").multiselect();
	$(".microzona").multiselect("checkAll");
	$(".negocio").multiselect();
	$(".negocio").multiselect("checkAll");
	$(".mdf").multiselect();
	$(".mdf").multiselect("checkAll");
	$(".nodo").multiselect();
	$(".nodo").multiselect("checkAll");

	$('.estados option').each(function(index, value) {
            if(this.value =="3,19" || this.value =="Temporal" || this.value =="21" || this.value =="6,5,4"){
            	//nada
            }else{
            	$('.estados option[value="'+this.value+'"]').attr('selected', 'selected')
            }
    });
	$(".estados").multiselect();

	$(".actividades").multiselect();
	$(".actividades").multiselect("checkAll");
	$(".quiebre").multiselect();
	$(".quiebre").multiselect("checkAll");
	$(".quiebre option[value='F_PL']").removeAttr("selected");
	$(".quiebre").multiselect('refresh');

	
	$(".filtro_tecnico").multiselect();
	$(".filtro_tecnico").multiselect("checkAll");

	
	//Para agregar clientes críticos
	$("td.registro_criticos").click(function(){

		var fonoBus = $(this).attr("data-telefono");
		var indice = $(this).attr("data-indice");
		var actividad = $(this).attr("data-actividad");
		var averia_ini = $(".faveria_ini"+indice).html()

		if(actividad=='Provision'){
			var url = "registro_clientes_criticos_provision.php?averia_ini="+averia_ini+"&actividad="+actividad;
		}else{
			var url = "registro_clientes_criticos.php?averia_ini="+averia_ini+"&actividad="+actividad;
		}
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
	
	//multiselect listado criticos
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
		   			    $(".mdf").multiselect("checkAll");
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

  //Filtro por Fechas
 $.datepicker.regional['es'] = {
      closeText: 'Cerrar',
      prevText: '<Ant',
      nextText: 'Sig>',
      currentText: 'Hoy',
      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
      monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
      dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
      dayNamesShort: ['Dom','Lun','Mar','Mie','Juv','Vie','Sab'],
      dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
      weekHeader: 'Sm',
      dateFormat: 'dd/mm/yy',
      firstDay: 1,
      isRTL: false,
      showMonthAfterYear: false,
      yearSuffix: ''};
   $.datepicker.setDefaults($.datepicker.regional['es']);

/*Datepicker Inicio*/
    $( ".buscarFecha" ).datepicker({
       showOn: "button",
         buttonImage: "img/calendar.gif",
         buttonImageOnly: true,
    });
    $( ".buscarFecha" ).datepicker( "option", "dateFormat","dd-mm-yy");

    $( "#fecha_liquidacion" ).datepicker({
       showOn: "button",
         buttonImage: "img/calendar.gif",
         buttonImageOnly: true,
    });
    $("#fecha_liquidacion" ).datepicker( "option", "dateFormat","dd-mm-yy");
    $("#fecha_liquidacion").datepicker('setDate', new Date());
/*Datepicker Fin*/
	
	/*Reporte de Movimientos*/
	$("#h_fecha_ini_atc").datepicker('disable');
	$("#h_fecha_fin_atc").datepicker('disable');

	$( "#reporte_mov_1" ).click(function(){
		$("#h_fecha_ini_act").datepicker('enable');
		$("#h_fecha_fin_act").datepicker('enable');
		$("#h_fecha_ini_act").attr('disabled','disabled');
		$("#h_fecha_fin_act").attr('disabled','disabled');//para las cajas
		$("#h_fecha_ini_atc").datepicker('disable');
		$("#h_fecha_fin_atc").datepicker('disable');
		$("#h_fecha_ini_atc").val('');
		$("#h_fecha_fin_atc").val('');
		
	})

	$( "#reporte_mov_2" ).click(function(){
		$("#h_fecha_ini_atc").datepicker('enable');
		$("#h_fecha_fin_atc").datepicker('enable');
		$("#h_fecha_ini_atc").attr('disabled','disabled');
		$("#h_fecha_fin_atc").attr('disabled','disabled');
		$("#h_fecha_ini_act").datepicker('disable');
		$("#h_fecha_fin_act").datepicker('disable');
		$("#h_fecha_ini_act").val('');
		$("#h_fecha_fin_act").val('');
	})

	$( ".generar_movimientos" ).click(function(){
		if($( "#reporte_mov_1" ).attr("checked")){
			var fecha_ini_act = $("#h_fecha_ini_act").val();
			var fecha_fin_act = $("#h_fecha_fin_act").val();
			$("#fecha_ini_act").val(fecha_ini_act);
			$("#fecha_fin_act").val(fecha_fin_act);
			if(fecha_ini_act!="" && fecha_fin_act!=""){
				$("#frmExcelMov").submit()
			}else{
				alert("Indique la Fecha Registro de Actuación de inicio y fin correctamente")
			}
		}

		if($( "#reporte_mov_2" ).attr("checked")){
			var fecha_ini_atc = $("#h_fecha_ini_atc").val();
			var fecha_fin_atc = $("#h_fecha_fin_atc").val();
			$("#fecha_ini_atc").val(fecha_ini_atc);
			$("#fecha_fin_atc").val(fecha_fin_atc);
			if(fecha_ini_atc!="" && fecha_fin_atc!=""){
				$("#frmExcelMov").submit()
			}else{
				alert("Indique la Fecha Registro de ATC de inicio y fin correctamente")
			}
		}
	})

    $( ".generar_excel" ).click(function(){

  		//Agregando las fechas ya que estan deshabilitadas en la bandeja y no las toma
  		var fecha_ini = $("#fecha_ini").val()
  		var fecha_campo_ini  = "<input type='text' name='fecha_ini' value='"+fecha_ini+"' class='fecha_ini_neo'>"
  		var fecha_fin = $("#fecha_fin").val()
  		var fecha_campo_fin  = "<input type='text' name='fecha_fin' value='"+fecha_fin+"' class='fecha_fin_neo'>"
  		$("#frmExcel").append(fecha_campo_ini)
  		$("#frmExcel").append(fecha_campo_fin)
  		$("#frmExcel").submit()
  		$( ".fecha_ini_neo" ).remove();
  		$( ".fecha_fin_neo" ).remove();
   })


$("#btn_limpiar_filtros").click(function(){
		
		$('.empresa option.seleccion').attr('selected', 'selected');
		$(".empresa").multiselect();
		//$(".empresa").multiselect("checkAll");
		$(".zonales").multiselect();
		$(".zonales").multiselect("checkAll");
		$(".negocio").multiselect();
		$(".negocio").multiselect("checkAll");
		$(".mdf").multiselect();
		$(".mdf").multiselect("checkAll");
		$(".nodo").multiselect();
		$(".nodo").multiselect("checkAll");
		$(".actividades").multiselect();
		$(".actividades").multiselect("checkAll");
		$(".nodo").multiselect();
		$(".nodo").multiselect("checkAll");		
		$(".distrito").multiselect("checkAll");
		$(".esttransmision").multiselect("checkAll");		
		$(".quiebre").multiselect("checkAll");
		$(".quiebre option[value='F_PL']").removeAttr("selected");
		$(".quiebre").multiselect('refresh');

		$(".lejano").multiselect("uncheckAll");
		$('.lejano option').each(function(index, value) {
            if(this.value =="CRITICOS"){
            	$('.lejano option[value="'+this.value+'"]').attr('selected', 'selected')

            }
	    });
	    $(".lejano").multiselect("refresh");

	    $(".area2").multiselect("uncheckAll");
	    $('.area2 option').each(function(index, value) {
            if(this.value =="CATV EN CAMPO"){
            	$('.area2 option[value="CATV EN CAMPO"]').attr('selected', 'selected')
            }else if(this.value =="EN CAMPO"){
            	$('.area2 option[value="EN CAMPO"]').attr('selected', 'selected')
            }else if(this.value ==""){
            	$('.area2 option[value=""]').attr('selected', 'selected')
            }
    });
	    $(".area2").multiselect("refresh");

	    $(".estados").multiselect("uncheckAll");
	    $('.estados option').each(function(index, value) {
            if(this.value =="3,19" || this.value =="Temporal" || this.value =="21" || this.value =="6,5,4"){
            	//nada
            }else{
            	$('.estados option[value="'+this.value+'"]').attr('selected', 'selected')
            }
	    });
		$(".estados").multiselect("refresh");

		$(".microzona").multiselect();
		$(".microzona").multiselect("checkAll");
		$(".tecnico_nombre").multiselect();
		$(".tecnico_nombre").multiselect("checkAll");
		$(".filtro_tecnico").multiselect();
		$(".filtro_tecnico").multiselect("checkAll");
		$("#fecha_ini").val("");
		$("#fecha_fin").val("");
		$("#txt_tan").val("");
		
		$("#filtro_general").click()
	});

});

