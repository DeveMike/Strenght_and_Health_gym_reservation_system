<?php
session_start();

function recordAuditLog($userId, $userType, $action, $email)
{
    global $conn;
    $customerColumn = $userType === 'customer' ? $userId : null;
    $instructorColumn = $userType === 'instructor' ? $userId : null;
    $stmt = $conn->prepare("INSERT INTO Audit_Log
    (customer_id, instructor_id, action_type, ip_address, user_agent, email)
    VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $customerColumn, $instructorColumn, $action, $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT'], $email
    ]);
}

// Tarkista, onko käyttäjä kirjautunut sisään
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    require 'includes/dbconnect.php'; // Varmista, että tietokantayhteys on olemassa
    recordAuditLog($_SESSION['user_id'], $_SESSION['role'], 'logout', $_SESSION['email']);
}

session_destroy(); // Tuhoaa kaikki istunnon muuttujat
header("Location: etusivu.php"); // Ohjaa takaisin etusivulle
exit();
