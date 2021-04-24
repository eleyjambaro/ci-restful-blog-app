<?php
declare(strict_types = 1);

defined('BASEPATH') OR exit('No direct script access allowed');

function json_response(int $status_header, mixed $response) {
  $CI =& get_instance();
  $CI->output
    ->set_status_header($status_header)
    ->set_content_type('application/json', 'utf-8')
    ->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
    ->_display();   
  
  exit;
}