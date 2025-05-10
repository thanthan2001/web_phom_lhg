<style>
    header {
        position: fixed;
        top: 0;
        left: 250px;
        width: calc(100% - 250px);
        height: 50px;
        background-color: white;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding: 0 20px;
        border-bottom: 4px solid #083b5b;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        z-index: 1000;
        transition: left 0.3s ease, width 0.3s ease;
    }

    .sidebar.collapsed ~ header {
        left: 100px;
        width: calc(100% - 100px);
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
</style>

<header>
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
            <div class="language-toggle">
                <i class="fas fa-globe"></i>
                <form method="POST" action="">
                    <select name="language" onchange="this.form.submit()">
                        <option value="vi" <?php if ($language == "vi") echo "selected"; ?>>Tiếng Việt</option>
                        <option value="en" <?php if ($language == "en") echo "selected"; ?>>English</option>
                        <option value="zh" <?php if ($language == "zh") echo "selected"; ?>>中文</option>
                        <option value="mm" <?php if ($language == "mm") echo "selected"; ?>>မြန်မာ</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</header>
