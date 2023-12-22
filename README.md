# Projet SQL Réalisé en PHP et TailwindCSS pour MyDigitalSchool

Les consignes du projet sont disponibles dans le fichier "Consignes.pdf"

## Installer les dépendances :

~~~bash 
npm install
composer install
~~~

# Base de données

## 1 - Créer La base de donnée

Le projet est prévu pour fonctionner avec une base de donnée MySql.

## 2 - Connexion a la base de donnée

Pour la connexion à la base de donnée, dupliquer le fichier config.php.exemple, le modifier et le renommer en "
config.php"

## 3 - Créer les tables

Pour créer les tables de la base de données, il faut exécuter dans l'ordre les fichiers présents dans le dossier
RUN_BEFORE_ANYTHING_ELSE

- Le premier fichier créé la structure de la base et y remplit les données sauf les emprunts.
- Le second fichier mets à jour le charset des tables et change la manière de récupérer l'état de disponibilité des
  livres. Créé aussi la table utilisateur.
- Le troisième fichier créé les emprunts et les retours de livres. (Peut mettre plusieurs minutes à s'exécuter)
- Le dernier fichier permet de créer un utilisateur par abonné, ainsi qu'un utilisateur gestionnaire.

## 4 - Comptes utilisateurs

Un compte gestionnaire est créé avec les identifiants suivants :

- email : gestionnaire@sqlmds.fr
- mot de passe : gestionnaire

Un compte abonné est créé avec les identifiants suivants :

- email : abonne@sqlmds.fr
- mot de passe : abonne

# Gestion des droits

Les gestionnaires peuvent consulter la liste des abonnés, modifier leurs informations et en créer de nouveaux.

Les abonnés peuvent consulter la liste des livres, consulter leur profil et modifier certaines de leurs informations.

## Si besoin de rebuild TailwindCSS :

~~~bash 
npx tailwindcss -i ./src/assets/input.css -o ./dist/output.css --watch
~~~