<?php
session_start();
require 'includes/dbconnect.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$customerId = $_SESSION['user_id']; // Käyttäjän ID sessiosta


// Tarkistetaan CSRF-token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error_message'] = 'Virheellinen CSRF-token.';
    header('Location: update_profile.php');
    exit;
}

// Tarkistetaan ensin, onko muutaSalasana-nappia painettu
if (isset($_POST['muutaSalasana'])) {
    $nykyinenSalasana = htmlspecialchars($_POST['nykyinenSalasana']);
    $uusiSalasana = htmlspecialchars($_POST['uusiSalasana']);
    $uusiSalasanaUudelleen = htmlspecialchars($_POST['uusiSalasanaUudelleen']);

    if (!empty($nykyinenSalasana) && !empty($uusiSalasana) && !empty($uusiSalasanaUudelleen)) {
        if ($uusiSalasana === $uusiSalasanaUudelleen && strlen($uusiSalasana) >= 6) {
            $stmt = $conn->prepare("SELECT password FROM Asiakkaat WHERE customer_id = :customerId");
            $stmt->execute(['customerId' => $customerId]);
            $user = $stmt->fetch();

            if (password_verify($nykyinenSalasana, $user['password'])) {
                $uusiSalasanaHash = password_hash($uusiSalasana, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE Asiakkaat SET password = :newPassword WHERE customer_id = :customerId");
                $updateStmt->execute([
                    'newPassword' => $uusiSalasanaHash,
                    'customerId' => $customerId
                ]);
                $_SESSION['success_message'] = 'Salasana on päivitetty onnistuneesti.';
            } else {
                $_SESSION['error_message'] = 'Nykyinen salasana on väärin.';
            }
        } else {
            $_SESSION['error_message'] = 'Uuden salasanan tulee olla vähintään 6 merkkiä pitkä ja salasanojen tulee täsmätä.';
        }
    } else {
        $_SESSION['error_message'] = 'Kaikki salasanan muutoskentät tulee täyttää.';
    }
    header('Location: update_profile.php');
    exit;
}

// Sitten tarkistetaan, onko tallennaMuutokset-nappia painettu
if (isset($_POST['tallennaMuutokset'])) {
    $katuosoite = htmlspecialchars($_POST['katuosoite']);
    $kaupunki = htmlspecialchars($_POST['kaupunki']);
    $postinumero = htmlspecialchars($_POST['postinumero']);
    $puhelin = htmlspecialchars($_POST['puhelin']);
    $yhdistettyOsoite = $katuosoite . ', ' . $kaupunki . ', ' . $postinumero;

    try {
        $updateStmt = $conn->prepare("UPDATE Asiakkaat SET address = :address, phone = :phone WHERE customer_id = :customerId");
        $updateStmt->execute([
            'address' => $yhdistettyOsoite,
            'phone' => $puhelin,
            'customerId' => $customerId
        ]);
        $_SESSION['success_message'] = 'Yhteystiedot on päivitetty onnistuneesti.';
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Yhteystietojen päivitys epäonnistui: ' . $e->getMessage();
    }
    header('Location: update_profile.php');
    exit;
}
// Lähetetään vahvistussähköposti
$to = $email;
$subject = 'Tilin vahvistus';
$message = "Hei $name, kiitos rekisteröitymisestäsi.\n\nVahvista sähköpostiosoitteesi klikkaamalla tästä linkistä.";
$headers = 'From: webmaster@example.com' . "\r\n" .
    'Reply-To: webmaster@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
