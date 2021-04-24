<?php
declare(strict_types = 1);

defined('BASEPATH') OR exit('No direct script access allowed');

class Accounts extends CI_Controller {
  public function __construct() {
    parent::__construct();

    $this->load->model('User_model');
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters(null, null);
  }

  public function signup_get() {
    return json_response(200, [
      'statusCode' => 200,
      'message' => 'User signup API',
      'request' => [
        'method' => 'POST',
        'path' => '/accounts/signup',
        'body_fields' => $this->User_model::SIGNUP_FIELDS
      ]
    ]);
  }

  public function signup_post() {
    $request_body = $this->input->post($this->User_model::SIGNUP_FIELDS);

    $this->form_validation->set_data($request_body);

    if ($this->form_validation->run() == false) {
      return json_response(400, [
        'statusCode' => 400,
        'errors' => $this->form_validation->error_array()
      ]);
    }

    $user = $this->User_model::create($request_body);

    if (!$this->db->insert('users', $user)) {
      return json_response(500, [
        'statusCode' => 500,
        'message' => 'Something went wrong'
      ]);
    }

    $signed_up_user = $this->db
      ->select(['id', 'first_name', 'last_name', 'username', 'email'])
      ->from('users')
      ->where(['username' => $user['username']])
      ->get()
      ->row();

    // TODO: Generate authentication token and add to the response data

    json_response(200, [
      'message' => 'User signed up successfully!',
      'user' => $signed_up_user
    ]);
  }

  public function login_get() {
    // TODO: Add login logic

    json_response(200, ['message' => 'Welcome to Login page']);
  }
}
