<?php

use ReallySimpleJWT\Token;
function create_jwt_token($date){

  $settings = get_option('booking_ext_settings');


  $app_id = $settings['jisti_app_id'];
  $secret = $settings['jisti_app_secret'];

  $payload = [
    "moderator" => true,
    "aud" => $app_id,
    "iss" => $app_id,
    "sub" => $settings['jisti_server_domain'],
    "room" => "*",
    "exp" => $date,
    'context' => [
      'user' => [
        'avatar' => 'http://www.gravatar.com/avatar/?d=identicon',
        'name' => 'Jacob Ross',
        'email' => 'mcbtv.twitch@gmail.com'
      ]
    ]
  ];


  $token = Token::customPayload($payload, $secret);
  return $token;
}