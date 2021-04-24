<?php
declare(strict_types = 1);

defined('BASEPATH') OR exit('No direct script access allowed');

$config = [
  'api/accounts/signup_post' => [
    [
      'field' => 'first_name',
      'label' => 'First name',
      'rules' => 'required|max_length[50]'
    ],
    [
      'field' => 'last_name',
      'label' => 'Last name',
      'rules' => 'required|max_length[50]'
    ],
    [
      'field' => 'username',
      'label' => 'Username',
      'rules' => 'required|is_unique[users.username]|alpha_dash|min_length[4]|max_length[50]',
      'errors' => [
        'is_unique' => 'Username already exists.'
      ]
    ],
    [
      'field' => 'password',
      'label' => 'Password',
      'rules' => 'required|min_length[8]|max_length[255]'
    ],
    [
      'field' => 'confirm_password',
      'label' => 'Confirmation password',
      'rules' => 'required|matches[password]'
    ],
    [
      'field' => 'email',
      'label' => 'Email',
      'rules' => 'required|valid_email|is_unique[users.email]',
      'errors' => [
        'is_unique' => 'Email already exists.'
      ]
    ]
  ],
  'api/accounts/login_post' => [
    [
      'field' => 'username',
      'label' => 'Username',
      'rules' => 'required'
    ],
    [
      'field' => 'password',
      'label' => 'Password',
      'rules' => 'required'
    ]
  ]
];