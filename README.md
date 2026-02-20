# 🍔 Projet Vite & Gourmand - Plateforme de Commande en Ligne

Un site pour un traiteur situé à Bordeaux géré par José et Julie qui proposent leurs services pour les évènements(telle que noel, mariage etc...).

Ce projet est une application web complète de restauration permettant la gestion des commandes, des employés et des statistiques de vente en temps réel.

## 🚀 Technologies Utilisées

- **Frontend :** HTML5, CSS3, Bootstrap 5 (Design responsive)
- **Backend :** PHP 8.x (Architecture Orientée Objet)
- **Base de données relationnelle :** MySQL (Gestion des utilisateurs, menus et commandes)
- **Base de données NoSQL :** MongoDB Atlas (Analyses et statistiques de performance)
- **Notifications :** PHPMailer via Mailtrap (Emails de confirmation)

## 📋 Fonctionnalités Clés

- **Système de commande :** Sélection de menus et personnalisation (nombre de personnes).
- **Dashboard Admin :** Gestion des flux de commandes et modération des avis.
- **Statistiques Dynamiques :** Analyse du Chiffre d'Affaires par menu et par période via MongoDB.
- **Sécurité :** Protection des routes par rôles (Admin / Employé / Client).

## ⚙️ Installation (Local)

1. **Code Source :** Extraire le dossier dans votre répertoire `htdocs` (XAMPP).
2. **Base de données MySQL :**
   - Créer une base nommée `vite_gourmand` dans phpMyAdmin.
   - Importer le fichier situé dans `/sql/vite_gourmand.sql`.
3. **Configuration :**
   - Les accès MySQL se trouvent dans `classes/Database.php`.
   - La connexion MongoDB Atlas est déjà configurée pour pointer vers le Cluster Cloud (accessible sans installation locale).Possibilité d'utiliser MongoDB Compass.
4. **Dépendances :** Le dossier `vendor/` est inclus. Si nécessaire, exécuter `composer install`.

## 🔑 Identifiants de test

| Rôle               | Email            | Mot de passe |
| :----------------- | :--------------- | :----------- |
| **Administrateur** | admin@test.com   | admin123     |
| **Employé**        | employe@test.com | employe123   |
| **Client**         | client@test.com  | client123    |

## 🌐 Version en ligne

Le projet est déployé à l'adresse suivante : **[https://vite-gourmand-app-d37150131136.herokuapp.com]**

---

_Projet réalisé par [Bechaa Abdessamad] dans le cadre de l'examen en cours de formation DWWM._
