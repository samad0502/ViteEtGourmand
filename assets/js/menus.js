/**
 * menus.js
 * Gère l'affichage des cartes menus
 */

/**
 * Charge et affiche les menus
 */
function loadMenus(filters = {}) {

  fetchMenus(filters).then(menus => {

    const container = document.getElementById('menusContainer');
    container.innerHTML = '';

    // Aucun menu trouvé
    if (!menus || menus.length === 0) {
      container.innerHTML = '<p>Aucun menu trouvé</p>';
      return;
    }

    // Génération des cards
    menus.forEach(menu => {

      container.innerHTML += `
        <div class="col-md-4 mb-4">
          <div class="card h-100">

            <img src="assets/img/menus/${menu.main_image}"
    class="card-img-top " alt="${menu.title}" >

          <div class="card-body">
  <h5 class="card-title">${menu.title}</h5>

    <p class="fw-bold">${menu.price} €</p>

  <p class="text-muted">
    Minimum ${menu.min_people} personnes
  </p>

  <p class="text-muted">
    Quantité restante : ${menu.remaining_quantity}
  </p>

  <a href="menu_detail.php?id=${menu.id}" class="btn btn-info">Voir le détail</a>
</div>
</div>
      `;
    });
  });
}
