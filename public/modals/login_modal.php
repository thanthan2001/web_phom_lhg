<?php
include '../configs/api.php';

$msg = '';
$success = false;

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
            $success = true;
            $msg = "Chào mừng " . htmlspecialchars($result['data']['USERNAME']) . "!";
            echo "<meta http-equiv='refresh' content='1.5;url=index.php'>";
        } else {
            $msg = $result['message'] ?? "Đăng nhập thất bại.";
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

        <!-- ✅ Hidden field để xác định đây là form đăng nhập -->
        <input type="hidden" name="login_form" value="1">

        <div class="form-floating mb-3">
          <input type="text" class="form-control rounded-3" id="iduser" name="iduser" placeholder="Tên đăng nhập" required>
          <label for="iduser">Tên đăng nhập</label>
        </div>

        <div class="form-floating mb-3">
          <input type="password" class="form-control rounded-3" id="password" name="password" placeholder="Mật khẩu" required>
          <label for="password">Mật khẩu</label>
        </div>

        <div class="form-floating mb-3">
          <select class="form-select rounded-3" id="companyName" name="companyName" required>
            <option value="">-- Chọn công ty --</option>
            <option value="lhg">LHG</option>
            <option value="abc">ABC</option>
            <option value="xyz">XYZ</option>
          </select>
          <label for="companyName">Công ty</label>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" class="btn btn-primary rounded-pill px-4">Đăng nhập</button>
      </div>
    </form>
  </div>
</div>

<?php if (!empty($msg)): ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
    loginModal.show();

    const toastEl = document.getElementById('loginToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
    toast.show();
  });
</script>
<?php endif; ?>
