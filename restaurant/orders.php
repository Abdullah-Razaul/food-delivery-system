<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("restaurant");

$PAGE_TITLE="Restaurant Orders | SmartBite";
$PAGE_CSS="restaurant_orders.css";

$u=current_user();
$uid=(int)$u["id"];

$r=$conn->query("SELECT id FROM restaurants WHERE owner_user_id=$uid LIMIT 1")->fetch_assoc();
$rid=(int)($r["id"]??0);

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $oid=(int)($_POST["order_id"]??0);
  $action=$_POST["action"]??"";

  if($oid>0){
    if($action==="status"){
      $status=$_POST["status"]??"pending";
      $allowed=["pending","preparing","ready","cancelled"];
      if(in_array($status,$allowed,true)){
        $stmt=$conn->prepare("UPDATE orders SET status=? WHERE id=? AND restaurant_id=?");
        $stmt->bind_param("sii",$status,$oid,$rid);
        $stmt->execute();
      }
    }

    if($action==="assign"){
      $delivery_id=(int)($_POST["delivery_user_id"]??0);
      if($delivery_id>0){
        $stmt=$conn->prepare("UPDATE orders SET delivery_user_id=? WHERE id=? AND restaurant_id=?");
        $stmt->bind_param("iii",$delivery_id,$oid,$rid);
        $stmt->execute();
      }
    }
  }

  redirect("/restaurant/orders.php");
}

$orders=$conn->query("
  SELECT o.*, u.name AS customer_name, d.name AS rider_name
  FROM orders o
  JOIN users u ON u.id=o.customer_user_id
  LEFT JOIN users d ON d.id=o.delivery_user_id
  WHERE o.restaurant_id=$rid
  ORDER BY o.id DESC
")->fetch_all(MYSQLI_ASSOC);

$deliveryUsers=$conn->query("SELECT id,name FROM users WHERE role='delivery' ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

// ✅ delivery charge fixed (config/config.php এ define করা থাকলে সেখান থেকে নিবে)
$delivery_charge = defined("DELIVERY_CHARGE") ? DELIVERY_CHARGE : 10;

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Orders</h1>
    <div class="muted">Update order status & assign delivery.</div>

    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Status</th>
          <th>Items Total</th>
          <th>Assign Rider</th>
          <th>Change Status</th>
        </tr>
      </thead>

      <tbody>
        <?php if(!$orders): ?>
          <tr><td colspan="6" class="muted">No orders yet.</td></tr>
        <?php else: foreach($orders as $o): ?>

          <?php
            // ✅ Restaurant owner শুধু item total দেখবে (delivery বাদ)
            $items_total = (float)$o["total"] - $delivery_charge;
            if($items_total < 0) $items_total = 0;
          ?>

          <tr>
            <td>#<?= (int)$o["id"] ?></td>
            <td><?= htmlspecialchars($o["customer_name"]) ?></td>
            <td><span class="badge"><?= htmlspecialchars($o["status"]) ?></span></td>

            <!-- ✅ Changed here -->
            <td>৳ <?= number_format($items_total,2) ?></td>

            <td>
              <form method="post" class="inline">
                <input type="hidden" name="action" value="assign">
                <input type="hidden" name="order_id" value="<?= (int)$o["id"] ?>">

                <select class="input select" name="delivery_user_id">
                  <option value="">Select</option>
                  <?php foreach($deliveryUsers as $d): ?>
                    <option value="<?= (int)$d["id"] ?>" <?= ((int)$o["delivery_user_id"]===(int)$d["id"])?"selected":"" ?>>
                      <?= htmlspecialchars($d["name"]) ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <button class="btn">Assign</button>
              </form>

              <div class="muted small">Current: <?= htmlspecialchars($o["rider_name"] ?? "None") ?></div>
            </td>

            <td>
              <form method="post" class="inline">
                <input type="hidden" name="action" value="status">
                <input type="hidden" name="order_id" value="<?= (int)$o["id"] ?>">

                <select class="input select" name="status">
                  <?php foreach(["pending","preparing","ready","cancelled"] as $s): ?>
                    <option value="<?= $s ?>" <?= ($o["status"]===$s)?"selected":"" ?>><?= $s ?></option>
                  <?php endforeach; ?>
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
