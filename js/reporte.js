$(document).ready(function(){

  $( ".buscarFecha" ).datepicker({
     showOn: "button",
       buttonImage: "img/calendar.gif",
       buttonImageOnly: true,
  });
  $( ".buscarFecha" ).datepicker( "option", "dateFormat","yy-mm-dd");
   
  $("#fecha_ini").datepicker('setDate', $('#fecha_i').val());
  $("#fecha_fin").datepicker('setDate', $('#fecha_f').val());  


  $("#slct_quiebre").multiselect();

  if($("#slct_quiebre").val()==null){
    $("#slct_quiebre").multiselect("checkAll");  
  }
  

  $( "form" ).submit(function( event ) {
    if($("#fecha_ini").val()==''){
      alert('Seleccione una fecha inicio');
      event.preventDefault();
    }
    else if($("#fecha_fin").val()==''){
      alert('Seleccione una fecha fin');
      event.preventDefault();
    }
    else if($("#slct_quiebre").val()==null){
      alert('Seleccione almenos 1 quiebre');
      event.preventDefault();
    }
    else if($("#fecha_ini").val()>$("#fecha_fin").val()){
      alert('La fecha inicial: '+$("#fecha_ini").val()+' no puede ser mayor a la fecha final: '+$("#fecha_fin").val());
      event.preventDefault();
    }
    
  });
  
})
