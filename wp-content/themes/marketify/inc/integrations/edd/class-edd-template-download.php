<?php

class Marketify_EDD_Template_Download {

    public function __construct() {
        add_action( 'wp_head', array( $this, 'featured_area' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'marketify_entry_before', array( $this, 'download_title' ), 5 );
        add_action( 'marketify_entry_before', array( $this, 'featured_area_header_actions' ), 5 );

        add_action( 'marketify_download_info', array( $this, 'download_price' ), 5 );
        add_action( 'marketify_download_actions', array( $this, 'demo_link' ) );

        add_action( 'marketify_download_entry_meta_before_audio', array( $this, 'featured_audio' ) );

        add_filter( 'post_class', array( $this, 'post_class' ), 10, 3 );
        add_filter( 'body_class', array( $this, 'body_class' ) );
        add_action('draw_hidden_forms', array($this, 'draw_hidden_forms'));
    }

    public function draw_hidden_forms()
    {
        echo <<<eof
        <div style="display:none">
	</div>
<div id="ninja-forms-modal-5" class="nf-modal" style="display: none;"><div class="nf-modal-content">	<div id="ninja_forms_form_5_cont" class="ninja-forms-cont">
		<div id="ninja_forms_form_5_wrap" class="ninja-forms-form-wrap">
	<div id="ninja_forms_form_5_response_msg" style="" class="ninja-forms-response-msg "></div>	<form id="ninja_forms_form_5" enctype="multipart/form-data" method="post" name="" action="https://ghostpro.io/wp-admin/admin-ajax.php?action=ninja_forms_ajax_submit" class="ninja-forms-form">

	<input type="hidden" id="_wpnonce" name="_wpnonce" value="e61bc7ab78" /><input type="hidden" name="_wp_http_referer" value="/buy-now-terms-form/" />	<input type="hidden" name="_ninja_forms_display_submit" value="1">
	<input type="hidden" name="_form_id"  id="_form_id" value="5">
		<div class="hp-wrap">
		<label>If you are a human and are seeing this field, please leave it blank.			<input type="text" value="" name="_7B2lh">
			<input type="hidden" value="_7B2lh" name="_hp_name">
		</label>
	</div>
		<div id="ninja_forms_form_5_all_fields_wrap" class="ninja-forms-all-fields-wrap">
			<div class="ninja-forms-required-items">Fields marked with an <span class="ninja-forms-req-symbol">*</span> are required</div>
								<div class="field-wrap text-wrap label-inside"  id="ninja_forms_field_17_div_wrap" data-visible="1">
							<input type="hidden" id="ninja_forms_field_17_type" value="text">
	<input id="ninja_forms_field_17" data-mask="" data-input-limit="" data-input-limit-type="char" data-input-limit-msg="" name="ninja_forms_field_17" type="text" placeholder="" class="ninja-forms-field  ninja-forms-req " value="Full Name *" rel="17"   />
			<input type="hidden" id="ninja_forms_field_17_label_hidden" value="Full Name *">
			<div id="ninja_forms_field_17_error" style="display:none;" class="ninja-forms-field-error">
		</div>
							</div>
												<div class="field-wrap text-wrap label-inside"  id="ninja_forms_field_18_div_wrap" data-visible="1">
							<input type="hidden" id="ninja_forms_field_18_type" value="text">
	<input id="ninja_forms_field_18" data-mask="" data-input-limit="" data-input-limit-type="char" data-input-limit-msg="" name="ninja_forms_field_18" type="text" placeholder="" class="ninja-forms-field  ninja-forms-req email " value="Email *" rel="18"   />
			<input type="hidden" id="ninja_forms_field_18_label_hidden" value="Email *">
			<div id="ninja_forms_field_18_error" style="display:none;" class="ninja-forms-field-error">
		</div>
							</div>
												<div class="field-wrap checkbox-wrap label-above"  id="ninja_forms_field_24_div_wrap" data-visible="1">
							<input type="hidden" id="ninja_forms_field_24_type" value="checkbox">
		<label for="ninja_forms_field_24" id="ninja_forms_field_24_label" class="">I agree to the GhostPro <a href="https://ghostpro.io/terms" target="_blank">Terms & Conditions</a> <span class='ninja-forms-req-symbol'><strong>*</strong></span>				</label>
		<input id="" name="ninja_forms_field_24" type="hidden" value="unchecked" /><input id="ninja_forms_field_24" name="ninja_forms_field_24" type="checkbox" class="ninja-forms-field  ninja-forms-req" value="checked"  rel="24"/>	<div id="ninja_forms_field_24_error" style="display:none;" class="ninja-forms-field-error">
		</div>
							</div>
												<div class="field-wrap submit-wrap label-above"  id="ninja_forms_field_25_div_wrap" data-visible="1">
							<input type="hidden" id="ninja_forms_field_25_type" value="submit">
	<div id="nf_submit_5">
		<input type="submit" name="_ninja_forms_field_25" class="ninja-forms-field " id="ninja_forms_field_25" value="Submit & Proceed to PayPal" rel="25" >
	</div>
	<div id="nf_processing_5" style="display:none;">
		<input type="submit" name="_ninja_forms_field_25" class="ninja-forms-field " id="ninja_forms_field_25" value="Processing" rel="25" disabled>
	</div>
		<div id="ninja_forms_field_25_error" style="display:none;" class="ninja-forms-field-error">
		</div>
							</div>
							</div>
		</form>
		</div>
		</div>
	</div></div>
<form id="edd_register_form" class="edd_form" action="" method="post">
	
	<fieldset>
		<legend>Register New Account</legend>

		
		<p>
			<label for="edd-user-login">Username</label>
			<input id="edd-user-login" class="required edd-input" type="text" name="edd_user_login" title="Username" />
		</p>

		<p>
			<label for="edd-user-email">Email</label>
			<input id="edd-user-email" class="required edd-input" type="email" name="edd_user_email" title="Email Address" />
		</p>

		<p>
			<label for="edd-user-pass">Password</label>
			<input id="edd-user-pass" class="password required edd-input" type="password" name="edd_user_pass" />
		</p>

		<p>
			<label for="edd-user-pass2">Confirm Password</label>
			<input id="edd-user-pass2" class="password required edd-input" type="password" name="edd_user_pass2" />
		</p>


		
		<p>
			<input type="hidden" name="edd_honeypot" value="" />
			<input type="hidden" name="edd_action" value="user_register" />
			<input type="hidden" name="edd_redirect" value="https://ghostpro.io/buy-now-terms-form/"/>
			<input class="button" name="edd_register_submit" type="submit" value="Register" />
		</p>

			</fieldset>

	</form>

	<form id="edd_login_form" class="edd_form" action="" method="post">
		<fieldset>
			<span><legend>Log into Your Account</legend></span>
						<p>
				<label for="edd_user_login">Username or Email</label>
				<input name="edd_user_login" id="edd_user_login" class="required edd-input" type="text" title="Username or Email"/>
			</p>
			<p>
				<label for="edd_user_pass">Password</label>
				<input name="edd_user_pass" id="edd_user_pass" class="password required edd-input" type="password"/>
			</p>
			<p>
				<input type="hidden" name="edd_redirect" value="https://ghostpro.io/buy-now-terms-form/"/>
				<input type="hidden" name="edd_login_nonce" value="7d577ec4c7"/>
				<input type="hidden" name="edd_action" value="user_login"/>
				<input id="edd_login_submit" type="submit" class="edd_submit" value="Log In"/>
			</p>
			<p class="edd-lost-password">
				<a href="https://ghostpro.io/wp-login.php?action=lostpassword" title="Lost Password">
					Lost Password?				</a>
			</p>
					</fieldset>
	</form>
	<link rel='stylesheet' id='jquery-modal-css-css'  href='/wp-content/plugins/ninja-forms-modal/css/ninja-forms-modal-display.css?ver=4.5.2' type='text/css' media='all' />
<link rel='stylesheet' id='ninja-forms-display-css'  href='/wp-content/plugins/ninja-forms/deprecated/css/ninja-forms-display.css?nf_ver=2.9.45&#038;ver=4.5.2' type='text/css' media='all' />
<link rel='stylesheet' id='jquery-qtip-css'  href='/wp-content/plugins/ninja-forms/deprecated/css/qtip.css?ver=4.5.2' type='text/css' media='all' />
<link rel='stylesheet' id='jquery-rating-css'  href='/wp-content/plugins/ninja-forms/deprecated/css/jquery.rating.css?ver=4.5.2' type='text/css' media='all' />
<script type='text/javascript' src='/wp-includes/js/jquery/jquery.form.min.js?ver=3.37.0'></script>
<script type='text/javascript' src='/wp-includes/js/underscore.min.js?ver=1.8.3'></script>
<script type='text/javascript' src='/wp-includes/js/backbone.min.js?ver=1.2.3'></script>
<script type='text/javascript'>
/* <![CDATA[ */
var ninja_forms_settings = {"ajax_msg_format":"inline","password_mismatch":"The passwords provided do not match.","plugin_url":"https:\/\/ghostpro.io\/wp-content\/plugins\/ninja-forms\/deprecated\/","datepicker_args":{"dateFormat":"dd\/mm\/yy"},"currency_symbol":"$","date_format":"dd\/mm\/yy"};
var thousandsSeparator = ",";
var decimalPoint = ".";
var ninja_forms_form_5_settings = {"ajax":"1","hide_complete":"1","clear_complete":"1"};
var ninja_forms_form_5_calc_settings = {"calc_value":{"24":{"checked":"0","unchecked":"0"}},"calc_fields":[]};
var ninja_forms_password_strength = {"empty":"Strength indicator","short":"Very weak","bad":"Weak","good":"Medium","strong":"Strong","mismatch":"Mismatch"};
/* ]]> */
</script>
<script type='text/javascript' src='/wp-content/plugins/ninja-forms/deprecated/js/min/ninja-forms-display.min.js?nf_ver=2.9.45&#038;ver=4.5.2'></script>
<script type='text/javascript' src='/wp-content/plugins/ninja-forms-modal/js/jquery.modal.min.js?ver=4.5.2'></script>
<script type='text/javascript' src='/wp-content/plugins/ninja-forms-modal/js/jquery.modal.options.js?ver=4.5.2'></script>
eof;

    }

    public function post_class( $classes, $class, $post_id ) {
        if( ! $post_id || get_post_type( $post_id ) !== 'download' || is_admin() ) {
            return $classes;
        }

        if ( 'on' == esc_attr( marketify_theme_mod( 'downloads-archives-truncate-title' ) ) ) {
            $classes[] = 'edd-download--truncated-title';
        }

        return $classes;
    }

    public function body_class( $classes ) {
        $format = $this->get_post_format();
        $setting = esc_attr( marketify_theme_mod( "download-{$format}-feature-area" ) );

        $classes[] = 'feature-location-' . $setting;

        return $classes;
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'marketify-download', get_template_directory_uri() . '/js/download/download.js', array( 'marketify' ) );
    }

