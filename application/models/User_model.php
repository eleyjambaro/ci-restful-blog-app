<?php
declare(strict_types = 1);

defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;

class User_model extends CI_Model {
  public const COMPLETE_NAME = [
    'first_name',
    'last_name'
  ];

  /**
   * List of all fields needed when logging in
   */
  public const LOCAL_LOGIN_FIELDS = [
    'username',
    'password'
  ];

  /**
   * List of all fields needed when signing up
   */
  public const LOCAL_SIGNUP_FIELDS = [
    ...self::COMPLETE_NAME,
    'username',
    'password',
    'confirm_password',
    'email'
  ];

  /**
   * List of all fields required in the database
   */
  public const LOCAL_SIGNUP_REQUIRED_DATA = [
    ...self::COMPLETE_NAME,
    'username',
    'password',
    'email'
  ];

  public const RESPONSE_USER_FIELDS = [
    'id',
    ...self::COMPLETE_NAME,
    'username',
    'email'
  ];

  public const RESPONSE_TOKEN_PAYLOAD = [
    'id',
    ...self::COMPLETE_NAME,
    'username',
    'email'
  ];

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  /**
   * Excludes field/s of data that is/are not needed.
   * The filter will be based on the required_fields param.
   */
  private function filter_user_data(array $user_data, array $required_fields): array {
    return array_filter($user_data, function($key) use($required_fields) {
      return in_array($key, $required_fields);
    }, ARRAY_FILTER_USE_KEY);
  }

  /**
   * Local user signup
   */
  public function signup_local(array $request_body): object | bool {
    $filtered_user_data = $this->filter_user_data($request_body, self::LOCAL_SIGNUP_REQUIRED_DATA);
    $filtered_user_data['password'] = password_hash($filtered_user_data['password'], PASSWORD_DEFAULT);

    if (!$this->db->insert('users', $filtered_user_data)) {
      return false;
    }

    return $this->db
      ->select(self::RESPONSE_USER_FIELDS)
      ->from('users')
      ->where(['username' => $filtered_user_data['username']])
      ->get()
      ->row();
  }

  /**
   * Local user login
   */
  public function login_local(array $request_body): object | bool {
    // find user by username
    $found_user = $this->db
      ->select([...self::RESPONSE_USER_FIELDS, 'password'])
      ->from('users')
      ->where(['username' => $request_body['username']])
      ->get()
      ->row();

    if (!$found_user) {
      return false;
    }

    // verify password
    if (!password_verify($request_body['password'], $found_user->password)) {
      return false;
    }

    unset($found_user->password);

    return $found_user;
  }

  /**
   * Returns a generated token with user data payload
   */
  public static function generate_auth_token(
    array | object $user_data,
    array $payload_fields = self::RESPONSE_TOKEN_PAYLOAD
  ): string {

    $user_data_payload = [];

    foreach ($payload_fields as $field) {
      $user_data_payload[$field] = $user_data->{$field};
    }

    $claims = [
      'iat' => time(),
      'exp' => time() + (60 * 60 * 24 * 2)
    ];

    return JWT::encode(array_merge($user_data_payload, $claims), $_ENV['JWT_SECRET_KEY']);
  }

  /**
   * Returns a decoded token if the token is valid
   */
  public static function decode_auth_token(string $token): ?object {
    try {
      return JWT::decode($token, $_ENV['JWT_SECRET_KEY'], ['HS256']);
    } catch (Exception $e) {
      throw $e;
    }
  }
}