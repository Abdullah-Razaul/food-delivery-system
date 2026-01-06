<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("admin");

$PAGE_TITLE="Admin Dashboard | SmartBite";
$PAGE_CSS="admin_dashboard.css";

$users=$conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()["c"]??0;
$restaurants=$conn->query("SELECT COUNT(*) c FROM restaurants")->fetch_assoc()["c"]??0;
$orders=$conn->query("SELECT COUNT(*) c FROM orders")->fetch_assoc()["c"]??0;

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Admin Dashboard</h1>
    <div class="muted">Manage users, restaurants, and orders.</div>

    <div class="grid3 stats">
      <div class="card stat"><div class="muted">Users</div><div class="num"><?= (int)$users ?></div></div>
      <div class="card stat"><div class="muted">Restaurants</div><div class="num"><?= (int)$restaurants ?></div></div>
      <div class="card stat"><div class="muted">Orders</div><div class="num"><?= (int)$orders ?></div></div>
    </div>

    <div class="links">
      <a class="btn btn-primary" href="<?= BASE_URL ?>/admin/users.php">Users</a>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/admin/restaurants.php">Restaurants</a>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/admin/orders.php">Orders</a>
    </div>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
