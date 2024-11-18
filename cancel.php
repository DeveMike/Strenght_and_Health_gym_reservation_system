<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Et ole kirjautunut sisään.']);
    exit;
}

require 'includes/dbconnect.php';
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userId = $_SESSION['user_id'];
$classId = $_POST['class_id'];
$userRole = $_SESSION['role'];

if (empty($classId) || !is_numeric($classId)) {
    echo json_encode(['success' => false, 'message' => 'Virheellinen class_id.']);
    exit;
}

try {
    $conn->beginTransaction();

    // Hae ensin kaikki käyttäjät, jotka ovat varanneet tunnin
    $users = $conn->prepare("SELECT customer_id FROM varaukset WHERE class_id = ?");
    $users->execute([$classId]);
    $usersToNotify = $users->fetchAll(PDO::FETCH_ASSOC);

    if ($userRole === 'customer') {
        // Yritä peruuttaa käyttäjän varaus kyseiselle tunnille
        $stmt = $conn->prepare("DELETE FROM varaukset WHERE customer_id = ? AND class_id = ?");
        $stmt->execute([$userId, $classId]);
    } elseif ($userRole === 'instructor') {
        // Poista ensin kaikki tunnin varaukset
        $stmt = $conn->prepare("DELETE FROM varaukset WHERE class_id = ?");
        $stmt->execute([$classId]);

        // Luo ilmoitukset kaikille käyttäjille, jotka olivat varanneet tunnin
        foreach ($usersToNotify as $user) {
            $stmt = $conn->prepare("INSERT INTO ilmoitukset (user_id, viesti, luettu, luotu) VALUES (?, ?, 0, NOW())");
            $stmt->execute([$user['customer_id'], 'Valitettavasti tunti, jonka varasit, on peruutettu.']);
        }

        // Sitten poista itse tunti
        $stmt = $conn->prepare("DELETE FROM Jumpat WHERE instructor_id = ? AND class_id = ?");
        $stmt->execute([$userId, $classId]);
    }

    // Tarkista, että ainakin yksi rivi on vaikuttunut (peruutettu tai poistettu)
    if ($stmt->rowCount() > 0) {
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Varaus tai tunti on peruutettu onnistuneesti.']);
    } else {
        // Jos ei rivejä vaikuttunut, peruuta transaktio ja anna virheilmoitus
        $conn->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Varausta tai tuntia ei löydy, tai sinulla ei ole oikeuksia sen poistamiseen.'
        ]);
    }
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Tietokantavirhe: ' . $e->getMessage()]);
    exit;
}
