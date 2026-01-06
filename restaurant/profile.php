<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("restaurant");

$PAGE_TITLE="Restaurant Profile | SmartBite";
$PAGE_CSS="restaurant_profile.css";

$u=current_user();
$uid=(int)$u["id"];

$r=$conn->query("SELECT * FROM restaurants WHERE owner_user_id=$uid LIMIT 1")->fetch_assoc();
if(!$r){ redirect("/restaurant/dashboard.php"); }

$ok="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $name=trim($_POST["name"]??"");
  $cat=trim($_POST["category"]??"Food");
  $addr=trim($_POST["address"]??"");
  $promo=trim($_POST["promo_text"]??"");

  $stmt=$conn->prepare("UPDATE restaurants SET name=?, category=?, address=?, promo_text=? WHERE id=? AND owner_user_id=?");
  $rid=(int)$r["id"];
  $stmt->bind_param("ssssii",$name,$cat,$addr,$promo,$rid,$uid);
  $stmt->execute();
  $ok="Updated successfully!";
  $r=$conn->query("SELECT * FROM restaurants WHERE owner_user_id=$uid LIMIT 1")->fetch_assoc();
}

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Profile</h1>
    <div class="muted">Update your restaurant details.</div>

    <?php if($ok): ?><div class="ok"><?= htmlspecialchars($ok) ?></div><?php endif; ?>

    <form method="post" class="form">
      <label>Restaurant Name</label>
      <input class="input" name="name" value="<?= htmlspecialchars($r["name"]) ?>" required>

      <label>Category</label>
      <input class="input" name="category" value="<?= htmlspecialchars($r["category"]) ?>">

      <label>Address</label>
      <input class="input" name="address" value="<?= htmlspecialchars($r["address"]) ?>">

      <label>Promo Text</label>
      <input class="input" name="promo_text" value="<?= htmlspecialchars($r["promo_text"]) ?>">

      <button class="btn btn-primary">Save</button>
    </form>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
