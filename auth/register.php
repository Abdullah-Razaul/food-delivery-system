<?php
require_once __DIR__."/../config/db.php";
$PAGE_TITLE="Register | SmartBite";
$PAGE_CSS="register.css";

$error=""; $ok="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $name=trim($_POST["name"]??"");
  $email=trim($_POST["email"]??"");
  $role=$_POST["role"]??"customer";
  $pass=$_POST["password"]??"";

  if(!in_array($role,["customer","restaurant","delivery"],true)) $role="customer";
  if(strlen($pass)<6) $error="Password must be at least 6 characters.";

  if(!$error){
    $hash=password_hash($pass,PASSWORD_DEFAULT);
    $stmt=$conn->prepare("INSERT INTO users(role,name,email,password_hash) VALUES(?,?,?,?)");
    $stmt->bind_param("ssss",$role,$name,$email,$hash);
    if($stmt->execute()){
      $uid=$stmt->insert_id;

      if($role==="restaurant"){
        $stmt2=$conn->prepare("INSERT INTO restaurants(owner_user_id,name,category,address) VALUES(?,?,?,?)");
        $rname=$name."'s Restaurant";
        $cat="Food"; $addr="";
        $stmt2->bind_param("isss",$uid,$rname,$cat,$addr);
        $stmt2->execute();
      }

      $ok="Registration successful. You can login now.";
    } else {
      $error="Email already exists or invalid data.";
    }
  }
}

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container auth-wrap">
  <div class="card auth">
    <h1>Registration</h1>
    <p class="muted">Create your account.</p>

    <?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if($ok): ?><div class="ok"><?= htmlspecialchars($ok) ?></div><?php endif; ?>

    <form method="post" class="form">
      <label>Name</label>
      <input class="input" name="name" required>

      <label>Email</label>
      <input class="input" name="email" type="email" required>

      <label>Account Type</label>
      <select class="input" name="role">
        <option value="customer">Customer</option>
        <option value="restaurant">Restaurant Owner</option>
        <option value="delivery">Delivery Rider</option>
      </select>

      <label>Password</label>
      <input class="input" name="password" type="password" required>

      <button class="btn btn-primary" type="submit">Create Account</button>
    </form>

    <div class="muted small">Already have an account? <a class="link" href="<?= BASE_URL ?>/auth/login.php">Login</a></div>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
