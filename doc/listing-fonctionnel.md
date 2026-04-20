# Listing Fonctionnel — EsportHub API

> **Légende** : 🔒 Authentification requise (Bearer token) · 👑 Rôle admin requis · — Public · ✅ Implémenté

---

## F01 — Authentification

### F01.1 · Inscription
| | |
|---|---|
| **Méthode** | `POST /api/auth/register` |
| **Auth** | — |
| **Corps** | `firstname` (req), `lastname` (req), `email` (req, unique), `password` (req, min 8 car., maj+min+chiffre), `password_confirmation` (req), `username` (req, unique, max 50), `birthdate` (req, date) |
| **Réponse 201** | `{ access_token, refresh_token, token_type, user }` |
| **Erreurs** | 422 email/username déjà utilisé · 422 mot de passe trop faible |
| **Statut** | ✅ |

### F01.2 · Connexion
| | |
|---|---|
| **Méthode** | `POST /api/auth/login` |
| **Auth** | — |
| **Corps** | `email` (req), `password` (req) |
| **Réponse 200** | `{ access_token, refresh_token, token_type, user }` |
| **Erreurs** | 401 identifiants invalides |
| **Statut** | ✅ |

### F01.3 · Rafraîchissement de token
| | |
|---|---|
| **Méthode** | `POST /api/auth/refresh` |
| **Auth** | — |
| **Corps** | `refresh_token` (req) |
| **Réponse 200** | `{ access_token, refresh_token, token_type }` |
| **Erreurs** | 401 token invalide · 401 token expiré · 401 token révoqué |
| **Statut** | ✅ |

### F01.4 · Déconnexion
| | |
|---|---|
| **Méthode** | `POST /api/auth/logout` |
| **Auth** | 🔒 |
| **Corps** | — |
| **Réponse 200** | Message de confirmation |
| **Comportement** | Révoque le refresh token + supprime l'access token Sanctum |
| **Statut** | ✅ |

---

## F02 — Compte Utilisateur

### F02.1 · Consulter son profil
| | |
|---|---|
| **Méthode** | `GET /api/users/me` |
| **Auth** | 🔒 |
| **Réponse 200** | `{ id, firstname, lastname, username, email, birthdate, avatar_url, created_at }` |
| **Statut** | ✅ |

### F02.2 · Modifier son profil
| | |
|---|---|
| **Méthode** | `PATCH /api/users/me` |
| **Auth** | 🔒 |
| **Corps** | `firstname` (opt), `lastname` (opt), `username` (opt, unique), `birthdate` (opt, date), `avatar_url` (opt, URL) |
| **Réponse 200** | Profil mis à jour |
| **Erreurs** | 422 username déjà pris |
| **Statut** | ✅ |

### F02.3 · Changer son mot de passe
| | |
|---|---|
| **Méthode** | `PATCH /api/users/me/password` |
| **Auth** | 🔒 |
| **Corps** | `current_password` (req), `new_password` (req, min 8 car., maj+min+chiffre) |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 422 mot de passe actuel incorrect · 422 nouveau mot de passe trop faible |
| **Statut** | ✅ |

### F02.4 · Changer son email
| | |
|---|---|
| **Méthode** | `PATCH /api/users/me/email` |
| **Auth** | 🔒 |
| **Corps** | `email` (req, unique), `password` (req, confirmation identité) |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 422 email déjà utilisé · 422 mot de passe incorrect |
| **Statut** | ✅ |

### F02.5 · Supprimer son compte (RGPD)
| | |
|---|---|
| **Méthode** | `DELETE /api/users/me` |
| **Auth** | 🔒 |
| **Corps** | — |
| **Réponse 200** | Message de confirmation |
| **Comportement** | Suppression définitive et irréversible : compte, tokens, likes et follows effacés en cascade |
| **Statut** | ✅ |

### F02.6 · Exporter ses données (RGPD)
| | |
|---|---|
| **Méthode** | `GET /api/users/me/export` |
| **Auth** | 🔒 |
| **Réponse 200** | `{ user, liked_teams[], followed_players[] }` — intégralité des données personnelles |
| **Statut** | ✅ |

### F02.7 · Consulter ses équipes likées
| | |
|---|---|
| **Méthode** | `GET /api/users/me/likes` |
| **Auth** | 🔒 |
| **Réponse 200** | Liste des équipes avec `liked_at` |
| **Statut** | ✅ |

