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
        <div class="muted">Email: support@smartbite.test</div>
        <div class="muted">Phone: +880 1XXXXXXXXX</div>
      </div>
      <div class="card inner">
        <div class="h2">Office</div>
        <div class="muted">Dhaka, Bangladesh</div>
        <div class="muted small">This is a demo project UI.</div>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
