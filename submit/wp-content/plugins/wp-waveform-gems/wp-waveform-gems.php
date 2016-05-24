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
}

add_action( 'wp_enqueue_scripts', 'wp_waveform_gems_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'wp_waveform_gems_enqueue_script' );
add_action( 'wp_footer', 'renderWaveForm', 100 );


function renderWaveForm()
{
    echo '
        <div id="wp-waveform-gems-waveform"></div>
        <div class="wp-waveform-gems-spinner">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
        <div id="wp-waveform-gems-layer1"></div>';

    echo<<<EOF
    <script>
    var spinnerSelector = '.wp-waveform-gems-spinner';
    var layerSelector = '#wp-waveform-gems-layer1';

jQuery(function () {

    var wavesurfer = WaveSurfer.create({
        container: '#wp-waveform-gems-waveform',
        waveColor: '#1e1e1e',
        cursorColor: 'white',
        cursorWidth: 5
    });

    jQuery("button").click(function (e) {
        e.stopPropagation();
        if (jQuery(this).attr('title') == 'Play') {
            jQuery(spinnerSelector).show();
            jQuery(layerSelector).show();
            jQuery(this).parent().removeClass('mejs-play').addClass('mejs-pause');
            jQuery(this).attr('title', 'Pause');
            var a = jQuery(this).parent().parent().parent().find("audio").attr('src');
            wavesurfer.load(a);
            var that = this;
            wavesurfer.un('finish');
            wavesurfer.on('finish', function () {
                jQuery(that).parent().removeClass('mejs-pause').addClass('mejs-play');
                jQuery(that).attr('title', 'Play');
            });
        } else {
            jQuery(this).parent().removeClass('mejs-pause').addClass('mejs-play');
            jQuery(this).attr('title', 'Play');
            wavesurfer.un('finish');
            wavesurfer.stop();
        }
    });

    wavesurfer.on('ready', function () {
        jQuery(layerSelector).hide();
        jQuery(spinnerSelector).hide();
        wavesurfer.play();
    });
});
</script>
EOF;
}

