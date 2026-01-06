<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("restaurant");

$PAGE_TITLE="Restaurant Menu | SmartBite";
$PAGE_CSS="restaurant_menu.css";

$u=current_user();
$uid=(int)$u["id"];
$r=$conn->query("SELECT id FROM restaurants WHERE owner_user_id=$uid LIMIT 1")->fetch_assoc();
$rid=(int)($r["id"]??0);

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $action=$_POST["action"]??"";
  if($action==="add"){
    $name=trim($_POST["name"]??"");
    $price=(float)($_POST["price"]??0);
    if($name!=="" && $price>0){
      $stmt=$conn->prepare("INSERT INTO menu_items(restaurant_id,name,price) VALUES(?,?,?)");
      $stmt->bind_param("isd",$rid,$name,$price);
      $stmt->execute();
    }
  }
  if($action==="toggle"){
    $id=(int)($_POST["id"]??0);
    $stmt=$conn->prepare("UPDATE menu_items SET is_active=1-is_active WHERE id=? AND restaurant_id=?");
    $stmt->bind_param("ii",$id,$rid);
    $stmt->execute();
  }
  redirect("/restaurant/menu.php");
}

$items=$conn->query("SELECT * FROM menu_items WHERE restaurant_id=$rid ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Menu</h1>
    <div class="muted">Add items and enable/disable them.</div>

    <form method="post" class="add">
      <input type="hidden" name="action" value="add">
      <input class="input" name="name" placeholder="Item name" required>
      <input class="input" name="price" placeholder="Price" required>
      <button class="btn btn-primary">Add Item</button>
    </form>

    <table class="table" style="margin-top:14px;">
      <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Active</th><th>Action</th></tr></thead>
      <tbody>
        <?php if(!$items): ?>
          <tr><td colspan="5" class="muted">No items yet.</td></tr>
        <?php else: foreach($items as $it): ?>
          <tr>
            <td>#<?= (int)$it["id"] ?></td>
            <td><?= htmlspecialchars($it["name"]) ?></td>
            <td>৳ <?= number_format((float)$it["price"],2) ?></td>
            <td><?= (int)$it["is_active"] ? "Yes" : "No" ?></td>
            <td>
              <form method="post">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$it["id"] ?>">
                <button class="btn"><?= (int)$it["is_active"] ? "Disable" : "Enable" ?></button>
              </form>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
