<?php
// Käynnistetään output buffering.
// Tämä tallentaa kaiken skriptin tulosteen välimuistiin sen sijaan, että se lähetettäisiin suoraan selaimelle.
// Tämä mahdollistaa otsikoiden (headers) muokkaamisen myöhemmin skriptin suorituksen aikana,
// koska todellista lähetystä selaimelle ei ole vielä tapahtunut.
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Aloittaa uuden tai jatkaa olemassa olevaa PHP-sessiota
// 'session_start()' on välttämätön kutsua ennen kuin pääsee käsiksi $_SESSION-muuttujiin.
// Tämä funktio varmistaa, että käyttäjän sessiotiedot ovat saatavilla tällä sivulla.
session_start();

// Haetaan käyttäjän tunniste (user_id) sessiosta
// Tarkistetaan ensin, onko 'user_id' asetettu $_SESSION-muuttujaan.
// Jos 'user_id' on olemassa sessiossa, käytetään sitä arvoa.
// Muussa tapauksessa 'user_id' asetetaan arvoksi null, mikä tarkoittaa, että käyttäjä ei ole kirjautunut sisään tai tunnistetta ei ole asetettu.
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Sisällytä tietokantayhteyden tiedot
require 'includes/dbconnect.php';

include 'csp-header.php';


// Sisällytä viikonpäivät generoiva tiedosto
include_once 'get_weekdays.php';


// Haetaan jumppatuntien tiedot, ohjaajan nimi ja varausmäärä yhdessä kyselyssä
// Valitaan kaikki sarakkeet 'Jumpat'-taulusta
// Liitetään ohjaajan nimi 'Ohjaajat'-taulusta
// Lasketaan varauksien määrä ja käytetään 0, jos ei varauksia
// Jumpat taulu, josta haetaan tietoja
// Liitetään 'Ohjaajat'-taulu 'Jumpat'-tauluun ohjaajan ID:n perusteella
// Valitaan luokan ID
/*  Tässä osiossa luodaan alikysely (subquery) laskemaan kunkin tunnin varausmäärä.
'COUNT(*)' laskee jokaisen varauksen määrän tietylle tunnille 'varaukset' taulussa.
 Tämä laskettu määrä nimetään 'reservation_count', jota käytetään tuloksissa tunnin varausmäärän esittämiseen.
 Huomaa, että 'reservation_count' ei ole suoraan tietokannassa oleva sarake, vaan se luodaan tässä kyselyssä.*/
