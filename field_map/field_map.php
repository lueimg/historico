
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places"></script>
<div style="display: none">
    <input type="text" maxlength="100" id="address" placeholder="Dirección" style="width:250px;" />
</div><br/>
<div id='map_canvas' style="width:450px; height:150px;text-align: center"></div>


<script type="text/javascript">
    $(document).ready(function() {
//        load_map();
        initialize()
    });

    var map;

//    function load_map() {
//        var myLatlng = new google.maps.LatLng(-12.046374, -77.0427934);
//        var myOptions = {
//            zoom:12,
//            center: myLatlng,
//            mapTypeId: google.maps.MapTypeId.ROADMAP
//        };
//        map = new google.maps.Map($("#map_canvas").get(0), myOptions);
//    }



//    function geocodeResult(results, status) {
//
//        // Verificamos el estatus
//        if (status == 'OK') {
//            // Si hay resultados encontrados, centramos y repintamos el mapa
//            // esto para eliminar cualquier pin antes puesto
//            var mapOptions = {
//                center: results[0].geometry.location,
//                mapTypeId: google.maps.MapTypeId.ROADMAP
//            };
//            map = new google.maps.Map($("#map_canvas").get(0), mapOptions);
//            // fitBounds acercará el mapa con el zoom adecuado de acuerdo a lo buscado
//            map.fitBounds(results[0].geometry.viewport);
//            // Dibujamos un marcador con la ubicación del primer resultado obtenido
//            var markerOptions = {
//                position: results[0].geometry.location,
//                draggable: true,
//                map: map
//            }
//            var marker = new google.maps.Marker(markerOptions);
//
//            google.maps.event.addListener(marker, 'click', function(){
//                var markerLatLng = marker.getPosition();
//                SetXY(markerLatLng.lat(),markerLatLng.lng())
//            });
//            google.maps.event.addListener(marker, 'dragend', function(){
//                var markerLatLng = marker.getPosition();
//                SetXY(markerLatLng.lat(),markerLatLng.lng())
//            });
//
//
//            google.maps.event.addListener(map, 'click', function(evento) {
//
//                var latitud = evento.latLng.lat();
//                var longitud = evento.latLng.lng();
//
//                SetXY(latitud,longitud);
//                var new_marker_position = new google.maps.LatLng(latitud, longitud);
//                marker.setPosition(new_marker_position);
//
//            });
//
//
//
//            //mostrar
//            SetXY(results[0].geometry.location.lat(), results[0].geometry.location.lng())
//
//        } else {
//            // En caso de no haber resultados o que haya ocurrido un error
//            // lanzamos un mensaje con el error
//            alert("Geocoding no tuvo éxito debido a: " + status);
//        }
//    }

    function SetXY(x,y){
        $("#x").val(x);
        $("#y").val(y);
    }
    var markers = [];
    function initialize() {

        var myLatlng = new google.maps.LatLng(-12.046374, -77.0427934);
        var map = new google.maps.Map(document.getElementById('map_canvas'), {
            zoom:12,
            center: myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });


        // Create the search box and link it to the UI element.
        var input = /** @type {HTMLInputElement} */(
            document.getElementById('address'));
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        var searchBox = new google.maps.places.SearchBox(
            /** @type {HTMLInputElement} */(input));

        // [START region_getplaces]
        // Listen for the event fired when the user selects an item from the
        // pick list. Retrieve the matching places for that item.
        google.maps.event.addListener(searchBox, 'places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }
            for (var i = 0, marker; marker = markers[i]; i++) {
                marker.setMap(null);
            }

            // For each place, get the icon, place name, and location.
            markers = [];
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0, place; place = places[i]; i++) {
                var image = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                var marker = new google.maps.Marker({
                    map: map,
                    icon: image,
                    title: place.name,
                    position: place.geometry.location
                });

                SetXY(place.geometry.location.lat(), place.geometry.location.lng())
                markers.push(marker);

                bounds.extend(place.geometry.location);
            }

            map.fitBounds(bounds);
            map.setZoom(16);
        });

        google.maps.event.addListener(map, 'bounds_changed', function() {
            var bounds = map.getBounds();
            searchBox.setBounds(bounds);
        });

        google.maps.event.addListener(map, 'click', function(evento) {

            var latitud = evento.latLng.lat();
            var longitud = evento.latLng.lng();

            SetXY(latitud,longitud);
            for (var i = 0, marker; marker = markers[i]; i++) {
                marker.setMap(null);
            }

            var markerOptions = {
                draggable: true,
                map: map
            }
            var new_marker_position = new google.maps.LatLng(latitud, longitud);
            var marker = new google.maps.Marker(markerOptions);
            marker.setPosition(new_marker_position);
            markers.push(marker);

            google.maps.event.addListener(marker, 'click', function(){
                var markerLatLng = marker.getPosition();
                SetXY(markerLatLng.lat(),markerLatLng.lng())
            });
            google.maps.event.addListener(marker, 'dragend', function(){
                var markerLatLng = marker.getPosition();
                SetXY(markerLatLng.lat(),markerLatLng.lng())
            });

        });

    }





</script>