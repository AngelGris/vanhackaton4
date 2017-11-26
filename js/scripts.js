var map;
var marker;
var infowindow;
var latlng;
var player;

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
    $('img[data-toggle="tooltip"]').tooltip({
        container: '.megamenu'
    });

    /**
     * Tooltips for video thumbnails
     */
    $('.videos-play[data-toggle="tooltip"]').tooltip({
        container: '#modal-videos-body',
        offset: '0, 33',
    });

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

    /**
     * Video gallery scrollers
     */
    $('#modal-videos').on('shown.bs.modal', function() {
        if ($('#videos-listing').width() <= $('#videos-wrapper').width()) {
            $('#videos-left').hide();
            $('#videos-right').hide();
        }
    });

    $('#videos-left').click(function(e) {
        e.preventDefault();

        var pos = $('#videos-listing').position();
        $('#videos-listing').animate({
            left : Math.min(0, pos.left + $('#videos-wrapper').width()),
        });
    });

    $('#videos-right').click(function(e) {
        e.preventDefault();

        var pos = $('#videos-listing').position();
        $('#videos-listing').animate({
            left : Math.max($('#videos-wrapper').width() - $('#videos-listing').width(), pos.left - $('#videos-wrapper').width()),
        });
    });

    $('.videos-play').click(function(e) {
        e.preventDefault();

        $('#videos-player').attr('src', 'https://www.youtube.com/embed/' + $(this).attr('href') + '?autoplay=true&enablejsapi=1&rel=0');
    });

    /**
     * On videos modal close pause the video player
     */
    $('#modal-videos').on('hide.bs.modal', function() {
        player.pauseVideo();
    });
});


/**
 * Use YouTube API to pause video when videos modal is closed
 */
function onYouTubePlayerAPIReady() {
    player = new YT.Player('videos-player', {
        events: {
            'onReady': onPlayerReady
        }
    });
}

function onPlayerReady(event) {
    var pauseButton = document.getElementById("pause-button");
        pauseButton.addEventListener("click", function() {
        player.pauseVideo();
    });
}

var tag = document.createElement('script');
tag.src = "//www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);