<?php
require_once __DIR__."/../config/db.php";
$PAGE_TITLE="Cart | SmartBite";
$PAGE_CSS="cart.css";

if(!isset($_SESSION["cart"])) $_SESSION["cart"] = [];

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $action = $_POST["action"] ?? "";
  if($action === "update"){
    foreach($_SESSION["cart"] as $k=>$v){
      $qty = max(0, (int)($_POST["qty"][$k] ?? $v["qty"]));
      if($qty<=0) unset($_SESSION["cart"][$k]);
      else $_SESSION["cart"][$k]["qty"] = $qty;
    }
  }
  if($action === "clear"){
    $_SESSION["cart"] = [];
    unset($_SESSION["cart_restaurant_id"]);
  }
  redirect("/public/cart.php");
}

$cart = $_SESSION["cart"];
$total = 0;
foreach($cart as $c){ $total += $c["price"] * $c["qty"]; }

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="wrap">
    <div class="card box">
      <h1 class="h1">Cart</h1>
      <div class="muted">Review items before checkout.</div>

      <form method="post">
        <input type="hidden" name="action" value="update">
        <table class="table">
          <thead><tr><th>Item</th><th>Price</th><th style="width:140px;">Qty</th><th>Sub</th></tr></thead>
          <tbody>
            <?php if(!$cart): ?>
              <tr><td colspan="4" class="muted">Cart is empty.</td></tr>
            <?php else: foreach($cart as $k=>$c): ?>
              <tr>
                <td><?= htmlspecialchars($c["name"]) ?></td>
                <td>৳ <?= number_format((float)$c["price"],2) ?></td>
                <td><input class="input qty" name="qty[<?= htmlspecialchars($k) ?>]" value="<?= (int)$c["qty"] ?>"></td>
                <td>৳ <?= number_format($c["price"]*$c["qty"],2) ?></td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>

        <div class="actions">
          <button class="btn" type="submit">Update</button>
          <button class="btn btn-danger" type="submit" name="action" value="clear">Clear</button>
        </div>
      </form>
    </div>

    <div class="card summary">
      <h2 class="h2">Summary</h2>
      <div class="row"><span class="muted">Total</span><strong>৳ <?= number_format($total,2) ?></strong></div>
      <a class="btn btn-primary wide <?= $cart ? "" : "disabled" ?>" href="<?= $cart ? BASE_URL."/public/checkout.php" : "#" ?>">Checkout</a>
      <a class="btn wide" href="<?= BASE_URL ?>/public/browse.php">Continue Browsing</a>
    </div>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
