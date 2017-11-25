var map;
var marker;
var infowindow;
var latlng;

/**
 * Initialize Google Maps
 */
function initMap(lat = 0, lng = 0, venue = '') {
    latlng = new google.maps.LatLng(lat, lng);

    map = new google.maps.Map(document.getElementById('map'), {
        enter: latlng,
        zoom: 14
    });

    marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: venue
    });

    marker.addListener('click', function() {
        infowindow.open(map, marker);
    });

    infowindow = new google.maps.InfoWindow({
        content: ''
    });
}

$(function() {
    /**
     * Tooltips for search history artists
     */
    $('[data-toggle="tooltip"]').tooltip();

    /**
     * Trigger search when ENTER is pressed
     */
    $("form.menu-search input").keypress(function (e) {
        if ((e.which && e.which == 13) || (e.keyCode && e.keyCode == 13)) {
            $("form.menu-search").submit();
            return false;
        } else {
            return true;
        }
    });

    /**
     * Change information in modal and how it when event is clicked
     */
    $('.show-event').click(function(e) {
        e.preventDefault();

        latlng = new google.maps.LatLng($(this).data('latitude'), $(this).data('longitude'));

        $('#modal-event-date').text($(this).data('date'));
        $('#modal-event-title').text($(this).data('venue'));
        $('#modal-event-location').text($(this).data('location'));
        $('#modal-event-lineup').html($(this).data('lineup'));
        map.setCenter(latlng);
        marker.setPosition(latlng);

        infowindow.setContent('<div>' +
                     '<h5>' + $(this).data('venue') + '</h5>' +
                     '<p>' + $(this).data('date') + '</p>' +
                     '<ul>' +
                     $(this).data('lineup') +
                     '</ul>' +
                     '</div>');

        if ($(this).data('tickets') == '') {
            $('#modal-buy').hide();
        } else {
            $('#modal-buy').attr('href', $(this).data('tickets'));
            $('#modal-buy').show();
        }

        $('#modal-event').modal('show');
    });

    /** Fix for Google Maps in dynamic elements **/
    $('#modal-event').on('shown.bs.modal', function() {
        google.maps.event.trigger(map, "resize");
        map.setCenter(latlng);
    });
});