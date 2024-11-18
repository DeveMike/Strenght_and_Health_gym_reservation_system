// Ilmoita, että JS-tiedosto on ladattu konsolissa
console.log("JS-tiedosto ladattu!");

//Staattisen sivun scripti
function addBookingListeners() {
    console.log("addBookingListeners kutsuttu");
    document.querySelectorAll('.book-btn').forEach(btn => {
        console.log("Processing button:", btn);

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

            let endpoint, successMessage, errorMessage, newButtonText, newButtonClass, bookingChange;

            if (btn.classList.contains('booked')) {
                console.log("Button has 'booked' class");
                endpoint = 'cancel.php';
                successMessage = 'Varaus on peruttu onnistuneesti!';
                errorMessage = "Varauksen peruuttaminen epäonnistui. Yritä uudelleen.";
                newButtonText = 'Varaa';
                newButtonClass = '';
                bookingChange = -1;
            } else {
                console.log("Button does not have 'booked' class");
                endpoint = 'reserve.php';
                successMessage = 'Varaus on tehty onnistuneesti! Hyvää Treeniä!';
                errorMessage = "Varauksen tekeminen epäonnistui. Yritä uudelleen.";
                newButtonText = 'Peruuta';
                newButtonClass = 'booked';
                bookingChange = 1;
            }

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
                    console.log("Server returned success", data);
                    btn.textContent = newButtonText;
                    btn.classList.toggle('booked', bookingChange === 1);

                    const currentBookingsElement = classCard.querySelector('.name');
                    const currentCount = parseInt(currentBookingsElement.textContent.split('/')[0].split(' ').pop(), 10);
                    currentBookingsElement.textContent = currentBookingsElement.textContent.replace(currentCount, currentCount + bookingChange);

                    const message = document.createElement('div');
                    message.className = `book-msg ${bookingChange === 1 ? 'green' : 'red'}`;
                    message.textContent = successMessage;
                    classCard.appendChild(message);
                    message.style.display = 'block';

                    setTimeout(() => {
                        classCard.removeChild(message);
                    }, 3000);
                } else {
                    console.log("Server did not return success");
                    alert(data.message || errorMessage);
                }
            })
            .catch(error => {
                console.error('Virhe pyynnön käsittelyssä:', error);
                alert("Jotain meni pieleen. Yritä uudelleen.");
            });
        };

        // Lisää uusi tapahtumankuuntelija
        btn.addEventListener('click', btn._clickHandler);
    });
}

// Lisää tapahtumankuuntelijat 'Varaa' napeille
addBookingListeners();

// Kuuntelija jokaiselle 'info-btn'-napille, joka hallitsee tietojen näyttämistä tai piilottamista.
console.log("Lisätään kuuntelija 'info-btn'-napeille");
document.querySelectorAll('.info-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const classCard = btn.closest('.class-card');
        const infoSection = classCard.querySelector('.info-section');
        infoSection.style.display = infoSection.style.display === 'block' ? 'none' : 'block';
    });
});


document.querySelectorAll('.day').forEach(function(dayElement) {
    dayElement.addEventListener('click', function() {
        document.querySelectorAll('.day').forEach(d => d.classList.remove('active'));
        this.classList.add('active');

        const selectedDate = this.getAttribute('data-day');
        const selectedCity = document.querySelector('#citySelect').value;

       // Ei vaadita kaupungin valintaa
        // fetch-kutsuun lisätään kaupunki vain, jos se on valittu
        const cityParam = selectedCity ? `&city=${selectedCity}` : '';
        fetch(`fetch_data.php?day=${selectedDate}${cityParam}`)
        .then(response => response.json())
        .then(data => {
            console.log("Saatu data päiväelementistä:", data);
            if (Array.isArray(data.classes) && data.classes.length > 0) {
                updateUI(data.classes, data.instructors);
            } else {
                console.error("Data ei sisällä luokkia:", data);
                const resultsDiv = document.querySelector('.classes-list');
                resultsDiv.innerHTML = '<p style="color: white;">Tietoja ei ole saatavilla valitulle päivämäärälle.</p>';
            }
        })
        .catch(error => {
            console.error('Virhe haettaessa tietoja:', error);
            const resultsDiv = document.querySelector('.classes-list');
            resultsDiv.innerHTML = '<p>Virhe: ' + error.message + '</p>';
        });
    });
});

