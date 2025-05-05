<?php include "session_start.php"; ?>

<style>
    header {
        z-index: 1;
        background: #ffffff;
        color: #141e26;
        padding: 5px 10px;
        text-align: center;
        border-bottom: 2px solid #141e26;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 250px;
        width: calc(100% - 250px);
        z-index: 1000;
    }

    header form {
        margin-right: 30px;
    }

    header select {
        padding: 10px;
        border: 2px solid #141e26;
        border-radius: 5px;
        font-size: 14px;
    }

    header {
        left: 250px;
        width: calc(100% - 250px);
        transition: left 0.3s ease, width 0.3s ease;
    }

    .sidebar.collapsed~.content header {
        left: 100px;
        width: calc(100% - 100px);
    }
</style>

<header class="" style="z-index: 1;">
    <h1 class="ps-3">Website Title</h1>
    <form method="POST" action="">
        <label for="language"><?php echo $lang['language']; ?>:</label>
        <select name="language" onchange="this.form.submit()">
            <option value="en" <?php if ($language == "en") echo "selected"; ?>>English</option>
            <option value="vi" <?php if ($language == "vi") echo "selected"; ?>>Tiếng Việt</option>
            <option value="zh" <?php if ($language == "zh") echo "selected"; ?>>中文</option>
            <option value="mm" <?php if ($language == "mm") echo "selected"; ?>>မြန်မာ</option>
        </select>
    </form>
</header>