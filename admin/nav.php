<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    nav {
        width: 200px;
        background: white;
        height: 100vh;
        position: fixed;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        animation: slideIn 0.5s ease;
    }

    nav ul {
        list-style: none;
        padding: 2rem 0.5rem;
    }

    nav ul li {
        margin-bottom: 0.5rem;
    }

    nav ul li a {
        display: block;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
        color: #333;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    nav ul li a i {
        margin-right: 10px;
        width: 20px;
    }

    nav ul li a:before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 3px;
        background: #b21f1f;
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    nav ul li a:hover:before {
        transform: scaleY(1);
    }

    nav ul li a:hover {
        background: #f8f9fa;
        padding-left: 1.5rem;
    }

    nav ul li a.active {
        background: #1a2a6c;
        color: white;
    }

    @keyframes slideIn {
        from { transform: translateX(-100%); }
        to { transform: translateX(0); }
    }
</style>
<nav>
    <ul>
        <li><a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-home"></i>Dashboard</a></li>
        <li><a href="categories.php" class="<?= $current_page == 'categories.php' ? 'active' : '' ?>"><i class="fas fa-list"></i>Manage Categories</a></li>
        <li><a href="items.php" class="<?= $current_page == 'items.php' ? 'active' : '' ?>"><i class="fas fa-box"></i>Manage Items</a></li>
        <li><a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>"><i class="fas fa-users"></i>Manage Users</a></li>
        <li><a href="contacts.php" class="<?= $current_page == 'contacts.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i>Contact Page</a></li>
        <li><a href="import.php" class="<?= $current_page == 'import.php' ? 'active' : '' ?>"><i class="fas fa-file-import"></i>Import</a></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
    </ul>
</nav>
