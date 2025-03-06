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

5.Installez les migrations: 
symfony console doctrine:migrations:migrate 
	
6.Installez les fixtures: 
symfony console doctrine:fixtures:load 


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