### F02.8 · Consulter ses joueurs suivis
| | |
|---|---|
| **Méthode** | `GET /api/users/me/follows` |
| **Auth** | 🔒 |
| **Réponse 200** | Liste des joueurs avec `followed_at` |
| **Statut** | ✅ |

---

## F03 — Jeux

### F03.1 · Lister les jeux
| | |
|---|---|
| **Méthode** | `GET /api/games` |
| **Auth** | — |
| **Réponse 200** | `[{ id, name, slug, logo_url }]` |
| **Statut** | ✅ |

### F03.2 · Créer un jeu
| | |
|---|---|
| **Méthode** | `POST /api/games` |
| **Auth** | 🔒 👑 |
| **Corps** | `name` (req, unique), `logo_url` (req, URL) · `slug` auto-généré depuis `name` si absent |
| **Réponse 201** | Jeu créé |
| **Erreurs** | 403 non admin · 422 nom ou slug déjà existant |
| **Statut** | ✅ |

### F03.3 · Détail d'un jeu
| | |
|---|---|
| **Méthode** | `GET /api/games/{id}` |
| **Auth** | — |
| **Réponse 200** | `{ id, name, slug, logo_url }` |
| **Erreurs** | 404 jeu introuvable |
| **Statut** | ✅ |

### F03.4 · Modifier un jeu
| | |
|---|---|
| **Méthode** | `PATCH /api/games/{id}` |
| **Auth** | 🔒 👑 |
| **Corps** | `name` (opt), `slug` (opt), `logo_url` (opt) |
| **Réponse 200** | Jeu mis à jour |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F03.5 · Supprimer un jeu
| | |
|---|---|
| **Méthode** | `DELETE /api/games/{id}` |
| **Auth** | 🔒 👑 |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

---

## F04 — Événements

### F04.1 · Lister les événements
| | |
|---|---|
| **Méthode** | `GET /api/events` |
| **Auth** | — |
| **Paramètres** | `?game=valorant` · `?status=upcoming\|ongoing\|completed` |
| **Réponse 200** | `[{ id, name, logo_url, prize_pool, start_date, end_date, status, game }]` |
| **Statut** | ✅ |

### F04.2 · Créer un événement
| | |
|---|---|
| **Méthode** | `POST /api/events` |
| **Auth** | 🔒 👑 |
| **Corps** | `game_id` (req), `name` (req, max 200), `status` (req, upcoming/ongoing/completed), `logo_url` (opt, URL), `prize_pool` (opt), `start_date` (opt, date), `end_date` (opt, date, ≥ start_date) |
| **Réponse 201** | Événement créé |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F04.3 · Détail d'un événement
| | |
|---|---|
| **Méthode** | `GET /api/events/{id}` |
| **Auth** | 🔒 |
| **Réponse 200** | Fiche événement complète |
| **Statut** | ✅ |

### F04.4 · Modifier un événement
| | |
|---|---|
| **Méthode** | `PATCH /api/events/{id}` |
| **Auth** | 🔒 👑 |
| **Corps** | Tous les champs de F04.2 (optionnels) |
| **Réponse 200** | Événement mis à jour |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F04.5 · Supprimer un événement
| | |
|---|---|
| **Méthode** | `DELETE /api/events/{id}` |
| **Auth** | 🔒 👑 |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

---

## F05 — Équipes

### F05.1 · Lister les équipes
| | |
|---|---|
| **Méthode** | `GET /api/teams` |
| **Auth** | — |
| **Paramètres** | `?game=valorant` · `?region=EU` · `?sort=rank\|earnings` · `?page=` |
| **Réponse 200** | Liste paginée `[{ id, name, logo_url, region, rank, earnings, game }]` |
| **Statut** | ✅ |

### F05.2 · Rechercher une équipe
| | |
|---|---|
| **Méthode** | `GET /api/teams/search` |
| **Auth** | — |
| **Paramètres** | `?q=sentinels` (recherche insensible à la casse sur le nom) |
| **Réponse 200** | Liste des équipes correspondantes |
| **Statut** | ✅ |

### F05.3 · Classement des équipes
| | |
|---|---|
| **Méthode** | `GET /api/rankings` |
| **Auth** | — |
| **Paramètres** | `?game=valorant` · `?region=NA` |
| **Réponse 200** | Équipes triées par rang avec earnings |
| **Statut** | ✅ |

