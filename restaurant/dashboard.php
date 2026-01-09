<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("restaurant");

$PAGE_TITLE="Restaurant Dashboard | SmartBite";
$PAGE_CSS="restaurant_dashboard.css";

$u=current_user();
$uid=(int)$u["id"];

$r=$conn->query("SELECT id,name FROM restaurants WHERE owner_user_id=$uid LIMIT 1")->fetch_assoc();
$rid=(int)($r["id"]??0);

// KPIs
$today=$conn->query("SELECT COUNT(*) c FROM orders WHERE restaurant_id=$rid AND DATE(created_at)=CURDATE()")->fetch_assoc()["c"]??0;
$pending=$conn->query("SELECT COUNT(*) c FROM orders WHERE restaurant_id=$rid AND status='pending'")->fetch_assoc()["c"]??0;
$preparing=$conn->query("SELECT COUNT(*) c FROM orders WHERE restaurant_id=$rid AND status='preparing'")->fetch_assoc()["c"]??0;
$ready=$conn->query("SELECT COUNT(*) c FROM orders WHERE restaurant_id=$rid AND status='ready'")->fetch_assoc()["c"]??0;

// delivery charge (fixed)
$delivery_charge = defined("DELIVERY_CHARGE") ? DELIVERY_CHARGE : 10;

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>

<main class="dash">
  <aside class="sidebar">
    <div class="title">My Restaurant</div>
    <nav class="side-nav">
      <a class="active" href="<?= BASE_URL ?>/restaurant/dashboard.php">Dashboard</a>
      <a href="<?= BASE_URL ?>/restaurant/orders.php">Orders</a>
      <a href="<?= BASE_URL ?>/restaurant/menu.php">Menu</a>
      <a href="<?= BASE_URL ?>/restaurant/profile.php">Profile</a>
    </nav>
  </aside>

  <section class="main">
    <h1>Dashboard</h1>
    <div class="muted">Welcome, <?= htmlspecialchars($u["name"]) ?></div>

    <div class="kpi">
      <div class="card kpi-card">
        <div class="kpi-title">Today's Orders</div>
        <div class="kpi-num"><?= (int)$today ?></div>
      </div>
      <div class="card kpi-card">
        <div class="kpi-title">Pending</div>
        <div class="kpi-num"><?= (int)$pending ?></div>
      </div>
      <div class="card kpi-card">
        <div class="kpi-title">Preparing</div>
        <div class="kpi-num"><?= (int)$preparing ?></div>
      </div>
      <div class="card kpi-card">
        <div class="kpi-title">Ready</div>
        <div class="kpi-num"><?= (int)$ready ?></div>
      </div>
    </div>

    <!-- Live Orders -->
    <div class="card section">
      <div class="h2">Live Orders</div>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Status</th>
            <th>Items Total</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $q=$conn->query("
            SELECT id,status,total
            FROM orders
            WHERE restaurant_id=$rid
              AND status IN('pending','preparing','ready')
            ORDER BY id DESC
            LIMIT 10
          ");
          $rows=$q?$q->fetch_all(MYSQLI_ASSOC):[];
          ?>

          <?php if(!$rows): ?>
            <tr><td colspan="4" class="muted">No live orders</td></tr>
          <?php else: foreach($rows as $o): ?>

            <?php
              // ✅ delivery বাদ দিয়ে item total
              $items_total = (float)$o["total"] - $delivery_charge;
              if($items_total < 0) $items_total = 0;
            ?>

            <tr>
              <td>#<?= (int)$o["id"] ?></td>
              <td><span class="badge"><?= htmlspecialchars($o["status"]) ?></span></td>
              <td>৳ <?= number_format($items_total,2) ?></td>
              <td>
                <a class="btn" href="<?= BASE_URL ?>/restaurant/orders.php">Manage</a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Menu Manager -->
    <div class="card section">
      <div class="h2">Menu Manager</div>
      <div class="muted small">Add or update items from the Menu page.</div>
      <a class="btn btn-primary" href="<?= BASE_URL ?>/restaurant/menu.php">Open Menu</a>
    </div>
  </section>
</main>

<?php include __DIR__."/../partials/footer.php"; ?>
