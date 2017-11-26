<?php
require_once('includes/config.php');
session_start();

/**
 * If there's a search query q, perform teh search
 */
if (!empty($_GET['q'])) {
    $artist_url = 'https://rest.bandsintown.com/artists/' . $_GET['q'] . '/';
    $artist = json_decode(file_get_contents($artist_url . '?app_id=' . APP_ID));

    /**
     * If not artist found, prepare error message
     */
    if (empty($artist)) {
        $error_message = 'No results found for "' . strtoupper($_GET['q']) . '"';
    }
}

/**
 * If no artist found, or no search performed and artist ID provided
 */
if (empty($artist) and !empty($_GET['id'])) {
    /**
     * If artist ID provided is in the cache, load artist from history cache
     * If not in cacche, prepare error message
     */
    if (!empty($_SESSION['history'][$_GET['id']])) {
        $artist = $_SESSION['history'][$_GET['id']];
        $artist_url = 'https://rest.bandsintown.com/artists/' . $artist->name . '/';
    } else {
        $error_message = 'Requested artist is not in your history, please use the search box to find it';
    }
}

/**
 * If artist loaded, apply changes to search history
 */
if (!empty($artist)) {
    $artist->term = $_GET['q'];

    if (!empty($_SESSION['history'])) {
        /**
         * Remove artist from history if already there
         */
        unset($_SESSION['history'][$artist->id]);

        /**
         * Limit the number of previous artists to 6
         * The array will actualy have 7 artists, but since the last element is the current artist
         * we don't need to show it, so just need to show to first 6 items
         */
        if (count($_SESSION['history']) > 6) {
            $_SESSION['history'] = array_slice($_SESSION['history'], 1, 6, tue);
        }
    }

    /**
     * Add user to the end of the history list
     */
    $_SESSION['history'][$artist->id] = $artist;
} elseif (!empty($_SESSION['history'])) {
    /**
     * If not artist loaded and history is not empty, then load last successful search
     */
    $artist = end($_SESSION['history']);
    $artist_url = 'https://rest.bandsintown.com/artists/' . $artist->name . '/';
}

/**
 * Get artist's videos
 */
if (!empty($artist)) {
    $videos = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&videoCategoryId=10&maxResults=20&q=' . urlencode($artist->name) . '&key=' . YOUTUBE_API_KEY));
}
?>
<!DOCTYPE html>
<html lang="en-us">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Vanhackaton 4.0 - Frontend Challenge</title>
        <link rel="shortcut icon" href="img/favicon.ico">
        <meta name="author" content="Alex Camargo - Luciano Garcia Bes">
        <meta name="description" content="Solution for frontend challenge in Vanhackaton 4.0">
        <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700%7CMontserrat:400,700%7CRaleway:300,400,600" rel="stylesheet">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/fonts.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body onLoad="initMap();">
        <div id="wrapper">
            <div class="header-transparent header-transparent-light menu-fixed-dark menu-dark-mobiles xs-menu-wrapper-dark">
                <header class="header-wrapper">
                    <div class="megamenu">
                        <div class="row">
                            <div class="col-sm-8 col-md-6">
                                <form method="GET" class="menu-search">
                                    <input type="text" name="q" placeholder="Search">
                                    <button type="submit" class="default"><i class="icon icon_search"></i></button>
                                </form>
                            </div>
                            <div id="previous-searches" class="col-sm-4 col-md-6">
<?php
/**
 * If have previous searches, show history
 */
if (!empty($_SESSION['history'])) {
    foreach (array_reverse(array_slice($_SESSION['history'], (empty($artist) ? 1 : 0), (empty($artist) ? 6 : count($_SESSION['history']) - 1), true), true) as $key => $history) {
?>
                                <div class="history">
                                    <a href="./?id=<?php echo($key); ?>"><img src="<?php echo($history->thumb_url); ?>" data-toggle="tooltip" title="<?php echo($history->name); ?>"></a>
                                </div>
<?php
    }
}
?>
                            </div>
                        </div>
<?php
if (!empty($error_message)) {
?>
                        <div class="alert-danger" style="padding:0 10px;">
                            <?php echo($error_message); ?>
                        </div>
<?php
}
?>
                    </div>
                </header>
            </div>
            <section class="section-larger bg-img bg53 stellar" data-stellar-background-ratio="0.4">
                <div class="intro-with-transparent-menu"></div>
                <div class="bg-overlay gradient-1"></div>
                <div class="container">
                    <div class="row mt50 mb50">
                        <div class="col-sm-12 col-sm-offset-2 text-center">
