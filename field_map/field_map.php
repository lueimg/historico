
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<div>
    <input type="text" maxlength="100" id="address" placeholder="Dirección" />
    <input type="button" id="search" value="Buscar" />
</div><br/>
<div id='map_canvas' style="width:450px; height:150px;text-align: center"></div>


<script type="text/javascript">
    $(document).ready(function() {
        load_map();
    });

    var map;

    function load_map() {
        var myLatlng = new google.maps.LatLng(-12.046374, -77.0427934);
        var myOptions = {
            zoom:12,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map($("#map_canvas").get(0), myOptions);
    }

    $('#search').live('click', function() {
        // Obtenemos la dirección y la asignamos a una variable
        var address = $('#address').val();
        // Creamos el Objeto Geocoder
        var geocoder = new google.maps.Geocoder();
        // Hacemos la petición indicando la dirección e invocamos la función
        // geocodeResult enviando todo el resultado obtenido
        geocoder.geocode({ 'address': address}, geocodeResult);
    });

    function geocodeResult(results, status) {

        // Verificamos el estatus
        if (status == 'OK') {
            // Si hay resultados encontrados, centramos y repintamos el mapa
            // esto para eliminar cualquier pin antes puesto
            var mapOptions = {
                center: results[0].geometry.location,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map($("#map_canvas").get(0), mapOptions);
            // fitBounds acercará el mapa con el zoom adecuado de acuerdo a lo buscado
            map.fitBounds(results[0].geometry.viewport);
            // Dibujamos un marcador con la ubicación del primer resultado obtenido
            var markerOptions = {
                position: results[0].geometry.location,
                draggable: true,
                map: map
            }
            var marker = new google.maps.Marker(markerOptions);

            google.maps.event.addListener(marker, 'click', function(){
                var markerLatLng = marker.getPosition();
                SetXY(markerLatLng.lat(),markerLatLng.lng())
            });
            google.maps.event.addListener(marker, 'dragend', function(){
                var markerLatLng = marker.getPosition();
                SetXY(markerLatLng.lat(),markerLatLng.lng())
            });
            //mostrar
            SetXY(results[0].geometry.location.lat(), results[0].geometry.location.lng())

        } else {
            // En caso de no haber resultados o que haya ocurrido un error
            // lanzamos un mensaje con el error
            alert("Geocoding no tuvo éxito debido a: " + status);
        }
    }

    function SetXY(x,y){
        $("#x").val(x);
        $("#y").val(y);
    }





</script>