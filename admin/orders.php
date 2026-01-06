<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("admin");

$PAGE_TITLE="Admin Orders | SmartBite";
$PAGE_CSS="admin_orders.css";

$rows=$conn->query("
  SELECT o.*, r.name AS rname, cu.name AS cname, d.name AS rider
  FROM orders o
  JOIN restaurants r ON r.id=o.restaurant_id
  JOIN users cu ON cu.id=o.customer_user_id
  LEFT JOIN users d ON d.id=o.delivery_user_id
  ORDER BY o.id DESC
")->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Orders</h1>
    <div class="muted">All orders overview.</div>

    <table class="table">
      <thead><tr><th>ID</th><th>Restaurant</th><th>Customer</th><th>Rider</th><th>Status</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach($rows as $o): ?>
          <tr>
            <td>#<?= (int)$o["id"] ?></td>
            <td><?= htmlspecialchars($o["rname"]) ?></td>
            <td><?= htmlspecialchars($o["cname"]) ?></td>
            <td class="muted small"><?= htmlspecialchars($o["rider"] ?? "—") ?></td>
            <td><span class="badge"><?= htmlspecialchars($o["status"]) ?></span></td>
            <td>৳ <?= number_format((float)$o["total"],2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
