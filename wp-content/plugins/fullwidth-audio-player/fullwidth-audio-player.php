<?php
/*
Plugin Name: Fullwidth Audio Player
Plugin URI: http://radykal.de/wordpress-plugins/fullwidth-audio-player/
Description: Add a fixed audio player to any page on your wordpress site. Create your playlists and add them into the player or anywhere in your wordpress site.
Version: 1.1.61
Author: Rafael Dery
*/


if(!class_exists('FullwidthAudioPlayer')) {
	class FullwidthAudioPlayer {

		private $default_general_options = array( 'player_visibility' => 'all',
												  'play_css_class' => 'fap-play-button',
												  'enqueue_css_class' => 'fap-enqueue-button',
												  'referral_css_class' => 'fap-referral-button',
												  'play_button_text' => 'Play',
												  'pause_button_text' => 'Pause',
												  'enqueue_button_text' => 'Enqueue',
												  'referral_button_text' => 'Buy',
												  'login_text' => 'Log in to download',
												  'login_to_download' => 0,
												  'list_image_width' => 100,
												  'list_image_height' => 100,
												  'grid_image_width' => 200,
												  'grid_image_height' => 200,
												  'base64' => 0,
												  'public_posts' => 0,
												  'souncloud_sdk' => 1,
												  'exclude_from_search' => 0
		                                		);

		private $default_audioplayer_options = array( 'default_playlist' => 'none',
		        									  'wrapper_position' => 'bottom',
		        									  'main_position' => 'center',
		        									  'wrapper_color' => '#f0f0f0',
		        									  'main_color' => '#3c3c3c',
		        									  'fill_color' => '#e3e3e3',
		        									  'fill_hover_color' => '#d1d1d1',
		        									  'meta_color' => '#666666',
		        									  'stroke_color' => '#e0e0e0',
		        									  'active_track_color' => '#E8E8E8',
		        									  'wrapper_height' => 70,
		        									  'playlist_height' => 210,
		        									  'cover_width' => 50,
		        									  'cover_height' => 50,
		        									  'offset' => 20,
		        									  'facebook_text' => 'Share on Facebook',
		        									  'twitter_text' => 'Share on Twitter',
		        									  'download_text' => 'Download',
		        									  'opened' => 1,
		        									  'volume' => 1,
		        									  'playlist' => 1,
		        									  'autoPlay' => 0,
		        									  'autoLoad' => 1,
		        									  'playNextWhenFinished' => 1,
		        									  'keyboard' => 1,
		        									  'socials' => 1,
		        									  'auto_popup' => 0,
		        									  'randomize' => 0,
		        									  'shuffle' => 1,
		        									  'init_on_window' => 0,
		        									  'sortable' => 0,
		        									  'hide_on_mobile' => 0,
		        									  'loop_playlist' => 1,
		        									  'store_playlist' => 0,
		        									  'layout' => 'fullwidth',
		        									  'keep_closed_once_closed' => 0,
		        									  'animate_page_on_player_top' => 0,
		        									  'open_player_on_track_play' => 0,
		        									  'popup' => 1,
		        									  'open_label' => '+',
		        									  'close_label' => '&times;'
		                                		);
		private $default_playlist;
		private $activate_demo = false;
		private $mp3_dir;
		private $mp3_dir_url;

		//constants
		const CAPABILITY = 'edit_fullwidth_audio_player';
		const VERSION = '1.1.61';
		const VERSION_FIELD_NAME = 'fullwidth_audio_player_version';

		//Constructer
		public function __construct() {

			//the path and url to the mp3 directory
			$this->mp3_dir =  WP_CONTENT_DIR . '/uploads/fwap-mp3/';
			$this->mp3_dir_url = WP_CONTENT_URL . '/uploads/fwap-mp3/';

			register_activation_hook( __FILE__, array( &$this, 'activate_fap_plugin' ) );

			//action hooks
			add_action( 'after_setup_theme', array( &$this, 'setup_fap' ) );
			add_action( 'init', array( &$this,'init_plugin') );
			add_action( 'admin_init', array( &$this,'init_admin' ) );
			add_action( 'plugins_loaded', array( &$this,'check_version' ) );
			add_action( 'admin_menu', array( &$this,'add_pp_sub_pages' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_styles_scripts') );
			add_action( 'add_meta_boxes', array( &$this, 'add_custom_box' ) );
			add_action( 'save_post', array( &$this,'update_custom_meta_fields' ) );
			add_action( 'manage_posts_custom_column', array( &$this, 'posts_custom_column' ), 10, 2 );
			add_action( 'restrict_manage_posts', array( &$this,'playlist_filter_list' ) );
			add_filter( 'parse_query', array( &$this,'perform_filtering' ) );
			//frontend
			add_action( 'wp_enqueue_scripts', array( &$this,'add_scripts_styles' ) );
			add_action( 'wp_footer', array( &$this,'include_fap_frontend' ) );


			//filters
			add_filter( 'manage_track_posts_columns', array( &$this, 'add_custom_columns' ) );

			//shortcodes
			add_shortcode( 'fap_track', array( &$this, 'create_single_track' ) );
			add_shortcode( 'fap_playlist', array( &$this, 'create_playlist' ) );
			add_shortcode( 'fap_default_playlist', array( &$this, 'change_default_playlist' ) );
			add_shortcode( 'fap', array( &$this, 'add_player' ) );
			add_shortcode( 'fap_popup_button', array( &$this, 'add_popup_button' ) );
			add_shortcode( 'fap_clear_button', array( &$this, 'add_clear_button' ) );
			//only for demo shortcode
			add_shortcode( 'fap_demo', array( &$this, 'add_demo_panel' ) );

			if(!file_exists($this->mp3_dir))
				mkdir($this->mp3_dir);

			//uncomment next line to delete the options from the DB
			//delete_option('fap_options');

		}

		public function activate_fap_plugin() {

			if( !get_option('fap_options') ) {

		        $default_fap_options = array( 'general' => $this->default_general_options,
		        							  'audioplayer' => $this->default_audioplayer_options
		                                	);

				add_option('fap_options', $default_fap_options );
			}

		}

		public function check_version() {

			//to 1.1.1
			$current_version = 	get_option(FullwidthAudioPlayer::VERSION_FIELD_NAME);
			if( $current_version == false) {
				//upgrade

				global $wpdb;
				$wpdb->query("UPDATE $wpdb->posts SET post_type='track' WHERE post_type='fap_track'");

				//upgrade to 1.1.1

				update_option(FullwidthAudioPlayer::VERSION_FIELD_NAME, '1.1.1');
			}

			//to 1.1.3
			if($current_version == '1.1.1' || $current_version == '1.1.2') {

				$options = get_option( 'fap_options');
				$options['general']['player_visibility'] = 'all';
				$options['general']['enqueue_button_text'] = 'Enqueue';
				update_option( 'fap_options', $options );

				update_option(FullwidthAudioPlayer::VERSION_FIELD_NAME, '1.1.3');
			}

			//to 1.1.31
			if($current_version == '1.1.3') {
				update_option(FullwidthAudioPlayer::VERSION_FIELD_NAME, '1.1.31');
			}

			//to 1.1.32
			if($current_version == '1.1.31') {
				update_option(FullwidthAudioPlayer::VERSION_FIELD_NAME, '1.1.32');
			}

			//to 1.1.32
			if($current_version == '1.1.32') {
				$options = get_option( 'fap_options');
				$options['general']['souncloud_sdk'] = 1;
				$options['general']['exclude_from_search'] = 0;
				$options['general']['pause_button_text'] = 'Pause';
				$options['general']['enqueue_css_class'] = 'fap-enqueue-button';
				update_option( 'fap_options', $options );
				update_option(FullwidthAudioPlayer::VERSION_FIELD_NAME, '1.1.5');
			}

			//to 1.1.51
			if($current_version == '1.1.5') {
				update_option(FullwidthAudioPlayer::VERSION_FIELD_NAME, '1.1.51');
			}

			//to 1.1.6
			if($current_version == '1.1.6') {
				update_option(FullwidthAudioPlayer::VERSION_FIELD_NAME, '1.1.6');
			}

		}

		public function setup_fap() {

			$fap_options = get_option('fap_options');
			$general_options = $fap_options['general'];

			add_theme_support('post-thumbnails');

			//CUSTOM POST TYPES
			$pp_labels = array(
			  'name' => _x('Tracks', 'post type general name', 'radykal'),
			  'singular_name' => _x('Track', 'post type singular name', 'radykal'),
			  'add_new' => _x('Add New', 'track', 'radykal'),
			  'add_new_item' => __('Add New Track', 'radykal'),
			  'edit_item' => __('Edit Track', 'radykal'),
			  'new_item' => __('New Track', 'radykal'),
			  'all_items' => __('All Tracks', 'radykal'),
			  'view_item' => __('View Track', 'radykal'),
			  'search_items' => __('Search Tracks', 'radykal'),
			  'not_found' =>  __('No Tracks found', 'radykal'),
			  'not_found_in_trash' => __('No Tracks found in Trash', 'radykal'),
			  'parent_item_colon' => '',
			  'menu_name' => 'Fullwidth Audio Player'

			);

			$pp_args = array(
			  'labels' => $pp_labels,
			  'public' => $this->int_to_bool($general_options['public_posts']),
			  'exclude_from_search' => $this->int_to_bool($general_options['exclude_from_search']),
			  'show_ui' => true,
			  'show_in_menu' => true,
			  'has_archive' => true,
			  'hierarchical' => false,
			  'menu_icon' => plugins_url( '/admin/images/menu_icon.png', __FILE__ ),
			  'supports' => array('title','editor','thumbnail', 'page-attributes', 'comments', 'custom_fields'),
			  'register_meta_box_cb' => array(&$this, 'add_meta_boxes')
			);

			register_post_type( 'track', $pp_args );


			//TAXONOMIES
			$tax_playlists_labels = array(
			  'name' => _x( 'Playlists', 'taxonomy general name', 'radykal' ),
			  'singular_name' => _x( 'Playlist', 'taxonomy singular name', 'radykal' ),
			  'search_items' =>  __( 'Search Playlists', 'radykal' ),
			  'all_items' => __( 'All Playlists', 'radykal' ),
			  'parent_item' => __( 'Parent Playlist', 'radykal' ),
			  'parent_item_colon' => __( 'Parent Playlist:', 'radykal' ),
			  'edit_item' => __( 'Edit Playlist', 'radykal' ),
			  'update_item' => __( 'Update Playlist', 'radykal' ),
			  'add_new_item' => __( 'Add New Playlist', 'radykal' ),
			  'new_item_name' => __( 'New Playlist Name', 'radykal' ),
			  'menu_name' => __( 'Playlists', 'radykal' ),
			);

			register_taxonomy('dt_playlist', 'track', array(
			  'hierarchical' => true,
			  'labels' => $tax_playlists_labels,
			  'show_ui' => true,
			  'query_var' => true,
			  'rewrite' => array( 'slug' => 'playlist' ),
			));

		}

		public function init_plugin() {

			$options = get_option('fap_options');
			$general_options = $options['general'];

			add_image_size( 'fap-list-thumbnail', $general_options['list_image_width'], $general_options['list_image_height'], true );
			add_image_size( 'fap-grid-thumbnail', $general_options['grid_image_width'], $general_options['grid_image_height'], true );

			//load textdomain
			load_plugin_textdomain('radykal', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

		}

		public function init_admin() {

			add_action( 'wp_ajax_settrack', array( &$this, 'set_mp3_track' ) );
			add_action( 'wp_ajax_deletetrack', array( &$this, 'delete_mp3_track' ) );
			add_action( 'wp_ajax_updatesort', array( &$this, 'update_mp3_sort' ) );

			$role = get_role( 'administrator' );
			$role->add_cap( FullwidthAudioPlayer::CAPABILITY );

		}

		public function add_pp_sub_pages() {

			//add options page
			$options_page = add_submenu_page( 'edit.php?post_type=track', __('Options', 'radykal'), __('Options', 'radykal'), FullwidthAudioPlayer::CAPABILITY, 'fap-options', array($this, 'options_admin_page') );
			add_action( "load-{$options_page}", array( &$this,'load_options_page' ) );

			add_submenu_page( 'edit.php?post_type=track', __('MP3 Directory', 'radykal'), __('MP3 Directory', 'radykal'), FullwidthAudioPlayer::CAPABILITY, 'fap-mp3-directory', array($this, 'mp3_directory_page') );

		}

		//enqueue js and css for the admin
		public function enqueue_admin_styles_scripts( $hook ) {

			if($hook == 'track_page_fap-options') {
				wp_enqueue_style( 'spectrum-colorpicker', plugins_url( '/admin/css/spectrum.css', __FILE__ ) );
				wp_enqueue_style( 'fap-options', plugins_url( '/admin/css/options.css', __FILE__ ), array('thickbox') );

				wp_enqueue_script( 'spectrum-colorpicker', plugins_url( '/admin/js/spectrum.js', __FILE__ ) );
				wp_enqueue_script( 'fap-options', plugins_url( '/admin/js/options.js', __FILE__ ), array('media-upload', 'thickbox') );
			}
			else if($hook == 'track_page_fap-mp3-directory') {
				wp_enqueue_style( 'fap-mp3-directory', plugins_url( '/admin/css/mp3-directory.css', __FILE__ ) );
				wp_enqueue_script( 'jquery-ui-sortable', 'wp-ajax-response' );
			}

		}

		public function mp3_directory_page() {

			require_once(dirname(__FILE__) . '/admin/mp3-directory.php');
		}

		public function set_mp3_track() {

			header( "Content-Type: application/json" );

			//set
			if(intval($_POST['id']) == -1) {
				$mp3_track = array(
					'post_title' => $_POST['title'],
					'post_content' => $_POST['meta'],
					'post_type' => 'track',
					'post_status' => 'publish'
				);

				$id = wp_insert_post($mp3_track);
				if($id) {
					$playlist_id = array(intval($_POST['playlistId']));
					wp_set_post_terms( $id, $playlist_id, 'dt_playlist' );
					add_post_meta( $id, 'fap_track_url', $_POST['url'] );
					add_post_meta( $id, 'fap_track_path', $_POST['path'] );
					add_post_meta( $id, "fap_track_shortcode", '[fap_track id="'.$id.'" layout="list" enqueue="no" auto_enqueue="no"]');
					if( !empty($_POST['cover']) )
						add_post_meta( $id, 'fap_track_cover', $_POST['cover'] );
				}
				echo json_encode($id);
			}
			//update
			else {
				$id = intval($_POST['id']);
				$track_post = array();
				$track_post['ID'] = $id;
				if($update = wp_update_post( $track_post ) ) {
					$playlist_id = array(intval($_POST['playlistId']));
					$update = wp_set_post_terms( $id, $playlist_id, 'dt_playlist' );
				}
				echo json_encode($update);
			}

			exit;
		}

		public function delete_mp3_track() {

			header( "Content-Type: application/json" );

			//delete
			$id = intval( $_POST['id'] );
			if( $delete = wp_delete_post($id, true) ) {
				delete_post_meta($id, 'fap_track_url');
				delete_post_meta($id, 'fap_track_path');
				delete_post_meta($id, 'fap_track_cover');
				delete_post_meta($id, 'fap_track_shortcode');
			}

			echo json_encode($delete);

			exit;
		}

		public function update_mp3_sort() {

			header( "Content-Type: application/json" );

			//delete
			$ids =	$_POST['ids'];

			for($i=0; $i < sizeof($ids); $i++) {
				$track_post = array();
				$track_post['ID'] = $ids[$i];
				$track_post['menu_order'] = $i;
				wp_update_post( $track_post );
			}

			echo json_encode(1);

			exit;
		}

		public function options_admin_page () {

			global $pagenow;

			//get options
			$fap_options = get_option('fap_options');
			$general_options = $fap_options['general'];
			$audioplayer_options = $fap_options['audioplayer'];
			$audioplayer_options = $this->check_options_availability($audioplayer_options);
			?>

			<div class="wrap">
				<h2>Options</h2>

				<?php
				    //get tab
				    if ( isset ( $_GET['tab'] ) ) $tab = $_GET['tab'];
							else $tab = 'general';

					$this->options_admin_tabs($tab);


					if ( isset($_GET['updated']) && 'true' == esc_attr( $_GET['updated'] ) ) echo '<div class="updated" ><p>'.ucfirst($tab).' Options updated.</p></div>';
				?>

				<div id="tab-content">
					<form method="post" action="<?php admin_url( 'edit.php?post_type=track&page=fap-options' ); ?>">
						<?php
						wp_nonce_field( "fap-options-page" );

						if ( $pagenow == 'edit.php' && $_GET['page'] == 'fap-options' ){
							//include corresponding options page
							include_once(dirname(__FILE__)  .'/admin/'.$tab.'.php');
						}
						?>
		                <p class="description"><?php _e('Always save before switching to another tab!', 'radykal'); ?></p>
						<p style="clear: both;">
						<input type="submit" name="save_fap_options" class="button-primary" value="<?php _e('Save Changes', 'radykal'); ?>" <?php disabled( !current_user_can('manage_options') ); ?> />
						<input type="submit" name="reset_fap_options" class="button-secondary" value="<?php _e('Reset Options', 'radykal'); ?>" <?php disabled( !current_user_can('manage_options') ); ?> />
						</p>
					</form>
				</div>
			</div>
			<?php

		}

		public function options_admin_tabs( $current = 'homepage' ) {

		    $tabs = array( 'general' => 'General', 'audioplayer' => 'Audio Player' );

		    echo '<div id="icon-themes" class="icon32"><br></div>';
		    echo '<h2 class="nav-tab-wrapper">';
		    foreach( $tabs as $tab => $name ){
		        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
		        echo "<a class='nav-tab$class' href='?post_type=track&page=fap-options&tab=$tab'>$name</a>";

		    }
		    echo '</h2>';

		}

		public function load_options_page() {

			if ( isset($_POST["save_fap_options"]) || isset($_POST["reset_fap_options"]) ) {
				check_admin_referer( "fap-options-page" );
				$this->save_options();
				$url_parameters = isset($_GET['tab'])? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
				wp_redirect(admin_url('edit.php?post_type=track&page=fap-options&'.$url_parameters));
				exit;
			}

		}

		public function save_options() {

			global $pagenow;
			if ( $pagenow == 'edit.php' && $_GET['page'] == 'fap-options' ){

				if ( isset ( $_GET['tab'] ) )
			        $tab = $_GET['tab'];
			    else
			        $tab = 'general';

			    $tab_options = array();
			    if( isset($_POST["reset_fap_options"]) ) {

			    	switch( $tab ){
				        case 'general' :
				        	$tab_options = $this->default_general_options;
						break;
				        case 'audioplayer' :
				        	$tab_options = $this->default_audioplayer_options;
						break;
				    }

			    }
			    else if( isset($_POST["save_fap_options"]) ) {
			    	if($tab == 'general') {
				    	foreach($this->default_general_options as $key => $value) {
							$tab_options[$key] = $_POST[$key] === null ? 0 : $_POST[$key];
			        	}
			    	}
			    	else if($tab == 'audioplayer') {
				    	foreach($this->default_audioplayer_options as $key => $value) {
							$tab_options[$key] = $_POST[$key] === null ? 0 : $_POST[$key];
			        	}
			    	}

			    }

			}

			//update options associated to the selected tab
			$options = get_option( "fap_options" );
			$options[$tab] = $tab_options;
			update_option( 'fap_options', $options );

		}

		//add meta box in the "Add New Track" page
		public function add_meta_boxes() {

			wp_enqueue_script('fap-admin-new-track', plugins_url('/admin/js/new-track.js', __FILE__));

			add_meta_box('fap-meta-box', __('Track URL & Referral Link', 'radykal'), array( &$this, 'create_meta_box'), 'track', 'normal', 'high');

		}

		//add meta box in the post and page
		public function add_custom_box() {

			wp_enqueue_script('fap-admin-metabox', plugins_url('/admin/js/metabox.js', __FILE__));

			add_meta_box( 'fap-tracklists-meta-box', __('Fullwidth Audio Player - Shortcode Creator', 'radykal'), array( &$this, 'create_tracklists_meta_box'), 'post', 'side' );
			add_meta_box( 'fap-tracklists-meta-box', __('Fullwidth Audio Player - Shortcode Creator', 'radykal'), array( &$this, 'create_tracklists_meta_box'), 'page', 'side' );

		}

		//HTML meta box for the "Add New Track" page
		public function create_meta_box() {

			global $post;
			$custom_fields = get_post_custom($post->ID);

			?>

			<label for="fap_track_url"></label><?php _e('<strong>Required</strong> - Set here the URL of the MP3 or the Soundcloud resource:', 'radykal') ?></label>
			<input type="text" name="fap_track_url" value="<?php echo $custom_fields["fap_track_url"][0]; ?>" class="widefat" style="margin-bottom: 5px;" />
			<input type="button" id="fap-add-track" class="button-secondary" value="<?php _e('Add from media library', 'radykal'); ?>" />
			<br /><br />

			<label  for="fap_referral_link"></label><?php _e('<strong>Optional</strong> - Set here the referral link that should be shared on facebook and twitter:', 'radykal') ?></label>
			<input type="text" name="fap_referral_link" value="<?php echo $custom_fields["fap_referral_link"][0]; ?>" class="widefat" />

			<?php
		}

		//HTML meta box for the post and page
		public function create_tracklists_meta_box() {

			?>

			<h4><?php _e('What would you like to include?', 'radykal'); ?></h4>
			<select id="fap-type-selector">
				<option value="playlist" selected="selected"><?php _e('Playlist', 'radykal'); ?></option>
				<option value="track-url"><?php _e('Single track from URL', 'radykal'); ?></option>
				<option value="change-playlist"><?php _e('Default playlist', 'radykal'); ?></option>
			</select>

			<div id="fap-playlist-form">
				<h4><?php _e('Select a playlist:', 'radykal'); ?></h4>
				<select id="fap-playlists">
	      			<?php
	      			$playlists = get_terms('dt_playlist');
					if ( count($playlists) > 0 ){
						echo "<ul>";
					    foreach ( $playlists as $playlist ) {
					    	echo '<option value="'.$playlist->term_id.'">' . $playlist->name . '</option>';
					    }
					    echo "</ul>";
					}
	      			?>
	      		</select>
	      		<br />
			</div>

			<div id="fap-single-track-form" class="hidden fap-setting">
      			<h4><?php _e('Soundcloud, Official.fm or MP3 URL:', 'radykal'); ?></h4>
      			<span class="description"><?php _e('Soundcloud, Official.fm or MP3 URL:', 'radykal'); ?></span>
	      		<input type="text" id="fap-single-url" class="widefat" />
      			<span class="description"><?php _e('Title:', 'radykal'); ?></span>
	      		<input type="text" id="fap-single-title" class="widefat" />
	      		<span class="description"><?php _e('Share URL:', 'radykal'); ?> *</span>
	      		<input type="text" id="fap-single-share" class="widefat" />
	      		<span class="description"><?php _e('Cover URL:', 'radykal'); ?> *</span>
	      		<input type="text" id="fap-single-cover" class="widefat" />
	      		<span class="description"><?php _e('Meta text:', 'radykal'); ?> *</span>
	      		<input type="text" id="fap-single-meta" class="widefat" />
	      		<p class="description">* = optional</p>
      		</div>

      		<div class="fap-setting">
	      		<h4><?php _e('Layout:', 'radykal'); ?></h4>
	      		<select name="fap_layout">
		      		<option value="list"><?php _e('List - Image Left/Text Right', 'radykal'); ?></option>
		      		<option value="grid"><?php _e('Grid - Image Top/Text Bottom', 'radykal'); ?></option>
		      		<option value="interactive-image"><?php _e('Grid - Interactive Image', 'radykal'); ?></option>
		      		<option value="simple"><?php _e('Simple - Just Anchors', 'radykal'); ?></option>
		      		<option value="hidden"><?php _e('Hidden - Invisible anchors', 'radykal'); ?></option>
	      		</select>
	          	<h4><?php _e('Enqueue:', 'radykal'); ?></h4>
	          	<p><label><input type="checkbox" id="fap_auto_enqueue" value="1" /> <span class="description"><?php _e('Enqueue into the player when player is ready.', 'radykal'); ?></span></label></p>
	          	<p id="fap-enqueue-click-option"><label><input type="checkbox" id="fap_enqueue" value="1" /> <span class="description"><?php _e('Enable enqueue button.', 'radykal'); ?></span></label></p>

      		</div>

      		<div class="fap-center-playlist">
	      		<h4><?php _e('Center:', 'radykal'); ?></h4>
		  		<label><input type="checkbox" id="fap_center_playlist" value="1" /> <span class="description"><?php _e('Center playlist in page?', 'radykal'); ?></span></label>
      		</div>

      		<div id="fap-playlist-options" class="fap-setting">
      			<h4><?php _e('Playlist Options:', 'radykal'); ?></h4>
	      		<span class="description"><?php _e('Set a text for the playlist play button. If you do not want this button, just leave it empty:', 'radykal'); ?></span>
	      		<input type="text" id="fap_playlist_button" value="" class="widefat" />
      		</div>
	  		<br />
      		<a href="#" id="fap-form-submit" class="button-secondary"><?php _e('Add playlist', 'radykal') ?></a>

      		<?php
		}

		public function update_custom_meta_fields()	{

			//disable autosave,so custom fields will not be empty
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		        return $post_id;

			global $post;

			if(isset($_POST["fap_track_url"])) {
				update_post_meta($post->ID, "fap_track_url", trim($_POST["fap_track_url"]));
				update_post_meta($post->ID, "fap_referral_link", trim($_POST["fap_referral_link"]));
				update_post_meta($post->ID, "fap_track_shortcode", '[fap_track id="'.$post->ID.'" layout="list" enqueue="no" auto_enqueue="no"]');
			}

		}

		//create custom column shortcode
		public function add_custom_columns( $defaults ) {

			unset($defaults['date']);
		    $defaults['shortcode'] = __('Shortcode', 'radykal');
			$defaults['date'] = __('Date', 'radykal');
		    return $defaults;

		}

		//add associated data to column
		public function posts_custom_column( $column_name, $id ) {

			global $typenow;
		    if ( $typenow=='track' ) {
		        echo get_post_meta( $id, 'fap_track_shortcode', true );
		    }

		}

		//add filter dropdown with playlists
		public function playlist_filter_list() {
			$screen = get_current_screen();
		    global $wp_query;
		    if ( $screen->post_type == 'track' ) {
		        wp_dropdown_categories( array(
		            'show_option_all' => 'Show All Playlists',
		            'taxonomy' => 'dt_playlist',
		            'name' => 'dt_playlist',
		            'orderby' => 'name',
		            'selected' => ( isset( $wp_query->query['dt_playlist'] ) ? $wp_query->query['dt_playlist'] : '' ),
		            'hierarchical' => false,
		            'depth' => 3,
		            'show_count' => false,
		            'hide_empty' => true,
		        ) );
		    }
		}

		//filter tracks by playlist
		public function perform_filtering( $query ) {

			global $pagenow;
		    $qv = &$query->query_vars;
		    if ( $pagenow=='edit.php' && isset($qv['dt_playlist']) && is_numeric( $qv['dt_playlist'] ) && $qv['dt_playlist'] != 0 ) {
		        $term = get_term_by( 'id', $qv['dt_playlist'], 'dt_playlist' );
		        $qv['dt_playlist'] = $term->slug;
		    }

		}

		//include all styles and scripts for the player
		public function add_scripts_styles() {


			$options = get_option('fap_options');
			$audio_player_options = $options['audioplayer'];

			wp_enqueue_style( 'fullwidth-audio-player-tracks', plugins_url('/css/fullwidthAudioPlayer-tracks.css', __FILE__), false, self::VERSION );
			wp_enqueue_style( 'fullwidth-audio-player', plugins_url('/css/jquery.fullwidthAudioPlayer.css', __FILE__) );
			wp_enqueue_style( 'font-awesome-4', 'https://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css', false, '4.1.0' );

			if($this->int_to_bool($audio_player_options['store_playlist']) || $this->int_to_bool($audio_player_options['keep_closed_once_closed']))
				wp_enqueue_script( 'amplify-js', plugins_url('/js/amplify.min.js', __FILE__), false, '1.1.0' );

			wp_enqueue_script( 'soundmanager-2', plugins_url('/js/soundmanager2-nodebug-jsmin.js', __FILE__), false, '2.97a' );
			wp_enqueue_script( 'soundcloud-sdk', 'https://connect.soundcloud.com/sdk.js' );
			wp_enqueue_script( 'fullwidth-audio-player', plugins_url('/js/jquery.fullwidthAudioPlayer.min.js', __FILE__), array('jquery', 'jquery-ui-draggable', 'jquery-ui-sortable'), '1.6' );


		}

		//shorcode handler for a single track container
		public function create_single_track( $atts ) {

			extract( shortcode_atts( array(
				'id' => null,
				'url' => null,
				'title' => '',
				'share_link' => '',
				'cover' => '',
				'meta' => '',
				'auto_enqueue' => 'no', //enqueue the tracks when player is ready
				'layout' => 'grid', //grid, list, simple, hidden
				'enqueue' => 'no' //enqueue the track on button-click
			), $atts ) );

			if($id) {
				$track_data = $this->get_stored_track_data($id, $layout);
				return $this->get_track_wrapper( $track_data['url'], $track_data['title'], $track_data['cover'], $track_data['meta'], $track_data['share_link'], $layout, $enqueue, $auto_enqueue );
			}
			else {
				$options = get_option('fap_options');
				$general_options = $options['general'];
				$url = $this->int_to_bool($general_options['base64']) ? base64_encode($url) : $url;
				return $this->get_track_wrapper( $url, $title, $cover, $meta, $share_link, $layout, $enqueue, $auto_enqueue );
			}

		}

		//shortcode handler for a tracklist
		public function create_playlist( $atts ) {

			extract( shortcode_atts( array(
				'id' => 0,
				'layout' => 'grid', //grid,list,simple,hide
				'enqueue' => 'no', //yes,no
				'playlist_button' => '', //empty or custom text for the button
				'auto_enqueue' => 'no',
				'center' => 'no'
			), $atts ) );

			$args = array(
				'orderby' => 'menu_order',
				'order' => 'ASC',
				'posts_per_page' => -1,
				'post_type' => 'track',
				'tax_query' => array(
					array(
						'taxonomy' => 'dt_playlist',
						'field' => 'id',
						'terms' => $id
					)
				)
			);

			$options = get_option('fap_options');
			$general_options = $options['general'];

			$query = new WP_Query( $args );
			$output = '';

			if($playlist_button != '')
				$output .= '<input type="submit" value="'.$playlist_button.'" class="fap-add-playlist '.$general_options['play_css_class'].'" data-playlist="'.$id.'" data-enqueue="'.$enqueue.'" />';

			$center_html = '';
			if($center == 'yes' && $layout != 'list')
				$center_html = 'fap-center';

			$output .= '<div class="fap-in-page-playlist-'.$layout.' '.$center_html.' fap-clearfix" data-playlist="'.$id.'">';

			//loop starts
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$track_data = $this->get_stored_track_data(get_the_ID(), $layout);
					$output .= $this->get_track_wrapper( $track_data['url'], $track_data['title'], $track_data['cover'], $track_data['meta'], $track_data['share_link'], $layout, $enqueue, $auto_enqueue );
				}
			}

			wp_reset_query();

			$output .= '</div>';
			//loop ends

			return $output;

		}

		//shorcode handler for adding the player into a page, just return nothing
		public function add_player() {

			return '';
		}

		//shorcode handler for adding a popup button
		public function add_popup_button( $atts ) {

			extract( shortcode_atts( array(
				'label' => 'Pop up player'
			), $atts ) );

			return '<a href="#" class="fap-popup-player">'.$label.'</a>';
		}

		//shorcode handler for changing the default playlist
		public function change_default_playlist( $atts ) {

			extract( shortcode_atts( array(
				'id' => 0
			), $atts ) );

			$this->default_playlist = $id;

			return '';
		}

		public function add_demo_panel() {

			ob_start();

			?>
			<form action="" method="post">
				<div style="float: left; width: 250px;">
					<h5>Checkers</h5>
					<label><input type="checkbox" name="fap_option_autoplay" value="1" /> Autoplay</label><br />
					<label><input type="checkbox" name="fap_option_auto_popup" value="1" /> Auto Pop-Up</label><br />
					<label><input type="checkbox" name="fap_option_visual_playlist" value="1" checked="checked" /> Visual Playlist</label><br />
					<label><input type="checkbox" name="fap_option_volume" value="1" checked="checked" /> Volume</label><br />
					<label><input type="checkbox" name="fap_option_opened" value="1" checked="checked" /> Opened</label><br />
					<label><input type="checkbox" name="fap_option_randomize" value="1" /> Randomize tracks</label><br />
					<input type="submit" name="fap_options_form_submit" value="Apply Options" class="qbutton  small" style="margin-top: 40px;" />
				</div>
				<div>
					<h5>Player Position</h5>
					<select name="fap_option_wrapper_position" style="margin-bottom: 15px;">
						<option value="bottom">Bottom</option>
						<option value="top">Top</option>
						<option value="popup">Pop-Up</option>
					</select>
					<h5>Layout</h5>
					<select name="fap_option_layout" style="margin-bottom: 15px;">
						<option value="fullwidth" selected="selected">Fullwidth</option>
						<option value="boxed">Boxed</option>
					</select>
					<h5>Alignment</h5>
					<select name="fap_option_alignment">
						<option value="left">Left</option>
						<option value="center" selected="selected">Center</option>
						<option value="right">Right</option>
					</select>
				</div>
			</form>
			<?php

			$output_demo = ob_get_contents();
			ob_end_clean();

			return $output_demo;

		}

		public function add_clear_button( $atts ) {

			extract( shortcode_atts( array(
				'label' => 'Clear',
				'css_class' => ''
			), $atts ) );

			return '<a href="#" class="fwap-clear-button '.$css_class.'">'.$label.'</a>';
		}

		public function get_stored_track_data( $post_id, $layout ) {

			$track_data = array();

			$options = get_option('fap_options');
			$general_options = $options['general'];

			$track_post = get_post( $post_id );
			$custom_fields = get_post_custom( $post_id );

			//store url
			$track_data['url'] = $this->int_to_bool($general_options['base64']) ? base64_encode($custom_fields['fap_track_url'][0]) : $custom_fields['fap_track_url'][0];

			//store title
			$track_data['title'] = str_replace('-', '&ndash;', $track_post->post_title);

			//store cover
	        $track_data['cover'] = null;
	        if(!empty($custom_fields['fap_track_cover'][0])) {
		        $track_data['cover'] = $custom_fields['fap_track_cover'][0];
            }
            else if( has_post_thumbnail( $post_id ) ) {
            	$image_attributes = wp_get_attachment_image_src ( get_post_thumbnail_id ( $post_id ), ($layout == 'list' ? 'fap-list-thumbnail' : 'fap-grid-thumbnail'));
            	$track_data['cover'] = $image_attributes[0];
            }

            //store meta
            $track_data['meta'] = empty( $track_post->post_content ) ? '' : $track_post->post_content;

            //store share link
            $track_data['share_link'] = empty( $custom_fields['fap_referral_link'][0] ) ? '' : $custom_fields['fap_referral_link'][0];

            return $track_data;
		}

		//returns a track container
		public function get_track_wrapper( $url, $title, $cover, $meta, $share_link, $layout, $enqueue, $auto_enqueue ) {

			if(empty($url))
				return;

			$options = get_option('fap_options');
			$general_options = $options['general'];

			$sanitized_title = sanitize_title($title);
			$unique_id = uniqid();
			$unique_id_meta = '#fap-meta-'.$unique_id;


			if( !empty( $meta ) ) {
				$meta_html = '<span id="fap-meta-'.$unique_id.'" style="display: none;">'.do_shortcode($meta).'</span>';
			}

			if( !empty( $share_link ) ) {
				if( !is_user_logged_in() && $general_options['login_to_download'] ) {
					$referral_link_html = '<a href="'.wp_login_url( "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'] ).'" title="'.$general_options['login_text'].'" class="'.$general_options['referral_css_class'].'">'.$general_options['login_text'].'</a>';
				}
				else {
					$referral_link_html = '<a href="'.$share_link.'" target="_blank" class="'.$general_options['referral_css_class'].'">'.$general_options['referral_button_text'].'</a>';
				}
			}

			if($layout == 'list' || $layout == 'grid' || $layout == 'interactive-image') {
				$thumbnail_dom = '';
				$item_width = $layout == 'list' ? $general_options['list_image_width'] : $general_options['grid_image_width'];
				$item_height = $layout == 'list' ? $general_options['list_image_height'] : $general_options['grid_image_height'];

				if ( !empty($cover) ) {
					$thumbnail_html = '<img src="'.$cover.'" width='.$item_width.' height='.$item_height.' />';
				}

				if($enqueue == 'yes') {
					$enqueue_html = '<a href="'.$url.'" title="'.htmlspecialchars($title) .'" rel="'.$cover.'" target="'.$share_link.'" data-meta="'.$unique_id_meta.'" class="'.$general_options['enqueue_css_class'].' fap-single-track" data-enqueue="yes">'. $general_options['enqueue_button_text'].'</a>';
				}

				if($layout == 'interactive-image') {
					return '<div style="width: '.$item_width.'px;height: '.$item_height.'px;" class="fap-cover-wrapper fap-grid-item fap-interactive-image">'.$thumbnail_html.'<div><h3>'.$title.'</h3><p><a href="'.$url.'" title="'.htmlspecialchars($title) .'" rel="'.$cover.'" target="'.$share_link.'" data-meta="'.$unique_id_meta.'" class="'.$general_options['play_css_class'].' fap-single-track" data-autoenqueue="'.$auto_enqueue.'">'.$general_options['play_button_text'].'</a>'.$enqueue_html.''.$referral_link_html.'</p>'.$meta_html.'</div></div>';
				}
				else if($layout == 'grid') {
					return '<div class="fap-grid-item"><div style="width: '.$item_width.'px;height: '.$item_height.'px;" class="fap-cover-wrapper">'.$thumbnail_html.'</div><div class="fap-track-content" style="width: '.$item_width.'px;"><h3>'.$title.'</h3><p>'.$meta.'</p><div><a href="'.$url.'" title="'.htmlspecialchars($title) .'" rel="'.$cover.'" target="'.$share_link.'" data-meta="'.$unique_id_meta.'" class="'.$general_options['play_css_class'].' fap-single-track" data-autoenqueue="'.$auto_enqueue.'">'.$general_options['play_button_text'].'</a>'.$enqueue_html.''.$referral_link_html.'</div></div>'.$meta_html.'</div>';
				}
				else {
					return '<div class="fap-list-item fap-clearfix"><div style="width: '.$item_width.'px;height: '.$item_height.'px;" class="fap-cover-wrapper">'.$thumbnail_html.'</div><div class="fap-track-content"><h3>'.$title.'</h3><p>'.$meta.'</p><div><a href="'.$url.'" title="'.htmlspecialchars($title) .'" rel="'.$cover.'" target="'.$share_link.'" data-meta="'.$unique_id_meta.'" class="'.$general_options['play_css_class'].' fap-single-track" data-autoenqueue="'.$auto_enqueue.'">'.$general_options['play_button_text'].'</a>'.$enqueue_html.''.$referral_link_html.'</div></div>'.$meta_html.'</div>';
				}

			}
			else {
				return '<a href="'.$url.'" title="'.htmlspecialchars($title).'" rel="'.$cover.'" target="'.$share_link.'" data-meta="'.$unique_id_meta.'" class="fap-track-'.$layout.' fap-single-track" data-enqueue="'.$enqueue.'" data-autoenqueue="'.$auto_enqueue.'">'.$title .'</a>'.$meta_html.'';
			}

		}

		//setup player in frontend
		public function include_fap_frontend() {

			if(is_admin())
				return 0;

			$options = get_option('fap_options');
			$general_options = $options['general'];

			global $post;
			//add player only in frontend, when player visibility is set to true or a shortcode for this plugin are found
			if( $general_options['player_visibility'] == 'all' || ($general_options['player_visibility'] == 'shortcodes' && strpos($post->post_content,'[fap') !== false ) || ($general_options['player_visibility'] == 'frontpage' && is_front_page()) ) {
				?>
				<!-- HTML starts here -->
				<div id="fullwidthAudioPlayer" style="display: none;">

				<?php
				//get options

				$audio_player_options = $options['audioplayer'];
				$audio_player_options = $this->check_options_availability($audio_player_options);

				if($this->activate_demo && isset($_POST['fap_options_form_submit'])) {
					$audio_player_options['wrapper_position'] = $_POST['fap_option_wrapper_position'];
					$audio_player_options['autoPlay'] = $this->int_to_bool($_POST['fap_option_autoplay']);
					$audio_player_options['auto_popup'] = $this->int_to_bool($_POST['fap_option_auto_popup']);
					$audio_player_options['volume'] = $this->int_to_bool($_POST['fap_option_volume']);
					$audio_player_options['opened'] = $this->int_to_bool($_POST['fap_option_opened']);
					$audio_player_options['playlist'] = $this->int_to_bool($_POST['fap_option_visual_playlist']);
					$audio_player_options['randomize'] = $this->int_to_bool($_POST['fap_option_randomize']);
					$audio_player_options['layout'] = $_POST['fap_option_layout'];
					$audio_player_options['mainPosition'] = $_POST['fap_option_alignment'];
				}

				$args = array(
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'posts_per_page' => -1,
					'post_type' => 'track',
					'tax_query' => array(
						array(
							'taxonomy' => 'dt_playlist',
							'field' => 'id',
							'terms' => $this->default_playlist ? $this->default_playlist : $audio_player_options['default_playlist']
						)
					)
				);
				$query = new WP_Query( $args );
				//loop starts
				if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();

				//get custom fields
                $custom_fields = get_post_custom( get_the_ID() );

                //get cover if one is set
                $cover = '';
                if(!empty($custom_fields['fap_track_cover'][0])) {
	                $cover = $custom_fields['fap_track_cover'][0];
                }
                else if( has_post_thumbnail() ) {
                	$image_attributes = wp_get_attachment_image_src ( get_post_thumbnail_id ( get_the_ID() ), 'thumbnail');
                	$cover = $image_attributes[0];
                }

                //get description text
                $content = get_the_content();
                $meta_id = 'fap-meta-'.get_the_ID();
				?>
				<a href="<?php echo $this->int_to_bool($general_options['base64']) ? base64_encode($custom_fields['fap_track_url'][0]) : $custom_fields['fap_track_url'][0]; ?>" title="<?php the_title(); ?>" target="<?php echo $custom_fields['fap_referral_link'][0]; ?>"
				rel="<?php echo $cover; ?>" data-meta="#<?php echo $meta_id; ?>"></a>

				<!-- Set description text if track has one -->
				<?php if( !empty($content) ): ?>
				<span id="<?php echo $meta_id; ?>" ><?php the_content(); ?></span>
				<?php endif; ?>

				<?php endwhile; endif; wp_reset_query(); ?>
				</div>
				<!-- HTML ends here -->

				<script type="text/javascript">

					soundManager.url = "<?php echo plugins_url('/swf/', __FILE__); ?>";
					soundManager.flashVersion = 9;
					soundManager.useHTML5Audio = true;
					soundManager.debugMode = false;

					<?php if($this->int_to_bool($audio_player_options['init_on_window'])): ?>
					jQuery(window).load(function(){
					<?php else: ?>
					jQuery(document).ready(function(){
						<?php endif; ?>
						$ = jQuery.noConflict();

						setTimeout(function() {

							jQuery('#fullwidthAudioPlayer').fullwidthAudioPlayer({
								opened: <?php echo $this->int_to_bool($audio_player_options['opened']); ?>,
								volume: <?php echo $this->int_to_bool($audio_player_options['volume']); ?>,
								playlist: <?php echo $this->int_to_bool($audio_player_options['playlist']); ?>,
								autoPlay: <?php echo $this->int_to_bool($audio_player_options['autoPlay']); ?>,
								autoLoad:<?php echo $this->int_to_bool($audio_player_options['autoLoad']); ?>,
								playNextWhenFinished: <?php echo $this->int_to_bool($audio_player_options['playNextWhenFinished']); ?>,
								keyboard: <?php echo $this->int_to_bool($audio_player_options['keyboard']); ?>,
								socials: <?php echo $this->int_to_bool($audio_player_options['socials']); ?>,
								wrapperColor: '<?php echo $audio_player_options['wrapper_color']; ?>',
								mainColor: '<?php echo $audio_player_options['main_color']; ?>',
								fillColor: '<?php echo $audio_player_options['fill_color']; ?>',
								metaColor: '<?php echo $audio_player_options['meta_color']; ?>',
								strokeColor: '<?php echo $audio_player_options['stroke_color']; ?>',
								fillColorHover: '<?php echo $audio_player_options['fill_hover_color']; ?>',
								activeTrackColor: '<?php echo $audio_player_options['active_track_color']; ?>',
								wrapperPosition: window.fapPopupWin ? 'popup' : '<?php echo $audio_player_options['wrapper_position']; ?>',
								mainPosition: '<?php echo $audio_player_options['main_position']; ?>',
								height: <?php echo $audio_player_options['wrapper_height']; ?>,
								playlistHeight: <?php echo $audio_player_options['playlist_height']; ?>,
								coverSize: [<?php echo $audio_player_options['cover_width']; ?>,<?php echo $audio_player_options['cover_height']; ?>],
								offset: <?php echo $audio_player_options['offset']; ?>,
								twitterText: '<?php echo $audio_player_options['twitter_text']; ?>',
								facebookText: '<?php echo $audio_player_options['facebook_text']; ?>',
								downloadText: '<?php echo $audio_player_options['download_text']; ?>',
								popupUrl: '<?php echo plugins_url('popup.html', __FILE__);  ?>',
								autoPopup: <?php echo $this->int_to_bool($audio_player_options['auto_popup']); ?>,
								randomize: <?php echo $this->int_to_bool($audio_player_options['randomize']); ?>,
								shuffle:<?php echo $this->int_to_bool($audio_player_options['shuffle']); ?>,
								base64: <?php echo $this->int_to_bool($general_options['base64']); ?>,
								sortable: <?php echo $this->int_to_bool($audio_player_options['sortable']); ?>,
								hideOnMobile: <?php echo $this->int_to_bool($audio_player_options['hide_on_mobile']); ?>,
								loopPlaylist : <?php echo $this->int_to_bool($audio_player_options['loop_playlist']); ?>,
								storePlaylist: <?php echo $this->int_to_bool($audio_player_options['store_playlist']); ?>,
								layout: '<?php echo $audio_player_options['layout']; ?>',
								keepClosedOnceClosed: <?php echo $this->int_to_bool($audio_player_options['keep_closed_once_closed']); ?>,
								animatePageOnPlayerTop: <?php echo $this->int_to_bool($audio_player_options['animate_page_on_player_top']); ?>,
								openPlayerOnTrackPlay: <?php echo $this->int_to_bool($audio_player_options['open_player_on_track_play']); ?>,
								popup: <?php echo $this->int_to_bool($audio_player_options['popup']); ?>,
								openLabel: '<?php echo $audio_player_options['open_label']; ?>',
								closeLabel: '<?php echo $audio_player_options['close_label']; ?>'
							});
						}, <?php echo $this->activate_demo ? 201 : 0; ?>);

						jQuery('.fap-popup-player').click(function() {
							jQuery.fullwidthAudioPlayer.popUp();
							return false;
						});

						jQuery('.fwap-clear-button').on('click', function() {
							jQuery.fullwidthAudioPlayer.clear();
							return false;
						});

						var playText = "<?php echo $general_options['play_button_text']; ?>",
							pauseText = "<?php echo $general_options['pause_button_text']; ?>",
							currentTrackUrl = null,
							selectedPlayButton = null;
						jQuery('#fullwidthAudioPlayer').on('onFapTrackSelect', function(evt, trackData, playState) {

							if(trackData.duration == null) {
								//mp3,official.fm
								currentTrackUrl = trackData.stream_url;

								if(currentTrackUrl.search('official.fm') != -1) {

									currentTrackUrl = trackData.permalink_url;

								}
							}
							else {
								//soundcloud
								currentTrackUrl = trackData.permalink_url;

							}

							//pre-selected track button to play
							if(selectedPlayButton != null) {
								selectedPlayButton.text(playText);
							}
							//search for a play button with the same title
							if( <?php echo $this->int_to_bool($general_options['base64']); ?> ) {
								currentTrackUrl = Base64.encode(currentTrackUrl);
							}
							else {
								currentTrackUrl = currentTrackUrl.replace(/.*?:\/\//g, "").replace(/^www./, "");
							}

							selectedPlayButton = jQuery('.<?php echo $general_options['play_css_class']; ?>[href*="'+currentTrackUrl+'"]');
							if(playState) {
								selectedPlayButton.text(pauseText);
							}
							else {
								selectedPlayButton.text(playText);
							}
						})
						.on('onFapPlay', function() {
							if(selectedPlayButton != null) {
								selectedPlayButton.text(pauseText);
							}
						})
						.on('onFapPause', function() {
							if(selectedPlayButton != null) {
								selectedPlayButton.text(playText);
							}
						});

						var trackWrapperMargin = null;

						jQuery(window).resize(function() {

							jQuery('.fap-center').each(function(i, item) {

								var $item = $(item),
									$trackWrapper = $item.children('div:first'),
									trackWrapperWidth = $trackWrapper.outerWidth(true),
									numOfTracksPerRow = Math.floor($item.parent().width() / trackWrapperWidth);

								if(trackWrapperMargin === null) {
									trackWrapperMargin = parseInt($trackWrapper.css('marginRight'));
								}

								$item.children('div').css('marginRight', trackWrapperMargin)
								.filter(':nth-child('+numOfTracksPerRow+'n)').css('marginRight', 0);

								if(numOfTracksPerRow == 1) {
									$item.width(trackWrapperWidth);
								}
								else {
									$item.width((trackWrapperWidth * numOfTracksPerRow) - trackWrapperMargin);
								}



							});

						}).resize();


					});
				</script>

				<?php
			}

		}

		public function check_options_availability($options) {
			foreach($this->default_audioplayer_options as $key => $value) {
				$options[$key] = $options[$key] === null ? $value : $options[$key];
			}
			return $options;
		}

		public function redirect_to_template() {
			global $wp, $post;
			if($wp->query_vars['post_type'] == 'track') {
				include(dirname(__FILE__). '/single-track.php');
				die();
			}
		}

		private function int_to_bool( $value ) {
			return empty($value) ? 0 : 1;
		}

		private function getMp3Files() {

			require(dirname(__FILE__).'/getid3/getid3.php');
			$files = scandir($this->mp3_dir);

			$mp3_files = array();

			if( sizeof($files) ) {
				foreach($files as $file ) {
					$mp3_file = array();
					$filepath = $this->mp3_dir . $file;
					$fileurl = $this->mp3_dir_url . $file;

					$getID3 = new getID3;
					$ThisFileInfo = $getID3->analyze($filepath);
					getid3_lib::CopyTagsToComments($ThisFileInfo);

					if($ThisFileInfo['error'] == null) {
						$tags = @$ThisFileInfo['tags'];

						$title = '';
						if(@$tags['id3v2']['artist'][0]) {
							$title = @$tags['id3v2']['artist'][0] . ' - ';
						}
						$title .= @$tags['id3v2']['title'][0];
						$mp3_file['path'] = $filepath;
						$mp3_file['url'] = $fileurl;
						$mp3_file['title'] = $title;
						$mp3_file['meta'] = @$tags['id3v2']['genre'][0];
						$mp3_file['cover'] = isset($getID3->info['id3v2']['PIC'][0]['data']) || isset($getID3->info['id3v2']['APIC'][0]['data']) ? plugins_url('/inc/cover.php?mp3_path='.$filepath.'', __FILE__) : null;

						array_push($mp3_files, $mp3_file);
					}
				}
			}

			return $mp3_files;
		}

	}
}


//init Fullwidth Audio Player
if(class_exists('FullwidthAudioPlayer')) {
	$fap = new FullwidthAudioPlayer();
}

?>