    public function download_price() {
        global $post;
?>
<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
    <span itemprop="price" class="edd_price">
        <?php edd_price( $post->ID ); ?>
    </span>
</span>
<?php
    }

    function demo_link( $download_id = null ) {
        global $post, $edd_options;

        if ( 'download' != get_post_type() ) {
            return;
        }

        if ( ! $download_id ) {
            $download_id = $post->ID;
        }

        $field = apply_filters( 'marketify_demo_field', 'demo' );
        $demo  = get_post_meta( $download_id, $field, true );

        if ( ! $demo ) {
            return;
        }

        $label = apply_filters( 'marketify_demo_button_label', __( 'Demo', 'marketify' ) );

        if ( $post->_edd_cp_custom_pricing ) {
            echo '<br /><br />';
        }

        $class = 'button';

        if ( ! did_action( 'marketify_single_download_content_before' ) ) {
            $class .= ' button--color-white';
        }

        echo apply_filters( 'marketify_demo_link', sprintf( '<a href="%s" class="%s" target="_blank">%s</a>', esc_url( $demo ), $class, $label ) );
    }

    public function get_featured_images() {
        global $post;

        $images  = array();
        $_images = get_post_meta( $post->ID, 'preview_images', true );

        if ( is_array( $_images ) && ! empty( $_images ) ) {
            foreach ( $_images as $image ) {
                $images[] = get_post( $image );
            }
        } else {
            $images = get_attached_media( 'image', $post->ID );
        }

        return apply_filters( 'marketify_download_get_featured_images', $images, $post );
    }

