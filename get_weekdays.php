<?php
// Tarkistetaan, onko URL:ssa parametri 'updateSchedule' ja onko sen arvo 'true'
// Jos on, suoritetaan viikkotuntien päivitys
if (isset($_GET['updateSchedule']) && $_GET['updateSchedule'] == 'true') {
    updateWeeklySchedule($conn);
}

// Tietokantayhteyden luominen tiedostosta 'includes/dbconnect.php'
require 'includes/dbconnect.php';

// Asetetaan skriptin aikavyöhyke
date_default_timezone_set('Europe/Helsinki');

// Viikkotuntien päivitystoiminnon suorittaminen
// Tämä on oletusarvoisesti suoritettava koodi, jota ei suositella jättää aktiiviseksi
// poista tai kommentoi rivi pois käytöstä välttääksesi automaattiset päivitykset
updateWeeklySchedule($conn, true); // Ei tulosta viestejä


// Funktio viikkotuntien päivittämiseksi
function updateWeeklySchedule($conn, $silentMode = false)
{
    // Määritellään tämänpäiväinen päivämäärä
    $today = new DateTime();
    // Laske seuraavan viikon numero
    $nextWeekNumber = $today->modify('+1 week')->format("W");
    // Tarkistetaan, onko ensi viikolle jo tallennettu tunteja
    $query = "SELECT COUNT(*) FROM Jumpat WHERE WEEK(start_time) = :nextWeekNumber";
    $stmt = $conn->prepare($query);
    $stmt->execute(['nextWeekNumber' => $nextWeekNumber]);

    // Jos ensi viikolle ei ole tunteja, päivitetään nykyiset tunnit ensi viikolle
    if ($stmt->fetchColumn() == 0) {
        try {
            // Päivitetään kaikkien tuntien aloitus- ja lopetusajat seuraavalle viikolle
            $query = "UPDATE Jumpat SET
                        start_time = DATE_ADD(start_time, INTERVAL 7 DAY),
                        end_time = DATE_ADD(end_time, INTERVAL 7 DAY)";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            if (!$silentMode) {
                echo "Tuntien ajat päivitetty onnistuneesti seuraavalle viikolle.\n";
            }
        } catch (PDOException $e) {
            exit("Tietokantavirhe: " . $e->getMessage());
        }
    } else {
        // Jos tunnit ovat jo päivitetty, näytetään viesti
        if (!$silentMode) {
            echo "Tunnit on jo päivitetty tälle viikolle.\n";
        }
    }
}

// Funktio viikonpäivien luomiseen
function generateWeekdays()
{
    $weekdaysHtml = '<div class="weekdays">';
    $currentDay = (new DateTime())->format('N');
    // Suomenkieliset lyhenteet viikonpäiville
    $finnishWeekdays = ['Ma', 'Ti', 'Ke', 'To', 'Pe', 'La', 'Su'];

    // Luo HTML-koodin jokaiselle viikonpäivälle
    for ($i = 1; $i <= 7; $i++) {
        // Asetetaan päivämäärä-objekti tälle viikolle ja i:nnelle päivälle
        $date = new DateTime();
        $date->setISODate((int)$date->format('o'), (int)$date->format('W'), $i);

        // Haetaan viikonpäivän lyhenne suomenkielisestä taulukosta
        $dayLetter = $finnishWeekdays[$i - 1];

        // Haetaan päivän numero kuukaudessa
        $dayNumber = $date->format('j');

        // Muodostetaan täysi päivämäärä vuosi-kuukausi-päivä -muodossa
        $fullDate = $date->format('Y-m-d');

        // Asetetaan 'active' luokka nykyiselle päivälle
        $activeClass = $i == $currentDay ? ' active' : '';

        // Lisätään luotu HTML-elementti viikonpäivien HTML-koodiin
        $weekdaysHtml .= "<div class=\"day$activeClass\" data-day=\"$fullDate\">$dayLetter<br>$dayNumber</div>";
    }
    // Suljetaan viikonpäivien HTML-elementti
    $weekdaysHtml .= '</div>';

    // Palautetaan muodostettu HTML-koodi
    return $weekdaysHtml;
}
