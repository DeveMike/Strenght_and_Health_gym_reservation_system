
document.addEventListener('DOMContentLoaded', function() {
    var updateForm = document.getElementById('updateForm');
    updateForm.addEventListener('submit', function(event) {
        var nykyinenSalasana = document.querySelector('input[name="nykyinenSalasana"]').value;
        var uusiSalasana = document.querySelector('input[name="uusiSalasana"]').value;
        var uusiSalasanaUudelleen = document.querySelector('input[name="uusiSalasanaUudelleen"]').value;

        // Tarkista, onko käyttäjä yrittänyt vaihtaa salasanaa
        if (nykyinenSalasana || uusiSalasana || uusiSalasanaUudelleen) {
            if (!nykyinenSalasana || !uusiSalasana || !uusiSalasanaUudelleen || uusiSalasana.length < 6 || uusiSalasana !== uusiSalasanaUudelleen) {
                event.preventDefault(); // Estetään lomakkeen lähetys
                if (!nykyinenSalasana) {
                    alert('Anna nykyinen salasana.');
                } else if (uusiSalasana.length < 6) {
                    alert('Uuden salasanan on oltava vähintään 6 merkkiä pitkä.');
                } else if (uusiSalasana !== uusiSalasanaUudelleen) {
                    alert('Uusi salasana ja sen toisto eivät täsmää.');
                }
            }
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    window.setTimeout(function() {
        var alertElement = document.querySelector(".alert");
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, 10000);
});