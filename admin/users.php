<?php
require_once __DIR__."/../config/db.php";
require_once __DIR__."/../config/auth.php";
require_role("admin");

$PAGE_TITLE="Admin Users | SmartBite";
$PAGE_CSS="admin_users.css";

if($_SERVER["REQUEST_METHOD"]==="POST"){
  $id=(int)($_POST["id"]??0);
  $role=$_POST["role"]??"customer";
  $allowed=["admin","customer","restaurant","delivery"];
  if($id>0 && in_array($role,$allowed,true)){
    $stmt=$conn->prepare("UPDATE users SET role=? WHERE id=?");
    $stmt->bind_param("si",$role,$id);
    $stmt->execute();
  }
  redirect("/admin/users.php");
}

$users=$conn->query("SELECT id,name,email,role,created_at FROM users ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

include __DIR__."/../partials/head.php";
include __DIR__."/../partials/header.php";
?>
<main class="container">
  <div class="card box">
    <h1 class="h1">Users</h1>
    <div class="muted">Change user roles.</div>

    <table class="table">
      <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Change</th></tr></thead>
      <tbody>
        <?php foreach($users as $u): ?>
          <tr>
            <td>#<?= (int)$u["id"] ?></td>
            <td><?= htmlspecialchars($u["name"]) ?></td>
            <td class="muted small"><?= htmlspecialchars($u["email"]) ?></td>
            <td><span class="badge"><?= htmlspecialchars($u["role"]) ?></span></td>
            <td>
              <form method="post" class="inline">
                <input type="hidden" name="id" value="<?= (int)$u["id"] ?>">
                <select class="input select" name="role">
                  <?php foreach(["admin","customer","restaurant","delivery"] as $r): ?>
                    <option value="<?= $r ?>" <?= ($u["role"]===$r)?"selected":"" ?>><?= $r ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-primary">Update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__."/../partials/footer.php"; ?>
