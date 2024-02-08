<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] 
=== true) {
    // User logged in, display user navbar
    echo '<nav>
            <ul>
                <li><a href="profile.php">Profile</a><li>
                <li><a href="profile.php">Logout</a><li>
                <!-- Other Links -->
            </ul>
        </nav>';
} else {
    echo '<nav>
            <ul>
                <li><a href="profile.php">Profile</a><li>
                <li><a href="profile.php">Logout</a><li>
                <!-- Other Links -->
            </ul>
        </nav>';
}
?>