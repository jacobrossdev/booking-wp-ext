<?php

/**
 * Show Order email with the Appointment Date Range
 * as well as the Jitsi link for the meeting
 */
add_action( 'woocommerce_email_before_order_table', 'bext_localpickup_extra_info', 10, 4 );
function bext_localpickup_extra_info( $order, $sent_to_admin, $plain_text, $email ) {
  $meetings_ordered = create_meetings_ordered_array($order);
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

  $meetings_ordered = array();

  $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

  $rows = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_parent` = {$order_id}");

  if( function_exists('get_wc_appointment') ){

    foreach( $rows as $row ){

      $appointment = get_wc_appointment($row->ID);

      if( $appointment ){

        $customer_id = $order->get_user_id() ? $order->get_user_id() : $order->get_customer_id();

        $start_date = $appointment->get_start_date();
        $end_date = $appointment->get_end_date();
        
        //meeting duration
        $start_time = strtotime($start_date);
        $end_time = strtotime($end_date);
        $duration = $end_time - $start_time;

        $meeting_url_path = 'meet_'.bext_generate_string();
        $jitsi_domain = $settings['jisti_server_domain'];
        $link = $jitsi_domain .'/'.$meeting_url_path;

        $post_id = wp_insert_post([
          'post_title'    => 'Booking reserved for Customer # '.$customer_id,
          'post_content'  => 'Booking reserved on '.$start_date.' via '. $link,
          'post_status'   => 'publish',
          'post_type'     => 'booking-notif',
          'post_author'   => $order->get_user_id() ? $order->get_user_id() : $order->get_customer_id()
        ]);

        $jwt_token = create_jwt_token($start_time+$duration);

        $meetings_ordered[] = array(
          'start_date' => $start_date,
          'end_date' => $end_date,
          'link' => $link,
          'admin_link' => $link . '?jwt='.$jwt_token
        );
      }

    }

  }


  $order->update_meta_data( '_meetings_ordered', $meetings_ordered ); // Add the custom field
  $order->save(); // Save data (as order exist yet)
}

/**
 * This will display the jitsi link on the order thank you page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'bext_custom_checkout_field_display_admin_order_meta', 10, 1 );
function bext_custom_checkout_field_display_admin_order_meta( $order ){

  global $wpdb;

  $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

  $meetings_ordered = get_post_meta( $order_id, '_meetings_ordered', true );

  foreach($meetings_ordered as $mo){
    echo '<p><strong>'.__($mo['start_date'].' Meeting Link').':</strong> <a href="'.$mo['admin_link'].'">' . $mo['link'] . '</a></p>';
  }
}

/**
 * This will display the jitsi link on the order details admin page
 */
add_action( 'woocommerce_order_details_after_order_table', 'bext_custom_field_display_cust_order_meta', 10, 1 );
function bext_custom_field_display_cust_order_meta($order){

  $settings = get_option('booking_ext_settings');

  $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

  $meetings_ordered = get_post_meta( $order_id, '_meetings_ordered', true );

  foreach($meetings_ordered as $mo){
    echo '<p><strong>'.__($mo['start_date'].' Meeting Link').':</strong> <a href="'.$mo['link'].'">' . $mo['link'] . '</a></p>';
  }
}

function create_meetings_ordered_array($order){

  global $wpdb;

  $meetings_ordered = array();

  $settings = get_option('booking_ext_settings');

  $order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;

  $rows = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}posts` WHERE `post_parent` = {$order_id}");

  if( function_exists('get_wc_appointment') ){

    foreach($rows as $row){

      $appointment = get_wc_appointment($row->ID);

      $customer_id = $order->get_user_id() ? $order->get_user_id() : $order->get_customer_id();

      $start_date = $appointment->get_start_date();
      $end_date = $appointment->get_end_date();
      
      //meeting duration
      $start_time = strtotime($start_date);
      $end_time = strtotime($end_date);
      $duration = $end_time - $start_time;

      $meeting_url_path = 'meet_'.bext_generate_string();
      $jitsi_domain = $settings['jisti_server_domain'];
      $link = $jitsi_domain .'/'.$meeting_url_path;

      $jwt_token = create_jwt_token($start_time+$duration);

      $meetings_ordered[] = array(
        'start_date' => $start_date,
        'end_date' => $end_date,
        'link' => $link,
        'admin_link' => $link . '?jwt='.$jwt_token
      );

    }

  }
  return $meetings_ordered;
}