### F05.4 · Fiche équipe
| | |
|---|---|
| **Méthode** | `GET /api/teams/{id}` |
| **Auth** | — |
| **Réponse 200** | `{ id, name, logo_url, region, rank, earnings, game }` |
| **Erreurs** | 404 équipe introuvable |
| **Statut** | ✅ |

### F05.5 · Créer une équipe
| | |
|---|---|
| **Méthode** | `POST /api/teams` |
| **Auth** | 🔒 👑 |
| **Corps** | `game_id` (req), `name` (req, unique), `logo_url` (req, URL), `region` (req), `rank` (opt, entier), `earnings` (opt, décimal) |
| **Réponse 201** | Équipe créée |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F05.6 · Modifier une équipe
| | |
|---|---|
| **Méthode** | `PATCH /api/teams/{id}` |
| **Auth** | 🔒 👑 |
| **Corps** | Tous les champs de F05.5 (optionnels) |
| **Réponse 200** | Équipe mise à jour |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F05.7 · Supprimer une équipe
| | |
|---|---|
| **Méthode** | `DELETE /api/teams/{id}` |
| **Auth** | 🔒 👑 |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F05.8 · Liker une équipe
| | |
|---|---|
| **Méthode** | `POST /api/teams/{id}/like` |
| **Auth** | 🔒 |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 400 équipe déjà likée |
| **Statut** | ✅ |

### F05.9 · Retirer son like
| | |
|---|---|
| **Méthode** | `DELETE /api/teams/{id}/like` |
| **Auth** | 🔒 |
| **Réponse 200** | Message de confirmation |
| **Statut** | ✅ |

### F05.10 · Matchs en direct d'une équipe
| | |
|---|---|
| **Méthode** | `GET /api/teams/{id}/matches/live` |
| **Auth** | — |
| **Réponse 200** | Liste des matchs avec `status=live` de l'équipe |
| **Statut** | ✅ |

### F05.11 · Historique des matchs d'une équipe
| | |
|---|---|
| **Méthode** | `GET /api/teams/{id}/matches` |
| **Auth** | — |
| **Réponse 200** | Liste paginée des matchs de l'équipe, triés par date décroissante |
| **Statut** | ✅ |

### F05.12 · Roster d'une équipe
| | |
|---|---|
| **Méthode** | `GET /api/teams/{id}/players` |
| **Auth** | — |
| **Réponse 200** | Liste des joueurs dont `current_team_id` correspond à l'équipe |
| **Statut** | ✅ |

### F05.13 · Historique des transferts d'une équipe
| | |
|---|---|
| **Méthode** | `GET /api/teams/{id}/transactions` |
| **Auth** | — |
| **Réponse 200** | `[{ player, type (join/leave), transaction_date, description }]` |
| **Statut** | ✅ |

---

## F06 — Matchs

### F06.1 · Lister les matchs
| | |
|---|---|
| **Méthode** | `GET /api/matches` |
| **Auth** | — |
| **Paramètres** | `?status=live\|upcoming\|completed` · `?game=cs2` · `?page=` |
| **Réponse 200** | Liste paginée triée par `scheduled_at` décroissant |
| **Statut** | ✅ |

### F06.2 · Matchs en direct
| | |
|---|---|
| **Méthode** | `GET /api/matches/live` |
| **Auth** | — |
| **Paramètres** | `?game=valorant` |
| **Réponse 200** | Tous les matchs avec `status=live` |
| **Statut** | ✅ |

### F06.3 · Détail d'un match
| | |
|---|---|
| **Méthode** | `GET /api/matches/{id}` |
| **Auth** | — |
| **Réponse 200** | `{ id, event, team1, team2, score_team1, score_team2, status, scheduled_at, maps[] }` |
| **Erreurs** | 404 match introuvable |
| **Statut** | ✅ |

### F06.4 · Statistiques d'un match
| | |
|---|---|
| **Méthode** | `GET /api/matches/{id}/stats` |
| **Auth** | — |
| **Réponse 200** | `[{ player, team, match_map, rating, acs, kd_ratio, kast, adr, kpr, headshot_pct, clutch_pct }]` |
| **Statut** | ✅ |

