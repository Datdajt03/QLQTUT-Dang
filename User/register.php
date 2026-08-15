<?php
// User/register.php - Đăng ký tài khoản mới

require_once dirname(__DIR__) . '/config.php';

$errors = [];
$success = false;

if (isset($_SESSION['user_id'])) {
    redirect(BASE_URL . 'index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ho_ten   = trim($_POST['ho_ten'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $vai_tro  = trim($_POST['vai_tro'] ?? 'Người dùng thường');

    // Validation
    if (empty($ho_ten)) $errors[] = 'Họ và tên không được để trống.';
    if (empty($username)) $errors[] = 'Tên đăng nhập không được để trống.';
    if (empty($password)) $errors[] = 'Mật khẩu không được để trống.';
    if (strlen($password) < 6) $errors[] = 'Mật khẩu phải từ 6 ký tự trở lên.';

    if (empty($errors)) {
        try {
            $db = getDB();
            
            // Kiểm tra username trùng lặp
            $stmt = $db->prepare("SELECT COUNT(*) FROM nguoi_dung WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $errors[] = 'Tên đăng nhập này đã tồn tại.';
            } else {
                // Thêm tài khoản mới
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO nguoi_dung (username, password, ho_ten, vai_tro) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $hash, $ho_ten, $vai_tro]);
                
                $success = true;
                setFlash('success', 'Đăng ký tài khoản thành công! Hãy đăng nhập.');
                redirect(BASE_URL . 'User/login.php');
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
  <title>Đăng ký tài khoản – <?= SITE_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>Giao_dien/assets/style.css">
  <style>
    body {
      background: #f4f6f9;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
      font-family: 'Roboto', sans-serif;
      color: #333333;
    }
    .auth-container {
      max-width: 450px;
      width: 100%;
      background: #ffffff;
      border: 1px solid #e0e0e0;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.06);
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
      color: rgba(255,255,255,0.85);
      margin-top: 6px;
    }
    .auth-body {
      padding: 30px;
    }
    .form-label {
      color: #495057;
      font-weight: 500;
      margin-bottom: 6px;
      font-size: 13px;
    }
    .form-control {
      background: #ffffff !important;
      border: 1px solid #ced4da !important;
      color: #212529 !important;
    }
    .form-control:focus {
      border-color: #C8102E !important;
      box-shadow: 0 0 0 0.2rem rgba(200, 16, 46, 0.15) !important;
    }
    .role-selection {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
      margin-top: 6px;
    }
    .role-box {
      border: 1.5px solid #e0e0e0;
      border-radius: 8px;
      padding: 10px 4px;
      text-align: center;
      cursor: pointer;
      transition: all 0.2s;
      background: #fafafa;
    }
    .role-box:hover {
      border-color: #C8102E;
      background: #fff5f5;
    }
    .role-box.active {
      border-color: #C8102E;
      background: #fff0f2;
      color: #C8102E;
      font-weight: 600;
    }
    .role-box input[type="radio"] {
      display: none;
    }
    .role-box span {
      font-size: 12px;
      display: block;
      line-height: 1.3;
    }
    .links {
      text-align: center;
      margin-top: 20px;
      font-size: 13px;
      color: #666;
    }
    .links a {
      color: #C8102E;
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
    <h2>ĐĂNG KÝ TÀI KHOẢN</h2>
    <p><?= SITE_NAME ?></p>
  </div>

  <div class="auth-body">
    <?php if (!empty($errors)): ?>
      <div class="flash flash-danger" style="margin-bottom: 20px;">
        <ul style="margin-left: 20px;">
          <?php foreach ($errors as $err): ?>
            <li><i class="bi bi-exclamation-triangle-fill" style="margin-right:6px;"></i> <?= e($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Họ và tên</label>
        <input type="text" name="ho_ten" class="form-control" placeholder="Nguyễn Văn A" value="<?= e($ho_ten ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Tên đăng nhập</label>
        <input type="text" name="username" class="form-control" placeholder="username" value="<?= e($username ?? '') ?>" required>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Mật khẩu</label>
        <input type="password" name="password" class="form-control" placeholder="Mật khẩu ít nhất 6 ký tự" required>
      </div>

      <div class="form-group" style="margin-bottom: 24px;">
        <label class="form-label">Chọn vai trò đăng ký (Mặc định: Người dùng thường)</label>
        <div class="role-selection">
          <label class="role-box active" id="role-user">
            <input type="radio" name="vai_tro" value="Người dùng thường" checked onclick="selectRole('user')">
            <i class="bi bi-person"></i>
            <span>Người dùng</span>
          </label>
          <label class="role-box" id="role-manager">
            <input type="radio" name="vai_tro" value="Quản lý" onclick="selectRole('manager')">
            <i class="bi bi-briefcase"></i>
            <span>Quản lý</span>
          </label>
          <label class="role-box" id="role-admin">
            <input type="radio" name="vai_tro" value="Admin" onclick="selectRole('admin')">
            <i class="bi bi-shield-lock"></i>
            <span>Admin</span>
          </label>
        </div>
      </div>

      <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px; font-size: 15px;">
        <i class="bi bi-person-check-fill" style="margin-right:6px;"></i> Đăng ký tài khoản
      </button>
    </form>

    <div class="links">
      Đã có tài khoản? <a href="<?= BASE_URL ?>User/login.php">Đăng nhập ngay</a>
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
