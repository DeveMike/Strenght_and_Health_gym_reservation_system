<?php
session_start();

include 'csp-header.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <title>Strength & Health 24/7</title>
</head>

<body>
    <?php include_once 'navbar.php'; ?>

    <section class="welcome-section">
        <div class="welcome-image"></div>
        <div class="welcome-content">
            <h1>Vahvista itseäsi ja terveyttäsi meidän kanssa!</h1>
            <p class="price-text">VAIN 29,90 € / KK</p>
            <a href="join.php" class="welcome-join-button">Liity jäseneksi</a>
        </div>
    </section>

    <section class="why-us-section">
        <h1>Neljä Syytä Valita Meidät!</h1>
        <div class="why-us-boxes">
            <div class="why-us-box">
                <img src="assets/time.svg" alt="Icon 1">
                <h2>Avoinna 24/7</h2>
                <p>Avoinna aina kuin sinulle sopii!</p>
            </div>
            <div class="why-us-box">
                <img src="assets/hahmot.svg" alt="Icon 2">
                <h2>Ryhmäliikunta</h2>
                <p>Meidän salilla voit unohtaa erilliset ryhmäliikunta maksut ja nauttia näistä palveluista
                    rahjattomasti jäsenenä ILMAISEKSI.</p>
            </div>
            <div class="why-us-box">
                <img src="assets/euro.svg" alt="Icon 3">
                <h2>Kilpailukykyinen Hinta</h2>
                <p>Hintamme on kilpailukykyinen ja tarjoaa ensiluokkalaista palvelua halvalla.</p>
            </div>
            <div class="why-us-box">
                <img src="assets/shield.svg" alt="Icon 4">
                <h2>30 vuotta kokemusta</h2>
                <p>Voit turvallisen mielin urheilla kanssamme, sillä perustajillamme on vankka kokemus alasta!</p>
            </div>
        </div>

    </section>

    <section class="mbr-container">
        <h1>Liity Jäseneksi Nyt!</h1>
        <h2>Valitse itsellesi sopiva jäsenyys</h2>

        <div class="membership-options">
            <div class="membership-option">
                <h3>Perus Jäsenyys</h3>
                <p class="price">29.90€ / KK</p>
                <a href="join.php" class="mber-join-button">Liity</a>
                <p class="note">+ Aloitusmaksu 19.90€</p>
            </div>

            <div class="membership-option">
                <h3>Eläkeläinen</h3>
                <p class="price">24.90€ / KK</p>
                <a href="join.php" class="mber-join-button">Liity</a>
                <p class="note">+ Aloitusmaksu 19.90€</p>
            </div>

            <div class="membership-option">
                <h3>Opiskelija</h3>
                <p class="price">24.90€  / KK</p>
                <a href="join.php" class="mber-join-button">Liity</a>
                <p class="note">+ Aloitusmaksu 19.90€</p>
            </div>
        </div>
    </section>

    <section class="info-section">
        <div class="info-image">
            <img src="assets/pexels-leon-ardho-1552104-scaled 1.png" alt="Kuva" width="458" height="612">
        </div>
        <div class="info-text">
            <h2>Tervetuloa Strenght & Health 24/7 Kuntokeskusketjuun!</h2>
            <h4 class="yellow-text">We empower lives with strength and holistic health!</h4>
            <p>
                Olemme iloisia saadessamme esitellä sinulle Strenght & Health 24/7 -kuntosalejamme, jotka tarjoavat
                monipuolisia treenipalveluja sekä laadukasta ryhmäliikuntaa. Haluamme olla osa matkaasi kohti parempaa
                terveyttä ja kokonaisvaltaista hyvinvointia.
            </p>
        </div>
    </section>

    <section class="info-section2">
        <h2>Liity mukaan!</h2>
        <div class="yellow-lines">
            <div class="yellow-line1"></div>
            <div class="yellow-line2"></div>
        </div>

        <div class="info-block">
            <div class="icon-title-wrapper">

                <img src="assets/Barbell.svg" alt="Icon 1">
                <h3>Monipuolista Treeniä:</h3>
            </div>
            <div class="text-wrapper">
                <p>Kuntosaleillamme voit nauttia modernista ympäristöstä sekä laadukkaista välineistä, jotka tukevat tavoitteitasi. Tarjoamme myös monipuolista ryhmäliikuntaa eri tyyleissä ja tasoilla. Olitpa sitten innostunut kardiotreenistä, voimaharjoittelusta tai kehoa ja mieltä tasapainottavista lajeista, meillä on jotain jokaiselle.</p>
            </div>
        </div>

        <div class="info-block">
            <div class="icon-title-wrapper">

                <img src="assets/Growth Graph.svg" alt="Icon 2">
                <h3>Kasvava Yhteisö:</h3>
            </div>
            <div class="text-wrapper">
                <p>Emme tyydy nykyiseen, vaan pyrimme kasvamaan ja kehittymään.<br>
                    Suunnitelmissamme on avata lisää saleja,jotta voimme palvella sinua entistä laajemmin ja monipuolisemmin. Tavoitteenamme on olla Suomen parhain urheilukeskusketju, joka tukee terveyttäsi ja hyvinvointiasi.</p>
            </div>
        </div>

        <div class="info-block">
            <div class="icon-title-wrapper">
                <img src="assets/Shop.svg" alt="Icon 3">
                <h3>Tutustu Meihin:</h3>
            </div>
            <div class="text-wrapper">
                <p>
                    Meidän Strenght & Health 24/7-sivumme tarjoavat sinulle mahdollisuuden sukeltaa treeniympäristöömme.<br>
                    Yhdessä pyrimme tarjoamaan sinulle parhaat mahdollisuudet saavuttaa omat tavoitteesi terveyden ja kunnon suhteen. Strenght & Health 24/7 on myös osa verkkokauppatiimiämme “Strenght & Health”, tarjoten laadukkaita treenivarusteita ja -vaatteita, jotka tukevat aktiivista elämäntapaa. Meiltä löydät kaiken tarvittavan hyvinvointiisi liittyen.
                </p>
            </div>
        </div>

        <div class="yellow-text2">
            <p> Olemme täällä sinua varten. Tule tutustumaan Strenght & Health 24/7 -perheeseen ja anna meidän auttaa sinua
                kohti vahvempaa ja terveempää elämää. Yhdessä teemme ihmisistä terveempiä ja onnellisempia!</p>
        </div>
    </section>

    <?php include_once 'footer.php'; ?>

</body>

</html>