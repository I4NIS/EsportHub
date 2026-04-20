# EsportHub API

API REST dédiée à l'esport sur les FPS tactiques (Valorant, CS2), construite avec Laravel 12 et PostgreSQL.

---

## Stack technique

- **Backend** : PHP 8.2, Laravel 12
- **Base de données** : PostgreSQL 15
- **Authentification** : Laravel Sanctum (Access Token + Refresh Token)
- **Serveur web** : Nginx 1.25 + PHP-FPM
- **Environnement** : Docker / Docker Compose

---

## Lancement avec Docker

### Prérequis
- Docker Desktop installé et démarré
- Ports 80 et 5432 disponibles

### Démarrage

```bash
# Cloner le projet
git clone <repo-url>
cd EsportHub

# Copier le fichier d'environnement
cp .env.example .env

# Démarrer les conteneurs (build + migrate + seed automatique)
docker compose up --build -d

# Vérifier les logs
docker compose logs -f app
```

L'API est accessible sur : **http://localhost/api**

### Dump SQL

Pour exporter un dump de la base de données :

```bash
docker exec esport_db pg_dump -U laravel esport_hub > dump.sql
```

Pour restaurer depuis un dump :

```bash
docker exec -i esport_db psql -U laravel esport_hub < dump.sql
```

### Arrêt

```bash
docker compose down
# Avec suppression des volumes :
docker compose down -v
```

---

## Lancement sans Docker

```bash
# Installer les dépendances
composer install

# Configurer l'environnement
cp .env.example .env
# Éditer .env : DB_CONNECTION=pgsql, DB_DATABASE=esport_hub, etc.

# Générer la clé
php artisan key:generate

# Migrer et seeder
php artisan migrate --seed

# Démarrer le serveur
php artisan serve
```

---

## Tests

Le projet inclut une suite de tests Feature complète (73 tests, 190 assertions) couvrant l'ensemble des endpoints.

```bash
# Lancer tous les tests (SQLite in-memory, aucune config requise)
php artisan test

# En parallèle
php artisan test --parallel
```

---

## Authentification et rôles

L'API utilise **Laravel Sanctum** avec un système double token et deux rôles utilisateur :

| Rôle | Description |
|------|-------------|
| `user` | Rôle par défaut — lecture, likes, follows, gestion du compte |
| `admin` | Accès complet — création, modification et suppression de toutes les ressources |

| Token | Durée | Usage |
|-------|-------|-------|
| Access Token | Session navigateur | Header `Authorization: Bearer <token>` |
| Refresh Token | 30 jours | Corps de requête vers `POST /api/auth/refresh` |

### Exemple de flux

```bash
# 1. Inscription
POST /api/auth/register
{ "firstname": "Jean", "lastname": "Dupont", "email": "jean@example.com",
  "password": "Password1", "password_confirmation": "Password1",
  "username": "jeandupont", "birthdate": "1998-01-15" }

# 2. Connexion → récupérer access_token et refresh_token
POST /api/auth/login

# 3. Utiliser l'access token
GET /api/users/me
Authorization: Bearer <access_token>

# 4. Rafraîchir quand expiré
POST /api/auth/refresh
{ "refresh_token": "<refresh_token>" }

# 5. Déconnexion
POST /api/auth/logout
Authorization: Bearer <access_token>
```

---

## Endpoints

> **Légende** : — Public · 🔒 Authentifié · 🔒 Admin — réservé au rôle admin

### Auth

| Méthode | Route | Auth | Description |
|---------|-------|------|-------------|
| POST | `/api/auth/register` | — | Inscription |
| POST | `/api/auth/login` | — | Connexion |
| POST | `/api/auth/refresh` | — | Rafraîchir les tokens |
| POST | `/api/auth/logout` | 🔒 | Déconnexion |

### Compte Utilisateur

| Méthode | Route | Auth | Description |
|---------|-------|------|-------------|
| GET | `/api/users/me` | 🔒 | Profil de l'utilisateur connecté |
| PATCH | `/api/users/me` | 🔒 | Modifier le profil (firstname, lastname, username, birthdate, avatar_url) |
| PATCH | `/api/users/me/password` | 🔒 | Changer le mot de passe |
| PATCH | `/api/users/me/email` | 🔒 | Changer l'email |
| DELETE | `/api/users/me` | 🔒 | Supprimer définitivement le compte (RGPD) |
| GET | `/api/users/me/export` | 🔒 | Exporter toutes les données personnelles (RGPD) |
| GET | `/api/users/me/likes` | 🔒 | Équipes likées |
| GET | `/api/users/me/follows` | 🔒 | Joueurs suivis |

