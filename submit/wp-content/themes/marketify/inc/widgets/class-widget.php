<?php
/**
 * Widget base
 */
class Marketify_Widget extends WP_Widget {

    public $widget_cssclass;
    public $widget_description;
    public $widget_id;
    public $widget_name;
    public $settings;
    public $control_ops;

    /**
     * Constructor
     */
    public function __construct() {
        $widget_ops = array(
            'classname'   => $this->widget_cssclass,
            'description' => $this->widget_description
        );

        if ( ! $this->widget_id ) {
            $this->widget_id = null;
        }

        parent::__construct( $this->widget_id, $this->widget_name, $widget_ops, $this->control_ops );

        $this->settings = apply_filters( 'jobify_widget_settings', $this->settings );
        $this->settings = apply_filters( 'jobify_widget_settings_' . $this->widget_id, $this->settings );

        add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
        add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
        add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ) );
    }

    public function admin_print_styles() {
?>
<style>
.marketify-widget-usage { display: block; color: #a00; }
#sidebar-download-single .marketify-widget-usage.sidebar-download-single { display: none; }
#sidebar-download .marketify-widget-usage.sidebar-download { display: none; }
#sidebar-vendor .marketify-widget-usage.sidebar-vendor { display: none; }
#home-1 .marketify-widget-usage.home-1 { display: none; }
div[id^="widget-area-page"] .marketify-widget-usage.widgetized { display: none; }
</style>
<?php
    }

    /**
     * get_cached_widget function.
     */
    function get_cached_widget( $args ) {
        $cache = wp_cache_get( $this->widget_id, 'widget' );

        if ( ! is_array( $cache ) )
            $cache = array();

        if ( ! isset( $args[ 'widget_id' ] ) )
            return false;

        if ( isset( $cache[ $args[ 'widget_id' ] ] ) ) {
            echo $cache[ $args[ 'widget_id' ] ];
            return true;
        }

        return false;
    }

    /**
     * Cache the widget
     */
    public function cache_widget( $args, $content ) {
        if ( ! isset( $args[ 'widget_id' ] ) )
            return;

        $cache[ $args[ 'widget_id' ] ] = $content;

        wp_cache_set( $this->widget_id, $cache, 'widget' );
    }

    /**
     * Flush the cache
     * @return [type]
     */
    public function flush_widget_cache() {
        wp_cache_delete( $this->widget_id, 'widget' );
    }

    /**
     * update function.
     *
     * @see WP_Widget->update
     * @access public
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        if ( ! $this->settings )
            return $instance;

        foreach ( $this->settings as $key => $setting ) {
            switch ( $setting[ 'type' ] ) {
                case 'textarea' :
                    $instance[ $key ] = $new_instance[ $key ];
                break;
                case 'number' :
                    $instance[ $key ] = absint( $new_instance[ $key ] );
                break;
                case 'multicheck' :
                    $instance[ $key ] = maybe_serialize( $new_instance[ $key ] );
                break;
                default :
                    $instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
                break;
            }
        }

        $this->flush_widget_cache();

        return $instance;
    }

    /**
     * form function.
     *
     * @see WP_Widget->form
     * @access public
     * @param array $instance
     * @return void
     */
    function form( $instance ) {

        if ( ! $this->settings )
            return;

        foreach ( $this->settings as $key => $setting ) {

            $value = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting[ 'std' ];

            switch ( $setting[ 'type' ] ) {
                case 'widget-area' :
                ?>
                    <p class="marketify-widget-usage <?php echo $key; ?>"><em><?php printf( __( 'Only for use in the <strong>%s</strong> widget area.', 'marketify' ), $setting[ 'std' ] ); ?></em></p>
                <?php
                break;
                case 'description' :
                    ?>
                    <p class="description"><?php echo $value; ?></p>
                    <?php
                break;
                case 'text' :
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
                        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
                    </p>
                    <?php
                break;
                case 'checkbox' :
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id( $key ); ?>">
                            <input type="checkbox" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" value="1" <?php checked( 1, esc_attr( $value ) ); ?>/>
                            <?php echo $setting[ 'label' ]; ?>
                        </label>
                    </p>
                    <?php
                break;
                case 'multicheck' :
                    $value = maybe_unserialize( $value );

                    if ( ! is_array( $value ) ) {
                        $value = array();
                    }
                    ?>
                    <p><?php echo esc_attr( $setting[ 'label' ] ); ?></p>
                    <p>
                        <?php foreach ( $setting[ 'options' ] as $id => $label ) : ?>
                        <label for="<?php echo esc_attr( $id ); ?>">
                            <input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo $this->get_field_name( $key ); ?>[]" value="<?php echo esc_attr( $id ); ?>" <?php checked( in_array( $id, $value ) ); ?>/>
                            <?php echo esc_attr( $label ); ?><br />
                        </label>
                        <?php endforeach; ?>
                    </p>
                    <?php
                break;
                case 'select' :
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
                        <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
                            <?php foreach ( $setting[ 'options' ] as $key => $label ) : ?>
                            <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $value ); ?>><?php echo esc_attr( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <?php
                break;
                case 'number' :
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
                        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting[ 'step' ] ); ?>" min="<?php echo esc_attr( $setting[ 'min' ] ); ?>" max="<?php echo esc_attr( $setting[ 'max' ] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
                    </p>
                    <?php
                break;
                case 'textarea' :
                    if ( ! isset( $settings[ 'rows' ] ) ) {
                        $settings[ 'rows' ] = 4;
                    }
                    ?>
                    <p>
                        <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
                        <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" rows="<?php echo $setting[ 'rows' ]; ?>"><?php echo esc_html( $value ); ?></textarea>
                    </p>
                    <?php if ( isset ( $setting[ 'description' ] ) ) : ?>
                    <p><?php echo $setting[ 'description' ]; ?></p>
                    <?php endif; ?>
                <?php
                break;
                case 'colorpicker' :
                        wp_enqueue_script( 'wp-color-picker' );
                        wp_enqueue_style( 'wp-color-picker' );
                    ?>
                        <p style="margin-bottom: 0;">
                            <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
                        </p>
                        <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" data-default-color="<?php echo $value; ?>" value="<?php echo $value; ?>" />
                        <script>
                            jQuery(document).ready(function($){
                                $( 'input[name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>"]' ).wpColorPicker();
                            });
                        </script>
                        <p></p>
                    <?php
                break;
                case 'image' :
                    wp_enqueue_media();
                    wp_enqueue_script( 'app-image-widget-admin', get_template_directory_uri() . '/js/app/app-image-widget-admin.js', array( 'jquery' ), '', true );
                ?>
                    <p style="margin-bottom: 0;">
                        <label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting[ 'label' ]; ?></label>
                    </p>
                    <p style="margin-top: 3px;">
                        <a href="#" class="button-secondary <?php echo esc_attr( $this->get_field_id( $key ) ); ?>-add"><?php _e( 'Choose Image', 'marketify' ); ?></a>
                    </p>
                    <p>
                        <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>"value="<?php echo $value; ?>" placeholder="http://" />
                    </p>
                    <script>
                        jQuery(document).on( 'ready', function($) {
                            var <?php echo $key; ?> = new cImageWidget.MediaManager({
                                target: '<?php echo esc_attr( $this->get_field_id( $key ) ); ?>',
                            });
                        });
                    </script>
                <?php
                break;
            }
        }
    }
}
