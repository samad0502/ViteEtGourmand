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
