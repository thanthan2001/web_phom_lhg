<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .wrapper {
        display: flex;
        height: 100vh;
    }

    .sidebar {
        background-color: #13293d;
        color: white;
        width: 250px;
        height: 100vh;
        padding: 20px;
        box-sizing: border-box;
        transition: width 0.3s ease;
        position: relative;
        /* For positioning the toggle button */
    }

    .sidebar.collapsed {
        width: 100px;
    }

    .h2,
    h2 {
        font-size: 20px !important;
        font-weight: bold;
        /* Hoặc giá trị mong muốn */
    }

    .sidebar h2 {
        margin-top: 0;
        white-space: nowrap;
        /* Prevent text wrapping */
        display: flex;
        /* Use flexbox to align the title and icon */
        align-items: center;
        /* Vertically align items */
    }

    .sidebar h2 span {
        margin-right: 5px;
        /* Add some spacing between text and icon */
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar li {
        padding: 10px;
        width: 100%;
        border-bottom: 1px solid #555;
    }

    .sidebar li:last-child {
        border-bottom: none;
    }


    .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 10px;
        white-space: nowrap;
        /* Prevent text wrapping */
    }

    .sidebar a:hover {
        background-color: #555;
    }

    .toggle-btn {

        position: absolute;
        overflow: visible;
        z-index: 10;
        top: 20px;
        right: -15px;
        /* Position outside the sidebar */
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #448aff;
        /* Example blue color */
        color: white;
        border: none;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 20px;
        transition: transform 0.3s ease;
        /* Transition for the icon */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        /* Add a subtle shadow */
    }

    .toggle-btn:focus {
        outline: none;
    }

    .toggle-btn i {
        transition: transform 0.5s ease;
        /* Rotate the icon */
    }

    .sidebar.collapsed .toggle-btn i {
        transform: rotate(180deg);
        transition: 0.5s
            /* Rotate icon when collapsed */
    }

    .dashboard-text {
        text-transform: uppercase;
    }

    .sidebar.collapsed h2 .dashboard-text,
    /* Hide the text inside h2 when collapsed */
    .sidebar.collapsed li span {
        display: none;
    }

    .sidebar ul li {
        display: flex;
        align-items: center;
    }

    .sidebar ul li i {
        font-size: 25px;
        width: 30px;
        text-align: center;
    }

    .sidebar.collapsed a {
        text-align: center;
        padding: 10px 0;
    }

    .sidebar.collapsed h2 {
        justify-content: center;
        /* Center icon when collapsed */
    }

    .content {
        flex: 1;
        box-sizing: border-box;
        background-color: white;
        overflow-y: auto;
    }

    .sidebar a:hover {
        background-color: #34495E;
        /* Hover Color */
    }

    .sidebar ul li a.active {
        color: #1b98e0;
        border-left: 1px solid #ffff;
        font-weight: bold;
    }

    .sidebar ul li a {
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<div class="sidebar" id="sidebar">
    <button class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-chevron-left"></i>
    </button>
    <h2><span id="lhg-text">LHG</span> <span class="dashboard-text">DASHBOARD</span></h2>
    <ul>
        <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> <span>Users</span></a></li>
        <li><a href="#"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="#"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
        <li><a href="#"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
        <li><a href="#"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
    }

    function setActiveLink() {
        const links = document.querySelectorAll('.sidebar a'); // select all links inside the sidebar
        links.forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    window.addEventListener('load', setActiveLink);
</script>