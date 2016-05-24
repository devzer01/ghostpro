/**
 * Created by nayana on 5/3/16.
 */

var spinnerSelector = '.wp-waveform-gems-spinner';
var layerSelector = '#wp-waveform-gems-layer1';

jQuery(function () {
    var wavesurfer = WaveSurfer.create({
        container: '#waveform',
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