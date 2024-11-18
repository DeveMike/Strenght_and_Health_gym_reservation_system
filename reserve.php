<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Et ole kirjautunut sisään.']);
    exit;
}

// Sisällytetään tietokantayhteyden luomisen tiedosto
require 'includes/dbconnect.php';
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$customerId = $_SESSION['user_id'];
$classId = $_POST['class_id'];



// Tarkista ensin, onko käyttäjällä jo varaus kyseiselle tunnille
$stmt = $conn->prepare("SELECT * FROM varaukset WHERE customer_id = ? AND class_id = ?");
$stmt->execute([$customerId, $classId]);

if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Sinulla on jo varaus tälle tunnille.']);
    exit;
}

try {
    $conn->beginTransaction();

    /* // Tarkista sitten, onko käyttäjällä varaus mille tahansa tunnille
    $stmt = $conn->prepare("SELECT * FROM varaukset WHERE customer_id = ?");
    $stmt->execute([$customerId]);

    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Sinulla on jo varaus toiselle tunnille.']);
        exit();
    } */

    // Jos kumpikin tarkistus on läpäisty, yritä tehdä varaus
    $stmt = $conn->prepare("INSERT INTO varaukset (customer_id, class_id, booking_datetime) VALUES (?, ?, NOW())");
    if ($stmt->execute([$customerId, $classId])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Tietokantavirhe.']);
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Tietokantavirhe.']);
    exit;
}
