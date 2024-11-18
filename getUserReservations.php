<?php
session_start();

header('Content-Type: application/json');

// Tarkista, että käyttäjä on kirjautunut sisään
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Käyttäjä ei ole kirjautunut sisään']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Yhdistä tietokantaan
require 'includes/dbconnect.php';

// Hae käyttäjän varaukset ja jumppatunnin tiedot
$stmt = $conn->prepare('
    SELECT varaukset.*, Jumpat.name,
    Jumpat.description, Jumpat.start_time as class_start_time,
    Jumpat.end_time as class_end_time, Jumpat.address, Ohjaajat.name as instructor_name
    FROM varaukset
    JOIN Jumpat ON varaukset.class_id = Jumpat.class_id
    JOIN Ohjaajat ON varaukset.instructor_id = Ohjaajat.instructor_id
    WHERE varaukset.customer_id = :user_id
');
$stmt->execute(['user_id' => $user_id]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['reservations' => $reservations]);
