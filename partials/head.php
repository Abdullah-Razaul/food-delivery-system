<?php
require_once __DIR__."/../config/config.php";
if(!isset($PAGE_TITLE)) $PAGE_TITLE = "SmartBite";
if(!isset($PAGE_CSS)) $PAGE_CSS = "base.css";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?= htmlspecialchars($PAGE_TITLE) ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/base.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/<?= htmlspecialchars($PAGE_CSS) ?>">
</head>
<body>
