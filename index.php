<?php
require_once('includes/config.php');

if (!empty($_GET['q'])) {
    $artist_url = 'https://rest.bandsintown.com/artists/' . $_GET['q'] . '/';
    $artist = json_decode(file_get_contents($artist_url . '?app_id=' . APP_ID));
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
        <meta name="author" content="Luciano Garcia Bes">
        <meta name="description" content="Solution for frontend challenge in Vanhackaton 4.0">
        <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700%7CMontserrat:400,700%7CRaleway:300,400,600" rel="stylesheet">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/fonts.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="css/owl.carousel.min.css">
        <link rel="stylesheet" href="css/main.css">
    </head>
    <body>
        <div id="wrapper">
            <div class="header-transparent header-transparent-light menu-fixed-dark menu-dark-mobiles xs-menu-wrapper-dark">
                <header class="header-wrapper">
                    <div class="megamenu">
                        <div class="row">
                            <div class="col-12">
                                <form method="GET" class="menu-search">
                                    <input type="text" name="q" placeholder="Search">
                                    <button type="submit" class="default"><i class="icon icon_search"></i></button>
                                </form>
                            </div>
                        </div>
<?php
if (!empty($_GET['q']) and empty($artist)) {
?>
                        <div class="alert-danger" style="padding:0 10px;">
                            No results found for "<?php echo(strtoupper($_GET['q'])); ?>"
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
                        <div class="col-sm-8 col-sm-offset-2 text-center">
<?php
if (empty($artist)) {
?>
                            <h3 class="title-slider small uppercased mb20 color-main">follow your favorite artists around the globe</h3>
                            <h2 class="title-slider large uppercased mb40 word-wrap">search your fav artists<br>go to their next show</h2>
<?php
} else {
?>
                            <img src="<?php echo($artist->thumb_url); ?>">
                            <h2 class="title-slider large uppercased mb40 word-wrap"><?php echo($artist->name); ?></h2>
<?php
    if (!empty($artist->facebook_page_url)) {
?>
                            <h3><a href="<?php echo($artist->facebook_page_url); ?>" target="_blank"><span class="fa fa-facebook-square"></span></a></h3>
<?php
    }
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
                    $tickets = TRUE;
                }
            }
?>
                            <div class="col-md-4 box-services-2">
                                <div class="br-bottom mt40 mb0"></div>
                                <div style="position:relative;">
                                    <h3 class="title-small">
<?php
        echo(date(TIME_FORMAT, strtotime($event->datetime)));
        if ($tickets) {
            echo(' <span class="fa fa-ticket" style="color:#f00;font-size:12px;"></span>');
        }
?>
                                    </h3>
                                    <a data-toggle="modal" data-target=".bd-example-modal-lg" OnClick="initMap(<?php echo($event->venue->latitude)  ?>,<?php echo($event->venue->longitude)  ?>)" ><p><?php echo(strtoupper($event->venue->name)); ?> Latitude <?php echo($event->venue->latitude)  ?>  Longitude <?php echo($event->venue->longitude)  ?> </p></a>
                                    <p><?php echo($event->venue->city . ', ' . (!ctype_digit($event->venue->region) ? $event->venue->region . ', ' : '') . $event->venue->country); ?></p>
                                </div>
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
            <footer class="footer-wrapper footer-background style-2 bg-img bg07 stellar" data-stellar-background-ratio="0.4">
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
        <!-- Modal Maps -->
       <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div id="map"></div>
                </div>
            </div>
        </div>
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/plugins.min.js"></script>
        <script src="js/jquery.nav.min.js"></script>
        <script src="js/owl.carousel.min.js"></script>
        <script src="js/main.js"></script>
        <script src="js/scripts.js"></script>
        <script>
            function initMap(latitude,longitude) {
                var map;
                map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: latitude, lng: longitude},
                zoom: 8
                });
            }
        </script>
        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAm3KarFLFFjlDCuVDIcixLRhQ-ANyGwAc" async defer></script>
    </body>
</html>