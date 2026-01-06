<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
$PAGE_TITLE="My Orders | SmartBite";
$PAGE_CSS="orders.css";

require_login();
$u=current_user();
$uid=(int)$u["id"];

$q=$conn->query("
  SELECT o.*, r.name AS rname
  FROM orders o
  JOIN restaurants r ON r.id=o.restaurant_id
  WHERE o.customer_user_id=$uid
  ORDER BY o.id DESC
");
$orders = $q ? $q->fetch_all(MYSQLI_ASSOC) : [];

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">My Orders</h1>
    <div class="muted">Track your order statuses.</div>

    <table class="table">
      <thead><tr><th>ID</th><th>Restaurant</th><th>Status</th><th>Total</th><th>Date</th></tr></thead>
      <tbody>
        <?php if(!$orders): ?>
          <tr><td colspan="5" class="muted">No orders yet.</td></tr>
        <?php else: foreach($orders as $o): ?>
          <tr>
            <td>#<?= (int)$o["id"] ?></td>
            <td><?= htmlspecialchars($o["rname"]) ?></td>
            <td><span class="badge"><?= htmlspecialchars($o["status"]) ?></span></td>
            <td>৳ <?= number_format((float)$o["total"],2) ?></td>
            <td class="muted small"><?= htmlspecialchars($o["created_at"]) ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
