var googleMap;
var googleMapIsInitialized = false;
function initialize() {
    var initLat = $('#latitude_google').val();
    if (initLat === '') {
        initLat = '3.0968929878971525';
    }

    var initLong = $('#longitude_google').val();
    if (initLong === '') {
        initLong = '101.6631031036377';
    }

    var myLatlng = new google.maps.LatLng(initLat, initLong);
    var mapOptions = {
        zoom: 10,
        center: myLatlng,
        disableDefaultUI: true,
        panControl: true,
        zoomControl: true,
        mapTypeControl: true,
        streetViewControl: true,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    googleMap = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
    var marker = new google.maps.Marker({
        position: myLatlng,
        map: googleMap,
        visible: true,
        draggable:true,
    });

    google.maps.event.addListener(marker, 'dragend', function(e) {
        var lat = this.getPosition().lat();
        var lng = this.getPosition().lng();
        $('#latitude').val(lat);
        $('#longtitude').val(lng);
        $('#latitude_google').val(lat);
        $('#longitude_google').val(lng);
    });

    var input = document.getElementById('location_google');
    var autocomplete = new google.maps.places.Autocomplete(input);

    // Bias the SearchBox results towards current map's viewport.
    googleMap.addListener('bounds_changed', function() {
        autocomplete.setBounds(googleMap.getBounds());
    });

    googleMap.addListener('idle', function() {
        if (googleMapIsInitialized == false) {
            setTimeout(switchMap, 1000);
        }
        googleMapIsInitialized = true;
    });

    //autocomplete.bindTo('bounds', googleMap);
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();

        if (place.geometry.viewport) {
            googleMap.fitBounds(place.geometry.viewport);
            var myLatlng = place.geometry.location;
            var lat = place.geometry.location.lat();
            var lng = place.geometry.location.lng();
            $('#latitude').val(lat);
            $('#longtitude').val(lng);
            $('#latitude_google').val(lat);
            $('#longitude_google').val(lng);

            if (marker) {
                //if marker already was created change positon
                marker.setPosition(myLatlng);
            } else {
                //create a marker
                marker = new google.maps.Marker({
                    position: myLatlng,
                    visible: true,
                    map: googleMap,
                    draggable:true,
                });
            }

            google.maps.event.addListener(marker, 'dragend', function(e) {
                var lat = this.getPosition().lat();
                var lng = this.getPosition().lng();
                $('#latitude').val(lat);
                $('#longtitude').val(lng);
                $('#latitude_google').val(lat);
                $('#longitude_google').val(lng);
            });
        } else {
            googleMap.setCenter(place.geometry.location);
            googleMap.setZoom(17);
        }
    });
}
google.maps.event.addDomListener(window, 'load', initialize);