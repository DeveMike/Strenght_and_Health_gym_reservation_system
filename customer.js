// Generoidaan satunnainen numero 1-10000
document.getElementById("day-number").textContent += Math.floor(Math.random() * 10000) + 1;

fetch('getUserReservations.php')
    .then(response => response.json())
    .then(data => {
        if (!data.reservations) {
            console.error('Varauksia ei löytynyt palvelimen vastauksesta:', data);
        } else {
            updateReservationsUI({ reservations: data.reservations });
        }
    })
    .catch(error => console.error('Virhe ladattaessa varauksia:', error))

// Funktio päivittää UI:n käyttäjän varauksilla
// Päivittää varaukset käyttöliittymässä
function updateReservationsUI(data) {
    // Tulostaa saadut tiedot palvelimelta
    console.log("Saadut tiedot palvelimelta:", data);

    // Haetaan varausten näyttöelementti
    const resultsDiv = document.querySelector('.reservations-container');
    resultsDiv.innerHTML = '';

    // Tarkistetaan, onko varauksia näytettävissä
    if (!data.reservations || data.reservations.length === 0) {
        console.log("Ei varauksia näytettäväksi.");
        resultsDiv.innerHTML = '<p style="color: white;">Ei varauksia</p> <a href="varaus.php" class="booking-link">Varaa tunti</a><div class="plus-icon">+</div>';
        return;
    }
    console.log("Varausdata:", data.reservations);

    // Käydään läpi kaikki varaukset ja luodaan niille UI-elementit
    data.reservations.forEach(reservation => {
        console.log("Käsitellään varausta:", reservation);

        // Luodaan div-elementti varaukselle
        const div = document.createElement('div');
        div.classList.add('class-card');
        div.setAttribute('data-class-id', reservation.class_id);
        div.setAttribute('data-booking-id', reservation.booking_id);
        div.setAttribute('data-instructor-id', reservation.instructor_id);

        // Suomalaiset kuukaudet
        const finnishMonths = ["", "Tammi", "Helmi", "Maalis", "Huhti", "Touko", "Kesä", "Heinä", "Elo", "Syys", "Loka", "Marras", "Joulu"];

        // Muunnetaan aloitus- ja lopetusaika Date-objekteiksi
        const startDate = new Date(reservation.class_start_time);
        const endDate = new Date(reservation.class_end_time);

        // Muotoillaan aloitus- ja lopetusaika selkeämpään muotoon
        const formattedStartDate = `${startDate.getDate()} ${finnishMonths[startDate.getMonth() + 1]} | ${startDate.getHours()}:${startDate.getMinutes().toString().padStart(2, '0')} - ${endDate.getHours()}:${endDate.getMinutes().toString().padStart(2, '0')}`;

        // Asetetaan varausdatan HTML-sisältö
        div.innerHTML = `
        <div class="class-info">
        <div class="date-time">${formattedStartDate}</div>
            <div class="booking-id">Varaus ID: ${reservation.booking_id}</div>
            <div class="instructor-name">Ohjaaja: ${reservation.instructor_name}</div>
                <div class="name">${reservation.name}</div>
                <div class="location">Kuntosali: ${reservation.address}</div>
            </div>
            <div class="class-actions">
                <button class="info-btn">Info</button>
                <div class="info-section">${reservation.description}</div>
                <button class="cancel-btn">Peruuta</button>
            </div>
        `;

        // Lisätään varauskortti näkymään
        resultsDiv.appendChild(div);
        console.log("Varauskortti lisätty:", div);

        // Funktio tapahtumankuuntelijoiden lisäämiseen 'Peruuta'-napeille
        function addCancelListeners() {
            console.log("Lisätään tapahtumankuuntelijat 'Peruuta' napeille");

            // Käydään läpi kaikki 'Peruuta'-napit
            document.querySelectorAll('.cancel-btn').forEach(btn => {
                console.log("Käsitellään nappi:", btn);

                // Poista vanha tapahtumankuuntelija, jos sellainen on olemassa
                if (btn._clickHandler) {
                    btn.removeEventListener('click', btn._clickHandler);
                }

                // Määritä uusi tapahtumankuuntelija
                btn._clickHandler = function () {
                    const classCard = btn.closest('.class-card');
                    console.log("Class card:", classCard);
                    const classId = classCard.dataset.classId;
                    console.log("Nappia painettu, classId on:", classId);

                    const endpoint = 'cancel.php';
                    const errorMessage = "Varauksen peruuttaminen epäonnistui. Yritä uudelleen.";

                    console.log("Lähetetään pyyntö palvelimelle:", endpoint);
                    fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'class_id=' + classId
                    })
                    .then(response => {
                        console.log('Vastaus palvelimelta:', response);
                        if (!response.ok) {
                            throw new Error('Network response was not ok ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Vastaus palvelimelta (JSON):', data);
                        if (data.success) {
                            // Poista varauskortti käyttöliittymästä
                            classCard.remove();

                            // Jos varauksia ei ole jäljellä, näytä viesti
                            const resultsDiv = document.querySelector('.reservations-container');
                            if (resultsDiv.childElementCount === 0) {
                                resultsDiv.innerHTML = '<p style="color: white;">Ei varauksia</p> <a href="varaus.php" class="booking-link">Varaa tunti</a><div class="plus-icon">+</div>';
                            }
                        } else {
                            console.log("Server did not return success");
                            alert(data.message || errorMessage);
                        }
                    })
                    .catch(error => {
                        console.error('Virhe pyynnön käsittelyssä:', error.message);
                        alert("Jotain meni pieleen. Yritä uudelleen.");
                    });
                    
                };

                // Lisää uusi tapahtumankuuntelija
                btn.addEventListener('click', btn._clickHandler);
            });
        }

        // Lisää tapahtumankuuntelijat 'Peruuta'-napeille
        addCancelListeners();




        // Lisää kuuntelija info-napille
        const infoBtn = div.querySelector('.info-btn');
        infoBtn.addEventListener('click', function() {
            const classCard = infoBtn.closest('.class-card');
            const infoSection = classCard.querySelector('.info-section');

            if (infoSection.style.display === 'none' || infoSection.style.display === '') {
                console.log("Näytetään lisätiedot varaukselle:", reservation);
                infoSection.style.display = 'block';
            } else {
                console.log("Piilotetaan lisätiedot varaukselle:", reservation);
                infoSection.style.display = 'none';
            }
        });
    });

}

//Ilmoituksen/viorheviestin katoaminen 


function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    const navOverlay = document.querySelector('.nav-overlay');

    navLinks.classList.toggle('active');
    navOverlay.style.display = navLinks.classList.contains('active') ? 'block' : 'none';

    navOverlay.addEventListener('click', () => {
        navLinks.classList.remove('active');
        navOverlay.style.display = 'none';
    });

   
}

/* document.querySelector('.sub-menu').addEventListener('click', function() {
    var allLinks = document.querySelectorAll('.nav-links a');
    var submenu = document.querySelector('.submenu');
    if (this.classList.contains('active')) {
        // Kun valikko on aktiivinen
        for (var i = 0; i < allLinks.length; i++) {
            if (allLinks[i].nextElementSibling === submenu) {
                break;
            }
            allLinks[i].style.marginBottom = submenu.offsetHeight + 'px';
        }
    } else {
        // Kun valikko ei ole aktiivinen
        for (var i = 0; i < allLinks.length; i++) {
            allLinks[i].style.marginBottom = '0';
        }
    }
}); */



