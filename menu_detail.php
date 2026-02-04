<?php

/**
 * Page de détail d’un menu
 * Affiche les informations via AJAX
 */
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container my-5">

    <h1>Détail du menu</h1>

    <!-- Zone où le menu sera injecté -->
    <div id="menuDetail" class="mt-4"></div>

</div>

<!-- JS -->
<script src="assets/js/api.js"></script>
<script src="assets/js/menuDetail.js"></script>
</body>

</html>