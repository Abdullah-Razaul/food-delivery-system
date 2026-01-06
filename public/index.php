<?php
require_once __DIR__."/../config/db.php";
$PAGE_TITLE="SmartBite | Home";
$PAGE_CSS="home.css";
include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";

$q = $conn->query("SELECT * FROM restaurants ORDER BY rating DESC LIMIT 8");
$restaurants = $q ? $q->fetch_all(MYSQLI_ASSOC) : [];
?>
<main class="container">
  <section class="hero card">
    <div class="hero-left">
      <h1 class="hero-title">Delicious Food,<br>Delivered Fast.</h1>
      <p class="hero-sub">Order from your favorite restaurants near you.</p>

      <form class="hero-search" action="<?= BASE_URL ?>/public/browse.php" method="get">
        <input class="input" name="q" placeholder="Search for food or Restaurants">
        <button class="btn btn-primary" type="submit">Order Now</button>
      </form>
    </div>
    <div class="hero-right">
      <div class="phone card">
        <div class="muted small">SmartBite Delivery</div>
        <div class="phone-screen">ORDER</div>
      </div>
    </div>
  </section>

  <section class="block">
    <h2 class="block-title">Browse by Category</h2>
    <div class="muted">What are you craving today?</div>

    <div class="cats">
      <a class="cat card" href="<?= BASE_URL ?>/public/browse.php?cat=Burger">🍔</a>
      <a class="cat card" href="<?= BASE_URL ?>/public/browse.php?cat=Pizza">🍕</a>
      <a class="cat card" href="<?= BASE_URL ?>/public/browse.php?cat=Drinks">🥤</a>
      <a class="cat card" href="<?= BASE_URL ?>/public/browse.php?cat=Dessert">🍰</a>
    </div>
  </section>

  <section class="block">
    <h2 class="block-title">Popular Restaurants</h2>
    <div class="grid">
      <?php foreach($restaurants as $r): ?>
        <a class="r-card card" href="<?= BASE_URL ?>/public/restaurant.php?id=<?= (int)$r["id"] ?>">
          <div class="promo"><?= htmlspecialchars($r["promo_text"]) ?></div>
          <div class="r-img">🍽️</div>
          <div class="r-name"><?= htmlspecialchars($r["name"]) ?></div>
          <div class="r-meta">
            ⭐ <?= htmlspecialchars($r["rating"]) ?> (<?= (int)$r["ratings_count"] ?>+)
            <span class="muted"> • <?= htmlspecialchars($r["category"]) ?></span>
          </div>
        </a>
      <?php endforeach; ?>
      <?php if(count($restaurants)===0): ?>
        <div class="card" style="padding:18px;">No restaurants yet.</div>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
