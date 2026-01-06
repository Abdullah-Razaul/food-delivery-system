<?php
require_once __DIR__ . "/config.php";

function current_user(){
  return $_SESSION["user"] ?? null;
}

function require_login(){
  if(!current_user()) redirect("/auth/login.php");
}

function require_role($role){
  require_login();
  $u = current_user();
  if(($u["role"] ?? "") !== $role){
    redirect("/public/index.php");
  }
}
