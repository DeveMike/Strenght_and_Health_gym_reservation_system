<?php
session_start();

require 'includes/dbconnect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lomakkeen kenttien arvot
    $name = $_POST['name'];
    $description = $_POST['description'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $capacity = $_POST['capacity'];
    $address = $_POST['address'];
    $instructor_id = $_SESSION['user_id'];

    // Lisää tunti tietokantaan
    $sql = "INSERT INTO Jumpat
    (name, instructor_id, description, start_time, end_time, capacity, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql); // Käytä $conn muuttujaa

    if ($stmt->execute([$name, $instructor_id, $description, $start_time, $end_time, $capacity, $address])) {
        // Tunnin lisäys onnistui
        header('Location: instructor.php?status=success');
    } else {
        // Tunnin lisäys epäonnistui
        header('Location: instructor.php?status=error');
    }
    exit;
}
