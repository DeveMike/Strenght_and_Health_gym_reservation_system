<?php
?>

<nav class="navbar">
   <a href="etusivu.php" class="specific-link">
  <img src="assets/Asset 5.png" alt="Logo" class="logo">
</a>
    <ul class="nav-links">
        <li><a href="#">Toimipisteet</a></li>
        <li class="has-dropdown">
            <a href="#">
                Palvelut
                <img src="assets/Infolaunch.svg" alt="Icon" class="icon">
            </a>
            <div class="submenu">
                <ul>
                    <li><a href="#">Ryhmäliikunta</a></li>
                    <li><a href="#">Personal Trainer</a></li>
                    <li><a href="#">Vinkit ja Treenit</a></li>
                </ul>
            </div>
        </li>
        <li class="has-dropdown">
            <a href="#">
                Jäsennyys
                <img src="assets/Infolaunch.svg" alt="Icon" class="icon">
            </a>
            <div class="submenu">
                <ul>
                    <li><a href="#">Hinnasto</a></li>
                </ul>
            </div>
        </li>
        <li><a href="#">Ota yhteyttä</a></li>
    </ul>
    <div class="buttons">
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'instructor') {
            // Ohjaaja on kirjautunut sisään
            echo '<a href="logout.php" class="login-button">Kirjaudu ulos</a>';
            echo '<a href="instructor.php" class="join-button">Oma tili</a>';
        } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'customer') {
            // Asiakas on kirjautunut sisään
            echo '<a href="logout.php" class="login-button">Kirjaudu ulos</a>';
            echo '<a href="customer.php" class="join-button">Oma tili</a>';
        } else {
            // Kukaan ei ole kirjautunut sisään
            echo '<a href="login.php" class="login-button">Kirjaudu sisään</a>';
            echo '<a href="join.php" class="join-button">Liity Jäseneksi</a>';
        }

        ?>
    </div>

    <div class="burger-menu">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>

    <div class="nav-overlay"></div>

</nav>
<script src="navbar.js"></script>