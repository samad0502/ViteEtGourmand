/**
 * Fichier responsable des appels AJAX
 * Communique avec le backend PHP
 */
function fetchMenus(filters = {}) {

  // Transformation de l’objet en query string
  const query = new URLSearchParams(filters).toString();

  // Appel AJAX vers le backend
  return fetch('ajax/menus.php?' + query)
    .then(response => response.json());
}



/**
 * Récupère le détail d’un menu
 */
function fetchMenuDetail(id) {
  return fetch('ajax/menu_detail.php?id=' + id)
    .then(res => res.json());
}