<?php
if (empty($artist)) {
?>
                            <h3 class="title-slider small uppercased mb20 color-main">follow your favorite artists around the globe</h3>
                            <h2 class="title-slider large uppercased mb40 word-wrap">search your fav artists<br>go to their next show</h2>
<?php
} else {
?>
                            <img class="imgBand" src="<?php echo($artist->thumb_url); ?>">
                            <h2 class="title-slider large uppercased mb40 word-wrap"><?php echo($artist->name); ?></h2>
                            <h3>
<?php
    if (!empty($artist->facebook_page_url)) {
?>
                                <a href="<?php echo($artist->facebook_page_url); ?>" target="_blank"><span class="fa fa-facebook-square social-network"></span></a>
<?php
    }

    if (!empty($videos->items)) {
?>
                                <a href="#" data-toggle="modal" data-target="#modal-videos" data-keyboard="true"><span class="fa fa-youtube social-network"></span></a>
<?php
    }
?>
                            </h3>
<?php
}
?>
                        </div>
                    </div>
                </div>
            </section>
            <div class="shadow3"></div>
<?php
if (!empty($artist)) {
?>
            <section class="section-bg section-gray section-large">
                <div class="container">
                    <div class="row col-p30">
                        <div class="col-sm-12 sm-box3">
                            <div class="mb20"></div>
                            <h3 class="title-uppercased large color-main mb30">upcoming events</h3>
                        </div>
                        <div class="col-sm-12">
<?php
    if ($artist->upcoming_event_count == 0) {
?>
                            <div class="br-bottom mt40 mb0"></div>
                            <h3>NO UPCOMING EVENTS</h3>
<?php
    } else {
        $events = json_decode(file_get_contents($artist_url . 'events/?app_id=' . APP_ID));

        foreach ($events as $event) {
            $tickets = FALSE;
            foreach ($event->offers AS $offer) {
                if ($offer->type == 'Tickets' && $offer->status == 'available') {
                    $tickets = $offer->url;
                }
            }
?>
                            <div class="col-md-4 event" style="height:170px;">
                                <div class="br-bottom mt40 mb0"></div>
<?php
        $date = date(TIME_FORMAT, strtotime($event->datetime));
        $location = $event->venue->city . ', ' . (!empty($event->venue->region) && !ctype_digit($event->venue->region) ? $event->venue->region . ', ' : '') . $event->venue->country;
?>
                                <a href="#" class="show-event" data-date="<?php echo($date); ?>" data-venue="<?php echo(strtoupper($event->venue->name)); ?>" data-location="<?php echo($location); ?>" data-latitude="<?php echo($event->venue->latitude); ?>" data-longitude="<?php echo($event->venue->longitude); ?>" data-lineup="<?php echo('<li>' . implode('</li><li>', $event->lineup) . '</li>'); ?>" data-tickets="<?php echo($tickets); ?>" >
                                    <div style="position:relative;">
                                        <h3 class="title-small">
<?php
        echo($date);
        if ($tickets) {
            echo(' <span class="fa fa-ticket" style="color:#f00;font-size:12px;"></span>');
        }
?>
                                        </h3>
                                        <p><?php echo(strtoupper($event->venue->name)); ?></p>
                                        <p><?php echo($location); ?></p>
                                    </div>
                                </a>
                            </div>
<?php
        }
    }
?>
                        </div>
                    </div>
                </div>
            </section>
<?php
}
?>
            <footer class="footer-wrapper footer-background style-2 bg-img stellar" data-stellar-background-ratio="0.4">
                <div class="sub-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <p class="copyright">&copy; &nbsp; Copyright 2017 Vanhackaton 4.0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
<?php
if (!empty($videos->items)) {
?>
        <!-- Videos Modal -->
        <div id="modal-videos" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="pause-button">
                            <span aria-hidden="true" id="closeModalVideo">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modal-videos-body">

                        <iframe id="videos-player" src="https://www.youtube.com/embed/<?php echo($videos->items[0]->id->videoId); ?>?enablejsapi=1&rel=01" frameborder="0" allowfullscreen></iframe>

                        <div id="videos-wrapper">
                            <div id="videos-left"><span class="fa fa-angle-left"></span></div>
                            <div id="videos-listing" style="width:<?php echo(count($videos->items) * 130); ?>px">
<?php
    foreach($videos->items as $video) {
?>
                                <div><a href="<?php echo($video->id->videoId); ?>" class="videos-play" title="<?php echo($video->snippet->title); ?>" data-toggle="tooltip"><img src="<?php echo($video->snippet->thumbnails->default->url); ?>" style="width:120px;"></a></div>
<?php
    }
?>
                            </div>
                            <div id="videos-right"><span class="fa fa-angle-right"></span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
}
?>
        <!-- Events Modal -->
        <div id="modal-event" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-6">
                            <h2 id="modal-event-date">01/01/1900 00:00am</h2>
                            <h3 id="modal-event-title">VENUE NAME</h3>
                            <p id="modal-event-location">City, State, Country</p>
                            <ul id="modal-event-lineup"></ul>
                        </div>
                        <div class="col-sm-6">
                            <div id="map"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" id="modal-buy" class="btn btn-primary" target="_blank">Buy tickets</a>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/jquery.min.js"></script>
        <script src="js/popper.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAm3KarFLFFjlDCuVDIcixLRhQ-ANyGwAc" async defer></script>
        <script src="js/scripts.js"></script>
    </body>
</html>