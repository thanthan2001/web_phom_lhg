<!-- sidebar.php -->
<style>
  body {
    margin: 0;
    font-family: "Segoe UI", sans-serif;
  }

  .sidebar {
    width: 260px;
    background-color: #0f172a;
    color: white;
    height: 100vh;
    padding: 20px 0;
    position: fixed;
    transition: width 0.3s ease;
    z-index: 1000;
  }

  .sidebar.collapsed {
    width: 80px;
  }

  .sidebar .logo {
    font-size: 1.2rem;
    font-weight: bold;
    padding: 0 20px;
    margin-bottom: 30px;
    color: #38bdf8;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .sidebar ul {
    list-style: none;
    padding: 0;
  }

  .sidebar li {
    margin-bottom: 5px;
  }

  .sidebar a {
    color: #cbd5e1;
    text-decoration: none;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    border-left: 4px solid transparent;
    transition: background 0.2s, color 0.2s;
  }

  .sidebar a:hover {
    background-color: #1e293b;
    color: white;
  }

  .sidebar a.active {
    background-color: #1e40af;
    border-left: 4px solid #53EDF5;
    color: white;
    font-weight: 500;
  }

  .sidebar.collapsed a span {
    display: none;
  }

  .sidebar.collapsed .logo span {
    display: none;
  }

  .toggle-btn {
    background-color: #53EDF5;
    border: none;
    color: white;
    position: absolute;
    right: -15px;
    top: 20px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    font-size: 16px;
    cursor: pointer;
    transition: transform 0.3s;
  }

  .sidebar.collapsed .toggle-btn i {
    transform: rotate(180deg);
  }

  .content {
    margin-left: 260px;
    transition: margin-left 0.3s;
    padding: 20px;
  }

  .sidebar.collapsed ~ .content {
    margin-left: 80px;
  }

  .sidebar i {
    font-size: 24px;
}
</style>

<?php
$userID = isset($_SESSION['user']['USERID']) ? $_SESSION['user']['USERID'] : null;
$adminUserIDs = ['65563', '65557', '65558']; // có quyền quản lý đơn mượn
?>

<div class="sidebar" id="sidebar">
  <button class="toggle-btn" onclick="toggleSidebar()">
    <i class="fas fa-angle-left"></i>
  </button>
  <div class="logo"><i class="fas fa-shoe-prints"></i><span>Phom System</span></div>
  <ul>
    <?php if (in_array($userID, $adminUserIDs)): ?>
    <li><a href="index.php" data-page="Trang chủ"><i class="fas fa-home"></i> <span>Trang chủ</span></a></li>
    <li><a href="manage_borrow.php" data-page="Quản lý đơn mượn"><i class="fas fa-tasks"></i> <span>Quản lý đơn mượn</span></a></li>
    <li><a href="manage_return.php" data-page="Quản lý đơn trả"><i class="fas fa-undo-alt"></i> <span>Quản lý đơn trả</span></a></li>
    <li><a href="manage_phom.php" data-page="Quản lý phom"><i class="fas fa-shoe-prints"></i> <span>Quản lý phom</span></a></li>
  <?php else: ?>
    <li><a href="lend_register.php" data-page="Đăng ký mượn"><i class="fas fa-hand-holding"></i> <span>Đăng ký mượn</span></a></li>
    <li><a href="history_register.php" data-page="Lịch sử mượn"><i class="fas fa-file-alt"></i> <span>Lịch sử mượn trả</span></a></li>
    <!-- <li><a href="trans_form.php" data-page="Chuyển phom"><i class="fas fa-exchange-alt"></i> <span>Chuyển phom</span></a></li> -->
  <?php endif; ?>
  </ul>
</div>

<script>
  const sidebar = document.getElementById('sidebar');
  
  function toggleSidebar() {
    sidebar.classList.toggle('collapsed');
    document.body.classList.toggle('sidebar-collapsed');
    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
  }

  function setActiveLink() {
    const currentUrl = window.location.pathname.split('/').pop();
    const links = document.querySelectorAll('.sidebar a');
    links.forEach(link => {
      if (link.getAttribute('href') === currentUrl) {
        link.classList.add('active');
        localStorage.setItem('currentPageTitle', link.getAttribute('data-page'));
      } else {
        link.classList.remove('active');
      }
    });
  }

  window.addEventListener('DOMContentLoaded', () => {
    const sidebarCollapsed = localStorage.getItem('sidebarCollapsed');

    // Nếu chưa có giá trị, mặc định collapsed là true (tức sidebar đóng)
    if (sidebarCollapsed === null) {
        localStorage.setItem('sidebarCollapsed', 'true');
        document.getElementById('sidebar').classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
    } else if (sidebarCollapsed === 'true') {
        document.getElementById('sidebar').classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
    } else {
        document.getElementById('sidebar').classList.remove('collapsed');
        document.body.classList.remove('sidebar-collapsed');
    }

    setActiveLink(); // vẫn giữ lại highlight menu
});

</script>
