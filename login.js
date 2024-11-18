window.setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        alert.style.display = 'none';
    });
}, 5000); // Viestit katoavat 5 sekunnin kuluttua