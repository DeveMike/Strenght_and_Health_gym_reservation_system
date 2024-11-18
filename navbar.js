
document.addEventListener('DOMContentLoaded', function () {
    const burger = document.querySelector('.burger-menu');
    const navLinks = document.querySelector('.nav-links');
    const navOverlay = document.querySelector('.nav-overlay');

    burger.addEventListener('click', function () {
        navLinks.classList.toggle('active');
        navOverlay.style.display = navLinks.classList.contains('active') ? 'block' : 'none';
        burger.classList.toggle('toggle');
    });

    // Lisätään klikkaustapahtuma koko dokumentille
    document.addEventListener('click', function (e) {
        // Tarkistetaan, ettei klikkaus tapahdu burger-menu:ssa tai nav-links:ssa
        if (!burger.contains(e.target) && !navLinks.contains(e.target)) {
            navLinks.classList.remove('active');
            navOverlay.style.display = 'none';
            burger.classList.remove('toggle');
        }
    });
});

