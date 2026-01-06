<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("delivery");

$PAGE_TITLE="Delivery Stats | SmartBite";
$PAGE_CSS="delivery_stats.css";

$u=current_user();
$uid=(int)$u["id"];

$rows=$conn->query("
  SELECT DATE(created_at) day, COUNT(*) c
  FROM orders
  WHERE delivery_user_id=$uid AND status='delivered'
  GROUP BY DATE(created_at)
  ORDER BY day DESC
  LIMIT 14
")->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Delivered Stats</h1>
    <div class="muted">Last 14 days delivered orders.</div>

    <table class="table" style="margin-top:14px;">
      <thead><tr><th>Date</th><th>Delivered</th><th>Estimated Earnings</th></tr></thead>
      <tbody>
        <?php if(!$rows): ?>
          <tr><td colspan="3" class="muted">No data yet.</td></tr>
        <?php else: foreach($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r["day"]) ?></td>
            <td><?= (int)$r["c"] ?></td>
            <td>৳ <?= (int)$r["c"] * 30 ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
