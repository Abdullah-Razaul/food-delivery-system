<?php
require_once __DIR__."/../config/db.php";
$PAGE_TITLE="Browse | SmartBite";
$PAGE_CSS="browse.css";

$qText = trim($_GET["q"] ?? "");
$cat = trim($_GET["cat"] ?? "");

$where = "1=1";
$params = [];
$types = "";

if($qText !== ""){
  $where .= " AND (name LIKE ? OR category LIKE ?)";
  $like = "%".$qText."%";
  $params[] = $like; $params[] = $like;
  $types .= "ss";
}
if($cat !== ""){
  $where .= " AND category LIKE ?";
  $params[] = "%".$cat."%";
  $types .= "s";
}

$sql = "SELECT * FROM restaurants WHERE $where ORDER BY rating DESC";
$stmt = $conn->prepare($sql);
if($types !== ""){
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$restaurants = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="top card">
    <div>
      <h1 class="h1">Browse</h1>
      <div class="muted">Find restaurants by name or category.</div>
    </div>
    <form class="search" method="get">
      <input class="input" name="q" placeholder="Search restaurants..." value="<?= htmlspecialchars($qText) ?>">
      <button class="btn btn-primary">Search</button>
    </form>
  </div>

  <div class="grid">
    <?php foreach($restaurants as $r): ?>
      <a class="r-card card" href="<?= BASE_URL ?>/public/restaurant.php?id=<?= (int)$r["id"] ?>">
        <div class="promo"><?= htmlspecialchars($r["promo_text"]) ?></div>
        <div class="img">🍽️</div>
        <div class="name"><?= htmlspecialchars($r["name"]) ?></div>
        <div class="meta">
          ⭐ <?= htmlspecialchars($r["rating"]) ?> (<?= (int)$r["ratings_count"] ?>+)
          <span class="muted"> • <?= htmlspecialchars($r["category"]) ?></span>
        </div>
      </a>
    <?php endforeach; ?>
    <?php if(!$restaurants): ?>
      <div class="card empty">No restaurants found.</div>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
