<?php
require_once __DIR__."/../config/db.php";
$PAGE_TITLE="Restaurant | SmartBite";
$PAGE_CSS="restaurant_view.css";

$rid = (int)($_GET["id"] ?? 0);
$r = $conn->query("SELECT * FROM restaurants WHERE id=$rid")->fetch_assoc();
if(!$r){ redirect("/public/browse.php"); }

if(!isset($_SESSION["cart"])) $_SESSION["cart"] = [];
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $item_id = (int)($_POST["item_id"] ?? 0);
  $qty = max(1, (int)($_POST["qty"] ?? 1));

  $it = $conn->query("SELECT id,name,price,restaurant_id FROM menu_items WHERE id=$item_id AND restaurant_id=$rid AND is_active=1")->fetch_assoc();
  if($it){
    // force single restaurant cart
    if(isset($_SESSION["cart_restaurant_id"]) && (int)$_SESSION["cart_restaurant_id"] !== $rid){
      $_SESSION["cart"] = [];
    }
    $_SESSION["cart_restaurant_id"] = $rid;

    $key = (string)$it["id"];
    if(!isset($_SESSION["cart"][$key])){
      $_SESSION["cart"][$key] = ["id"=>$it["id"],"name"=>$it["name"],"price"=>(float)$it["price"],"qty"=>0];
    }
    $_SESSION["cart"][$key]["qty"] += $qty;
    redirect("/public/cart.php");
  }
}

$items = $conn->query("SELECT * FROM menu_items WHERE restaurant_id=$rid AND is_active=1 ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <section class="top card">
    <div class="hero">
      <div class="pic">🍽️</div>
      <div>
        <h1 class="h1"><?= htmlspecialchars($r["name"]) ?></h1>
        <div class="muted"><?= htmlspecialchars($r["category"]) ?> • <?= htmlspecialchars($r["address"]) ?></div>
        <div class="rate">⭐ <?= htmlspecialchars($r["rating"]) ?> (<?= (int)$r["ratings_count"] ?>+)</div>
      </div>
    </div>
    <div class="promo"><?= htmlspecialchars($r["promo_text"]) ?></div>
  </section>

  <section class="card section">
    <h2 class="h2">Menu</h2>
    <div class="menu">
      <?php foreach($items as $it): ?>
        <div class="item card">
          <div class="item-name"><?= htmlspecialchars($it["name"]) ?></div>
          <div class="muted small">Tasty & fresh</div>

          <div class="price">৳ <?= number_format((float)$it["price"],2) ?></div>

          <!-- ✅ NEW: Delivery charge display -->
          <div class="muted small">Delivery charge: ৳ <?= defined("DELIVERY_CHARGE") ? DELIVERY_CHARGE : 10 ?></div>

          <form method="post" class="add">
            <input type="hidden" name="item_id" value="<?= (int)$it["id"] ?>">
            <input class="input qty" name="qty" value="1">
            <button class="btn btn-primary">Add</button>
          </form>
        </div>
      <?php endforeach; ?>
      <?php if(!$items): ?>
        <div class="muted">No menu items yet.</div>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
