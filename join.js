document.addEventListener("DOMContentLoaded", function() {
    window.setTimeout(function() {
        var alertElement = document.querySelector(".alert");
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    }, 10000);
});