<?php
$playNowVideo = $video;
$transformation = "{rotate:" . $video['rotation'] . ", zoom: " . $video['zoom'] . "}";
if ($video['rotation'] === "90" || $video['rotation'] === "270") {
    $aspectRatio = "9:16";
    $vjsClass = "vjs-9-16";
    $embedResponsiveClass = "embed-responsive-9by16";
} else {
    $aspectRatio = "16:9";
    $vjsClass = "vjs-16-9";
    $embedResponsiveClass = "embed-responsive-16by9";
}

if (!empty($ad)) {
    $playNowVideo = $ad;
    $logId = Video_ad::log($ad['id']);
}
?>

    <!-- Archivos de PlateaPlayer -->

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="http://andresliu.xyz/platea-2/css/PlateaPlayer.css">
    <script src="http://andresliu.xyz/platea-2/js/player.js"></script>

    <!-- Fin de los archivos -->

    <div class="row main-video">
        <div class="col-xs-12 col-sm-12 col-lg-2"></div>
        <div class="col-xs-12 col-sm-12 col-lg-8">
            <!-- Contenedor de ejemplo -->
            <div id="main"></div>
        </div>

        <div class="col-xs-12 col-sm-12 col-lg-2"></div>
    </div>
    <!--/row-->
    <script>
        var fullFuration = 0;
        var isPlayingAd = false;
        $(document).ready(function() {
            fullFuration = strToSeconds('<?php echo $ad['
                duration ']; ?>');
            player = videojs('mainVideo');

            player.zoomrotate(<?php echo $transformation; ?>);
            player.ready(function() {
                <?php
if ($config->getAutoplay()) {
    echo "this.play();";
} else {
    ?>
                if (Cookies.get('autoplay') && Cookies.get('autoplay') !== 'false') {
                    this.play();
                }
                <?php }
?>
                <?php if (!empty($logId)) { ?>
                isPlayingAd = true;
                this.on('ended', function() {
                    console.log("Finish Video");
                    if (isPlayingAd) {
                        isPlayingAd = false;
                        $('#adButton').trigger("click");
                    }
                    <?php
    // if autoplay play next video
    if (!empty($autoPlayVideo)) {
        ?>
                    else if (Cookies.get('autoplay') && Cookies.get('autoplay') !== 'false') {
                        document.location = '<?php echo $global['
                        webSiteRootURL '], $catLink; ?>video/<?php echo $autoPlayVideo['
                        clean_title ']; ?>';
                    }
                    <?php
    }
    ?>

                });
                this.on('timeupdate', function() {
                    var durationLeft = fullFuration - this.currentTime();
                    $("#adUrl .time").text(secondsToStr(durationLeft + 1, 2));
                    <?php if (!empty($ad['skip_after_seconds'])) {
        ?>
                    if (isPlayingAd && this.currentTime() > <?php echo intval($ad['skip_after_seconds']); ?>) {
                        $('#adButton').fadeIn();
                    }
                    <?php }
    ?>
                });
                <?php } else {
    ?>
                this.on('ended', function() {
                    console.log("Finish Video");
                    <?php
    // if autoplay play next video
    if (!empty($autoPlayVideo)) {
        ?>
                    if (Cookies.get('autoplay') && Cookies.get('autoplay') !== 'false') {
                        document.location = '<?php echo $global['
                        webSiteRootURL '], $catLink; ?>video/<?php echo $autoPlayVideo['
                        clean_title ']; ?>';
                    }
                    <?php
    }
    ?>

                });
                <?php }
?>
            });
            player.persistvolume({
                namespace: "YouPHPTube"
            });
            <?php if (!empty($logId)) { ?>
            $('#adButton').click(function() {
                console.log("Change Video");
                fullFuration = strToSeconds('<?php echo $video['
                    duration ']; ?>');
                changeVideoSrc(player, "<?php echo $global['webSiteRootURL']; ?>videos/<?php echo $video['filename']; ?>");
                $(".ad").removeClass("ad");
                return false;
            });
            <?php } ?>
        });

    </script>

    <!-- p5.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/0.6.0/p5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/p5.js/0.6.0/addons/p5.dom.min.js"></script>

    <!-- Para la comunicación con el servidor -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.3/socket.io.js"></script>

    <script>
        var p5 = new p5(PlateaPlayer);
        var opciones = {
            contenedor: "main",
            path: "<?php echo $global['webSiteRootURL']; ?>videos/<?php echo $playNowVideo['filename']; ?>.mp4",
            json: "<?php echo $video['json']; ?>"
        }
        var socket = io.connect("https://platea.localtunnel.me"); // Usando "localtunnel" para exponer el servidor ejecutado localmente a internet a través de un subdominio estático
        var plateaPlayer = new PlateaPlayer(p5, opciones, socket);
        
        

        // Ejemplo de utilización de la API
        /*var btnStop = document.getElementById("btnStop");
        btnStop.onclick = function() {
            plateaPlayer.pausarVideo();
        }*/

    </script>

    <!-- Fin de la configuración -->
