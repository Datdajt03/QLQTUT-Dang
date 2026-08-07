<?php
// User/login.php - Đăng nhập hệ thống

require_once dirname(__DIR__) . '/config.php';

$errors = [];

if (isset($_SESSION['user_id'])) {
    redirect(BASE_URL . 'index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $vai_tro  = trim($_POST['vai_tro'] ?? 'Người dùng thường');

    if (empty($username)) $errors[] = 'Vui lòng nhập tên đăng nhập.';
    if (empty($password)) $errors[] = 'Vui lòng nhập mật khẩu.';

    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Tìm tài khoản
            $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            // Hỗ trợ đăng nhập trực tiếp tài khoản Admin/Admin123
            $isAdminBypass = ($username === 'Admin' && $password === 'Admin123');

            if ($user && (password_verify($password, $user['password']) || $isAdminBypass)) {
                // Kiểm tra vai trò đăng nhập có khớp không
                if ($user['vai_tro'] !== $vai_tro && !$isAdminBypass) {
                    $errors[] = "Tài khoản này có vai trò là '{$user['vai_tro']}', vui lòng chọn đúng vai trò khi đăng nhập.";
                } else {
                    // Đăng nhập thành công
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['vai_tro'] = $user['vai_tro'];
                    $_SESSION['ho_ten'] = $user['ho_ten'];
                    
                    setFlash('success', 'Đăng nhập hệ thống thành công! Chào mừng ' . e($user['ho_ten']));
                    redirect(BASE_URL . 'index.php');
                }
            } else {
                $errors[] = 'Tên đăng nhập hoặc mật khẩu không chính xác.';
            }
        } catch (Exception $e) {
            $errors[] = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập hệ thống – <?= SITE_NAME ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/style.css">
  <style>
    body {
      background: #0f0f14;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
      font-family: 'Roboto', sans-serif;
    }
    .auth-container {
      max-width: 450px;
      width: 100%;
      background: #16161f;
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.5);
      overflow: hidden;
    }
    .auth-header {
      background: linear-gradient(135deg, #C8102E, #9e0b22);
      padding: 30px;
      text-align: center;
    }
    .auth-header h2 {
      font-size: 22px;
      font-weight: 700;
      color: #fff;
    }
    .auth-header p {
      font-size: 13px;
      color: rgba(255,255,255,0.8);
      margin-top: 6px;
    }
    .auth-body {
      padding: 30px;
    }
    .role-selection {
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
      margin-top: 8px;
    }
    .role-box {
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 8px;
      padding: 10px 14px;
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      background: rgba(255,255,255,0.02);
      transition: all 0.2s;
    }
    .role-box:hover {
      background: rgba(255,255,255,0.05);
      border-color: rgba(255,255,255,0.2);
    }
    .role-box input[type="radio"] {
      margin: 0;
      accent-color: #FFD700;
    }
    .role-box span {
      font-size: 13px;
      font-weight: 500;
      color: #e8e8f0;
    }
    .role-box.active {
      border-color: #FFD700;
      background: rgba(255,215,0,0.05);
    }
    .links {
      text-align: center;
      margin-top: 20px;
      font-size: 13px;
    }
    .links a {
      color: #FFD700;
      text-decoration: none;
      font-weight: 600;
    }
    .links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="auth-container">
  <div class="auth-header">
    <div style="font-size: 36px; margin-bottom: 8px;">⭐</div>
    <h2>ĐĂNG NHẬP HỆ THỐNG</h2>
    <p>Hệ thống quản lý quần chúng ưu tú phục vụ kết nạp Đảng</p>
  </div>

  <div class="auth-body">
    
    <?php 
    $flash = getFlash();
    if ($flash): ?>
      <div class="flash flash-<?= e($flash['type']) ?>" style="margin-bottom: 20px;">
        <?= $flash['type'] === 'success' ? '✅' : '❌' ?> <?= e($flash['msg']) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="flash flash-danger" style="margin-bottom: 20px;">
        <ul style="margin-left: 20px;">
          <?php foreach ($errors as $err): ?>
            <li><?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Tên đăng nhập</label>
        <input type="text" name="username" class="form-control" placeholder="Tên đăng nhập..." value="<?= e($username ?? '') ?>" required autocomplete="username">
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Mật khẩu</label>
        <input type="password" name="password" class="form-control" placeholder="Mật khẩu..." required autocomplete="current-password">
      </div>

      <div class="form-group" style="margin-bottom: 24px;">
        <label class="form-label">Chọn vai trò đăng nhập (Mặc định: Người dùng thường)</label>
        <div class="role-selection">
          <label class="role-box active" id="role-user">
            <input type="radio" name="vai_tro" value="Người dùng thường" checked onclick="selectRole('user')">
            <span>Người dùng thường (Mặc định)</span>
          </label>
          <label class="role-box" id="role-manager">
            <input type="radio" name="vai_tro" value="Quản lý" onclick="selectRole('manager')">
            <span>Quản lý</span>
          </label>
          <label class="role-box" id="role-admin">
            <input type="radio" name="vai_tro" value="Admin" onclick="selectRole('admin')">
            <span>Admin (Quản trị viên)</span>
          </label>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 15px;">
        🔑 Đăng nhập
      </button>
    </form>

    <div class="links">
      Chưa có tài khoản? <a href="<?= BASE_URL ?>User/register.php">Đăng ký ngay</a>
    </div>
  </div>
</div>

<script>
function selectRole(type) {
  document.getElementById('role-user').classList.remove('active');
  document.getElementById('role-manager').classList.remove('active');
  document.getElementById('role-admin').classList.remove('active');
  
  document.getElementById('role-' + type).classList.add('active');
}
</script>

</body>
</html>
