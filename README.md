# Application de Gestion d'Événements
Cette application permet à des utilisateurs de créer, modifier et gérer des événements. Elle inclut des fonctionnalités d'inscription, de connexion et de gestion des événements, le tout dans une interface responsive.

## Technologies Utilisées
- Symfony (dernière version)
- PHP
- MySQL
- Bootstrap (pour le design)
- Faker (pour la génération de données fictifs)
- PHPUnit (pour les tests unitaires)

## Prérequis
- PHP 8.1 ou supérieur
- Composer
- MySQL
- Node.js et npm (pour le développement front-end)

## Installation
1. Clonez le dépôt :
```
git clone git@github.com:Larcenyy/Planify.git
```
```
cd Planify
```

2. Installez les dépendances :

```
composer install
```

3. Configurez la base de données :

   Ajustez le fichier **.env** et mettez à jour les paramètres de connexion à la base de données.

4. Créez la base de données :
```
php bin/console doctrine:database:create
```
5. Appliquez les migrations :
```
php bin/console doctrine:migrations:migrate
```
7. Chargez les fixtures **(Uniquement si vous souhaitez obtenir des utilisateurs et des événements fictifs)** :
```
php bin/console doctrine:fixtures:load
```
9. Démarrez le serveur de développement :
```
symfony server:start
```

### Vous pouvez maintenant accéder à l'application à l'adresse http://localhost:8000.

# Utilisation
## Authentification
**Inscription :** Les utilisateurs peuvent s'inscrire en fournissant leur nom, adresse e-mail et mot de passe. Le mot de passe doit contenir plus de 8 caractères, au moins une majuscule, un chiffre et un caractère spécial.

**Connexion :** Les utilisateurs peuvent se connecter avec leur adresse e-mail et mot de passe.

**Déconnexion :** Les utilisateurs peuvent se déconnecter à tout moment.

## Gestion des Événements
Les événements à venir sont affichés sur la page d'accueil et sont triés par date.
Les utilisateurs authentifiés peuvent créer, modifier et supprimer leurs propres événements.
Les utilisateurs peuvent s'inscrire et se désinscrire d'événements.
La section "Mes événements" permet aux utilisateurs de visualiser les événements auxquels ils sont inscrits.
## Filtrage
Un filtre de date est disponible pour affiner l'affichage des événements.
## Choix de Conception
**Architecture :** L'application utilise une architecture MVC (Modèle-Vue-Contrôleur) pour séparer la logique métier de la présentation.

**Services :** Toute la logique métier a été placée dans des services dédiés pour faciliter la maintenance et les tests.

**Sécurité :** L'authentification et la gestion des accès sont gérées par le composant Security de Symfony.

**Limitations :** Actuellement, l'application ne prend pas en charge l'envoi d'e-mails pour les notifications d'inscription aux événements.
Les tests unitaires sont limités à certaines fonctionnalités (voir le dossier tests).

**Contribuer :**
Les contributions sont les bienvenues ! Pour proposer des améliorations ou signaler des problèmes, ouvrez une issue ou soumettez une pull request.
