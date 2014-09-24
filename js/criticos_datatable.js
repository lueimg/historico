var imgTecnicoEstado = '<img src="img/user-clean.png" alt="Sin Tecnico" title="Sin Tecnico" />';
$(document).ready(function() {

				var oTable = $('#clienteCriticos').dataTable({
					aaSorting:[],
				    aoColumnDefs: [
					  {
					     bSortable: false,
					     aTargets: [ 0 ]
					  }
					],
					"iDisplayLength": 11,
					"bPaginate": true,
					"aLengthMenu": [[50, 100, 200, -1], [50, 100, 200, "All"]],
					"sDom": 'T<"clear">lfrtip'
				});

$("#filtro_personalizado").click(function(){

	var tipo = $("#filtro_tan").val()
	var valor_filtro = $("#txt_tan").val()
	var empresa = $("#empresa").val()
	var empresa_raiz = $("#empresa_raiz").val()

	var empresa_usuario = $("#empresa_usuario").val()
	if(empresa_usuario>1){
		var empresas = $("#empresa").val()
		
		if(empresas!==null){
			var tot_empresas = empresas.length
			var empresa=""
			if(tot_empresas>0){
				for(i=0;i<tot_empresas;i++){
					empresa += "'"+empresas[i]+"',"
				}
			}
			empresa = empresa.substr(0,empresa.length-1)
		}else{
			empresa = ""
		}
	}else{
		var empresa = $("#empresa").val()
		if(empresa!=""){
			empresa = "'"+empresa+"'"
		}else{
			empresa = ""
		}
	}
	
	$("#filtro_inicial").val(2)
	//alert(typeof(empresa))
	/*if(typeof(empresa)=="object"){
		var parametros = {filtro:"filtro_personalizado",tipo:tipo,valor_filtro:valor_filtro,empresa:"",empresa_usuario:empresa_usuario}
	}else{
		var parametros = {filtro:"filtro_personalizado",tipo:tipo,valor_filtro:valor_filtro,empresa:empresa,empresa_usuario:empresa_usuario}
	}*/
	
	var parametros = {filtro:"filtro_personalizado",tipo:tipo,valor_filtro:valor_filtro,empresa:empresa,empresa_usuario:empresa_usuario}

	if(valor_filtro!=""){
		$.ajax({
			type: "POST",
			url: "controladorHistorico/historicoController.php",
			data: parametros,
			dataType: "Json",
			beforeSend: function(){
	        	$('.modalPop').show();  
	        },
	        complete: function(){
	          	$('.modalPop').hide();
	        },
			success: function (data) {
			  	//if(data!==null){
			  		oTable.fnClearTable();
			  		
			  		var tabla = ""
			   		$.each(data, function (i, info) {

			   			if(info.fecha_reg!=''){
							fecha_reg = info.fecha_reg.substr(8,2)+"-"+info.fecha_reg.substr(5,2)+"-"+info.fecha_reg.substr(0,4);
							fecha_reg += "<br>"+info.fecha_reg.substr(11,8)
						}else{
							fecha_reg = "";
						}

						if(info.fecha_cambio!='' && info.fecha_cambio!=null){
							fecha_cambio = info.fecha_cambio.substr(8,2)+"-"+info.fecha_cambio.substr(5,2)+"-"+info.fecha_cambio.substr(0,4);
							fecha_cambio += "<br>"+info.fecha_cambio.substr(11,8)
						}else{
							fecha_cambio = "";
						}

			   			if(info.fecha_agenda!=''){
							fecha = info.fecha_agenda.substr(8,2)+"-"+info.fecha_agenda.substr(5,2)+"-"+info.fecha_agenda.substr(0,4);
						}else{
							fecha = "";
						}

						if(info.codigo_estado=="1" || info.codigo_estado=="9" || info.codigo_estado=="10" || info.codigo_estado=="20"){
							fecha_horario = fecha+"<br>"+info.horario
						}else{
							fecha_horario = ""
						}

						img_Averia = '<img src="img/averia.png" alt="Mostrar Avería" title="Mostrar Avería" />'

						if(info.estado!="Temporal"  && info.tipo_actividad!="Manual"){
							if(info.existe!="" && info.existe!==null){
								existe = '';
							}else{
								existe = '<img src="img/info_2.png" alt="No esta Pendiente" title="No esta Pendiente" />';
							}
							img_mov = existe + '<img src="img/mov.jpg" alt="Mostrar Movimiento" title="Mostrar Movimiento" />'
							
						}else if(info.estado!="Temporal"  && info.tipo_actividad=="Manual"){
								img_mov = '<img src="img/mov.jpg" alt="Mostrar Movimiento" title="Mostrar Movimiento" />'
						}else{
							img_mov = "";
						}
                        
                        if(info.codigo_estado!="21"){
							img_gestion = '<img src="img/gestionar.png" alt="Gestionar" title="Gestionar" />'
						}else{
							img_gestion = ""
						}           
                        //Flag tecnico
                        if(info.codigo_estado!="21"){
		                    if ( info.flag_tecnico=="Tecnico Asignado" ) {
		                        imgTecnicoEstado = '<img src="img/user-yellow.png" alt="Tecnico Asignado" title="Tecnico Asignado" />';
		                    } else if (info.flag_tecnico=="Tecnico Entregado" ) {
		                        imgTecnicoEstado = '<img src="img/user-green.png" alt="Tecnico Entregado" title="Tecnico Entregado" />';
		                    }else{
		                     	imgTecnicoEstado = '<img title="Sin tecnico" alt="Sin tecnico" src="img/user-clean.png">';
		                    }
	                	}else{
	                		imgTecnicoEstado = ""
	                	}

	                	imgAgendamiento_wu="";
	                	if(info.wu_nagendas!="0"){
	                		imgAgendamiento_wu='<br><img src="img/agendamiento_wu.gif" alt="Agendamiento WU" title="Agendamiento WU" />('+info.wu_nagendas+')';
	                	}

	                	var imgn_evento='';
	                	var imagen_evento="";
	                	if(info.n_evento=="1"){
	                		if($.trim(info.estado_evento)!=''){
	                			if(info.estado_evento.split("-")[0]=='0001'){
	                				imagen_evento='<img src="img/verde.jpg" alt="'+info.estado_evento.split("-")[1]+'" />';
	                			}
	                			else if(info.estado_evento.split("-")[0]=='0002'){
	                				imagen_evento='<img src="img/amarillo.jpg" alt="'+info.estado_evento.split("-")[1]+'" />';
	                			}
	                			else if(info.estado_evento.split("-")[0]=='0003'){
	                				imagen_evento='<img src="img/azul.jpg" alt="'+info.estado_evento.split("-")[1]+'" />';
	                			}
	                		}
	                		imgn_evento=imagen_evento+'<img src="img/cel1.png" alt="Transmision de informacion" />';
	                	}

//console.log(info);
					rtn = oTable.fnAddData(['<input type="checkbox" name="seleccion_criticos" id="seleccion_criticos'+i+'" value="si">',info.averia,info.id_atc,
						info.tipo_actividad,
						'<span style="display: none;">' + info.fecha_reg + '</span>'+fecha_reg
						,info.quiebres,info.empresa,info.telefono_cliente_critico,
						fecha_horario,info.estado,info.tecnico,info.mdf,info.microzona,info.distrito,fecha_cambio,info.horas_cambio,
						/*info.flag_tecnico,*/img_Averia+imgAgendamiento_wu,img_mov + '<span class="nmov'+info.id+'">(' + info.nmov + ')', '<span class="flag_tecnico'+info.id+'">'+imgTecnicoEstado+'</span>'+ img_gestion, imgn_evento,info.tipo_averia,info.horas_averia,info.fecha_registro,
						info.ciudad,info.codigo_averia,info.inscripcion,info.fono1,info.telefono,info.mdf,info.observacion_102,info.segmento,
						info.area_,info.direccion_instalacion,info.codigo_distrito,info.nombre_cliente,info.orden_trabajo,info.veloc_adsl,
						info.clase_servicio_catv,info.codmotivo_req_catv,info.total_averias_cable,info.total_averias_cobre,info.total_averias,
						info.fftt,info.llave,info.dir_terminal,info.fonos_contacto,info.contrata,info.zonal,info.quiebre,info.lejano,
						info.distrito,info.eecc_final,info.zona_movistar_uno,info.paquete,info.data_multiproducto,info.averia_m1,
						info.fecha_data_fuente,info.telefono_codclientecms,info.rango_dias,info.sms1,
						info.sms2,info.area2,info.microzona,info.tecnico],false);

					var oSettings = oTable.fnSettings();
					//oTable.fnSort( oSettings.aaSorting ); 

					var nTr = oSettings.aoData[ rtn[0] ].nTr;
					 nTr.setAttribute( 'data-indice', i );
					$('td input', nTr)[0].setAttribute('data-id', $.trim(info.id) );
					$('td input', nTr)[0].setAttribute('data-empresa', info.eecc_final );
					$('td input', nTr)[0].setAttribute('data-empresa-raiz', empresa_raiz );
					$('td input', nTr)[0].setAttribute('data-atc', info.id_atc );
					$('td input', nTr)[0].setAttribute('data-estado', info.codigo_estado );
					$('td input', nTr)[0].setAttribute('data-tecnico', info.tecnico );
					$('td input', nTr)[0].setAttribute('data-quiebre', info.quiebres );

					$('td', nTr)[1].className = 'faveria_ini'+i;
					$('td', nTr)[3].className = 'factividad'+i;
					//$('td', nTr)[4].className = 'fnombre'+info.id;
					$('td', nTr)[6].className = 'fempresa'+info.id;
					
					var ind = (info.id!='')? info.id:i;
					$('td', nTr)[7].className = 'ftele'+ind;
					$('td', nTr)[8].className = 'fagenda'+info.id;
					$('td', nTr)[9].className = 'festado'+info.id;
					$('td', nTr)[10].className = 'ftecnico'+info.id;
					
						if(info.estado!="Temporal"){
							$('td', nTr)[16].className = 'mostrar_averia';
						}else{
							$('td', nTr)[16].className = 'mostrar_averia_raiz';
						}
						$('td', nTr)[16].setAttribute('data-id', info.id );
						$('td', nTr)[16].setAttribute('data-averia', info.codigo_averia );
						$('td', nTr)[16].setAttribute('data-indice', i );
						$('td', nTr)[16].setAttribute('data-actividad', info.tipo_actividad );

						if(info.estado!="Temporal"){
							$('td', nTr)[17].className = 'mostrar_mov';
						}
						$('td', nTr)[17].setAttribute('data-id', info.id );
						$('td', nTr)[17].setAttribute('data-indice', i );
						
						$('td', nTr)[18].setAttribute( 'data-id', info.id );
						$('td', nTr)[18].setAttribute('data-indice', i );
						$('td', nTr)[18].setAttribute('data-actividad', info.tipo_actividad );

						if(info.estado!="Temporal" && info.codigo_estado!="21"){
							$('td', nTr)[18].className = 'gestion quitar_gestion'+info.id;
						}else if(info.estado=="Temporal" && info.codigo_estado!="21"){
							$('td', nTr)[18].className = 'registro_criticos quitar_gestion'+i;
							$('td', nTr)[18].setAttribute( 'data-telefono', info.telefono_cliente_critico );
						}else if(info.estado!="Temporal" && info.codigo_estado=="21"){
							//
						}

					//$('td', nTr)[18].style.display = 'none';
					$('td', nTr)[19].className = 'transmision'+info.id+' transmision';
					$('td', nTr)[19].setAttribute( 'data-id', info.id );
					$('td', nTr)[19].setAttribute('data-actividad', info.tipo_actividad );

					$('td', nTr)[20].style.display = 'none';
					$('td', nTr)[21].style.display = 'none';
					$('td', nTr)[22].style.display = 'none';
					$('td', nTr)[23].style.display = 'none';
					$('td', nTr)[24].style.display = 'none';
					$('td', nTr)[25].style.display = 'none';
					$('td', nTr)[26].style.display = 'none';
					$('td', nTr)[27].style.display = 'none';
					$('td', nTr)[28].style.display = 'none';
					$('td', nTr)[29].style.display = 'none';
					$('td', nTr)[30].style.display = 'none';
					$('td', nTr)[31].style.display = 'none';
					$('td', nTr)[32].style.display = 'none';
					$('td', nTr)[33].style.display = 'none';
					$('td', nTr)[34].style.display = 'none';
					$('td', nTr)[35].style.display = 'none';
					$('td', nTr)[36].style.display = 'none';
					$('td', nTr)[37].style.display = 'none';
					$('td', nTr)[38].style.display = 'none';
					$('td', nTr)[39].style.display = 'none';
					$('td', nTr)[40].style.display = 'none';
					$('td', nTr)[41].style.display = 'none';
					$('td', nTr)[42].style.display = 'none';
					$('td', nTr)[43].style.display = 'none';
					$('td', nTr)[44].style.display = 'none';
					$('td', nTr)[45].style.display = 'none';
					$('td', nTr)[46].style.display = 'none';
					$('td', nTr)[47].style.display = 'none';
					$('td', nTr)[48].style.display = 'none';
					$('td', nTr)[49].style.display = 'none';
					$('td', nTr)[50].style.display = 'none';
					$('td', nTr)[51].style.display = 'none';
					$('td', nTr)[52].style.display = 'none';
					$('td', nTr)[53].style.display = 'none';
					$('td', nTr)[54].style.display = 'none';
					$('td', nTr)[55].style.display = 'none';
					$('td', nTr)[56].style.display = 'none';
					$('td', nTr)[57].style.display = 'none';
					$('td', nTr)[58].style.display = 'none';
					$('td', nTr)[59].style.display = 'none';
					$('td', nTr)[60].style.display = 'none';
					$('td', nTr)[61].style.display = 'none';
					$('td', nTr)[62].style.display = 'none';
					$('td', nTr)[63].style.display = 'none';

					});
					oTable.fnDraw();
					$("#seleccion_general").removeAttr('checked')//para el check de la lista

					$('#clienteCriticos').delegate('td.registro_criticos','click', function(){
					
						var fonoBus = $(this).attr("data-telefono");
						var indice = $(this).attr("data-indice");
						var actividad = $(this).attr("data-actividad");
						var averia_ini = $(".faveria_ini"+indice).html()
						
						if(actividad=='Provision'){
							var url = "registro_clientes_criticos_provision.php?averia_ini="+averia_ini+"&actividad="+actividad+"&indice="+indice;
						}else{
							var url = "registro_clientes_criticos.php?averia_ini="+averia_ini+"&actividad="+actividad+"&indice="+indice;
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
					})
			    /*}else{
			        alert("No se ha especificado un dato de consulta")
			    }*/
			},
			error: function () {
			    alert("No se ha especificado un dato de consulta");
			}
		});
	}else{
		$("#filtro_general").click()
	}
});

$("#filtro_general").click(function(){
	var zonales = $("#zonales").val()
	var empresa_raiz = $("#empresa_raiz").val()
	var fecha_ini = $("#fecha_ini").val()
	var fecha_fin = $("#fecha_fin").val()
	
	//para avisar al reporte de excel de la bandeja que ya se hizo el primer filtro
	$("#filtro_inicial").val(2)


		if(zonales!==null){
			var tot_zonales = zonales.length
			var zonal=""
			if(tot_zonales>0){
				for(i=0;i<tot_zonales;i++){
					zonal += "'"+zonales[i]+"',"
				}
			}
			zonal = zonal.substr(0,zonal.length-1)
		}else{
			zonal = ""
		}

	var actividades = $("#actividades").val()
		if(actividades!==null){
			var tot_actividades = actividades.length
			var actividad=""
			if(tot_actividades>0){
				for(i=0;i<tot_actividades;i++){
					actividad += "'"+actividades[i]+"',"
				}
			}
			actividad = actividad.substr(0,actividad.length-1)
		}else{
			actividad = ""
		}

	var microzonas = $("#microzona").val()
		if(microzonas!==null){
			var tot_microzonas = microzonas.length
			var microzona=""
			if(tot_microzonas>0){
				for(i=0;i<tot_microzonas;i++){
					microzona += "'"+microzonas[i]+"',"
				}
			}
			microzona = microzona.substr(0,microzona.length-1)
		}else{
			microzona = ""
		}

	var lejanos = $("#lejano").val()
	//alert(lejanos)
		if(lejanos!==null){
			var tot_lejanos = lejanos.length
			var lejano=""
			if(tot_lejanos>0){
				for(i=0;i<tot_lejanos;i++){
					lejano += "'"+lejanos[i]+"',"
				}
			}
			lejano = lejano.substr(0,lejano.length-1)
		}else{
			lejano = ""
		}

	var area2s = $("#area2").val()
		if(area2s!==null){
			var tot_area2s = area2s.length
			var area2=""
			if(tot_area2s>0){
				for(i=0;i<tot_area2s;i++){
					area2 += "'"+area2s[i]+"',"
				}
			}
			area2 = area2.substr(0,area2.length-1)
		}else{
			area2 = ""
		}
	
	var tecnico_nombre = $("#tecnico_nombre").val()
		if(tecnico_nombre!==null){
			var tot_tecnico_nombre = tecnico_nombre.length
			var tecnico=""
			if(tot_tecnico_nombre>0){
				for(i=0;i<tot_tecnico_nombre;i++){
					tecnico += "'"+tecnico_nombre[i]+"',"
				}
			}
			tecnico = tecnico.substr(0,tecnico.length-1)
		}else{
			tecnico = ""
		}

	var empresa_usuario = $("#empresa_usuario").val()
	if(empresa_usuario>1){
		var empresas = $("#empresa").val()
		
		if(empresas!==null){
			var tot_empresas = empresas.length
			var empresa=""
			if(tot_empresas>0){
				for(i=0;i<tot_empresas;i++){
					empresa += "'"+empresas[i]+"',"
				}
			}
			empresa = empresa.substr(0,empresa.length-1)
		}else{
			empresa = ""
		}
	}else{
		var empresa = $("#empresa").val()
		if(empresa!=""){
			empresa = "'"+empresa+"'"
		}else{
			empresa = ""
		}
	}

	var negocios = $("#negocio").val()
		if(negocios!==null){
			var tot_negocios = negocios.length
			var negocio=""
			if(tot_negocios>0){
				for(i=0;i<tot_negocios;i++){
					negocio += "'"+negocios[i]+"',"
				}
			}
			negocio = negocio.substr(0,negocio.length-1)
		}else{
			negocio = ""
		}
		

	var mdfs = $("#mdf").val()
		if(mdfs!==null){
			var tot_mdfs = mdfs.length
			var mdf=""
			if(tot_mdfs>0){
				for(i=0;i<tot_mdfs;i++){
					mdf += "'"+mdfs[i]+"',"
				}
			}
			mdf = mdf.substr(0,mdf.length-1)
		}else{
			mdf = ""
		}

	var nodos = $("#nodo").val()
		if(nodos!==null){
			var tot_nodos = nodos.length
			var nodo=""
			if(tot_nodos>0){
				for(i=0;i<tot_nodos;i++){
					nodo += "'"+nodos[i]+"',"
				}
			}
			nodo = nodo.substr(0,nodo.length-1)
		}else{
			nodo = ""
		}

	var estados = $("#estados").val()
	
		if(estados!==null){
			var tot_estados = estados.length
			var estado=""
			if(tot_estados>0){
				for(i=0;i<tot_estados;i++){
					if(estados[i]=="Temporal"){//si no es un numero
						estado += "'"+estados[i]+"',"
					}else{
						estado += estados[i]+","
					}
				}
			}
			estado = estado.substr(0,estado.length-1)
		}else{
			estado = ""
		}

	var filtro_tecnico = $("#filtro_tecnico").val()
		if(filtro_tecnico!==null){
			var tot_filtro_tecnico = filtro_tecnico.length
			var flags=""
			if(tot_filtro_tecnico>0){
				for(i=0;i<tot_filtro_tecnico;i++){
					flags += "'"+filtro_tecnico[i]+"',"
				}
			}
			flags = flags.substr(0,flags.length-1)
		}else{
			flags = ""
		}

	var quiebres = $("#quiebre").val()
		if(quiebres!==null){
			var tot_quiebres = quiebres.length
			var quiebre=""
			if(tot_quiebres>0){
				for(i=0;i<tot_quiebres;i++){
					quiebre += "'"+quiebres[i]+"',"
				}
			}
			quiebre = quiebre.substr(0,quiebre.length-1)
		}else{
			quiebre = ""
		}

	var area_n = $("#area_").val()
		if(area_n!==null){
			var tot_area_n = area_n.length
			var area_=""
			if(tot_area_n>0){
				for(i=0;i<tot_area_n;i++){
					area_ += "'"+area_n[i]+"',"
				}
			}
			area_ = area_.substr(0,area_.length-1)
		}else{
			area_ = ""
		}

	var mov_1 = $("#movistar1").val()
		if(mov_1!==null){
			var tot_mov_1 = mov_1.length
			var movistar1=""
			if(tot_mov_1>0){
				for(i=0;i<tot_mov_1;i++){
					movistar1 += "'"+mov_1[i]+"',"
				}
			}
			movistar1 = movistar1.substr(0,movistar1.length-1)
		}else{
			movistar1 = ""
		}
	
	var distrito="";
	if($("#distrito").val()!=null){
		distrito=$("#distrito").val().join(",");	
	}

	var esttransmision="";
	if($("#esttransmision").val()!=null){
		esttransmision=$("#esttransmision").val().join(",");	
	} 

	oTable.fnClearTable();
	var parametros = {actividad:actividad,empresa:empresa,zonal:zonal,microzona:microzona,lejano:lejano,area2:area2,tecnico:tecnico,
		negocio:negocio,mdf:mdf,nodo:nodo,estado:estado,flags:flags,quiebre:quiebre,area_:area_,
		movistar1:movistar1,fecha_ini:fecha_ini,fecha_fin:fecha_fin,filtro:"filtro_general",distrito:distrito,esttransmision:esttransmision}
	
	$.ajax({
		type: "POST",
		url: "controladorHistorico/historicoController.php",
		data: parametros,
		dataType: "Json",
		beforeSend: function(){
        	$('.modalPop').show();  
        },
        complete: function(){
          	$('.modalPop').hide();
        },
		success: function (data) {
		  	if(data!==null){
		  		
		  		var tabla = ""
		   		$.each(data, function (i, info) {

		   			if(info.fecha_reg!=''){
							fecha_reg = info.fecha_reg.substr(8,2)+"-"+info.fecha_reg.substr(5,2)+"-"+info.fecha_reg.substr(0,4);
							fecha_reg += "<br>"+info.fecha_reg.substr(11,8)
						}else{
							fecha_reg = "";
						}

						if(info.fecha_cambio!='' && info.fecha_cambio!=null  ){
							fecha_cambio = info.fecha_cambio.substr(8,2)+"-"+info.fecha_cambio.substr(5,2)+"-"+info.fecha_cambio.substr(0,4);
							fecha_cambio += "<br>"+info.fecha_cambio.substr(11,8)
						}else{
							fecha_cambio = "";
						}

			   			if(info.fecha_agenda!=''){
							fecha = info.fecha_agenda.substr(8,2)+"-"+info.fecha_agenda.substr(5,2)+"-"+info.fecha_agenda.substr(0,4);
						}else{
							fecha = "";
						}

						if(info.codigo_estado=="1" || info.codigo_estado=="9" || info.codigo_estado=="10" || info.codigo_estado=="20"){
							fecha_horario = fecha+"<br>"+info.horario
						}else{
							fecha_horario = ""
						}

						img_Averia = '<img src="img/averia.png" alt="Mostrar Avería" title="Mostrar Avería" />'

						if(info.estado!="Temporal"  && info.tipo_actividad!="Manual"){
							if(info.existe!="" && info.existe!==null){
								existe = '';
							}else{
								existe = '<img src="img/info_2.png" alt="No esta Pendiente" title="No esta Pendiente" />';
							}
							img_mov = existe + '<img src="img/mov.jpg" alt="Mostrar Movimiento" title="Mostrar Movimiento" />'
							
						}else if(info.estado!="Temporal"  && info.tipo_actividad=="Manual"){
								img_mov = '<img src="img/mov.jpg" alt="Mostrar Movimiento" title="Mostrar Movimiento" />'
						}else{
							img_mov = "";
						}
                        
                        if(info.codigo_estado!="21"){
							img_gestion = '<img src="img/gestionar.png" alt="Gestionar" title="Gestionar" />'
						}else{
							img_gestion = ""
						}           
                        //Flag tecnico
                        if(info.codigo_estado!="21"){
		                    if ( info.flag_tecnico=="Tecnico Asignado" ) {
		                        imgTecnicoEstado = '<img src="img/user-yellow.png" alt="Tecnico Asignado" title="Tecnico Asignado" />';
		                    } else if (info.flag_tecnico=="Tecnico Entregado" ) {
		                        imgTecnicoEstado = '<img src="img/user-green.png" alt="Tecnico Entregado" title="Tecnico Entregado" />';
		                    }else{
		                     	imgTecnicoEstado = '<img title="Sin tecnico" alt="Sin tecnico" src="img/user-clean.png">';
		                    }
	                	}else{
	                		imgTecnicoEstado = ""
	                	}

	                	imgAgendamiento_wu='';
	                	if(info.wu_nagendas!="0"){
	                		imgAgendamiento_wu='<br><img src="img/agendamiento_wu.gif" alt="Agendamiento WU" title="Agendamiento WU" />('+info.wu_nagendas+')';
	                	}

	                	var imgn_evento='';
	                	var imagen_evento="";
	                	if(info.n_evento=="1"){
	                		if($.trim(info.estado_evento)!=''){
	                			if(info.estado_evento.split("-")[0]=='0001'){
	                				imagen_evento='<img src="img/verde.jpg" alt="'+info.estado_evento.split("-")[1]+'" />';
	                			}
	                			else if(info.estado_evento.split("-")[0]=='0002'){
	                				imagen_evento='<img src="img/amarillo.jpg" alt="'+info.estado_evento.split("-")[1]+'" />';
	                			}
	                			else if(info.estado_evento.split("-")[0]=='0003'){
	                				imagen_evento='<img src="img/azul.jpg" alt="'+info.estado_evento.split("-")[1]+'" />';
	                			}
	                		}
	                		imgn_evento=imagen_evento+'<img src="img/cel1.png" alt="Transmision de informacion" />';
	                	}

					rtn = oTable.fnAddData(['<input type="checkbox" name="seleccion_criticos" id="seleccion_criticos'+i+'" value="si">',info.averia,info.id_atc,
						info.tipo_actividad,
						'<span style="display: none;">' + info.fecha_reg + '</span>'+fecha_reg
						,info.quiebres,info.empresa,info.telefono_cliente_critico,
						fecha_horario,info.estado,info.tecnico,info.mdf,info.microzona,info.distrito,fecha_cambio,info.horas_cambio,
						/*info.flag_tecnico,*/img_Averia+imgAgendamiento_wu,img_mov + '<span class="nmov'+info.id+'">(' + info.nmov + ')', '<span class="flag_tecnico'+info.id+'">'+imgTecnicoEstado+'</span>'+ img_gestion , imgn_evento,info.tipo_averia,info.horas_averia,info.fecha_registro,
						info.ciudad,info.codigo_averia,info.inscripcion,info.fono1,info.telefono,info.mdf,info.observacion_102,info.segmento,
						info.area_,info.direccion_instalacion,info.codigo_distrito,info.nombre_cliente,info.orden_trabajo,info.veloc_adsl,
						info.clase_servicio_catv,info.codmotivo_req_catv,info.total_averias_cable,info.total_averias_cobre,info.total_averias,
						info.fftt,info.llave,info.dir_terminal,info.fonos_contacto,info.contrata,info.zonal,info.quiebre,info.lejano,
						info.distrito,info.eecc_final,info.zona_movistar_uno,info.paquete,info.data_multiproducto,info.averia_m1,
						info.fecha_data_fuente,info.telefono_codclientecms,info.rango_dias,info.sms1,
						info.sms2,info.area2,info.microzona,info.tecnico],false);

					var oSettings = oTable.fnSettings();
					//oTable.fnSort( oSettings.aaSorting ); 

					var nTr = oSettings.aoData[ rtn[0] ].nTr;
					 nTr.setAttribute( 'data-indice', i );
					$('td input', nTr)[0].setAttribute('data-id', $.trim(info.id) );
					$('td input', nTr)[0].setAttribute('data-empresa', info.eecc_final );
					$('td input', nTr)[0].setAttribute('data-empresa-raiz', empresa_raiz );
					$('td input', nTr)[0].setAttribute('data-atc', info.id_atc );
					$('td input', nTr)[0].setAttribute('data-estado', info.codigo_estado );
					$('td input', nTr)[0].setAttribute('data-tecnico', info.tecnico );
					$('td input', nTr)[0].setAttribute('data-quiebre', info.quiebres );

					$('td', nTr)[1].className = 'faveria_ini'+i;
					$('td', nTr)[3].className = 'factividad'+i;
					//$('td', nTr)[4].className = 'fnombre'+info.id;
					$('td', nTr)[6].className = 'fempresa'+info.id;
					
					var ind = (info.id!='')? info.id:i;
					$('td', nTr)[7].className = 'ftele'+ind;
					$('td', nTr)[8].className = 'fagenda'+info.id;
					$('td', nTr)[9].className = 'festado'+info.id;
					$('td', nTr)[10].className = 'ftecnico'+info.id;
					
						if(info.estado!="Temporal"){
							$('td', nTr)[16].className = 'mostrar_averia';
						}else{
							$('td', nTr)[16].className = 'mostrar_averia_raiz';
						}
						$('td', nTr)[16].setAttribute('data-id', info.id );
						$('td', nTr)[16].setAttribute('data-averia', info.codigo_averia );
						$('td', nTr)[16].setAttribute('data-indice', i );
						$('td', nTr)[16].setAttribute('data-actividad', info.tipo_actividad );

						if(info.estado!="Temporal"){
							$('td', nTr)[17].className = 'mostrar_mov';
						}
						$('td', nTr)[17].setAttribute('data-id', info.id );
						$('td', nTr)[17].setAttribute('data-indice', i );
						
						$('td', nTr)[18].setAttribute( 'data-id', info.id );
						$('td', nTr)[18].setAttribute('data-indice', i );
						$('td', nTr)[18].setAttribute('data-actividad', info.tipo_actividad );

						if(info.estado!="Temporal" && info.codigo_estado!="21"){
							$('td', nTr)[18].className = 'gestion quitar_gestion'+info.id;
						}else if(info.estado=="Temporal" && info.codigo_estado!="21"){
							$('td', nTr)[18].className = 'registro_criticos quitar_gestion'+i;
							$('td', nTr)[18].setAttribute( 'data-telefono', info.telefono_cliente_critico );
						}else if(info.estado!="Temporal" && info.codigo_estado=="21"){
							//
						}

					//$('td', nTr)[18].style.display = 'none';
					$('td', nTr)[19].className = 'transmision'+info.id+' transmision';
					$('td', nTr)[19].setAttribute( 'data-id', info.id );
					$('td', nTr)[19].setAttribute('data-actividad', info.tipo_actividad );

					$('td', nTr)[20].style.display = 'none';
					$('td', nTr)[21].style.display = 'none';
					$('td', nTr)[22].style.display = 'none';
					$('td', nTr)[23].style.display = 'none';
					$('td', nTr)[24].style.display = 'none';
					$('td', nTr)[25].style.display = 'none';
					$('td', nTr)[26].style.display = 'none';
					$('td', nTr)[27].style.display = 'none';
					$('td', nTr)[28].style.display = 'none';
					$('td', nTr)[29].style.display = 'none';
					$('td', nTr)[30].style.display = 'none';
					$('td', nTr)[31].style.display = 'none';
					$('td', nTr)[32].style.display = 'none';
					$('td', nTr)[33].style.display = 'none';
					$('td', nTr)[34].style.display = 'none';
					$('td', nTr)[35].style.display = 'none';
					$('td', nTr)[36].style.display = 'none';
					$('td', nTr)[37].style.display = 'none';
					$('td', nTr)[38].style.display = 'none';
					$('td', nTr)[39].style.display = 'none';
					$('td', nTr)[40].style.display = 'none';
					$('td', nTr)[41].style.display = 'none';
					$('td', nTr)[42].style.display = 'none';
					$('td', nTr)[43].style.display = 'none';
					$('td', nTr)[44].style.display = 'none';
					$('td', nTr)[45].style.display = 'none';
					$('td', nTr)[46].style.display = 'none';
					$('td', nTr)[47].style.display = 'none';
					$('td', nTr)[48].style.display = 'none';
					$('td', nTr)[49].style.display = 'none';
					$('td', nTr)[50].style.display = 'none';
					$('td', nTr)[51].style.display = 'none';
					$('td', nTr)[52].style.display = 'none';
					$('td', nTr)[53].style.display = 'none';
					$('td', nTr)[54].style.display = 'none';
					$('td', nTr)[55].style.display = 'none';
					$('td', nTr)[56].style.display = 'none';
					$('td', nTr)[57].style.display = 'none';
					$('td', nTr)[58].style.display = 'none';
					$('td', nTr)[59].style.display = 'none';
					$('td', nTr)[60].style.display = 'none';
					$('td', nTr)[61].style.display = 'none';
					$('td', nTr)[62].style.display = 'none';
					$('td', nTr)[63].style.display = 'none';
					});
					oTable.fnDraw();
				$("#seleccion_general").removeAttr('checked')

				$('#clienteCriticos').delegate('td.registro_criticos','click', function(){
					var fonoBus = $(this).attr("data-telefono");
					var indice = $(this).attr("data-indice");
					var actividad = $(this).attr("data-actividad");
					var averia_ini = $(".faveria_ini"+indice).html()
					
					if(actividad=='Provision'){
						var url = "registro_clientes_criticos_provision.php?averia_ini="+averia_ini+"&actividad="+actividad+"&indice="+indice;
					}else{
						var url = "registro_clientes_criticos.php?averia_ini="+averia_ini+"&actividad="+actividad+"&indice="+indice;
					}
					var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
					$("#dialog-criticos").html(pagina);
					$("#dialog-criticos").dialog({
						 autoOpen: false,
						 closeOnScape: true,
			             modal: true,
			             height: 600,
			             width: 700,
					});

					$("#dialog-criticos").dialog( "open" );				 	
				})
		    }else{
		        alert("vacio")
		    }
		},
		error: function () {
		    //alert("No se pudo realizar la busqueda");
		}
	});
});

				$(".TableTools").append("<span style='position:absolute;font-size:11px;margin-left:-105px;margin-top:12px'><b>Generar Excel</b></span>")

				$("#clienteCriticos tbody td.gestion").live('click', function(event) {
		            id = $(this).attr("data-id")
		            indice = $(this).attr("data-indice")
		            actividad = $(this).attr("data-actividad")

		            	var url = "gestion_clientes_criticos.php?id="+id+"&indice="+indice+"&actividad="+actividad;
					var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
					$("#dialog-gestion-criticos").html(pagina);
					$("#dialog-gestion-criticos").dialog({
						 autoOpen: false,
						 closeOnScape: true,
			             modal: true,
			             height: 600,
			             width: 880,
					});

					$("#dialog-gestion-criticos").dialog( "open" );
		             
		        });

				$("#clienteCriticos tbody td.mostrar_mov").live('click', function(event) {
		            id = $(this).attr("data-id")

		            var url = "mostrar_movimientos.php?id="+id;
					var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
					$("#dialog-gestion-movimientos").html(pagina);
					$("#dialog-gestion-movimientos").dialog({
						 autoOpen: false,
						 closeOnScape: true,
			             modal: true,
			             height: 300,
			             width: 880,
					});

					$("#dialog-gestion-movimientos").dialog( "open" );
		             
		        });

				$("#clienteCriticos tbody td.mostrar_averia").live('click', function(event) {
		            id = $(this).attr("data-id")
		            actividad = $(this).attr("data-actividad")
		            var url = "mostrar_averia.php?id="+id+"&actividad="+actividad;
					var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
					$("#dialog-gestion-averias").html(pagina);
					$("#dialog-gestion-averias").dialog({
						 autoOpen: false,
						 closeOnScape: true,
			             modal: true,
			             height: 600,
			             width: 1100,
					});

					$("#dialog-gestion-averias").dialog( "open" );
		             
		        });

		        $("#clienteCriticos tbody td.mostrar_averia_raiz").live('click', function(event) {
		            id = $(this).attr("data-averia")
		            actividad = $(this).attr("data-actividad")

		            var url = "mostrar_averia.php?id="+id+"&tipo=raiz&actividad="+actividad;
					var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
					$("#dialog-gestion-averias").html(pagina);
					$("#dialog-gestion-averias").dialog({
						 autoOpen: false,
						 closeOnScape: true,
			             modal: true,
			             height: 600,
			             width: 1100,
					});

					$("#dialog-gestion-averias").dialog( "open" );
		             
		        });

		        $("#clienteCriticos tbody td.transmision").live('click', function(event) {
		            id = $(this).attr("data-id")
		            actividad = $(this).attr("data-actividad")
		            var url = "sergio.php?id="+id+"&actividad="+actividad;
					var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
					$("#dialog-transmision").html(pagina);
					$("#dialog-transmision").dialog({
						 autoOpen: false,
						 closeOnScape: true,
			             modal: true,
			             height: 700,
			             width: 1100,
					});

					$("#dialog-transmision").dialog( "open" );
		             
		        });

//Asignacion de empresas
	$(".asignar_empresa").click(function(){
		
		/*var tot_filas = oTable.fnSettings().fnRecordsTotal()
		var codigo = ""*/

		var selected = [];
		$('.listado_clientes input:checked').each(function() {
		    selected.push($(this).attr('id'));
		});

		var tot_filas = selected.length
		var codigo = ""
		var actividad = ""
		var qui=''
		var quiebre=''

		if(tot_filas>0){
			for(i=0;i<tot_filas;i++){
				if(selected[i]!="seleccion_general"){
					var check = $("#"+selected[i])
					pos = selected[i].length
					id = selected[i].substr(18,pos)
					tipo_actividad = $(".factividad"+id).html()
					var atc = check.attr("data-atc")
					var quiebre = check.attr('data-quiebre');
					if(qui==''){
						qui=quiebre;
					}
					//si me confirman valido por estado devuelto y etc
					if(quiebre==qui){
						if(check.attr("checked") && atc.indexOf("ATC_")!="-1"){
							codigo += "'" + check.attr("data-id") + "',"
							actividad += $.trim(tipo_actividad) + ","
						}else{
							alert("Si desea asignar empresa a las Temporales use el botón Registrar Pendientes")
							return -1
						}
					}
					else{
						alert("Los registros seleccionados deben tener el mismo quiebre");
						return -1
					}
				}
			}

			if(codigo==""){
				alert("No hay registros seleccionados")
			}else{
				codigo = codigo.substr(0,codigo.length-1)
				actividad = actividad.substr(0,actividad.length-1)

				var url = "asignar_empresa.php?codigo="+codigo+"&actividad="+actividad+"&quie="+quiebre;
				var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
				$("#dialog-asignar-empresa").html(pagina);
				$("#dialog-asignar-empresa").dialog({
					 autoOpen: false,
					 closeOnScape: true,
		             modal: true,
		             height: 150,
		             width: 500,
				});
				$("#dialog-asignar-empresa").dialog( "open" );
			}
		}else{
			alert("No hay datos")
		}

	});

	$(".asignar_tecnico").click(function(){
		
		var tot_filas = oTable.fnSettings().fnRecordsTotal()
		var codigo = ""
		var estado = ""
		var empresa = ""
		var qui=""
		var quiebre=""

		if(tot_filas>0){

			for(i=0;i<tot_filas;i++){
				
			
				if($("#seleccion_criticos"+i).attr("checked")){

					if(quiebre==""){
					quiebre = $("#seleccion_criticos"+i).attr("data-quiebre");	
					}
					
					if(quiebre!=$("#seleccion_criticos"+i).attr("data-quiebre")){
						alert("Los registros seleccionados deben tener el mismo quiebre"+ i+' : '+$("#seleccion_criticos"+i).attr("data-quiebre"));
						qui="si";
						break;
					}
					else{
						
						if(quiebre!='R9-REIT-CATV'){
							var atc = $("#seleccion_criticos"+i).attr("data-atc");
							if(atc.indexOf("ATC_")!="-1" && ($("#seleccion_criticos"+i).attr("data-estado")=="1" 
								|| $("#seleccion_criticos"+i).attr("data-estado")=="8" || $("#seleccion_criticos"+i).attr("data-estado")=="2")){
								if(empresa==""){
									empresa = $("#seleccion_criticos"+i).attr("data-empresa")
									codigo += "'" + $("#seleccion_criticos"+i).attr("data-id") + "',";
									estado += $("#seleccion_criticos"+i).attr("data-estado") + ",";
								}else{
									if(empresa==$("#seleccion_criticos"+i).attr("data-empresa")){
										codigo += "'" + $("#seleccion_criticos"+i).attr("data-id") + "',";
										estado += $("#seleccion_criticos"+i).attr("data-estado") + ",";
									}else{
										alert("Los registros seleccionados deben tener la misma empresa");
										return -1
									}
								}
							}else{
								alert("Solo se puede asignar técnicos a las ATC que esten agendadas o Pendientes");
								return -1
							}
						}
						else{
							qui="si";
							alert('Lo sentimos para gestionar quiebre:R9-REIT-CATV; Debe realizarse en el modulo de gestion.');
							break;
						}
					}
				}
			}

			if(codigo=="" && qui!="si"){
				alert("No hay registros seleccionados")
			}
			else if(qui!="si"){
				codigo = codigo.substr(0,codigo.length-1)
				estado = estado.substr(0,estado.length-1)
				var url = "asignar_tecnico.php?empresa="+empresa+"&codigo="+codigo+"&estado="+estado+"&quiebre="+quiebre;
				var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
				$("#dialog-asignar-tecnico").html(pagina);
				$("#dialog-asignar-tecnico").dialog({
					 autoOpen: false,
					 closeOnScape: true,
		             modal: true,
		             height: 180,
		             width: 490,
				});
				$("#dialog-asignar-tecnico").dialog( "open" );
			}

		}else{
			alert("No hay datos")
		}

	});

//Asignacion de Agendas Pendientes
	$(".asignar_pendientes").click(function(){
		
		var selected = [];
		$('.listado_clientes input:checked').each(function() {
		    selected.push($(this).attr('id'));
		});

		//var tot_filas = oTable.fnSettings().fnRecordsTotal()
		var tot_filas = selected.length
		
		var codigo = ""
		var empresa = ""
		var actividad = ""
		var quiebre=""
		var quie=""

		if(tot_filas>0){
			for(i=0;i<tot_filas;i++){
				if(selected[i]!="seleccion_general"){
					var check = $("#"+selected[i])
					pos = selected[i].length
					id = selected[i].substr(18,pos)
					averia = $(".faveria_ini"+id).html()
					tipo_actividad = $(".factividad"+id).html()
					quiebre= check.attr('data-quiebre');

					if(quie==''){
						quie=quiebre;
					}

					if(quiebre==quie){
						if(check.attr("checked") && check.attr("data-atc")=="" && check.attr("data-estado")=="Temporal" && check.attr("data-empresa")!=""){
							//codigo += "'" + $.trim(fono.html()) + "',"
							if(empresa==""){
								empresa = check.attr("data-empresa")
								codigo += $.trim(averia) + ","
								actividad += $.trim(tipo_actividad) + ","
							}else{
								//alert(empresa+"-"+check.attr("data-empresa"))
								if(empresa==check.attr("data-empresa")){
									empresa=empresa
									codigo += $.trim(averia) + ","
									actividad += $.trim(tipo_actividad) + ","
								}else{
									empresa = empresa + ","
									codigo += $.trim(averia) + ","
									actividad += $.trim(tipo_actividad) + ","
								}
							}
						}else if(check.attr("checked") && check.attr("data-atc")!=""){
							alert("Solo se pueden registrar masivamente como Pendientes los registros con estado Temporal")
							return -1
						}
					}
					else{
						alert("Los registros seleccionados deben tener el mismo quiebre")
						return -1
					}
				}
			}

			if(codigo==""){
				alert("No hay registros seleccionados")
			}else{
				codigo = codigo.substr(0,codigo.length-1)
				actividad = actividad.substr(0,actividad.length-1)
				//alert(codigo+"-"+empresa+"-"+actividad)
				var url = "asignar_pendiente.php?codigo="+codigo+"&empresa="+empresa+"&actividad="+actividad+"&quiebre="+quiebre;
				var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
				$("#dialog-asignar-pendiente").html(pagina);
				$("#dialog-asignar-pendiente").dialog({
					 autoOpen: false,
					 closeOnScape: true,
		             modal: true,
		             height: 210,
		             width: 700,
				});
				$("#dialog-asignar-pendiente").dialog( "open" );
			}
		}else{
			alert("No hay datos")
		}

	});

$(".registro_manual").click(function(){

	var url = "registro_manual_criticos.php";
	var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
	$("#dialog-registro-manual").html(pagina);
	$("#dialog-registro-manual").dialog({
		autoOpen: false,
	    closeOnScape: true,
		modal: true,
		height: 500,
		width: 800,
	});
	$("#dialog-registro-manual").dialog( "open" );
});
/**********/

$("#clienteCriticos_next").click(function(){
		/*var selected = [];
		$('.listado_clientes input:checked').each(function() {
		    selected.push($(this).attr('id'));
		});

		var marca = ""
		for(i=0;i<selected.length;i++){
        	if($("#"+selected[i]).attr('checked')){
        		marca = "1"
        	}
    	}

    	if(marca==1){
    		$("#seleccion_general").attr('checked','checked')
    	}*/

    	var selected = [];
		$('.listado_clientes input:checked').each(function() {
		    selected.push($(this).attr('id'));
		});

		var marca = ""
		for(i=0;i<selected.length;i++){
        	$("#"+selected[i]).removeAttr('checked')
    	}

    	$("#seleccion_general").removeAttr('checked')
});

$("#clienteCriticos_previous").click(function(){
	var selected = [];
		$('.listado_clientes input:checked').each(function() {
		    selected.push($(this).attr('id'));
		});

		var marca = ""
		for(i=0;i<selected.length;i++){
        	$("#"+selected[i]).removeAttr('checked')
    	}

    	$("#seleccion_general").removeAttr('checked')
 
});

$("#seleccion_general").click(function(){

	var selected = [];
	$('.listado_clientes input[type=checkbox]').each(function() {
	    selected.push($(this).attr('id'));
	});

	if($("#seleccion_general").attr('checked')){//si ya esta marcado
    	for(i=0;i<selected.length;i++){
        	$("#"+selected[i]).attr('checked', 'checked')
    	}
	}else{
		for(i=0;i<selected.length;i++){
        	$("#"+selected[i]).removeAttr('checked')
    	}
	}
	
});

$(".visor_gps").click(function(){

	var url = "../officetrack/ruta.geo.php";
	var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
	$("#dialog-visor-gps").html(pagina);
	$("#dialog-visor-gps").dialog({
		autoOpen: false,
	   closeOnScape: true,
		modal: true,
		height: '800',
		width: '90%',
	});
	$("#dialog-visor-gps").dialog( "open" );
});

$(".horarios").click(function(){

	var url = "horarios_tecnicos.php";
	var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
	$("#dialog-horarios").html(pagina);
	$("#dialog-horarios").dialog({
		autoOpen: false,
	    closeOnScape: true,
		modal: true,
		height: '700',
		width: '80%',
	});
	$("#dialog-horarios").dialog( "open" );
});

$(".pendientes").click(function(){

	var url = "informe_por_estados.php";
	var pagina = '<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>'
	$("#dialog-pendientes").html(pagina);
	$("#dialog-pendientes").dialog({
		autoOpen: false,
	    closeOnScape: true,
		modal: true,
		height: '700',
		width: '80%',
	});
	$("#dialog-pendientes").dialog( "open" );
});

});
