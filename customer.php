<?php

/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */

session_start();

require 'includes/dbconnect.php';

include 'csp-header.php';


// Tarkista, onko käyttäjä kirjautunut sisään
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$name = $_SESSION['name'];
$role = $_SESSION['role'];

require_once 'includes/encryption.php';

/* function encryptCustomerData($conn)
{
    // Haetaan kaikki asiakastiedot
    $query = "SELECT customer_id, name, email, phone, address FROM Asiakkaat";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Katkaistaan IV 16 merkkiin
    $iv = substr(ENCRYPTION_IV, 0, 16);

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Salataan tiedot käyttäen katkaistua IV:tä
        $encryptedName = openssl_encrypt($row['name'], 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
        $encryptedEmail = openssl_encrypt($row['email'], 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
        $encryptedPhone = openssl_encrypt($row['phone'], 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);
        $encryptedAddress = openssl_encrypt($row['address'], 'AES-256-CBC', ENCRYPTION_KEY, 0, $iv);

        // Päivitetään salatut tiedot tietokantaan
        $updateQuery = "UPDATE Asiakkaat SET 
                        name = ?, 
                        email = ?, 
                        phone = ?, 
                        address = ? 
                        WHERE customer_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$encryptedName, $encryptedEmail, $encryptedPhone, $encryptedAddress, $row['customer_id']]);
    }

    echo "All customer data encrypted successfully.";
}

// Kutsu funktiota
encryptCustomerData($conn); */


/* function decryptAndSaveCustomerData($conn)
{
    // Haetaan kaikki salatut asiakastiedot
    $query = "SELECT customer_id, name, email, phone, address FROM Asiakkaat";
    $stmt = $conn->prepare($query);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Puretaan salaus
        $decryptedName = openssl_decrypt($row['name'], 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
        $decryptedEmail = openssl_decrypt($row['email'], 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
        $decryptedPhone = openssl_decrypt($row['phone'], 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);
        $decryptedAddress = openssl_decrypt($row['address'], 'AES-256-CBC', ENCRYPTION_KEY, 0, ENCRYPTION_IV);

        // Tarkista, onnistuiko salauksen purkaminen
        if ($decryptedName === false || $decryptedEmail === false || $decryptedPhone === false || $decryptedAddress === false) {
            die("Error decrypting data");
        }

        // Päivitetään selväkieliset tiedot tietokantaan
        $updateQuery = "UPDATE Asiakkaat SET 
                        name = ?, 
                        email = ?, 
                        phone = ?, 
                        address = ? 
                        WHERE customer_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$decryptedName, $decryptedEmail, $decryptedPhone, $decryptedAddress, $row['customer_id']]);
    }

    echo "All customer data decrypted and updated successfully.";
}

// Kutsu funktiota
decryptAndSaveCustomerData($conn); */


// Tarkistetaan, onko käyttäjällä lukemattomia ilmoituksia
$ilmoituksetQuery = "SELECT * FROM ilmoitukset WHERE user_id = ? AND luettu = 0";
$ilmoituksetStmt = $conn->prepare($ilmoituksetQuery);
$ilmoituksetStmt->execute([$user_id]);

$ilmoitukset = $ilmoituksetStmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($ilmoitukset)) {
    // Asetetaan ensimmäinen ilmoitus virheviestiksi
    $_SESSION['error_message'] = $ilmoitukset[0]['viesti'];

    // Merkitään ilmoitukset luetuiksi tietokannassa
    foreach ($ilmoitukset as $ilmoitus) {
        $merkintaQuery = "UPDATE ilmoitukset SET luettu = 1 WHERE ilmoitus_id = ?";
        $merkintaStmt = $conn->prepare($merkintaQuery);
        $merkintaStmt->execute([$ilmoitus['ilmoitus_id']]);
    }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="customer.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="navbar.css">
    <title>Main Page</title>
</head>

<body>
    <?php include_once 'navbar.php' ?>

    <?php if (isset($_SESSION['error_message'])) : ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error_message']; ?>
            <?php unset($_SESSION['error_message']); // Poistetaan viesti sessiosta 
            ?>
        </div>
    <?php endif; ?>


    <section class="user-profile">
        <div class="info-box1">Name:
            <?php echo $name; ?>
        </div>
        <div class="info-box2">Membership: Student</div>
        <div class="info-box3">Email:
            <?php echo $email; ?>
        </div>
        <div class="info-box4">Status: Yellow</div>
        <a href="update_profile.php" class="edit-profile-link">Muokkaa profiilia</a>
    </section>

    <div class="membership-details">
        <div class="day-element" id="day-number">Days: </div>
        <button class="cancel-button">Irtisanoudu</button>
        <div class="membership-price">24.90€/kk</div>
    </div>

    <div class="white-section">
        <div class="content-container">
            <div class="box">
                <a href="varaus.php" class="title-link">
                    <h2 class="title">Varaukset</h2>
                </a>
                <div class="black-box reservations-box">
                    <div class="reservations-container">
                    </div>
                </div>
            </div>
            <div class="box">
                <h2 class="title">Maksutiedot</h2>
                <div class="black-box payment-details-box scrollable">
                    <div class="info-box">
                        Tuote: Basicmonth 29,95 €
                        <div>
                            <span>Summa: 29,95 €</span>
                            <span>Tila: <span class="payment-status">Maksettu</span></span>
                        </div>
                    </div>
                    <div class="info-box">
                        Tuote: Basicmonth 29,95 €
                        <div>
                            <span>Summa: 29,95 €</span>
                            <span>Tila: <span class="payment-status">Maksettu</span></span>
                        </div>
                    </div>
                    <div class="info-box">
                        Tuote: Basicmonth 29,95 €
                        <div>
                            <span>Summa: 29,95 €</span>
                            <span>Tila: <span class="payment-status">Maksettu</span></span>
                        </div>
                    </div>
                    <div class="info-box">
                        Tuote: Basicmonth 29,95 €
                        <div>
                            <span>Summa: 29,95 €</span>
                            <span>Tila: <span class="payment-status">Maksettu</span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="customer.js"></script>
    <script src="join.js"></script>

    <?php include_once 'footer.php' ?>

</body>

</html>