# Nom du Projet
Knowledge
## À propos

[Courte description expliquant le but et les fonctionnalités principales de votre projet.]

## Table des matières

- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Utilisation](#utilisation)
- [Contribuer](#contribuer)
- [Licence](#licence)

## Prérequis

Avant de commencer, assurez-vous d'avoir installé les éléments suivants :

- PHP >= 8.0
- Composer
- Symfony CLI
- Une base de données compatible (par exemple, MySQL, PostgreSQL)

## Installation

1. Clonez le dépôt du projet :

   ```bash
   git clone https://github.com/votre-utilisateur/votre-projet.git

2.Accédez au répertoire du projet :
cd knowledge

3.Installez les dépendances avec Composer :

bash
composer install

## Configuration

1. Copiez le fichier .env pour créer votre configuration locale :
  cp .env .env.local

2.Modifiez le fichier .env.local pour configurer votre connexion à la base de données :

  DATABASE_URL="mysql://root:@127.0.0.1:3306/knowledge?serverVersion=10.4.32"

3.Créez la base de données: 
  php bin/console doctrine:database:create

4.Mettez à jour le schéma de la base de données:
  php bin/console doctrine:schema:update --force

  ### Configuration de Stripe
  
  1. Ajoutez vos clés Stripe dans le fichier `.env.local` :
     STRIPE_SECRET_KEY=sk_test_51QRhxcJxoR8xsc2FubXoql05icoQtpvrveOP3Q2smOb58W3NQ198FGt6A10OzveCpO3qO1KCHBJfoXB5IcEZPdPA00aXhpFhL6
     STRIPE_PUBLIC_KEY=pk_test_51QRhxcJxoR8xsc2FLpIFMAD2lQuCnzVIwoDGhTg15nBRbVXXNbEBTU2fRQzKSTLDyCxgwzRL1G2ED6pKK5JG2Aij00Vv7sG8Pw
  2. Installez Stripe PHP SDK
     bash
      Composer require stripe/stripe-php
  3.  Vérifiez que les webhooks Stripe sont bien configurés (voir section ngrok ci-dessous).

  ### Ajout de **ngrok**
  1. Installez ngrok
  bash 
    npm install -g ngrok
  2. Exécuter ngrok pour exposer votre serveur local: 
    ngrok http 8000

  3. Copiez l'URL générée (https://xxxxxxx.ngrok.io) et configurez-la sur le Dashboard Stripe dans Webhooks.

  4. Vous pouvez aussi écouter les webhooks en local :
    stripe listen --forward-to http://127.0.0.1:8000/webhook/stripe

  5. Testez un évènement webhook avec : 
    stripe trigger payment_intent.succeeded

## Utilisation
1. Lancer les tests: 
  make tests

2. Lancez le serveur symfony
  Symfony server:start

3. https://localhost:8000

## Contribuer
Les contributions sont les bienvenues ! Si vous souhaitez contribuer, veuillez suivre les étapes suivantes :

Forkez le dépôt.
Créez une branche pour votre fonctionnalité (git checkout -b feature/ma-fonctionnalité).
Commitez vos modifications (git commit -am 'Ajoute une nouvelle fonctionnalité').
Poussez la branche (git push origin feature/ma-fonctionnalité).
Ouvrez une Pull Request.