### Jeux

| Méthode | Route | Auth | Description |
|---------|-------|------|-------------|
| GET | `/api/games` | — | Liste des jeux |
| GET | `/api/games/{id}` | 🔒 | Détail d'un jeu |
| POST | `/api/games` | 🔒 Admin | Créer un jeu |
| PATCH | `/api/games/{id}` | 🔒 Admin | Modifier un jeu |
| DELETE | `/api/games/{id}` | 🔒 Admin | Supprimer un jeu |

### Événements

| Méthode | Route | Auth | Description |
|---------|-------|------|-------------|
| GET | `/api/events` | — | Liste des événements |
| GET | `/api/events/{id}` | 🔒 | Détail d'un événement |
| POST | `/api/events` | 🔒 Admin | Créer un événement |
| PATCH | `/api/events/{id}` | 🔒 Admin | Modifier un événement |
| DELETE | `/api/events/{id}` | 🔒 Admin | Supprimer un événement |

### Équipes

| Méthode | Route | Auth | Filtres / Description |
|---------|-------|------|----------------------|
| GET | `/api/teams` | — | `?game=valorant&region=EU&sort=rank` |
| GET | `/api/teams/search` | — | `?q=sentinels` |
| GET | `/api/rankings` | — | `?game=valorant&region=NA` |
| GET | `/api/teams/{id}` | — | Fiche équipe |
| GET | `/api/teams/{id}/matches/live` | — | Matchs en direct de l'équipe |
| GET | `/api/teams/{id}/matches` | — | Historique des matchs |
| GET | `/api/teams/{id}/players` | — | Roster actuel |
| GET | `/api/teams/{id}/transactions` | — | Historique des transferts |
| POST | `/api/teams` | 🔒 Admin | Créer une équipe |
| PATCH | `/api/teams/{id}` | 🔒 Admin | Modifier une équipe |
| DELETE | `/api/teams/{id}` | 🔒 Admin | Supprimer une équipe |
| POST | `/api/teams/{id}/like` | 🔒 | Liker une équipe |
| DELETE | `/api/teams/{id}/like` | 🔒 | Retirer le like |

### Matchs

| Méthode | Route | Auth | Filtres / Description |
|---------|-------|------|----------------------|
| GET | `/api/matches` | — | `?status=live&game=cs2` |
| GET | `/api/matches/live` | — | `?game=valorant` |
| GET | `/api/matches/{id}` | — | Détail d'un match |
| GET | `/api/matches/{id}/stats` | — | Statistiques des joueurs |
| POST | `/api/matches` | 🔒 Admin | Créer un match |
| PATCH | `/api/matches/{id}` | 🔒 Admin | Modifier un match |
| DELETE | `/api/matches/{id}` | 🔒 Admin | Supprimer un match |
| POST | `/api/matches/{id}/map-results` | 🔒 Admin | Ajouter un résultat de map |
| PATCH | `/api/matches/{id}/map-results/{mapId}` | 🔒 Admin | Modifier un résultat de map |
| DELETE | `/api/matches/{id}/map-results/{mapId}` | 🔒 Admin | Supprimer un résultat de map |

### Joueurs

| Méthode | Route | Auth | Filtres / Description |
|---------|-------|------|----------------------|
| GET | `/api/players` | — | `?game=valorant&region=EU&sort=pseudo` |
| GET | `/api/players/search` | — | `?q=tenz` |
| GET | `/api/players/{id}` | — | Fiche joueur |
| GET | `/api/players/{id}/stats` | — | Statistiques |
| GET | `/api/players/{id}/teams` | — | Historique d'équipes |
| GET | `/api/players/{id}/events` | — | Événements disputés |
| GET | `/api/players/{id}/matches` | — | Matchs disputés |
| POST | `/api/players` | 🔒 Admin | Créer un joueur |
| PATCH | `/api/players/{id}` | 🔒 Admin | Modifier un joueur |
| DELETE | `/api/players/{id}` | 🔒 Admin | Supprimer un joueur |
| POST | `/api/players/{id}/follow` | 🔒 | Suivre un joueur |
| DELETE | `/api/players/{id}/follow` | 🔒 | Ne plus suivre |

