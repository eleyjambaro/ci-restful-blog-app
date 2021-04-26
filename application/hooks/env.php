<?php
declare(strict_types = 1);

/**
 * loads environment variables declared in .env file of the root folder (FCPATH).
 * This function should be called in the pre_controller hook (application/config/hooks.php)
 */
function load_env() {
  $dotenv = Dotenv\Dotenv::createImmutable(FCPATH);
  $dotenv->load();
}