    public function featured_area() {
        global $post;

        if ( ! $post || ! is_singular( 'download' ) ) {
            return;
        }

        $format = get_post_format();

        if ( '' == $format ) {
            $format = 'standard';
        }

        if ( $this->is_format_location( 'top' ) ) {
            add_action( 'marketify_entry_before', array( $this, "featured_{$format}" ), 5 );

            if ( 'standard' != $format && $this->is_format_style( 'inline' ) ) {
                add_action( 'marketify_entry_before', array( $this, 'featured_standard' ), 6 );
            }
        } else {
            add_action( 'marketify_single_download_content_before_content', array( $this, 'featured_' . $format ), 5 );

            if ( method_exists( $this, 'featured_' . $format . '_navigation' ) ) {
                add_action( 'marketify_single_download_content_before_content', array( $this, 'featured_'. $format . '_navigation' ), 7 );
            }

            if ( 'standard' != $format && $this->is_format_style( 'inline' ) ) {
                add_action( 'marketify_single_download_content_before_content', array( $this, 'featured_standard' ), 6 );
                add_action( 'marketify_single_download_content_before_content', array( $this, 'featured_standard_navigation' ), 7 );
            }
        }
    }

    private function get_post_format() {
        global $post;

        if ( ! $post ) {
            return false;
        }

        $format = get_post_format();

        if ( '' == $format ) {
            $format = 'standard';
        }

        return $format;
    }