// Taulu, josta varaukset lasketaan
// Ryhmitellään tulokset luokan ID:n perusteella
// Liitetään varausmäärätiedot 'Jumpat'-tauluun luokan ID:n perusteella
$stmt = $conn->prepare("
    SELECT
        j.*,
        o.name as instructor_name,
        COALESCE(v.reservation_count, 0) as reservation_count
    FROM
        Jumpat j
    JOIN
        Ohjaajat o ON j.instructor_id = o.instructor_id
    LEFT JOIN (
        SELECT
            class_id,
            COUNT(*) as reservation_count
        FROM
            varaukset
        GROUP BY
            class_id
    ) as v ON j.class_id = v.class_id
");

$stmt->execute(); // Suoritetaan SQL-kysely
$result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Haetaan kaikki tulokset assosiatiivisena taulukkona

//Räätälöidään kyselyt jotka näyttää "script" kaikki kaupungin olevat tiedot

// Haetaan kaikki uniikit kaupungit osoite-sarakkeesta
// Tässä käytetään SUBSTRING_INDEX-funktiota erottelemaan kaupungin nimi osoitteen lopusta.
// 'address' sarakkeesta otetaan viimeinen sana, joka asetetaan kaupungin nimeksi.
$cityQuery = "SELECT DISTINCT SUBSTRING_INDEX(address, ' ', -1) as city FROM Jumpat";
$cityStmt = $conn->prepare($cityQuery);
$cityStmt->execute();
$cityResult = $cityStmt->fetchAll();

// Haetaan kaikki uniikit osoitteet
// Tämä kysely valitsee kaikki tietyt osoitteet 'Jumpat' taulusta, joka vähentää toistuvia tietoja.
$addressQuery = "SELECT DISTINCT address FROM Jumpat";
$addressStmt = $conn->prepare($addressQuery);
$addressStmt->execute();
$addressResult = $addressStmt->fetchAll();

// Haetaan kaikki uniikit tunnin nimet
// Tässä kyselyssä valitaan tietyt 'name' sarakkeen arvot 'Jumpat' taulusta.
$classNameQuery = "SELECT DISTINCT name FROM Jumpat";
$classNameStmt = $conn->prepare($classNameQuery);
$classNameStmt->execute();
$classNameResult = $classNameStmt->fetchAll();

// Haetaan kaikki ohjaajat
// Tässä kyselyssä haetaan kaikki ohjaajien nimet ja niiden tunnisteet 'Ohjaajat' taulusta.
// Tämä antaa listan kaikista saatavilla olevista ohjaajista.
$instructorQuery = "SELECT instructor_id, name FROM Ohjaajat";
$instructorStmt = $conn->prepare($instructorQuery);
$instructorStmt->execute();
$instructorResult = $instructorStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="varaus.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="navbar.css">
    <title>Varaukset</title>
</head>

<body>

    <?php include_once 'navbar.php'; ?>

    <div class="content-container">
        <!-- Vasen laatikko -->
        <?php include_once 'search_container_varaus.php'; ?>
        <div class="classes-container">
            <?php echo generateWeekdays(); ?>
            <div class="classes-list">
                <?php if (count($result) > 0) : ?>

                    <?php
                    $finnishMonths = [
                        1 => "Tammi", 2 => "Helmi", 3 => "Maalis", 4 => "Huhti",
                        5 => "Touko", 6 => "Kesä", 7 => "Heinä", 8 => "Elo",
                        9 => "Syys", 10 => "Loka", 11 => "Marras", 12 => "Joulu"
                    ];
                    ?>

                    <?php foreach ($result as $row) : ?>
                        <?php
                        $startDate = new DateTime($row["start_time"]);
                        $endDate = new DateTime($row["end_time"]);
                        $formattedDate = sprintf(
                            '%d %s | %s - %s',
                            $startDate->format('j'),
                            $finnishMonths[$startDate->format('n')],
                            $startDate->format('H:i'),
                            $endDate->format('H:i')
                        );

                        $stmt = $conn->prepare("SELECT * FROM
                        varaukset WHERE customer_id = :user_id AND class_id = :class_id");
                        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                        $stmt->bindParam(':class_id', $row["class_id"], PDO::PARAM_INT);
                        $stmt->execute();
                        $reservation = $stmt->fetch();
                        $buttonText = $reservation ? "Peruuta" : "Varaa";
                        $buttonClass = $reservation ? "booked" : "";
                        ?>

                        <div class="class-card" data-class-id="<?= htmlspecialchars($row["class_id"]) ?>">
                            <div class="class-info">
                                <div class="date-time"><?= htmlspecialchars($formattedDate) ?></div>
                                <div class="name">
                                    <?= htmlspecialchars($row["name"]) ?>
                                    <?= htmlspecialchars($row["reservation_count"]) ?>/
                                    <?= htmlspecialchars($row["capacity"]) ?></div>
                                <div class="location">Kuntosali: <?= htmlspecialchars($row["address"]) ?></div>
                                <div class="instructor"><?= htmlspecialchars($row["instructor_name"]) ?></div>
                            </div>
                            <div class="class-actions">
                                <button class="info-btn">Info</button>
                                <div class="info-section"><?= htmlspecialchars($row["description"]) ?></div>
                                <button class="book-btn <?= htmlspecialchars($buttonClass) ?>">
                                    <?= htmlspecialchars($buttonText) ?></button>
                            </div>
                        </div>

                    <?php endforeach; ?>

                <?php else : ?>
                    <p>No classes found.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

    <script src="varaus.js"></script>

</body>

</html>
<?php
// Lopetetaan output buffering ja lähetetään kaikki välimuistiin tallennettu sisältö selaimelle.
// Tämä funktio tyhjentää output bufferin ja lähettää sen sisällön selaimelle.
// Tämä on tärkeää, koska se varmistaa, että kaikki skriptin tuottama sisältö
// todella näytetään käyttäjälle.
ob_end_flush();
?>