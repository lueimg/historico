<?php
/**
 * Created by PhpStorm.
 * User: lmori
 * Date: 25/09/14
 * Time: 12:34 PM
 */
?>

<script>
    $(function(){
        $("#horario td").click(function(){

            color = $(this).css("background-color")
            //para IE8 ya que toma el color como lo pone
            if(color.indexOf('#')!=-1){
                color = color
            }else{
                color = hexcolor(color)
            }

            id_celda = $(this).attr("title")

            horario_celda = document.getElementById("horario").getElementsByTagName("td")[id_celda]
            totales = horario_celda.getAttribute("data-total");
            //total = $(this).attr("data-total")

            if(color!="#ff0000" && color!="#ffff00" && color!="#c4e0f2" && totales>0){

                $("#horario td").each(function(){
                    color = $(this).css("background-color")
                    if(color.indexOf('#')!=-1){
                        color = color
                    }else{
                        color = hexcolor(color)
                    }
                    if(color!="#ff0000" && color!="#ffff00" && color!="#c4e0f2"){
                        $(this).css({"background":"","color":""})
                    }
                })

                $(this).css({"background":"green","color":"#fff"})
                /*$("#fecha_agenda").val($(this).attr("data-fec"))
                 $("#horario_agenda").val($(this).attr("data-horario"))
                 $("#dia_agenda").val($(this).attr("data-dia"))
                 $("#hora_agenda").val($(this).attr("data-hora"))*/
                $("#fecha_agenda").val(horario_celda.getAttribute("data-fec"))
                $("#horario_agenda").val(horario_celda.getAttribute("data-horario"))
                $("#dia_agenda").val(horario_celda.getAttribute("data-dia"))
                $("#hora_agenda").val(horario_celda.getAttribute("data-hora"))

                $(".horario .help-inline").css("display","none")
                $(".fecha_error").html("").css("display","none")

            }
        })
    })
</script>