### F06.5 · Créer un match
| | |
|---|---|
| **Méthode** | `POST /api/matches` |
| **Auth** | 🔒 👑 |
| **Corps** | `team1_id` (req, ≠ team2_id), `team2_id` (req), `status` (req, upcoming/live/completed), `event_id` (opt), `score_team1` (opt, ≥ 0), `score_team2` (opt, ≥ 0), `scheduled_at` (opt, date) |
| **Réponse 201** | Match créé |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F06.6 · Modifier un match
| | |
|---|---|
| **Méthode** | `PATCH /api/matches/{id}` |
| **Auth** | 🔒 👑 |
| **Corps** | Tous les champs de F06.5 (optionnels) |
| **Réponse 200** | Match mis à jour |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F06.7 · Supprimer un match
| | |
|---|---|
| **Méthode** | `DELETE /api/matches/{id}` |
| **Auth** | 🔒 👑 |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F06.8 · Ajouter un résultat de map
| | |
|---|---|
| **Méthode** | `POST /api/matches/{id}/map-results` |
| **Auth** | 🔒 👑 |
| **Corps** | `map_name` (req), `score_team1` (req, ≥ 0), `score_team2` (req, ≥ 0), `order` (req, entier) |
| **Réponse 201** | Map créée |
| **Erreurs** | 403 non admin · 404 match introuvable |
| **Statut** | ⚠️ Non implémenté |

### F06.9 · Modifier un résultat de map
| | |
|---|---|
| **Méthode** | `PATCH /api/matches/{id}/map-results/{mapId}` |
| **Auth** | 🔒 👑 |
| **Corps** | `map_name` (opt), `score_team1` (opt), `score_team2` (opt), `order` (opt) |
| **Réponse 200** | Map mise à jour |
| **Erreurs** | 403 non admin · 404 introuvable |
| **Statut** | ⚠️ Non implémenté |

### F06.10 · Supprimer un résultat de map
| | |
|---|---|
| **Méthode** | `DELETE /api/matches/{id}/map-results/{mapId}` |
| **Auth** | 🔒 👑 |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 403 non admin · 404 introuvable |
| **Statut** | ⚠️ Non implémenté |

---

## F07 — Joueurs

### F07.1 · Lister les joueurs
| | |
|---|---|
| **Méthode** | `GET /api/players` |
| **Auth** | — |
| **Paramètres** | `?game=valorant` · `?region=EU` · `?sort=pseudo` · `?page=` |
| **Réponse 200** | Liste paginée `[{ id, pseudo, real_name, nationality, photo_url, game, current_team }]` |
| **Statut** | ✅ |

### F07.2 · Rechercher un joueur
| | |
|---|---|
| **Méthode** | `GET /api/players/search` |
| **Auth** | — |
| **Paramètres** | `?q=tenz` (recherche insensible à la casse sur pseudo et real_name) |
| **Réponse 200** | Liste des joueurs correspondants |
| **Statut** | ✅ |

### F07.3 · Fiche joueur
| | |
|---|---|
| **Méthode** | `GET /api/players/{id}` |
| **Auth** | — |
| **Réponse 200** | `{ id, pseudo, real_name, nationality, photo_url, game, current_team }` |
| **Erreurs** | 404 joueur introuvable |
| **Statut** | ✅ |

### F07.4 · Créer un joueur
| | |
|---|---|
| **Méthode** | `POST /api/players` |
| **Auth** | 🔒 👑 |
| **Corps** | `game_id` (req), `pseudo` (req, max 100), `current_team_id` (opt), `real_name` (opt), `nationality` (opt, max 10), `photo_url` (opt, URL) |
| **Réponse 201** | Joueur créé |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F07.5 · Modifier un joueur
| | |
|---|---|
| **Méthode** | `PATCH /api/players/{id}` |
| **Auth** | 🔒 👑 |
| **Corps** | Tous les champs de F07.4 (optionnels) |
| **Réponse 200** | Joueur mis à jour |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F07.6 · Supprimer un joueur
| | |
|---|---|
| **Méthode** | `DELETE /api/players/{id}` |
| **Auth** | 🔒 👑 |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F07.7 · Suivre un joueur
| | |
|---|---|
| **Méthode** | `POST /api/players/{id}/follow` |
| **Auth** | 🔒 |
| **Réponse 200** | Message de confirmation |
| **Erreurs** | 400 joueur déjà suivi |
| **Statut** | ✅ |

### F07.8 · Ne plus suivre un joueur
| | |
|---|---|
| **Méthode** | `DELETE /api/players/{id}/follow` |
| **Auth** | 🔒 |
| **Réponse 200** | Message de confirmation |
| **Statut** | ✅ |

