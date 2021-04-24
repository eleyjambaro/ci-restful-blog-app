<?php
declare(strict_types = 1);

defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
  /**
   * List of all fields needed when signing up
   */
  public const SIGNUP_FIELDS = [
    'first_name',
    'last_name',
    'username',
    'password',
    'confirm_password',
    'email'
  ];

  /**
   * List of all fields required in the database
   */
  public const REQUIRED_USER_DATA = [
    'first_name',
    'last_name',
    'username',
    'password',
    'email'
  ];

  public function __construct() {
    parent::__construct();
    $this->load->database();
  }

  /**
   * Excludes field/s of user data that is/are not needed in the database.
   * The filter will be based on this REQUIRED_USER_DATA constant property
   */
  private static function filter_user_data(array $user_data): array {
    return array_filter($user_data, function($key) {
      return in_array($key, self::REQUIRED_USER_DATA);
    }, ARRAY_FILTER_USE_KEY);
  }

  /**
   * Returns a filtered user data with a hashed password
   */
  public static function create(array $user_data): array {
    $filtered_user_data = static::filter_user_data($user_data);
    $filtered_user_data['password'] = password_hash($filtered_user_data['password'], PASSWORD_DEFAULT);

    return $filtered_user_data;
  }
}