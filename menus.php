<?php

/**
 * Page principale d’affichage des menus
 * Aucun SQL ici, uniquement du HTML
 */
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">

    <h1 class="mb-4">Nos menus</h1>


    <!-- ZONE DES FILTRES -->

    <div class="row g-2 mb-4">

        <div class="col">
            <input type="number" id="prixMin" class="form-control" placeholder="Prix minimum">
        </div>

        <div class="col">
            <input type="number" id="prixMax" class="form-control" placeholder="Prix maximum">
        </div>

        <div class="col">
            <select id="fourchette" class="form-control">
                <option value="">Fourchette prix</option>
                <option value="0-50">0 - 50 €</option>
                <option value="50-100">50 - 100 €</option>
                <option value="100-200">100 - 200 €</option>
            </select>
        </div>

        <div class="col">
            <input type="number" id="personnesMin" class="form-control" placeholder="Personnes minimum">
        </div>

        <div class="col">
            <select id="theme" class="form-control">
                <option value="">Thème</option>
                <option value="mariage">Mariage</option>
                <option value="noel">Noël</option>
            </select>
        </div>

        <div class="col">
            <select id="regime" class="form-control">
                <option value="">Régime</option>
                <option value="vegetarien">Végétarien</option>
                <option value="halal">Halal</option>
            </select>
        </div>

        <div class="col">
            <button id="btnFiltrer" class="btn btn-primary w-100">
                Filtrer
            </button>
        </div>

    </div>


    <!-- ZONE D’AFFICHAGE -->

    <div class="row" id="menusContainer"></div>

</div>

<!-- JS -->
<script src="assets/js/api.js"></script>
<script src="assets/js/menus.js"></script>
<script src="assets/js/filters.js"></script>
<script src="assets/js/main.js"></script>
</body>

</html>
<?php include 'includes/footer.php'; ?>