### F07.9 · Statistiques d'un joueur
| | |
|---|---|
| **Méthode** | `GET /api/players/{id}/stats` |
| **Auth** | — |
| **Réponse 200** | `[{ match, match_map, team, rating, acs, kd_ratio, kast, adr, kpr, headshot_pct, clutch_pct }]` |
| **Statut** | ✅ |

### F07.10 · Historique des équipes d'un joueur
| | |
|---|---|
| **Méthode** | `GET /api/players/{id}/teams` |
| **Auth** | — |
| **Réponse 200** | `[{ team, type (join/leave), transaction_date, description }]` trié par date décroissante |
| **Statut** | ✅ |

### F07.11 · Événements disputés par un joueur
| | |
|---|---|
| **Méthode** | `GET /api/players/{id}/events` |
| **Auth** | — |
| **Réponse 200** | Événements dans lesquels le joueur a des stats, triés par date décroissante |
| **Statut** | ✅ |

### F07.12 · Matchs disputés par un joueur
| | |
|---|---|
| **Méthode** | `GET /api/players/{id}/matches` |
| **Auth** | — |
| **Réponse 200** | Liste paginée des matchs du joueur, triés par date décroissante |
| **Statut** | ✅ |

---

## F08 — Administration des utilisateurs

### F08.1 · Lister tous les utilisateurs
| | |
|---|---|
| **Méthode** | `GET /api/admin/users` |
| **Auth** | 🔒 👑 |
| **Paramètres** | `?role=user\|admin` · `?is_active=true\|false` · `?search=` (username/email/nom) · `?per_page=20` |
| **Réponse 200** | Liste paginée `[{ id, username, firstname, lastname, email, birthdate, avatar_url, role, is_active, created_at }]` + meta |
| **Erreurs** | 403 non admin |
| **Statut** | ✅ |

### F08.2 · Détail d'un utilisateur
| | |
|---|---|
| **Méthode** | `GET /api/admin/users/{id}` |
| **Auth** | 🔒 👑 |
| **Réponse 200** | `{ id, username, firstname, lastname, email, birthdate, avatar_url, role, is_active, created_at }` |
| **Erreurs** | 403 non admin · 404 utilisateur introuvable |
| **Statut** | ✅ |

### F08.3 · Modifier le rôle ou le statut d'un utilisateur
| | |
|---|---|
| **Méthode** | `PATCH /api/admin/users/{id}` |
| **Auth** | 🔒 👑 |
| **Corps** | `role` (opt, `user`/`admin`) · `is_active` (opt, booléen) |
| **Réponse 200** | Utilisateur mis à jour |
| **Erreurs** | 403 non admin · 404 utilisateur introuvable |
| **Statut** | ✅ |

### F08.4 · Supprimer un utilisateur
| | |
|---|---|
| **Méthode** | `DELETE /api/admin/users/{id}` |
| **Auth** | 🔒 👑 |
| **Réponse 200** | Message de confirmation |
| **Comportement** | Supprime les tokens, refresh tokens et le compte — impossible de supprimer son propre compte via cette route |
| **Erreurs** | 403 non admin · 403 auto-suppression interdite · 404 utilisateur introuvable |
| **Statut** | ✅ |

---

## Récapitulatif

| Module | Fonctionnalités | Public | Authentifié | Admin |
|--------|-----------------|--------|-------------|-------|
| Auth | 4 | 3 | 1 | 0 |
| Utilisateur | 8 | 0 | 8 | 0 |
| Jeux | 5 | 1 | 1 | 3 |
| Événements | 5 | 1 | 1 | 3 |
| Équipes | 13 | 8 | 2 | 3 |
| Matchs | 10 | 4 | 0 | 6 |
| Joueurs | 12 | 9 | 2 | 3 |
| Admin utilisateurs | 4 | 0 | 0 | 4 |
| **Total** | **61** | **26** | **15** | **22** |

---

## Contraintes transversales

| Contrainte | Détail |
|-----------|--------|
| **Rate limiting** | 60 req/min (public) · 120 req/min (authentifié) |
| **Format des réponses** | `{ success: bool, message: string, data: any }` systématique |
| **Pagination** | 15 éléments par page sur les listes longues |
| **UUID** | Toutes les clés primaires sont des UUID v4 |
| **Tokens** | Access token : durée de session · Refresh token : 30 jours, haché en SHA-256 |
| **RGPD** | Export JSON intégral + suppression en cascade garantie par les FK PostgreSQL |
| **Rôles** | `user` (défaut) · `admin` — colonne `role` sur la table `users` |
