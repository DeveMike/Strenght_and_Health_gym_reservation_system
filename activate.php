<?php
// Käynnistää uuden tai jatkaa olemassa olevaa istuntoa.
session_start();

// Tuo tietokantayhteyden luova skripti.
require 'includes/dbconnect.php';

// Tarkistaa, onko aktivointikoodi lähetetty GET-pyynnöllä.
if (isset($_GET['code']) && !empty($_GET['code'])) {
    $activation_code = $_GET['code'];

    try {
        // Tarkistaa, löytyykö aktivointikoodia tietokannasta.
        $stmt = $conn->prepare("SELECT * FROM Asiakkaat WHERE activation_code = ?");
        $stmt->execute([$activation_code]);
        $user = $stmt->fetch();

        if ($user) {
            // Aktivoi käyttäjän tilin.
            $updateStmt = $conn->prepare("UPDATE Asiakkaat SET activated = 1 WHERE activation_code = ?");
            $updateStmt->execute([$activation_code]);

            $_SESSION['success_message'] = 'Tilisi on nyt aktivoitu. Voit kirjautua sisään.';
        } else {
            $_SESSION['error_message'] = 'Virheellinen aktivointikoodi.';
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Tietokantavirhe: ' . $e->getMessage();
    }
} else {
    $_SESSION['error_message'] = 'Aktivointikoodia ei annettu.';
}

// Sisällyttää navigointipalkin.
include 'navbar.php';

?>

<!DOCTYPE html>
<html lang="fi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="activate.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <title>Tilin Aktivointi</title>
</head>
<body>

<div class="body">
<div class="container">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; ?>
        </div>
    <?php
        unset($_SESSION['success_message']);
    endif;
    ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error_message']; ?>
        </div>
    <?php
        unset($_SESSION['error_message']);
    endif;
    ?>
</div>
</div>

<?php
// Sisällyttää alatunnisteen.
include 'footer.php';
?>

</body>
</html>
