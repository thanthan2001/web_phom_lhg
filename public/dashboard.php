<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            margin: 0;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="sidebar" id="sidebar">
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class="fas fa-chevron-left"></i>
            </button>
            <h2><span id="lhg-text">LHG</span> <span class="dashboard-text">DASHBOARD</span></h2>
            <ul>
                <li><a href="#"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> <span>Users</span></a></li>
                <li><a href="#"><i class="fas fa-box"></i> <span>Products</span></a></li>
                <li><a href="#"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
                <li><a href="#"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
                <li><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>

        <div class="content">
            <div class="dashboard-header">
                <h1>Dashboard</h1>


            </div>



        </div>

    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
        }
    </script>

</body>

</html>