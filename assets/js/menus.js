/**
 * Gère l’affichage des menus
 */
function loadMenus(filters = {}) {

  fetchMenus(filters).then(menus => {

    const container = document.getElementById('menusContainer');
    container.innerHTML = '';

    menus.forEach(menu => {

      container.innerHTML += `
        <div class="col-md-4 mb-4">
          <div class="card">
            <img src="assets/images/menus/${menu.image}" class="card-img-top">
            <div class="card-body">
              <h5>${menu.titre}</h5>
              <p>${menu.prix} €</p>
            </div>
          </div>
        </div>
      `;
    });
  });
}
