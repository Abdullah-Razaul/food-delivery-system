<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("delivery");

$PAGE_TITLE="Delivery Dashboard | SmartBite";
$PAGE_CSS="delivery_dashboard.css";

$u=current_user();
$uid=(int)$u["id"];

$delivered=$conn->query("SELECT COUNT(*) c FROM orders WHERE delivery_user_id=$uid AND status='delivered'")->fetch_assoc()["c"]??0;
$active=$conn->query("SELECT COUNT(*) c FROM orders WHERE delivery_user_id=$uid AND status IN('picked','ready')")->fetch_assoc()["c"]??0;
$earnings = $delivered * 30; // demo calc

$incoming=$conn->query("
  SELECT o.*, r.name AS rname, cu.name AS cname
  FROM orders o
  JOIN restaurants r ON r.id=o.restaurant_id
  JOIN users cu ON cu.id=o.customer_user_id
  WHERE o.delivery_user_id=$uid AND o.status IN('ready','picked')
  ORDER BY o.id DESC LIMIT 10
")->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="dash">
  <aside class="sidebar">
    <div class="side-head">
      <div class="avatar">🍔</div>
      <div>
        <div class="side-title">SmartBite</div>
        <div class="muted small">Delivery Rider</div>
      </div>
    </div>

    <nav class="side-nav">
      <a class="active" href="<?= BASE_URL ?>/delivery/dashboard.php">Dashboard</a>
      <a href="<?= BASE_URL ?>/delivery/orders.php">Orders</a>
      <a href="<?= BASE_URL ?>/delivery/stats.php">Delivered Stats</a>
      <a href="<?= BASE_URL ?>/delivery/profile.php">Profile</a>
      <a href="<?= BASE_URL ?>/auth/logout.php">Logout</a>
    </nav>
  </aside>

  <section class="content">
    <div class="top-row">
      <div>
        <div class="h2">Welcome, <?= htmlspecialchars($u["name"]) ?></div>
        <div class="muted small">Last seen: just now</div>
      </div>
      <input class="input search" placeholder="Search orders...">
    </div>

    <div class="stats">
      <div class="stat card"><div class="label">Delivered</div><div class="value"><?= (int)$delivered ?></div></div>
      <div class="stat card"><div class="label">Active Orders</div><div class="value"><?= (int)$active ?></div></div>
      <div class="stat card"><div class="label">Earnings Today</div><div class="value">৳ <?= (int)$earnings ?></div></div>
    </div>

    <div class="card section">
      <div class="h2">Incoming Orders</div>
      <?php if(!$incoming): ?>
        <div class="muted">No assigned orders yet.</div>
      <?php else: foreach($incoming as $o): ?>
        <div class="order">
          <div>
            <div class="o-title">Order #<?= (int)$o["id"] ?> — <?= htmlspecialchars($o["cname"]) ?></div>
            <div class="muted small"><?= htmlspecialchars($o["rname"]) ?> • <?= htmlspecialchars($o["address"]) ?></div>
          </div>
          <div class="o-right">
            <span class="tag <?= $o["status"]==="ready" ? "pending" : "picked" ?>">
              <?= $o["status"]==="ready" ? "Ready" : "Picked" ?>
            </span>
            <a class="btn btn-primary" href="<?= BASE_URL ?>/delivery/orders.php">Update</a>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </section>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
