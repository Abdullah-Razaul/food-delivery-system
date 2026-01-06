<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("delivery");

$PAGE_TITLE="Delivery Orders | SmartBite";
$PAGE_CSS="delivery_orders.css";

$u=current_user();
$uid=(int)$u["id"];

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $oid=(int)($_POST["order_id"]??0);
  $status=$_POST["status"]??"";
  $allowed=["picked","delivered"];
  if($oid>0 && in_array($status,$allowed,true)){
    $stmt=$conn->prepare("UPDATE orders SET status=? WHERE id=? AND delivery_user_id=?");
    $stmt->bind_param("sii",$status,$oid,$uid);
    $stmt->execute();
  }
  redirect("/delivery/orders.php");
}

$orders=$conn->query("
  SELECT o.*, r.name AS rname, cu.name AS cname
  FROM orders o
  JOIN restaurants r ON r.id=o.restaurant_id
  JOIN users cu ON cu.id=o.customer_user_id
  WHERE o.delivery_user_id=$uid
  ORDER BY o.id DESC
")->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Orders</h1>
    <div class="muted">Update order delivery status.</div>

    <table class="table">
      <thead><tr><th>ID</th><th>Restaurant</th><th>Customer</th><th>Status</th><th>Address</th><th>Action</th></tr></thead>
      <tbody>
        <?php if(!$orders): ?>
          <tr><td colspan="6" class="muted">No assigned orders.</td></tr>
        <?php else: foreach($orders as $o): ?>
          <tr>
            <td>#<?= (int)$o["id"] ?></td>
            <td><?= htmlspecialchars($o["rname"]) ?></td>
            <td><?= htmlspecialchars($o["cname"]) ?></td>
            <td><span class="badge"><?= htmlspecialchars($o["status"]) ?></span></td>
            <td class="muted small"><?= htmlspecialchars($o["address"]) ?></td>
            <td>
              <form method="post" class="inline">
                <input type="hidden" name="order_id" value="<?= (int)$o["id"] ?>">
                <select class="input select" name="status">
                  <option value="picked">Picked</option>
                  <option value="delivered">Delivered</option>
                </select>
                <button class="btn btn-primary">Update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
