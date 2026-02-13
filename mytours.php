<?php
/**
 * Plugin Name: MyTours
 * Plugin URI:  https://rdeveloper.com/mytours
 * Description: Un plugin de reservas para transporte.
 * Version:     1.0.0
 * Author:      Ricardo Developer
 * Author URI:  https://rdeveloper.com
 * License:     GPL-2.0+
 * Text Domain: mytours
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants
define( 'MYTOURS_VERSION', '1.0.0' );
define( 'MYTOURS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MYTOURS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Register Custom Post Types
 */
function mytours_register_cpt() {
    // Register Tour Post Type
    $labels_tour = array(
        'name'                  => _x( 'Tours', 'Post Type General Name', 'mytours' ),
        'singular_name'         => _x( 'Tour', 'Post Type Singular Name', 'mytours' ),
        'menu_name'             => __( 'Tours', 'mytours' ),
        'name_admin_bar'        => __( 'Tour', 'mytours' ),
        'archives'              => __( 'Tour Archives', 'mytours' ),
        'attributes'            => __( 'Tour Attributes', 'mytours' ),
        'parent_item_colon'     => __( 'Parent Tour:', 'mytours' ),
        'all_items'             => __( 'All Tours', 'mytours' ),
        'add_new_item'          => __( 'Add New Tour', 'mytours' ),
        'add_new'               => __( 'Add New', 'mytours' ),
        'new_item'              => __( 'New Tour', 'mytours' ),
        'edit_item'             => __( 'Edit Tour', 'mytours' ),
        'update_item'           => __( 'Update Tour', 'mytours' ),
        'view_item'             => __( 'View Tour', 'mytours' ),
        'view_items'            => __( 'View Tours', 'mytours' ),
        'search_items'          => __( 'Search Tour', 'mytours' ),
        'not_found'             => __( 'Not found', 'mytours' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'mytours' ),
        'featured_image'        => __( 'Featured Image', 'mytours' ),
        'set_featured_image'    => __( 'Set featured image', 'mytours' ),
        'remove_featured_image' => __( 'Remove featured image', 'mytours' ),
        'use_featured_image'    => __( 'Use as featured image', 'mytours' ),
        'insert_into_item'      => __( 'Insert into tour', 'mytours' ),
        'uploaded_to_this_item' => __( 'Uploaded to this tour', 'mytours' ),
        'items_list'            => __( 'Tours list', 'mytours' ),
        'items_list_navigation' => __( 'Tours list navigation', 'mytours' ),
        'filter_items_list'     => __( 'Filter tours list', 'mytours' ),
    );
    $args_tour = array(
        'label'                 => __( 'Tour', 'mytours' ),
        'description'           => __( 'Post Type for Tours', 'mytours' ),
        'labels'                => $labels_tour,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'taxonomies'            => array( 'category', 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
        'menu_icon'             => 'dashicons-location-alt',
    );
    register_post_type( 'mytours_tour', $args_tour );

    // Register Reservation Post Type
    $labels_booking = array(
        'name'                  => _x( 'Reservations', 'Post Type General Name', 'mytours' ),
        'singular_name'         => _x( 'Reservation', 'Post Type Singular Name', 'mytours' ),
        'menu_name'             => __( 'Reservations', 'mytours' ),
        'name_admin_bar'        => __( 'Reservation', 'mytours' ),
        'all_items'             => __( 'All Reservations', 'mytours' ),
        'add_new_item'          => __( 'Add New Reservation', 'mytours' ),
        'add_new'               => __( 'Add New', 'mytours' ),
        'new_item'              => __( 'New Reservation', 'mytours' ),
        'edit_item'             => __( 'Edit Reservation', 'mytours' ),
        'update_item'           => __( 'Update Reservation', 'mytours' ),
        'view_item'             => __( 'View Reservation', 'mytours' ),
        'view_items'            => __( 'View Reservations', 'mytours' ),
        'search_items'          => __( 'Search Reservation', 'mytours' ),
    );
    $args_booking = array(
        'label'                 => __( 'Reservation', 'mytours' ),
        'description'           => __( 'Post Type for Reservations', 'mytours' ),
        'labels'                => $labels_booking,
        'supports'              => array( 'title', 'custom-fields' ), // Title can be "Reservation #ID"
        'hierarchical'          => false,
        'public'                => false, // Not public
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 6,
        'show_in_admin_bar'     => false,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
        'menu_icon'             => 'dashicons-calendar-alt',
        'capabilities' => array(
            'create_posts' => false, // Initial implementation: Admin only or programmatically
        ),
        'map_meta_cap' => true,
    );
    register_post_type( 'mytours_booking', $args_booking );
}
add_action( 'init', 'mytours_register_cpt', 0 );

/**
 * Shortcode for Booking Form
 */
function mytours_booking_form_shortcode( $atts ) {
    ob_start();
    ?>
    <div class="mytours-booking-form">
        <form id="mytours-booking" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <input type="hidden" name="action" value="mytours_submit_booking">
            <p>
                <label for="mytours_name"><?php _e('Name', 'mytours'); ?></label>
                <input type="text" name="mytours_name" id="mytours_name" required>
            </p>
            <p>
                <label for="mytours_email"><?php _e('Email', 'mytours'); ?></label>
                <input type="email" name="mytours_email" id="mytours_email" required>
            </p>
            <p>
                <label for="mytours_tour_id"><?php _e('Select Tour', 'mytours'); ?></label>
                <select name="mytours_tour_id" id="mytours_tour_id" required>
                    <?php
                    $tours = get_posts(array('post_type' => 'mytours_tour', 'numberposts' => -1));
                    foreach ($tours as $tour) {
                        echo '<option value="' . $tour->ID . '">' . $tour->post_title . '</option>';
                    }
                    ?>
                </select>
            </p>
             <p>
                <label for="mytours_date"><?php _e('Date', 'mytours'); ?></label>
                <input type="date" name="mytours_date" id="mytours_date" required>
            </p>
            <p>
                <input type="submit" value="<?php _e('Book Now', 'mytours'); ?>">
            </p>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'mytours_booking', 'mytours_booking_form_shortcode' );

/**
 * Handle Booking Submission
 */
function mytours_handle_booking_submission() {
    if ( isset($_POST['action']) && $_POST['action'] == 'mytours_submit_booking' ) {
        $name = sanitize_text_field( $_POST['mytours_name'] );
        $email = sanitize_email( $_POST['mytours_email'] );
        $tour_id = intval( $_POST['mytours_tour_id'] );
        $date = sanitize_text_field( $_POST['mytours_date'] );

        $booking_id = wp_insert_post(array(
            'post_type' => 'mytours_booking',
            'post_title' => 'Reservation - ' . $name . ' - ' . $date,
            'post_status' => 'publish', // or 'pending'
        ));

        if ( $booking_id ) {
            update_post_meta( $booking_id, '_mytours_customer_name', $name );
            update_post_meta( $booking_id, '_mytours_customer_email', $email );
            update_post_meta( $booking_id, '_mytours_tour_id', $tour_id );
             update_post_meta( $booking_id, '_mytours_reservation_date', $date );

            // Redirect or show success message
             wp_redirect( home_url( '/?booking=success' ) ); // Simple redirect for now
             exit;
        }
    }
}
add_action( 'admin_post_mytours_submit_booking', 'mytours_handle_booking_submission' );
add_action( 'admin_post_nopriv_mytours_submit_booking', 'mytours_handle_booking_submission' ); // Allow non-logged-in users

/**
 * Add Metaboxes for Tour Details
 */
function mytours_add_tour_metaboxes() {
    add_meta_box(
        'mytours_tour_details',
        __( 'Tour Details', 'mytours' ),
        'mytours_render_tour_metabox',
        'mytours_tour',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'mytours_add_tour_metaboxes' );

function mytours_render_tour_metabox( $post ) {
    $price = get_post_meta( $post->ID, '_mytours_price', true );
    $duration = get_post_meta( $post->ID, '_mytours_duration', true );
    wp_nonce_field( 'mytours_save_tour_details', 'mytours_tour_nonce' );
    ?>
    <p>
        <label for="mytours_price"><?php _e( 'Price:', 'mytours' ); ?></label>
        <input type="text" id="mytours_price" name="mytours_price" value="<?php echo esc_attr( $price ); ?>">
    </p>
    <p>
        <label for="mytours_duration"><?php _e( 'Duration:', 'mytours' ); ?></label>
        <input type="text" id="mytours_duration" name="mytours_duration" value="<?php echo esc_attr( $duration ); ?>">
    </p>
    <?php
}

function mytours_save_tour_details( $post_id ) {
    if ( ! isset( $_POST['mytours_tour_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['mytours_tour_nonce'], 'mytours_save_tour_details' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( isset( $_POST['post_type'] ) && 'mytours_tour' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }
    if ( isset( $_POST['mytours_price'] ) ) {
        update_post_meta( $post_id, '_mytours_price', sanitize_text_field( $_POST['mytours_price'] ) );
    }
    if ( isset( $_POST['mytours_duration'] ) ) {
        update_post_meta( $post_id, '_mytours_duration', sanitize_text_field( $_POST['mytours_duration'] ) );
    }
}
add_action( 'save_post', 'mytours_save_tour_details' );

/**
 * Custom Columns for Reservations
 */
function mytours_booking_columns( $columns ) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => $columns['title'],
        'customer' => __( 'Customer', 'mytours' ),
        'tour' => __( 'Tour', 'mytours' ),
        'booking_date' => __( 'Date', 'mytours' ),
        'date' => $columns['date'],
    );
    return $new_columns;
}
add_filter( 'manage_mytours_booking_posts_columns', 'mytours_booking_columns' );

function mytours_booking_custom_column( $column, $post_id ) {
    switch ( $column ) {
        case 'customer':
            echo get_post_meta( $post_id, '_mytours_customer_name', true );
            echo '<br>';
            echo '<a href="mailto:' . esc_attr( get_post_meta( $post_id, '_mytours_customer_email', true ) ) . '">' . esc_html( get_post_meta( $post_id, '_mytours_customer_email', true ) ) . '</a>';
            break;
        case 'tour':
            $tour_id = get_post_meta( $post_id, '_mytours_tour_id', true );
            if ( $tour_id ) {
                echo get_the_title( $tour_id );
            } else {
                echo __( 'Unknown Tour', 'mytours' );
            }
            break;
        case 'booking_date':
            echo get_post_meta( $post_id, '_mytours_reservation_date', true );
            break;
    }
}
add_action( 'manage_mytours_booking_posts_custom_column', 'mytours_booking_custom_column', 10, 2 );


