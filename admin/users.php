<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("admin");

$PAGE_TITLE="Admin - Users | SmartBite";
$PAGE_CSS="admin_users.css";

$me = current_user();
$my_id = (int)($me["id"] ?? 0);

// CSRF token
if (!isset($_SESSION["csrf_token"])) {
  $_SESSION["csrf_token"] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION["csrf_token"];

$msg = "";
$err = "";

// Delete user
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "delete_user") {
  $token = $_POST["csrf_token"] ?? "";
  if (!hash_equals($csrf, $token)) {
    $err = "Invalid request (CSRF).";
  } else {
    $uid = (int)($_POST["user_id"] ?? 0);
    if ($uid <= 0) {
      $err = "Invalid user id.";
    } elseif ($uid === $my_id) {
      $err = "You cannot delete your own admin account.";
    } else {
      // find role first
      $u = $conn->query("SELECT id, role, email FROM users WHERE id=$uid LIMIT 1")->fetch_assoc();
      if (!$u) {
        $err = "User not found.";
      } else {
        $role = $u["role"];

        // Start transaction for safe delete
        $conn->begin_transaction();
        try {
          // If user is a customer: delete orders + items
          if ($role === "customer") {
            // delete order_items for customer's orders
            $conn->query("
              DELETE oi FROM order_items oi
              JOIN orders o ON o.id=oi.order_id
              WHERE o.customer_user_id=$uid
            ");
            // delete orders
            $conn->query("DELETE FROM orders WHERE customer_user_id=$uid");
          }

          // If user is delivery: unassign rider from orders (keep orders)
          if ($role === "delivery") {
            $conn->query("UPDATE orders SET delivery_user_id=NULL WHERE delivery_user_id=$uid");
          }

          // If user is restaurant owner: delete restaurant + menu + related orders
          if ($role === "restaurant") {
            // find restaurant(s)
            $rs = $conn->query("SELECT id FROM restaurants WHERE owner_user_id=$uid")->fetch_all(MYSQLI_ASSOC);

            foreach ($rs as $rr) {
              $rid = (int)$rr["id"];

              // delete order_items for orders of this restaurant
              $conn->query("
                DELETE oi FROM order_items oi
                JOIN orders o ON o.id=oi.order_id
                WHERE o.restaurant_id=$rid
              ");

              // unassign any rider from those orders (optional)
              $conn->query("UPDATE orders SET delivery_user_id=NULL WHERE restaurant_id=$rid");

              // delete orders
              $conn->query("DELETE FROM orders WHERE restaurant_id=$rid");

              // delete menu items
              $conn->query("DELETE FROM menu_items WHERE restaurant_id=$rid");
            }

            // delete restaurants
            $conn->query("DELETE FROM restaurants WHERE owner_user_id=$uid");
          }

          // Finally delete user
          $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
          $stmt->bind_param("i", $uid);
          $stmt->execute();

          $conn->commit();
          $msg = "User deleted successfully.";
        } catch (Throwable $e) {
          $conn->rollback();
          $err = "Delete failed. " . $e->getMessage();
        }
      }
    }
  }
}

// Filters (optional)
$q = trim($_GET["q"] ?? "");
$where = "";
if ($q !== "") {
  $safe = $conn->real_escape_string($q);
  $where = "WHERE name LIKE '%$safe%' OR email LIKE '%$safe%' OR role LIKE '%$safe%'";
}

$users = $conn->query("SELECT id, role, name, email FROM users $where ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box" style="margin-top:18px;">
    <h1 class="h1">Users</h1>
    <div class="muted">Admin can view & delete any user.</div>

    <?php if($msg): ?><div class="muted" style="margin-top:10px;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if($err): ?><div class="muted" style="margin-top:10px;color:#c81d2c;"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form method="get" style="margin-top:14px;display:flex;gap:10px;align-items:center;">
      <input class="input" name="q" placeholder="Search by name, email, role..." value="<?= htmlspecialchars($q) ?>" style="max-width:380px;">
      <button class="btn">Search</button>
      <a class="btn" href="<?= BASE_URL ?>/admin/users.php">Reset</a>
    </form>

    <table class="table" style="margin-top:14px;">
      <thead>
        <tr>
          <th>ID</th>
          <th>Role</th>
          <th>Name</th>
          <th>Email</th>
          <th style="width:160px;">Action</th>
        </tr>
      </thead>
      <tbody>
      <?php if(!$users): ?>
        <tr><td colspan="5" class="muted">No users found.</td></tr>
      <?php else: foreach($users as $u): ?>
        <tr>
          <td>#<?= (int)$u["id"] ?></td>
          <td><span class="badge"><?= htmlspecialchars($u["role"]) ?></span></td>
          <td><?= htmlspecialchars($u["name"]) ?></td>
          <td><?= htmlspecialchars($u["email"]) ?></td>
          <td>
            <?php if((int)$u["id"] === $my_id): ?>
              <span class="muted small">This is you</span>
            <?php else: ?>
              <form method="post" onsubmit="return confirm('Delete this user? This will remove related data too.');" style="display:inline;">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="user_id" value="<?= (int)$u["id"] ?>">
                <button class="btn btn-danger" type="submit">Delete</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