// Funktio hakee tiedot palvelimelta valitsimien arvojen perusteella
async function fetchData() {
    console.log("fetchData kutsuttu");
    // Haetaan valitsimien arvot
    const city = document.querySelector('#citySelect').value || null;
    const gym = document.querySelector('#gymSelect').value || null;
    const className = document.querySelector('#classNameSelect').value || null;
    const instructor = document.querySelector('#instructorSelect').value || null;
    const startTime = document.querySelector('#startTime').value || null;
    const endTime = document.querySelector('#endTime').value || null;
    // Lähetettävät tiedot konsoliin
    console.log("Lähetettävät tiedot:", { city, address: gym, instructor, name: className, startTime, endTime });

    try {
        // Lähetetään tiedot palvelimelle
        console.log("Lähetetään tiedot palvelimelle");
        const response = await fetch('fetch_data.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ city, address: gym, instructor, name: className, startTime, endTime })
        });

        // Jos vastaus ei ole ok, heitetään virhe
        if (!response.ok) {
            throw new Error(`Network response was not ok. Status: ${response.status}, Text: ${await response.text()}`);
        }

        // Muutetaan vastaus JSON-muotoon
        console.log("Muutetaan vastaus JSON-muotoon");
        const data = await response.json();
        console.log("Palvelimelta saadut tiedot:", data);

        // Jos vastauksessa on osoitteita, luokkien nimiä ja ohjaajia, päivitetään valitsimet
        if (data.addresses && data.classNames && data.instructors) {
            console.log("Päivitetään valitsimet ja UI");
            updateDropdowns(data);
            updateUI(data.classes, data.instructors);
        } else {
            console.error("Odottamaton palvelimen vastaus");
        }
    } catch (error) {
        // Jos tapahtuu virhe, näytetään virheviesti
        console.error("Virhe:", error);
        const resultsDiv = document.querySelector('.classes-list');
        resultsDiv.innerHTML = '<p>Virhe: ' + error.message + '</p>';
    }
}

// Funktio päivittää valitsimet palvelimelta saaduilla tiedoilla
function updateDropdowns(data) {
    console.log("Päivitetään valitsimet seuraavilla tiedoilla:", data);
    updateDropdown('#gymSelect', data.addresses.map(address => ({ value: address, label: address })));
    updateDropdown('#classNameSelect', data.classNames.map(name => ({ value: name, label: name })));
    updateDropdown('#instructorSelect', Object.entries(data.instructors).map(([id, name]) => ({ value: id, label: name })));
}

// Funktio päivittää yksittäisen valitsimen
function updateDropdown(selector, items, placeholder = "Valitse...") {
    console.log(`Päivitetään valitsin ${selector} seuraavilla kohteilla:`, items);
    const dropdown = document.querySelector(selector);
    if (!dropdown) {
        console.error(`Valitsinta ${selector} ei löydy.`);
        return;
    }
    const currentValue = dropdown.value; // Tallennetaan nykyinen arvo
    console.log(`Nykyinen arvo valitsimessa ${selector}:`, currentValue);
    dropdown.innerHTML = `<option value="">${placeholder}</option>`; // Tyhjennetään valitsin ja lisätään "Valitse..." -vaihtoehto
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.value;
        option.textContent = item.label;
        dropdown.appendChild(option);
    });
    dropdown.value = currentValue; // Asetetaan tallennettu arvo takaisin
    console.log(`Valitsin ${selector} päivitetty.`);
}

// Funktio päivittää UI:n jumppatunneilla
function updateUI(classes, instructors) {
    console.log("updateUI kutsuttu:", classes, instructors);
    const resultsDiv = document.querySelector('.classes-list');
    resultsDiv.innerHTML = '';

    if (!classes || classes.length === 0) {
        console.log("Ei luokkia näytettäväksi.");
        resultsDiv.innerHTML = '<p style="color: white;">Ei tuloksia</p>';
        return;
    }
    console.log("Luokkadata:", classes);

    classes.forEach(jumppa => {
        const classElement = createClassElement(jumppa, instructors);
        resultsDiv.appendChild(classElement);
    });
    addBookingListeners();
}