    public function is_format_location( $location ) {
        if ( ! is_array( $location ) ) {
            $location = array( $location );
        }

        $format = $this->get_post_format();
        $setting = esc_attr( marketify_theme_mod( "download-{$format}-feature-area" ) );

        if ( in_array( $setting, $location ) ) {
            return true;
        }

        return false;
    }

    public function is_format_style( $style ) {
        if ( ! is_array( $style ) ) {
            $style = array( $style );
        }

        $format = $this->get_post_format();
        $setting = esc_attr( marketify_theme_mod( "download-{$format}-feature-image" ) );

        if ( in_array( $setting, $style ) ) {
            return true;
        }

        return false;
    }

    public function download_title() {
        if ( ! is_singular( 'download' ) ) {
            return;
        }

        the_post();
    ?>
        <div class="page-header page-header--download download-header container">
            <h1 class="page-title"><?php the_title(); ?></h1>
    <?php
        rewind_posts();
    }

    public function featured_area_header_actions() {
        if ( ! is_singular( 'download' ) ) {
            return;
        }
    ?>
        <div class="download-header__info download-header__info--actions">
            <?php do_action( 'marketify_download_actions' ); ?>
        </div>

        <div class="download-header__info">
            <?php do_action( 'marketify_download_info' ); ?>
        </div>
    <?php
    }

