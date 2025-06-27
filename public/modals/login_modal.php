<?php
include '../configs/api.php';

$msg = '';
$success = false;

$savedUserID = isset($_COOKIE['remember_userID']) ? $_COOKIE['remember_userID'] : '';
$savedCompany = isset($_COOKIE['remember_companyName']) ? $_COOKIE['remember_companyName'] : '';
$savedPassword = isset($_COOKIE['remember_password']) ? $_COOKIE['remember_password'] : '';
$savedRememberMe = isset($_COOKIE['remember_userID']) && isset($_COOKIE['remember_companyName']) && isset($_COOKIE['remember_password']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_form'])) {
  $payload = [
    'userID' => $_POST['iduser'],
    'pwd' => $_POST['password'],
    'companyName' => $_POST['companyName']
  ];

  try {
    $response = callAPI('auth_login', $payload);
    $result = json_decode($response, true);

    if ($result['status'] == 200) {
      $_SESSION['user'] = $result['data'];
      $_SESSION['user']['companyName'] = $payload['companyName'];
      $_SESSION['user']['userID'] = $payload['userID'];

      if (!empty($_POST['rememberMe'])) {
        setcookie('remember_userID', $_POST['iduser'], time() + (86400 * 30), "/");
        setcookie('remember_companyName', $_POST['companyName'], time() + (86400 * 30), "/");
        setcookie('remember_password', $_POST['password'], time() + (86400 * 30), "/");
      } else {
        setcookie('remember_userID', '', time() - 3600, "/");
        setcookie('remember_companyName', '', time() - 3600, "/");
        setcookie('remember_password', '', time() - 3600, "/");
      }

      $userID = $result['data']['USERID'];
      $adminUserIDs = include '../configs/admins.php';
      $success = true;
      $msg = "Chào mừng " . htmlspecialchars($result['data']['USERNAME']) . "!";

      if (in_array($userID, $adminUserIDs)) {
        echo "<meta http-equiv='refresh' content='1.5;url=index.php'>";
      } else {
        echo "<meta http-equiv='refresh' content='1.5;url=lend_register.php'>";
      }
    } else {
      $msg = isset($result['message']) ? $result['message'] : "Đăng nhập thất bại.";
    }
  } catch (Exception $e) {
    $msg = "Lỗi kết nối: " . $e->getMessage();
  }
}
?>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content border-0 shadow-lg rounded-4 p-4" method="POST">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-primary">Đăng nhập hệ thống</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>

      <div class="modal-body">
        <?php if (!empty($msg)): ?>
          <div class="position-fixed top-0 start-50 translate-middle-x p-3 toast-slide-down" style="z-index: 9999;">
            <div id="loginToast" class="toast align-items-center text-white <?= $success ? 'bg-success' : 'bg-danger' ?> border-0 shadow-lg show" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                <div class="toast-body fw-bold">
                  <?= htmlspecialchars($msg) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Đóng"></button>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <input type="hidden" name="login_form" value="1">

        <div class="form-floating mb-3">
          <input type="text" class="form-control rounded-3" id="iduser" name="iduser" placeholder="Số thẻ"
            value="<?= htmlspecialchars($savedUserID) ?>" required>
          <label for="iduser">Số thẻ</label>
        </div>

        <div class="form-floating mb-3 position-relative">
          <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Mật khẩu" value="<?= htmlspecialchars($savedPassword) ?>" required>
          <label for="password">Mật khẩu</label>
          <span class="toggle-password" onclick="togglePassword()" style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;">
            <i id="togglePasswordIcon" class="fas fa-eye"></i>
          </span>
        </div>

        <div class="form-floating mb-3">
          <select class="form-select rounded-3" id="companyName" name="companyName" required>
            <option value="">-- Chọn công ty --</option>
            <?php
            $companies = ['lhg', 'lyv', 'lvl', 'jaz', 'jzs', 'lym'];
            foreach ($companies as $company) {
              $selected = ($company == $savedCompany) ? 'selected' : '';
              echo "<option value=\"$company\" $selected>" . strtoupper($company) . "</option>";
            }
            ?>
          </select>
          <label for="companyName">Công ty</label>
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" value="1" id="rememberMe" name="rememberMe" <?= $savedRememberMe ? 'checked' : '' ?>>
          <label class="form-check-label" for="rememberMe">
            Ghi nhớ tôi
          </label>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" class="btn btn-primary rounded-pill px-4">Đăng nhập</button>
      </div>
    </form>
  </div>
</div>

<script>
  // Hàm toggle ẩn/hiện mật khẩu (luôn hoạt động)
  function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');

    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }

  <?php if (!empty($msg)): ?>
  document.addEventListener('DOMContentLoaded', () => {
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();

    const toastEl = document.getElementById('loginToast');
    const toast = new bootstrap.Toast(toastEl, {
      delay: 4000
    });
    toast.show();
  });
  <?php endif; ?>
</script>
