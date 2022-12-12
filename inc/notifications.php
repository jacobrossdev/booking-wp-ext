<?php

/**
 * Registers a new post type
 * @uses $wp_post_types Inserts new post type object into the list
 *
 * @param string  Post type key, must not exceed 20 characters
 * @param array|string  See optional args description above.
 * @return object|WP_Error the registered post type object, or an error object
 */
add_action( 'init', 'bext_booking_notifications_callback' );
function bext_booking_notifications_callback() {

  $labels = array(
    'name'               => __( 'Booking Notifications', 'text-domain' ),
    'singular_name'      => __( 'Booking Notification', 'text-domain' ),
    'add_new'            => _x( 'Add New Booking Notification', 'text-domain', 'text-domain' ),
    'add_new_item'       => __( 'Add New Booking Notification', 'text-domain' ),
    'edit_item'          => __( 'Edit Booking Notification', 'text-domain' ),
    'new_item'           => __( 'New Booking Notification', 'text-domain' ),
    'view_item'          => __( 'View Booking Notification', 'text-domain' ),
    'search_items'       => __( 'Search Booking Notifications', 'text-domain' ),
    'not_found'          => __( 'No Booking Notifications found', 'text-domain' ),
    'not_found_in_trash' => __( 'No Booking Notifications found in Trash', 'text-domain' ),
    'parent_item_colon'  => __( 'Parent Booking Notification:', 'text-domain' ),
    'menu_name'          => __( 'Booking Notifications', 'text-domain' ),
  );

  $args = array(
    'labels'              => $labels,
    'hierarchical'        => false,
    'description'         => 'description',
    'taxonomies'          => array(),
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => null,
    'menu_icon'           => null,
    'show_in_nav_menus'   => true,
    'publicly_queryable'  => true,
    'exclude_from_search' => false,
    'has_archive'         => true,
    'query_var'           => true,
    'can_export'          => true,
    'rewrite'             => true,
    'capability_type'     => 'post',
    'supports'            => array(
      'title',
      'editor',
      'author',
      'thumbnail',
      'excerpt',
      'custom-fields',
      'trackbacks',
      'comments',
      'revisions',
      'page-attributes',
      'post-formats',
    ),
  );

  register_post_type( 'booking-notif', $args );
}

// this is to add a fake component to BuddyPress. A registered component is needed to add notifications
add_filter( 'bp_notifications_get_registered_components', 'bext_filter_notifications_get_registered_components' );
function bext_filter_notifications_get_registered_components( $component_names = array() ) {

  // Force $component_names to be an array
  if ( ! is_array( $component_names ) ) {
    $component_names = array();
  }

  // Add 'custom' component to registered components array
  array_push( $component_names, 'custom' );

  // Return component's with 'custom' appended
  return $component_names;
}

// this gets the saved item id, compiles some data and then displays the notification
add_filter( 'bp_notifications_get_notifications_for_user', 'bext_format_buddypress_notifications', 10, 5 );
function bext_format_buddypress_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

  // New custom notifications
  if ( 'custom_action' === $action ) {
  
    $post = get_post( $item_id );
  
    $custom_title = 'You have booked a scheduled meeting';
    $custom_text = $post->post_content;

    // WordPress Toolbar
    if ( 'string' === $format ) {
      $return = esc_html( $custom_text );

    // Deprecated BuddyBar
    } else {
      $return = apply_filters( 'custom_filter', array(
        'text' => $custom_text,
        'link' => $custom_link
      ), $custom_link, (int) $total_items, $custom_text, $custom_title );
    }
    
    return $return;
    
  }
}

// this hooks to comment creation and saves the comment id
add_action( 'wp_insert_post', 'bext_custom_add_notification', 99, 2 );
function bext_custom_add_notification( $post_id, $post ) {

  if( $post->post_type != 'booking-notif' )
    return;

  $author_id = $post->post_author;
  if( function_exists('bp_notifications_add_notification') ){

    bp_notifications_add_notification( array(
      'user_id'           => $author_id,
      'item_id'           => $post_id,
      'component_name'    => 'custom',
      'component_action'  => 'custom_action',
      'date_notified'     => bp_core_current_time(),
      'is_new'            => 1,
    ) );
    
  }
  
}