    public function featured_standard() {
        $images = $this->get_featured_images();
        $before = '<div class="download-gallery">';
        $after  = '</div>';

        $size = apply_filters( 'marketify_featured_standard_image_size', 'large' );

        echo $before;

        if ( empty( $images ) && has_post_thumbnail( get_the_ID() ) ) {
            echo get_the_post_thumbnail( get_the_ID(), $size );
            echo $after;
            return;
        } else {
    ?>
        <?php foreach ( $images as $image ) : ?>
            <div class="download-gallery__image"><a href="<?php echo esc_url( wp_get_attachment_url( $image->ID ) ); ?>"><?php echo wp_get_attachment_image( $image->ID, $size ); ?></a></div>
        <?php endforeach; ?>
    <?php
        }

        echo $after;
    }

    public function featured_standard_navigation() {
        $images = $this->get_featured_images();

        if ( empty( $images ) ) {
            return;
        }

        $before = '<div class="download-gallery-navigation ' . ( count ( $images ) > 6 ? 'has-dots' : '' ) . '">';
        $after  = '</div>';

        $size = apply_filters( 'marketify_featured_standard_image_size_navigation', 'thumbnail' );

        if ( count( $images ) == 1 || ( empty( $images ) && has_post_thumbnail( get_the_ID() ) ) ) {
            return;
        } 

        echo $before;

        foreach ( $images as $image ) {
    ?>
        <div class="download-gallery-navigation__image"><?php echo wp_get_attachment_image( $image->ID, $size ); ?></div>
    <?php
        }

        echo $after;
    }

	/**
	 * Output featured audio.
	 *
	 * Depending on the found audio it will be oEmbedded or create a
	 * WordPress playlist from the found tracks.
	 *
	 * @since 2.0.0
	 *
	 * @return mixed
	 */
    public function featured_audio() {
        $audio = $this->_get_audio();

		// if we are using a URL try to embed it (only on single download)
		if ( ! is_array( $audio ) && is_singular( 'download' ) && ! did_action( 'marketify_single_download_content_after' ) ) {
			$audio = wp_oembed_get( $audio );
		} elseif ( $audio ) {
			// grid preview only needs one
			if ( ! is_singular( 'download' ) ) {
				$audio = array_splice( $audio, 0, 1 );
			}

			if ( ! empty( $audio ) ) {
				$audio = wp_playlist_shortcode( array(
					'id' => get_post()->ID,
					'ids' => $audio,
					'images' => false,
					'tracklist' => is_singular( 'download' )
				) );
			}
		}

		$audio = apply_filters( 'marketify_get_featured_audio', $audio );

		if ( $audio ) {
			echo '<div class="download-audio">' . $audio . '</div>';
		}
    }

	/**
	 * Find audio for a download. Searches a few places:
	 *
	 * 1. `preview_files` FES File Uplaod Field (playlist)
	 * 2. `audio` FES URL Field (oEmbed)
	 * 3. Attached audio files (playlist)
	 *
	 * @since 2.0.0
	 *
	 * @return mixed $audio
	 */
    private function _get_audio() {
		$audio = false;

		// check to see if the FES file upload field exists
        $audio = get_post()->preview_files;

		// check to see if the FES URL field exists
		if ( ! $audio || '' == $audio ) {
			$field = apply_filters( 'marketify_audio_field', 'audio' );
			$audio = get_post()->$field;
		}

		// query attached media
        if ( ! $audio || '' == $audio ) {
            $audio = get_attached_media( 'audio', get_post()->ID );

            if ( ! empty( $audio ) ) {
                $audio = wp_list_pluck( $audio, 'ID' );
            }
        }

        return $audio;
    }

    public function featured_video() {
        $field = apply_filters( 'marketify_video_field', 'video' );
        $video = get_post()->$field;

        if ( ! $video ) {
            return;
		}

        if ( is_array( $video ) ) {
            $video = current( $video );
        }

        $info = wp_check_filetype( $video );

        if ( '' == $info[ 'ext' ] ) {
            global $wp_embed;

            $output = $wp_embed->run_shortcode( '[embed]' . $video . '[/embed]' );
        } else {
            $output = do_shortcode( sprintf( '[video %s="%s"]', $info[ 'ext' ], $video ) );
        }

        echo '<div class="download-video">' . $output . '</div>';
    }

}
