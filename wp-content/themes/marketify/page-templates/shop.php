<?php
/**
 * Template Name: Page: Shop
 *
 * Load the [downloads] shortcode.
 *
 * @package Marketify
 */

get_header(); ?>

    <?php do_action( 'marketify_entry_before' ); ?>

    <div id="content" class="site-content container">

        <?php do_action( 'marketify_shop_before' ); ?>
        <a href="#ninja-forms-modal-5" rel="nf-modal:open" class="nf-modal-link">Click here to fill out out form</a>

        <div class="marketify-archive-download row">
            <div role="main" class="content-area col-xs-12 <?php echo is_active_sidebar( 'sidebar-download' ) ? 'col-md-8' : ''; ?>">

                <?php do_action( 'marketify_downloads_before' ); ?>

				<?php do_action( 'marketify_downloads' ); ?>
                <?php do_action( 'marketify_downloads_after' ); ?>

            </div>

            <?php get_sidebar( 'archive-download' ); ?>
        </div>

        <?php
            do_action( 'marketify_shop_after' );
            do_action('draw_hidden_forms');
        ?>

    </div>

<?php get_footer(); ?>