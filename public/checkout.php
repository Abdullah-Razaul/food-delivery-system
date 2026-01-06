<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
$PAGE_TITLE="Checkout | SmartBite";
$PAGE_CSS="checkout.css";

require_login();

if(!isset($_SESSION["cart"])) $_SESSION["cart"] = [];
$cart = $_SESSION["cart"];
if(!$cart) redirect("/public/cart.php");

$rid = (int)($_SESSION["cart_restaurant_id"] ?? 0);
if($rid<=0) redirect("/public/cart.php");

$u = current_user();
$uid = (int)$u["id"];

$error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $address = trim($_POST["address"] ?? "");
  if(strlen($address) < 6) $error="Please enter a valid delivery address.";
  if(!$error){
    $total=0;
    foreach($cart as $c) $total += $c["price"]*$c["qty"];

    $stmt=$conn->prepare("INSERT INTO orders(customer_user_id,restaurant_id,status,total,address) VALUES(?,?, 'pending', ?, ?)");
    $stmt->bind_param("iids",$uid,$rid,$total,$address);
    if($stmt->execute()){
      $oid = $stmt->insert_id;

      $stmt2=$conn->prepare("INSERT INTO order_items(order_id,item_name,qty,price) VALUES(?,?,?,?)");
      foreach($cart as $c){
        $name=$c["name"]; $qty=(int)$c["qty"]; $price=(float)$c["price"];
        $stmt2->bind_param("isid",$oid,$name,$qty,$price);
        $stmt2->execute();
      }

      $_SESSION["cart"] = [];
      unset($_SESSION["cart_restaurant_id"]);
      redirect("/public/orders.php");
    } else {
      $error="Could not create order.";
    }
  }
}

$total=0;
foreach($cart as $c) $total += $c["price"]*$c["qty"];

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="wrap">
    <div class="card box">
      <h1 class="h1">Checkout</h1>
      <div class="muted">Confirm address and place order.</div>

      <?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

      <form method="post" class="form">
        <label>Delivery Address</label>
        <input class="input" name="address" placeholder="House, Road, Area..." required>
        <button class="btn btn-primary" type="submit">Place Order</button>
      </form>
    </div>

    <div class="card summary">
      <h2 class="h2">Order Summary</h2>
      <div class="list">
        <?php foreach($cart as $c): ?>
          <div class="li">
            <div><strong><?= htmlspecialchars($c["name"]) ?></strong> <span class="muted small">x<?= (int)$c["qty"] ?></span></div>
            <div>৳ <?= number_format($c["price"]*$c["qty"],2) ?></div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="tot">
        <span class="muted">Total</span>
        <strong>৳ <?= number_format($total,2) ?></strong>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
