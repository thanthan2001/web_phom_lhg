<?php
include "session_start.php";



// echo 'echo ' . $_SESSION['ma_sinh_vien'];

include '../configs/conn.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
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
        background-color: #333;
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

    .sidebar.collapsed h2 .dashboard-text,
    /* Hide the text inside h2 when collapsed */
    .sidebar.collapsed li span {
        display: none;
    }

    .sidebar ul li i {
        font-size: 25px;
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
        padding: 20px;
        box-sizing: border-box;
        background-color: white;
        overflow-y: auto;
    }

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

<body>
    <div class="wrapper"> <?php include_once '../public/partials/sidebar.php' ?>

        <div class="content">
            <div class="dashboard-header">
                <h1>Quản lí User</h1>
            </div>

        </div>



    </div>
</body>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
    }
</script>

</html>