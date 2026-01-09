<?php
if (session_status() === PHP_SESSION_NONE) session_start();

define("BASE_URL", "/smartbite"); // <-- change if your folder name differs
define("DB_HOST", "localhost:3307");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "smartbite");
define("DELIVERY_CHARGE", 10);

function redirect($path){
  header("Location: " . BASE_URL . $path);
  exit;
}