### Administration des utilisateurs

| Méthode | Route | Auth | Filtres / Description |
|---------|-------|------|----------------------|
| GET | `/api/admin/users` | 🔒 Admin | `?search=&role=user&is_active=true&per_page=20` — liste paginée |
| GET | `/api/admin/users/{id}` | 🔒 Admin | Détail d'un utilisateur |
| PATCH | `/api/admin/users/{id}` | 🔒 Admin | Modifier le rôle (`user`/`admin`) et/ou le statut (`is_active`) |
| DELETE | `/api/admin/users/{id}` | 🔒 Admin | Supprimer un compte utilisateur |

---

## Rate Limiting

| Groupe | Limite |
|--------|--------|
| Routes publiques | 60 req/min par IP |
| Routes authentifiées | 120 req/min par utilisateur |

---

## RGPD

### Export des données (`GET /api/users/me/export`)
Retourne l'intégralité des données personnelles au format JSON : profil, équipes likées, joueurs suivis.

### Suppression du compte (`DELETE /api/users/me`)
Suppression définitive et irréversible : le compte, les tokens, les likes et les follows sont effacés en cascade via les contraintes de clé étrangère PostgreSQL.

---

## Format des réponses

Toutes les réponses suivent le format :

```json
{
  "success": true,
  "message": "Description de l'opération",
  "data": { ... }
}
```

Les erreurs de validation retournent :

```json
{
  "success": false,
  "message": "Données invalides.",
  "data": {
    "email": ["The email field is required."]
  }
}
```

---

## Collection Postman

Le fichier `doc/EsportHub.postman_collection.json` contient tous les endpoints préconfigurés.

### Import

1. Ouvrir Postman → **Import** → sélectionner `doc/EsportHub.postman_collection.json`
2. Lancer `POST Auth / Login` pour un compte utilisateur standard, ou `POST Auth / Login Admin` pour le compte admin
3. Les variables `access_token` / `admin_token` sont automatiquement sauvegardées
4. Les requêtes d'écriture admin utilisent `{{admin_token}}` automatiquement

### Variables de collection

| Variable | Renseignée par |
|----------|---------------|
| `base_url` | Prédéfinie (`http://localhost/api`) |
| `access_token` | Auto-sauvegardée après register/login/refresh |
| `refresh_token` | Auto-sauvegardée après register/login/refresh |
| `admin_token` | Auto-sauvegardée après Login Admin |
| `game_id` | Auto-sauvegardée après `POST /games` |
| `event_id` | Auto-sauvegardée après `POST /events` |
| `team_id` | Auto-sauvegardée après `POST /teams` |
| `match_id` | Auto-sauvegardée après `POST /matches` |
| `player_id` | Auto-sauvegardée après `POST /players` |
| `map_id` | Auto-sauvegardée après `POST /matches/{id}/map-results` |
| `user_id` | À renseigner manuellement (UUID d'un utilisateur) |

---

## Comptes de test (créés par le Seeder)

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| Utilisateur | `john@esporthub.test` | `Password1` |
| Admin | `admin@esporthub.test` | `AdminPassword1` |

---

## Déclaration d'utilisation de l'IA

Ce projet a été développé avec l'assistance de **Claude (Anthropic)** via Claude Code.

### Ce qui a été généré ou co-écrit avec l'IA

| Élément | Détail |
|---------|--------|
| **DatabaseSeeder** | Données réalistes (équipes, joueurs, événements Valorant & CS2) |
| **Factories** | Les 8 factories (`GameFactory`, `TeamFactory`, `PlayerFactory`, `EventFactory`, `GameMatchFactory`, `MatchMapFactory`, `PlayerStatFactory`, `TransactionFactory`) |
| **Tests Feature** | L'intégralité des fichiers de tests (`AuthTest`, `GamesTest`, `EventsTest`, `TeamsTest`, `MatchesTest`, `PlayersTest`, `UsersTest`) — 73 tests, 190 assertions |
| **Collection Postman** | Le fichier `doc/EsportHub.postman_collection.json` (routes admin/user séparées, variables automatiques) |
| **Environnement Docker** | La configuration complète Docker (`Dockerfile`, `docker-compose.yml`, `nginx.conf`, entrypoint) |
| **Scripts BDD** | Les commandes d'export (`pg_dump`) et d'import (`psql`) de la base de données documentées dans ce README |