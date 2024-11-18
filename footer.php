<?php
?>

<footer class="footer">
    <div class="footer-logo">
        <img src="assets/Asset 7.png" alt="Logo" width="369" height="76">
    </div>
    
    <div class="footer-sections">
    <div class="footer-section">
        <h4>Meistä</h4>
        <ul>
            <li><a href="#">Töihin meille</a></li>
            <li><a href="#">Historia</a></li>
            <li><a href="#">Asiakaspalvelu</a></li>
        </ul>
    </div>

    <div class="footer-section">
        <h4>Tuki</h4>
        <ul>
            <li><a href="#">Jäsenhinnasto</a></li>
            <li><a href="assets/docs/Tietosuojaseloste.pdf" target="_blank">Tietosuojaseloste</a></li>
            <li><a href="assets/docs/SäännötjaEhdotStrength.pdf" target="_blank">Säännöt ja Ehdot</a></li>
        </ul>
    </div>

    <div class="footer-section first-section">
        <h4>Yhteystiedot</h4>
        <p>Fitnessskuja 12<br>00100 Helsinki</p>
    </div>
    </div>

    <div class="footer-buttons">
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'instructor') {
            // Ohjaaja on kirjautunut sisään
            echo '<a href="logout.php" class="flogin-button">Kirjaudu ulos</a>';
            echo '<a href="instructor.php" class="fjoin-button">Oma tili</a>';
        } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'customer') {
            // Asiakas on kirjautunut sisään
            echo '<a href="logout.php" class="flogin-button">Kirjaudu ulos</a>';
            echo '<a href="customer.php" class="fjoin-button">Oma tili</a>';
        } else {
            // Kukaan ei ole kirjautunut sisään
            echo '<a href="login.php" class="flogin-button">Kirjaudu sisään</a>';
            echo '<a href="join.php" class="fjoin-button">Liity Jäseneksi</a>';
        }

        ?>
    </div>
    <div class="footer-line"></div>

    <div class="footer-icons">
        <p>Seuraa meitä:</p>
        <img src="assets/footer_icon/instagram.svg" alt="Icon 1">
        <img src="assets/footer_icon/twitter.svg" alt="Icon 2">
        <img src="assets/footer_icon/github.svg" alt="Icon 3">
        <img src="assets/footer_icon/linkedin.svg" alt="Icon 4">
    </div>
    <div class="footer-text">
        © Strength & Health. 2024. Healthy AF!
    </div>
</footer>