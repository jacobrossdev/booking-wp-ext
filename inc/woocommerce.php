<?php


/**
 * Show Order email with the Appointment Date Range
 * as well as the Jitsi link for the meeting
 */
add_action( 'woocommerce_email_before_order_table', 'bext_localpickup_extra_info', 10, 4 );
function bext_localpickup_extra_info( $order, $sent_to_admin, $plain_text, $email ) {

  $settings = get_option('booking_ext_settings');

  global $wpdb;
  foreach ($order->get_items() as $item_key => $item ){

    $order_id = $order->get_id();

    $product = $item->get_product();

    if( $product->get_type() == 'appointment' ){

      $row = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_parent` = {$order_id}");

      if( function_exists('get_wc_appointment') ){

        $appointment = get_wc_appointment($row->ID);

      }
    }
  }

  $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
  $meeting_url_path = get_post_meta( $order_id, '_meeting_url_path', true );

  ob_start();
  include dirname(__DIR__).'/partial/order-detail-appointment.php';
  $order_detail_appointment = ob_get_clean();
  echo $order_detail_appointment;
}

/**
 * Creates the jitsi link and saves it to the order meta
 */
add_action( 'woocommerce_checkout_order_created', 'bext_add_custom_field_on_placed_order' );
function bext_add_custom_field_on_placed_order( $order ){

  global $wpdb;
  
  $settings = get_option('booking_ext_settings');
  
  foreach ($order->get_items() as $item_key => $item ){

    $product = $item->get_product();

    if( $product->get_type() == 'appointment' ){

      $row = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_parent` = {$order_id}");

      if( function_exists('get_wc_appointment') ){

        $appointment = get_wc_appointment($row->ID);

      }

      $customer_id = $order->get_user_id() ? $order->get_user_id() : $order->get_customer_id();

      $meeting_string = bext_generate_string();
      $meeting_url_path = 'meet_'.$meeting_string;

      $post_id = wp_insert_post([
        'post_title'    => 'Booking reserved for Customer # '.$customer_id,
        'post_content'  => 'Booking reserved on '.$appointment->get_start_date().' via https://'.$settings['jisti_server_domain'].'/'.$meeting_url_path,
        'post_status'   => 'publish',
        'post_type'     => 'booking-notif',
        'post_author'   => $order->get_user_id() ? $order->get_user_id() : $order->get_customer_id()
      ]);


      $order->update_meta_data( '_meeting_url_path', $meeting_url_path ); // Add the custom field
      $order->save(); // Save data (as order exist yet)

    }
  }
}

/**
 * This will display the jitsi link on the order thank you page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'bext_custom_checkout_field_display_admin_order_meta', 10, 1 );
function bext_custom_checkout_field_display_admin_order_meta( $order ){
    global $wpdb;

    $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

    foreach ($order->get_items() as $item_key => $item ){

      $product = $item->get_product();

      if( $product->get_type() == 'appointment' ){

        $row = $wpdb->get_row("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_parent` = {$order_id}");

        if( function_exists('get_wc_appointment') ){

          $appointment = get_wc_appointment($row->ID);

        }
      }
    }

    $time = strtotime($appointment->get_start_date())+3600;
    $settings = get_option('booking_ext_settings');
    echo '<p><strong>'.__('Meeting Link').':</strong> <a href="https://'.$settings['jisti_server_domain'].'/' . get_post_meta( $order_id, '_meeting_url_path', true ). '?jwt='.create_jwt_token($time).'">https://'.$settings['jisti_server_domain'].'/' . get_post_meta( $order_id, '_meeting_url_path', true ). '</a></p>';
}

/**
 * This will display the jitsi link on the order details admin page
 */
add_action( 'woocommerce_order_details_after_order_table', 'bext_custom_field_display_cust_order_meta', 10, 1 );
function bext_custom_field_display_cust_order_meta($order){
    $settings = get_option('booking_ext_settings');
    $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
    echo '<p><strong>'.__('Meeting Link').':</strong> <a href="https://'.$settings['jisti_server_domain'].'/' . get_post_meta( $order_id, '_meeting_url_path', true ). '">'.$settings['jisti_server_domain'].'/' . get_post_meta( $order_id, '_meeting_url_path', true ). '</a></p>';
}