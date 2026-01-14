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

// -------- helpers --------
function safe_ext($name){
  $name = strtolower($name);
  $ext = pathinfo($name, PATHINFO_EXTENSION);
  $allowed = ["jpg","jpeg","png","webp"];
  return in_array($ext,$allowed,true) ? $ext : "";
}
function save_menu_image($file){
  if(!isset($file) || ($file["error"] ?? 1) !== UPLOAD_ERR_OK) return "";

  // size limit 2MB
  if(($file["size"] ?? 0) > 2*1024*1024) return "";

  $ext = safe_ext($file["name"] ?? "");
  if($ext==="") return "";

  // basic mime check
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime = finfo_file($finfo, $file["tmp_name"]);
  finfo_close($finfo);
  $ok_mime = ["image/jpeg","image/png","image/webp"];
  if(!in_array($mime,$ok_mime,true)) return "";

  $dir = __DIR__ . "/../uploads/menu";
  if(!is_dir($dir)) mkdir($dir, 0777, true);

  $filename = "m_" . time() . "_" . bin2hex(random_bytes(4)) . "." . $ext;
  $target = $dir . "/" . $filename;

  if(!move_uploaded_file($file["tmp_name"], $target)) return "";

  // store web path
  return "/uploads/menu/" . $filename;
}

// -------- POST actions --------
if($_SERVER["REQUEST_METHOD"]==="POST"){
  $action=$_POST["action"]??"";

  // ✅ Add item with photo
  if($action==="add"){
    $name=trim($_POST["name"]??"");
    $price=(float)($_POST["price"]??0);
    $img = "";

    if(!empty($_FILES["photo"]["name"] ?? "")){
      $img = save_menu_image($_FILES["photo"]);
    }

    if($name!=="" && $price>0){
      $stmt=$conn->prepare("INSERT INTO menu_items(restaurant_id,name,price,is_active,image_url) VALUES(?,?,?,1,?)");
      $stmt->bind_param("isds",$rid,$name,$price,$img);
      $stmt->execute();
    }
  }

  // ✅ Toggle active
  if($action==="toggle"){
    $id=(int)($_POST["id"]??0);
    if($id>0){
      $stmt=$conn->prepare("UPDATE menu_items SET is_active=1-is_active WHERE id=? AND restaurant_id=?");
      $stmt->bind_param("ii",$id,$rid);
      $stmt->execute();
    }
  }

  // ✅ Update name + price + (optional) new photo
  if($action==="update"){
    $id=(int)($_POST["id"]??0);
    $name=trim($_POST["name"]??"");
    $price=(float)($_POST["price"]??0);

    if($id>0 && $name!=="" && $price>0){
      $newImg = "";
      if(!empty($_FILES["photo"]["name"] ?? "")){
        $newImg = save_menu_image($_FILES["photo"]);
      }

      if($newImg !== ""){
        $stmt=$conn->prepare("UPDATE menu_items SET name=?, price=?, image_url=? WHERE id=? AND restaurant_id=?");
        $stmt->bind_param("sdsii",$name,$price,$newImg,$id,$rid);
      } else {
        $stmt=$conn->prepare("UPDATE menu_items SET name=?, price=? WHERE id=? AND restaurant_id=?");
        $stmt->bind_param("sdii",$name,$price,$id,$rid);
      }
      $stmt->execute();
    }
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
    <div class="muted">Add items, edit price/name, upload photo, and enable/disable them.</div>

    <!-- ✅ Add new item (with photo) -->
    <form method="post" enctype="multipart/form-data" class="add" style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
      <input type="hidden" name="action" value="add">
      <input class="input" name="name" placeholder="Item name" required>
      <input class="input" name="price" placeholder="Price" required style="max-width:140px;">
      <input class="input" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" style="max-width:260px;">
      <button class="btn btn-primary">Add Item</button>
      <div class="muted small">Photo optional • Max 2MB • jpg/png/webp</div>
    </form>

    <table class="table" style="margin-top:14px;">
      <thead>
        <tr>
          <th>ID</th>
          <th>Photo</th>
          <th>Name</th>
          <th>Price</th>
          <th>Active</th>
          <th style="width:420px;">Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php if(!$items): ?>
          <tr><td colspan="6" class="muted">No items yet.</td></tr>
        <?php else: foreach($items as $it): ?>
          <tr>
            <td>#<?= (int)$it["id"] ?></td>

            <td>
              <?php if(!empty($it["image_url"])): ?>
                <img src="<?= BASE_URL . htmlspecialchars($it["image_url"]) ?>" alt="photo" style="width:56px;height:56px;object-fit:cover;border-radius:12px;">
              <?php else: ?>
                <div class="muted small">No photo</div>
              <?php endif; ?>
            </td>

            <td>
              <!-- ✅ Update form (name+price+photo) -->
              <form method="post" enctype="multipart/form-data" class="inline" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= (int)$it["id"] ?>">
                <input class="input" name="name" value="<?= htmlspecialchars($it["name"]) ?>" required style="min-width:180px;">
            </td>

            <td>
                <input class="input" name="price" value="<?= number_format((float)$it["price"],2,'.','') ?>" required style="max-width:120px;">
            </td>

            <td><?= (int)$it["is_active"] ? "Yes" : "No" ?></td>

            <td>
                <input class="input" type="file" name="photo" accept=".jpg,.jpeg,.png,.webp" style="max-width:220px;">
                <button class="btn btn-primary" type="submit">Save</button>
              </form>

              <!-- Enable/Disable -->
              <form method="post" style="display:inline-block;margin-left:10px;">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$it["id"] ?>">
                <button class="btn" type="submit">
                  <?= (int)$it["is_active"] ? "Disable" : "Enable" ?>
                </button>
              </form>

              <div class="muted small" style="margin-top:6px;">Upload a new photo to replace the old one.</div>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
