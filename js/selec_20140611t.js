$(document).ready(function(){
	
	$(".empresa").multiselect();
	$(".empresa").multiselect("checkAll");
	$(".zonales").multiselect();
	$(".zonales").multiselect("checkAll");
	$(".tecnico_nombre").multiselect();
	$(".tecnico_nombre").multiselect("checkAll");
	$(".lejano").multiselect();
	$(".lejano").multiselect("checkAll");
	$(".microzona").multiselect();
	$(".microzona").multiselect("checkAll");
	$(".area2").multiselect();
	$(".area2").multiselect("checkAll");
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
	$(".filtro_tecnico").multiselect("checkAll");
	
		//Para agregar clientes críticos
		$("td.registro_criticos").click(function(){

		var fonoBus = $(this).attr("data-telefono");
		var url = "registro_clientes_criticos.php?fonoBus="+fonoBus;
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

//Datepicker
    $( ".buscarFecha" ).datepicker({
       showOn: "button",
         buttonImage: "img/calendar.gif",
         buttonImageOnly: true,
         //minDate: 0 //fechas de hoy en adelante
    });

    //configuracion de controles por defecto
    $( ".buscarFecha" ).datepicker( "option", "dateFormat","dd-mm-yy");
    //$(".buscarFecha").datepicker('setDate', new Date());

});
