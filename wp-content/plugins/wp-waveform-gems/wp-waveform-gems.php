<?php
/**
 * Plugin Name: wp-waveform-gems
 * Plugin URI: http://www.messycode.com
 * Description: integrate waveform-js with media-element and add gradient. with jQuery 1.2 compatibility
 * Version: 1.6
 * Author: Nayana Hettiarachchi
 * Author URI: http://www.messycode.com
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 * Text Domain: wp-waveform-gems
 * Domain Path: /
 */


function wp_waveform_gems_enqueue_style() {
    wp_enqueue_style( 'core-style', plugins_url( 'wp-waveform-gems.css' , __FILE__ ) );
}

function wp_waveform_gems_enqueue_script() {
    wp_enqueue_script( 'my-js-waveform', plugins_url( 'wavesurfer.min.js' , __FILE__ ) );
    wp_enqueue_script('jquery-ui-slider');
}

add_action( 'wp_enqueue_scripts', 'wp_waveform_gems_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'wp_waveform_gems_enqueue_script' );
add_action( 'wp_footer', 'renderWaveForm', 100 );


function renderWaveForm()
{
    $playIcon = plugins_url( 'play.png' , __FILE__ );
    $pauseIcon = plugins_url( 'pause.png' , __FILE__ );
    $speakerIcon = plugins_url( 'speaker.png' , __FILE__ );
    $muteIcon = plugins_url( 'mute.png' , __FILE__ );


    echo '<style>
            #play {
                background: url(' . $playIcon . ') no-repeat;
            }
            #pause {
                background: url('. $pauseIcon . ') no-repeat;
            }
            #unmute {
                background: url('. $speakerIcon . ') no-repeat;
            }
            #mute {
                background: url('. $muteIcon .') no-repeat;
            }

            .divControls button {
                float: left;
            }
        </style>

        <div id="wp-waveform-gems-waveform" style="display: none;">
            <div id="wp-waveform-gems-waveform1">
                <div id="divTitle">
                    <span id="trackName"></span>
                </div>
                <div id="wp-waveform-gems-waveform-canvas">

                </div>
                <div id="divDuration">
                    <span id="duration" style="width: 100%; text-align: center;">00:00</span>
                </div>
                <div class="divControls" id="playPause">
                    <button id="play" title="iconPlay"></button>
                    <button id="pause" title="iconPlay"></button>
                </div>
                <div class="divControls" id="muteUnmute">
                    <button id="unmute" title="mute"></button>
                    <button id="mute" title="mute"></button>
                </div>
            </div>
            <div class="wp-waveform-gems-spinner">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
        </div>
        ';

    echo<<<EOF

    <script type="text/javascript">
  WebFontConfig = {
    google: { families: [ 'Montserrat:400,700:latin' ] }
  };
  (function() {
    var wf = document.createElement('script');
    wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
  })(); </script>

    <script>
    var spinnerSelector = '.wp-waveform-gems-spinner';
    var trackName = "Foobar";
    var wavesurfer = null;
    var mute = false;
    var lastElement = null;
    var lastButton = null;
    var stillLoading = false;

    function muteSound() {
        if (!mute) {
            jQuery("#unmute").hide();
            jQuery("#mute").show();
        } else {
            jQuery("#mute").hide();
            jQuery("#unmute").show();
        }
        mute = !mute;
        wavesurfer.toggleMute();
    }

    function playPause() {
        if (wavesurfer.isPlaying()) {
            jQuery("#pause").hide();
            jQuery("#play").show();
            lastElement.removeClass('mejs-pause').addClass('mejs-play');
        } else {
            jQuery("#play").hide();
            jQuery("#pause").show();
            lastElement.removeClass('mejs-play').addClass('mejs-pause');
        }
        console.log('playPause');
        wavesurfer.playPause();
    }

jQuery(function () {

    wavesurfer = WaveSurfer.create({
        container: '#wp-waveform-gems-waveform-canvas',
        waveColor: '#1e1e1e',
        cursorColor: 'white',
        height: 100,
        cursorWidth: 5
    });

    jQuery("button[title='Play']").unbind("click");
    jQuery("button[title='Pause']").unbind("click");
    jQuery(".mejs-button").unbind("click");
    jQuery(".mejs-playpause-button").unbind("click");
    jQuery(".mejs-play").unbind("click");
    jQuery(".mejs-pause").unbind("click");

    jQuery("button").click(function (e) {
        console.log('click');
        if (!e) var e = window.event
        e.cancelBubble = true;
        if (e.stopPropagation) {
            console.log('prevent prop');
            e.stopPropagation();
        }

        if (jQuery(this).attr('title') == 'Play') {

            if ((wavesurfer.isPlaying() || stillLoading)  && lastElement !== null) {
                lastElement.removeClass('mejs-pause').addClass('mejs-play');
                lastButton.attr('title', 'Pause');
                if (wavesurfer.isPlaying()) wavesurfer.stop();
            }

            jQuery(spinnerSelector).show();
            lastElement = jQuery(this).parent();
            lastButton = jQuery(this);
            lastElement.removeClass('mejs-play').addClass('mejs-pause');
            lastButton .attr('title', 'Pause');
            var a = jQuery(this).parent().parent().parent().find("audio").attr('src');
            jQuery("#wp-waveform-gems-waveform").slideDown();
            if (a === undefined) {
                return false;
                jQuery("#wp-waveform-gems-waveform").slideUp();
            }
            if (wavesurfer.isPlaying()) wavesurfer.stop();
            wavesurfer.load(a);
            stillLoading = true;
            trackName = jQuery(this).parent().parent().parent().parent().parent().parent().parent().parent().find(".entry-title").text();
            trackName = trackName.split('|')[1];
            jQuery("#trackName").text(trackName);

        } else if (jQuery(this).attr('title') == 'Pause') {
            playPause();
        } else if (jQuery(this).attr('id') == 'mute' || jQuery(this).attr('id') == 'unmute') {
            muteSound();
        } else if (jQuery(this).attr('id') == 'play' || jQuery(this).attr('id') == 'pause') {
            playPause();
        }
    });

    jQuery("audio").bind("loadstart play playing", function (e) {
        if (!this.paused) this.stop();
    });

    wavesurfer.on('pause', function () {
        lastElement.removeClass('mejs-pause').addClass('mejs-play');
        lastButton.attr('title', 'Play');
    });

    wavesurfer.on('play', function () {
        lastElement.removeClass('mejs-play').addClass('mejs-pause');
        lastButton.attr('title', 'Pause');
    });


    wavesurfer.on('finish', function () {
        lastElement.removeClass('mejs-pause').addClass('mejs-play');
        lastButton.attr('title', 'Play');
        jQuery("#wp-waveform-gems-waveform").slideUp();
    });

    wavesurfer.on('ready', function () {
        stillLoading = false;
        jQuery(spinnerSelector).hide();
        jQuery("#trackName").text(trackName);
        jQuery("#play").hide();
        jQuery("#pause").show();
        wavesurfer.play();
    });

    wavesurfer.on('audioprocess', function () {
        var seconds = wavesurfer.getCurrentTime();
        var minutes = parseInt(seconds / 60);
        var paddingMinutes = "";
        if (minutes < 10) paddingMinutes = "0";
        var secondsLeft = parseInt(seconds % 60);
        var paddingSeconds = "";
        if (secondsLeft < 10)  paddingSeconds = "0";
        jQuery("#duration").text(paddingMinutes + minutes + ":" + paddingSeconds + secondsLeft);
    });
});
</script>
EOF;
}

