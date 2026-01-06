<?php
require_once __DIR__ . "/../config/config.php";
$u = $_SESSION["user"] ?? null;

function nav_link($label,$href){
  echo '<a class="nav-link" href="'.BASE_URL.$href.'">'.htmlspecialchars($label).'</a>';
}
?>
<header class="topbar">
  <div class="topbar-inner container">
    <div class="brand">
      <div class="logo-dot"></div>
      <a class="brand-name" href="<?= BASE_URL ?>/public/index.php">SmartBite</a>
    </div>

    <nav class="nav">
      <?php nav_link("Home","/public/index.php"); ?>
      <?php nav_link("Browse","/public/browse.php"); ?>
      <?php nav_link("Cart","/public/cart.php"); ?>
      <?php nav_link("Orders","/public/orders.php"); ?>
      <?php nav_link("Contact","/public/contact.php"); ?>

      <?php if(!$u): ?>
        <?php nav_link("Login","/auth/login.php"); ?>
        <?php nav_link("Registration","/auth/register.php"); ?>
      <?php else: ?>
        <div class="nav-user">
          <span class="badge"><?= htmlspecialchars($u["role"]) ?></span>
          <span class="muted small"><?= htmlspecialchars($u["name"]) ?></span>

          <?php if($u["role"]==="admin") nav_link("Admin","/admin/dashboard.php"); ?>
          <?php if($u["role"]==="restaurant") nav_link("Restaurant","/restaurant/dashboard.php"); ?>
          <?php if($u["role"]==="delivery") nav_link("Delivery","/delivery/dashboard.php"); ?>

          <?php nav_link("Logout","/auth/logout.php"); ?>
        </div>
      <?php endif; ?>
    </nav>
  </div>
</header>