function createClassElement(jumppa, instructors) {
    console.log('Instructor ID:', jumppa.instructor_id);
  console.log('Instructors object:', instructors);
    const div = document.createElement('div');
    div.classList.add('class-card');
    div.setAttribute('data-class-id', jumppa.class_id);

    const finnishMonths = ["", "Tammi", "Helmi", "Maalis", "Huhti", "Touko", "Kesä", "Heinä", "Elo", "Syys", "Loka", "Marras", "Joulu"];
    const startDate = new Date(jumppa.start_time);
    const endDate = new Date(jumppa.end_time);
    const formattedStartDate = `${startDate.getDate()} ${finnishMonths[startDate.getMonth() + 1]} | ${startDate.getHours()}:${startDate.getMinutes().toString().padStart(2, '0')} - ${endDate.getHours()}:${endDate.getMinutes().toString().padStart(2, '0')}`;
    const instructorName = instructors && instructors[jumppa.instructor_id] ? instructors[jumppa.instructor_id] : "Ei ohjaajaa";
    console.log('Instructor Name:', instructorName);

    const reservationCount = Number(jumppa.reservation_count) || 0;
    console.log('Varauslaskuri:', reservationCount);
    const userHasReservation = jumppa.user_has_reservation > 0;
    console.log('Käyttäjällä on varaus:', userHasReservation);
    const buttonText = userHasReservation ? "Peruuta" : "Varaa";
    const buttonClass = userHasReservation ? "booked" : "";

    div.innerHTML = `
        <div class="class-info">
            <div class="date-time">${formattedStartDate}</div>
            <div class="name">${jumppa.name} ${reservationCount}/${jumppa.capacity}</div>
            <div class="location">Kuntosali: ${jumppa.address}</div>
            <div class="instructor">${instructorName}</div>
        </div>
        <div class="class-actions">
            <button class="info-btn">Info</button>
            <div class="info-section" style="display: none;">${jumppa.description}</div>
            <button class="book-btn ${buttonClass}">${buttonText}</button>
        </div>
    `;
    console.log(`Nappi luotu: Teksti - ${buttonText}, Luokka - ${buttonClass}`);

    // Lisää tapahtumankäsittelijät
    const infoBtn = div.querySelector('.info-btn');
    infoBtn.addEventListener('click', function() {
        const infoSection = div.querySelector('.info-section');
        infoSection.style.display = infoSection.style.display === 'block' ? 'none' : 'block';
    });
    

    console.log("Uusi kortti luotu:", div);

    return div;
    
}

document.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', fetchData);
});

 /*        const finnishMonths = ["", "Tammi", "Helmi", "Maalis", "Huhti", "Touko", "Kesä", "Heinä", "Elo", "Syys", "Loka", "Marras", "Joulu"];

        const startDate = new Date(jumppa.start_time);
        const endDate = new Date(jumppa.end_time);
        
        const formattedStartDate = `${startDate.getDate()} ${finnishMonths[startDate.getMonth() + 1]} | ${startDate.getHours()}:${startDate.getMinutes().toString().padStart(2, '0')} - ${endDate.getHours()}:${endDate.getMinutes().toString().padStart(2, '0')}`;        
        const instructorName = data.instructors[jumppa.instructor_id];
        const reservationCount = Number(jumppa.reservation_count) || 0;
        console.log("Varausten määrä on:",jumppa.reservation_count);
        const userHasReservation = jumppa.user_has_reservation > 0;
        console.log("Käyttäjällä on varaus:",jumppa.user_has_reservation);
        const buttonText = userHasReservation ? "Peruuta" : "Varaa";
        console.log("Napin teksti on:",userHasReservation);
        const buttonClass = userHasReservation ? "booked" : "";
        console.log("Napin teksti on:",userHasReservation);


        div.innerHTML = `
            <div class="class-info">
            <div class="date-time">${formattedStartDate}</div>
            <div class="name">${jumppa.name} ${reservationCount}/${jumppa.capacity}</div>
                <div class="location">Kuntosali: ${jumppa.address}</div>
                <div class="instructor">${instructorName}</div>
            </div>
            <div class="class-actions">
                <button class="info-btn">Info</button>
                <div class="info-section">${jumppa.description}</div>
                <button class="book-btn ${buttonClass}">${buttonText}</button>
            </div>
        `;

        resultsDiv.appendChild(div);
        console.log("Luokkakortti lisätty:", div);

        addBookingListeners();

        const infoBtn = div.querySelector('.info-btn');
        infoBtn.addEventListener('click', function() {
            const classCard = infoBtn.closest('.class-card');
            const infoSection = classCard.querySelector('.info-section');
            
            if(infoSection.style.display === 'none' || infoSection.style.display === '') {
                console.log("Näytetään lisätiedot luokalle:", jumppa);
                infoSection.style.display = 'block';
            } else {
                console.log("Piilotetaan lisätiedot luokalle:", jumppa);
                infoSection.style.display = 'none';
            }
        });
    });
} */

/* document.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', function() {
        console.log(`${select.id} muuttunut`);
        fetchData();
    });
}); */