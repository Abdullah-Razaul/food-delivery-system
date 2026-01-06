<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("delivery");

$PAGE_TITLE="Delivery Profile | SmartBite";
$PAGE_CSS="delivery_profile.css";

$u=current_user();
$uid=(int)$u["id"];
$ok="";

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $name=trim($_POST["name"]??"");
  if($name!==""){
    $stmt=$conn->prepare("UPDATE users SET name=? WHERE id=? AND role='delivery'");
    $stmt->bind_param("si",$name,$uid);
    $stmt->execute();
    $_SESSION["user"]["name"]=$name;
    $ok="Updated!";
  }
}

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Profile</h1>
    <div class="muted">Update your rider name.</div>

    <?php if($ok): ?><div class="ok"><?= htmlspecialchars($ok) ?></div><?php endif; ?>

    <form method="post" class="form">
      <label>Name</label>
      <input class="input" name="name" value="<?= htmlspecialchars($_SESSION["user"]["name"]) ?>">
      <button class="btn btn-primary">Save</button>
    </form>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
