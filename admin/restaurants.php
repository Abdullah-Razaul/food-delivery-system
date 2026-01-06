<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("admin");

$PAGE_TITLE="Admin Restaurants | SmartBite";
$PAGE_CSS="admin_restaurants.css";

$rows=$conn->query("
  SELECT r.*, u.name AS owner_name
  FROM restaurants r
  JOIN users u ON u.id=r.owner_user_id
  ORDER BY r.id DESC
")->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Restaurants</h1>
    <div class="muted">View restaurants.</div>

    <table class="table">
      <thead><tr><th>ID</th><th>Name</th><th>Owner</th><th>Category</th><th>Rating</th></tr></thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td>#<?= (int)$r["id"] ?></td>
            <td><?= htmlspecialchars($r["name"]) ?></td>
            <td><?= htmlspecialchars($r["owner_name"]) ?></td>
            <td class="muted small"><?= htmlspecialchars($r["category"]) ?></td>
            <td>⭐ <?= htmlspecialchars($r["rating"]) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
