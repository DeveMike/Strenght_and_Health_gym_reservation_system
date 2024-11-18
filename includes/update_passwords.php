<!-- <?php

        $host = '127.0.0.1';
        $db = 'varausjarjestelma';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $options);

            // Päivitä asiakkaiden salasanat
            $stmt = $pdo->query("SELECT customer_id, password FROM Asiakkaat");
            while ($row = $stmt->fetch()) {
                $hashedPassword = password_hash($row['password'], PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE Asiakkaat SET password = :password WHERE customer_id = :id");
                $updateStmt->execute(['password' => $hashedPassword, 'id' => $row['customer_id']]);
            }

            // Päivitä ohjaajien salasanat
            $stmt = $pdo->query("SELECT instructor_id, password FROM Ohjaajat");
            while ($row = $stmt->fetch()) {
                $hashedPassword = password_hash($row['password'], PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE Ohjaajat SET password = :password WHERE instructor_id = :id");
                $updateStmt->execute(['password' => $hashedPassword, 'id' => $row['instructor_id']]);
            }

            echo "Asiakkaiden ja ohjaajien salasanat päivitetty onnistuneesti!";
        } catch (PDOException $e) {
            echo "Tietokantavirhe: " . $e->getMessage();
        }
        ?> -->