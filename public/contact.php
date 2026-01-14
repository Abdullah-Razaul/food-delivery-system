<?php
require_once __DIR__."/../config/config.php";
$PAGE_TITLE="Contact | SmartBite";
$PAGE_CSS="contact.css";
include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Contact Us</h1>
    <div class="muted">We’d love to hear from you.</div>

    <div class="grid2" style="margin-top:14px;">
      <div class="card inner">
        <div class="h2">Support</div>
        <div class="muted">Email: abdullahrezaul8998@gmail.com</div>
        <div class="muted">Phone: 01639041372</div>
      </div>
      <div class="card inner">
        <div class="h2">Office</div>
        <div class="muted">Cumilla, Bangladesh</div>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
