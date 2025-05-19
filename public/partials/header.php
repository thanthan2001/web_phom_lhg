<style>
    header {
        position: fixed;
        top: 0;
        left: 260px;
        width: calc(100% - 260px);
        height: 70px;
        background-color: white;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 0 20px;
        border-bottom: 4px solid #083b5b;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        z-index: 900;
        transition: left 0.3s ease, width 0.3s ease;
    }

    .sidebar~header {
        left: 260px;
        width: calc(100% - 260px);
    }

    .sidebar.collapsed~header {
        left: 80px;
        width: calc(100% - 80px);
    }

    .user-section {
        display: flex;
        align-items: center;
        gap: 25px;
        font-size: 14px;
        color: #083b5b;
        font-weight: 500;
    }

    .dropdown {
        position: relative;
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .dropdown-toggle {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 120%;
        right: 0;
        background: white;
        border: 1px solid #ccc;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        min-width: 150px;
        z-index: 999;
    }

    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .dropdown-menu button {
        width: 100%;
        padding: 10px;
        background: none;
        border: none;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-menu button:hover {
        background-color: #f2f2f2;
    }

    .language-dropdown {
        position: relative;
    }

    .language-menu {
        display: none;
        position: absolute;
        top: 120%;
        right: 0;
        background: white;
        border: 1px solid #ccc;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        min-width: 150px;
        z-index: 999;
        flex-direction: column;
    }

    .language-menu button {
        width: 100%;
        padding: 10px 12px;
        background: none;
        border: none;
        text-align: left;
        cursor: pointer;
        font-size: 15px;
        color: #083b5b;
        position: relative;
    }

    .language-menu button i {
        float: right;
        color: #14AE5C;
    }

    .language-menu button:hover {
        background-color: #9CCDDB;
    }


    .language-toggle {
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
    }

    .language-dropdown select {
        border: none;
        background: transparent;
        font-size: 14px;
        color: #083b5b;
        appearance: none;
        padding: 0;
        margin-left: 3px;
        cursor: pointer;
    }

    .language-dropdown select:focus {
        outline: none;
    }

    .btn-login {
        background-color: #1b98e0;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-login:hover {
        background-color: #147ca4;
    }

    body.sidebar-collapsed header {
        width: calc(100% - 80px);
        left: 80px;
    }

    body.sidebar-collapsed .content {
        margin-left: 80px;
    }
</style>

<header>
    <div style="flex-grow: 1; font-weight: bold; font-size: 20px; color: #083b5b;" id="page-title"></div>

    <div class="user-section">
        <?php if (isset($_SESSION['user'])): ?>
            <div class="dropdown">
                <div class="dropdown-toggle">
                    <span><?php echo htmlspecialchars($_SESSION['user']['USERNAME']); ?></span>
                </div>
                <div class="dropdown-menu">
                    <form method="POST" action="logout.php">
                        <button type="submit">Đăng xuất</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <button class="btn-login" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</button>
        <?php endif; ?>

        <div class="language-dropdown">
            <div class="language-toggle" onclick="toggleLanguageDropdown()">
                <i class="fas fa-globe"></i>
            </div>
            <div class="dropdown-menu language-menu" id="language-menu">
                <form method="POST" action="">
                    <button type="submit" name="language" value="vi">
                        Tiếng Việt <?php if ($language == "vi") echo '<i class="fas fa-check" style="float:right;"></i>'; ?>
                    </button>
                    <button type="submit" name="language" value="en">
                        English <?php if ($language == "en") echo '<i class="fas fa-check" style="float:right;"></i>'; ?>
                    </button>
                    <button type="submit" name="language" value="zh">
                        中文 <?php if ($language == "zh") echo '<i class="fas fa-check" style="float:right;"></i>'; ?>
                    </button>
                    <button type="submit" name="language" value="mm">
                        မြန်မာ <?php if ($language == "mm") echo '<i class="fas fa-check" style="float:right;"></i>'; ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const pageTitle = localStorage.getItem('currentPageTitle');
        if (pageTitle) {
            document.getElementById('page-title').innerText = pageTitle;
        }
    });

    function toggleLanguageDropdown() {
        const menu = document.getElementById('language-menu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    }

    // Tự động đóng khi click ra ngoài
    window.addEventListener('click', function(e) {
        const dropdown = document.querySelector('.language-dropdown');
        if (!dropdown.contains(e.target)) {
            document.getElementById('language-menu').style.display = 'none';
        }
    });

    document.querySelector('.btn-login')?.addEventListener('click', () => {
        document.getElementById('language-menu').style.display = 'none';
    });
</script>