<?php
require_once __DIR__."/../config/db.php";
$PAGE_TITLE="Login | SmartBite";
$PAGE_CSS="login.css";

$error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $email=trim($_POST["email"]??"");
  $pass=$_POST["password"]??"";

  $stmt=$conn->prepare("SELECT id,role,name,email,password_hash FROM users WHERE email=? LIMIT 1");
  $stmt->bind_param("s",$email);
  $stmt->execute();
  $u=$stmt->get_result()->fetch_assoc();

  if($u && password_verify($pass,$u["password_hash"])){
    $_SESSION["user"]=["id"=>(int)$u["id"],"role"=>$u["role"],"name"=>$u["name"],"email"=>$u["email"]];

    if($u["role"]==="admin") redirect("/admin/dashboard.php");
    if($u["role"]==="restaurant") redirect("/restaurant/dashboard.php");
    if($u["role"]==="delivery") redirect("/delivery/dashboard.php");
    redirect("/public/index.php");
  } else {
    $error="Invalid email or password.";
  }
}

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container auth-wrap">
  <div class="card auth">
    <h1>Login</h1>
    <p class="muted">Access your account.</p>

    <?php if($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post" class="form">
      <label>Email</label>
      <input class="input" name="email" type="email" required>
      <label>Password</label>
      <input class="input" name="password" type="password" required>
      <button class="btn btn-primary" type="submit">Login</button>
    </form>

    <div class="muted small">No account? <a class="link" href="<?= BASE_URL ?>/auth/register.php">Register</a></div>
    <div class="muted small">Demo: admin@demo.com / owner@demo.com / rider@demo.com / customer@demo.com (password: 123456)</div>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
