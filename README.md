# ServiceRDC - Plateforme de services en République Démocratique du Congo

ServiceRDC est une plateforme moderne permettant de mettre en relation des clients, des artisans et des chercheurs d'emploi en RDC.

## 🚀 Fonctionnalités
- Authentification complète (Client, Artisan, Super Admin)
- Dashboard personnalisé pour chaque rôle
- Système de recherche de services
- Gestion de profil

## 🛠️ Installation
1. Clonez le projet : `git clone https://github.com/Mozart1123/servicerdc.git`
2. Installez les dépendances : `composer install` et `npm install`
3. Copiez `.env.example` en `.env` et configurez votre base de données
4. Générez la clé : `php artisan key:generate`
5. Lancez les migrations et seeds : `php artisan migrate --seed`
6. Lancez le serveur : `php artisan serve`
