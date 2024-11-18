document.addEventListener('DOMContentLoaded', (event) => {
    const cancelButtons = document.querySelectorAll('.book-btn');

    cancelButtons.forEach(button => {
        button.addEventListener('click', function() {
            const classId = this.getAttribute('data-class-id');
            if (confirm('Haluatko varmasti peruuttaa tunnin?')) {
                fetch('cancel.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'class_id=' + classId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Tunti peruutettu onnistuneesti.');
                        // Päivitä sivu tai poista tunti DOM:sta
                        window.location.reload();
                    } else {
                        alert('Virhe: ' + data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert('Virhe peruutettaessa tuntia.');
                });
            }
        });
    });
});


  