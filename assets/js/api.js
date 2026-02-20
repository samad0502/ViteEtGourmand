/**
 * api.js
 * Centralise tous les appels AJAX vers le backend
 */

/**
 * Récupère les menus (avec ou sans filtres)
 */
function fetchMenus(filters = {}) {
  const params = new URLSearchParams(filters).toString();

  return fetch('ajax/menus.php?' + params)
    .then(response => response.json());
}

/**
 * Récupère le détail d'un menu
 */
function fetchMenuById(id) {
  return fetch('ajax/menu_detail.php?id=' + id)
    .then(response => response.json());
}

/**
 * Récupère les thèmes et régimes
 */
function fetchFilters() {
  return fetch('ajax/filters.php')
    .then(response => response.json());
}
