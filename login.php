<?php
// error_reporting(E_ALL);
// ini_set('display_errors', '1');
session_start();

// Luo CSRF-token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Sisällytä tietokantayhteyden tiedot
require 'includes/dbconnect.php';

include 'csp-header.php';


$message = '';

function cleanInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$fetchedUser = null;
$fetchedInstructor = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Tarkistetaan CSRF-tokenin oikeellisuus
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'CSRF token validation failed.';
    } else {
        $email = cleanInput($_POST['email']);
        $password = cleanInput($_POST['password']);

        // Jos käyttäjätunnusta ei löydy
        if (!$fetchedUser && !$fetchedInstructor) {
            $message = 'Käyttäjää ei ole olemassa.';
        }
        // Jos salasana on väärä
        elseif ($fetchedUser && !password_verify($password, $fetchedUser['password'])) {
            $message = 'Väärä salasana tai sähköposti.';
        }
    }
}

function recordAuditLog($userId, $userType, $action, $email = null)
{
    global $conn;
    $customerColumn = $userType === 'customer' ? $userId : null;
    $instructorColumn = $userType === 'instructor' ? $userId : null;
    $stmt = $conn->prepare("INSERT INTO Audit_Log
    (customer_id, instructor_id, action_type, ip_address, user_agent, email) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $customerColumn, $instructorColumn, $action, $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT'], $email
    ]);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Käytä $conn-muuttujaa, joka on määritelty dbconnect.php-tiedostossa
        // Ensin tarkistetaan Asiakkaat-taulusta
        $stmt = $conn->prepare("SELECT * FROM Asiakkaat WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $fetchedUser = $stmt->fetch();

        if ($fetchedUser && password_verify($password, $fetchedUser['password'])) {
            $_SESSION['user_id'] = $fetchedUser['customer_id'];
            $_SESSION['email'] = $fetchedUser['email'];
            $_SESSION['name'] = $fetchedUser['name'];
            $_SESSION['role'] = 'customer';
            recordAuditLog($fetchedUser['customer_id'], 'customer', 'login', $fetchedUser['email']);
            header('Location: customer.php');
            exit;
        } else {
            // Jos ei löydy Asiakkaat-taulusta, tarkistetaan Ohjaajat-taulusta
            $stmt = $conn->prepare("SELECT * FROM Ohjaajat WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $fetchedInstructor = $stmt->fetch();

            if ($fetchedInstructor && password_verify($password, $fetchedInstructor['password'])) {
                $_SESSION['user_id'] = $fetchedInstructor['instructor_id'];
                $_SESSION['email'] = $fetchedInstructor['email'];
                $_SESSION['name'] = $fetchedInstructor['name'];
                $_SESSION['role'] = 'instructor';
                recordAuditLog($fetchedInstructor['instructor_id'], 'instructor', 'login', $fetchedInstructor['email']);
                header('Location: instructor.php');
                exit;
            } else {
                // Epäonnistunut kirjautuminen
                recordAuditLog(null, 'unknown', 'login_attempt', $email);
                $message = 'Väärä salasana tai käyttäjänimi.';
            }
        }
    } catch (PDOException $e) {
        $message = "Tietokantavirhe: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="navbar.css">
    <title>Log in page</title>
</head>

<body>
    <?php require_once 'navbar.php'; ?>

    <?php if ($message) : ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <main class="login-container">
        <div class="login-box">
            <h2>Kirjaudu Sisään</h2>
            <div class="yellow-lines">
                <div class="yellow-line1"></div>
                <div class="yellow-line2"></div>
            </div>
            <h3>Hallinnoi aktiviteettejasi ja jäsennyttäsi</h3>
            <h4 class="small-title">Have a great day</h4>
            <form method="post" action="login.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="input-container">
                    <input type="email" id="email" name="email" required placeholder="Sähköposti" autocomplete="username">
                </div>
                <div class="input-container">
                    <input type="password" id="password" name="password" required placeholder="Salasana" autocomplete="current-password">
                </div>
                <button type="submit" class="form-login-button">Kirjaudu Sisään</button>
                <p class="forgot-password"><img src="assets/Icon/lock-alt-solid 1.svg" alt="Kuvake"> Unohditko
                    salasanasi?</p>

            </form>
        </div>
    </main>

    <script src="login.js">
    </script>

    <?php require_once 'footer.php'; ?>
</body>

</html>