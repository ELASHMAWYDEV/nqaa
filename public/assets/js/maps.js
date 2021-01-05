// @ts-nocheck
/*--------Show Map BEGIN----------*/

function showMap() {
    var map_con = document.getElementById('map-order');
    var default_lng = map_con.getAttribute('data-lng') || 39.51939;
    var default_lat = map_con.getAttribute('data-lat') || 24.46405;
    var map = new google.maps.Map(map_con, {
    center: new google.maps.LatLng(default_lat, default_lng),
    zoom: 14
    });
    var infoWindow = new google.maps.InfoWindow;


    var name = map_con.getAttribute('data-name');
    var address = map_con.getAttribute('data-address');
    var phone = map_con.getAttribute('data-phone');
    var point = new google.maps.LatLng(
        parseFloat(map_con.getAttribute('data-lat')),
        parseFloat(map_con.getAttribute('data-lng')));

    var infowincontent = document.createElement('div');
    var strong = document.createElement('strong');
    strong.textContent = name;
    infowincontent.appendChild(strong);
    infowincontent.appendChild(document.createElement('br'));

    var text = document.createElement('text');
    text.textContent = address;
    infowincontent.appendChild(text);
    infowincontent.appendChild(document.createElement('br'));

    var text = document.createElement('text');
    text.textContent = phone;
    infowincontent.appendChild(text);

    var marker = new google.maps.Marker({
    map: map,
    position: point,
    });
    marker.addListener('click', function() {
    infoWindow.setContent(infowincontent);
    infoWindow.open(map, marker);
    });
}



/*--------Show Map END----------*/


/*
*
*
*
*       USER MAP SET MARKER
*
*
*
*/

/*---------User Map Set Marker BEGIN---------*/

function userMap() {
    var map_con = document.getElementById('map-form');
    var default_lng = 39.51939;
    var default_lat = 24.46405;
    var map = new google.maps.Map(map_con, {
    center: new google.maps.LatLng(default_lat, default_lng),
    zoom: 10
    });



    //by default center to current location
    navigator.geolocation.getCurrentPosition(function(position) {
        map.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
    }, function() {
        console.log('Cannot get current position');
    });



    var markers_array = [];

    google.maps.event.addListener(map, 'click', function setMarker(event) {;
        clearMap();
        placeMarker(event.latLng);
    });

    function placeMarker(location) {
        let marker = new google.maps.Marker({
            position: location, 
            map: map
        });

        // map.setCenter(location);
        map.setZoom(15);
        setLatLng(location.lat(), location.lng());
        markers_array.push(marker);
    }

    function clearMap() {
        for (let i = 0; i < markers_array.length; i++) {
            markers_array[i].setMap(null)
        }
        markers_array = [];
    }

    function setLatLng(lat, lng) {
        let latInput = document.querySelector('input[name="lat"]');
        let lngInput = document.querySelector('input[name="lng"]');

        latInput.setAttribute('value', lat);
        lngInput.setAttribute('value', lng);
        
    }




    
}

/*---------User Map Set Marker END---------*/
