jQuery(document).ready(function() {

	var mediaUploader;


	jQuery('#fap-add-track').click(function(e) {

		if(wp == undefined || wp.media == undefined) {
	    	alert('This button works only with Wordpress 3.5+!');
	    	return false;
    	}

	     e.preventDefault();

		if (mediaUploader) {
			mediaUploader.open();
			return;
		}

		mediaUploader = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
        	var attachment = mediaUploader.state().get('selection').first().changed.url;
        	jQuery('input[name=fap_track_url]').val(attachment);
        });

		mediaUploader.open();

	});

});