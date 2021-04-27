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

  /**
   * GET /index.php/api/accounts/signup
   */
  public function signup_get() {
    return json_response(200, [
      'statusCode' => 200,
      'message' => 'User signup API',
      'request' => [
        'method' => 'POST',
        'path' => '/api/accounts/signup',
        'body_fields' => $this->User_model::SIGNUP_FIELDS
      ]
    ]);
  }

  /**
   * POST /index.php/api/accounts/signup
   */
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

    json_response(200, [
      'message' => 'User signed up successfully!',
      'user' => $signed_up_user,
      'token' => $this->User_model::generate_auth_token($signed_up_user)
    ]);
  }

  /**
   * GET /index.php/api/accounts/login
   */
  public function login_get() {
    return json_response(200, [
      'statusCode' => 200,
      'message' => 'User login API',
      'request' => [
        'method' => 'POST',
        'path' => '/api/accounts/login',
        'body_fields' => $this->User_model::LOGIN_FIELDS
      ]
    ]);
  }

  /**
   * POST /index.php/api/accounts/login
   */
  public function login_post() {
    $request_body = $this->input->post($this->User_model::LOGIN_FIELDS);

    $this->form_validation->set_data($request_body);

    if ($this->form_validation->run() == false) {
      return json_response(400, [
        'statusCode' => 400,
        'errors' => $this->form_validation->error_array()
      ]);
    }

    // find user by username
    $found_user = $this->db
      ->select(['id', 'first_name', 'last_name', 'username', 'password', 'email'])
      ->from('users')
      ->where(['username' => $request_body['username']])
      ->get()
      ->row();

    if (!$found_user) {
      return json_response(401, [
        'statusCode' => 401,
        'message' => 'Invalid username or password'
      ]);
    }

    // verify password
    if (!password_verify($request_body['password'], $found_user->password)) {
      return json_response(401, [
        'statusCode' => 401,
        'message' => 'Invalid username or password'
      ]);
    }

    unset($found_user->password);

    json_response(200, [
      'statusCode' => 200,
      'message' => 'User logged in successfully!',
      'user' => $found_user,
      'token' => $this->User_model::generate_auth_token($found_user)
    ]);
  }

  /**
   * GET /index.php/api/accounts/validate_user
   */
  public function validate_user_get() {
    $request_token = $this->input->get_request_header('authorization');
    
    try {
      $token_payload = $this->User_model::decode_auth_token($request_token);
    } catch (Exception $e) {
      json_response(401, [
        'statusCode' => 401,
        'message' => 'Invalid token',
        'error' => $e->getMessage()
      ]);
    }

    $authenticated_user = $this->db
      ->select(['id', 'first_name', 'last_name', 'username', 'email'])
      ->from('users')
      ->where(['id' => $token_payload->id])
      ->get()
      ->row();

    json_response(200, [
      'statusCode' => 200,
      'message' => 'Authenticated user',
      'user' => $authenticated_user
    ]);
  }
}
