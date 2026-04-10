# Déploiement Laravel + Vite sur mutualisé LWS (sans SSH, sans root)

## Vite : pas de processus sur le serveur

En production, `@vite()` lit uniquement `public/build/manifest.json` et les fichiers hashés dans `public/build/assets/`. **Aucun `npm run dev` ni Node n’est nécessaires sur LWS.** Tu compiles en local (ou en CI), puis tu envoies le dossier `public/build/`.

## Build avant envoi

Depuis la racine du projet (Node **20.19+** requis pour Vite 8) :

```bash
./scripts/build-for-production.sh
```

Ou manuellement :

```bash
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
```

## Artefacts à déployer (Git + FTP)

1. **Code** : tout le dépôt (sauf `.env` local — utiliser `.env` adapté sur le serveur).
2. **`vendor/`** : soit généré localement par le script ci-dessus et uploadé par FTP, soit `composer install` si ton hébergeur propose Composer en ligne de commande (même version PHP que le mutualisé).
3. **`public/build/`** : **obligatoire** après chaque changement des assets (`resources/css`, `resources/js`, vues utilisant Vite). Ce dossier est listé dans [`.gitignore`](../.gitignore) : il **n’est pas** poussé par Git tant qu’il est ignoré.

### Deux stratégies pour `public/build/`

| Stratégie | Avantage | Inconvénient |
|-----------|----------|--------------|
| **FTP après chaque `npm run build`** | Repo propre, pas de fichiers hashés versionnés | Oublier le dossier = erreur `ViteManifestNotFoundException` |
| **Retirer `/public/build` du `.gitignore` et committer le build** | Déploiement Git seul, rien à oublier | Diff bruyants à chaque build |

## Document root (LWS)

**Idéal** : dans le panneau LWS, définir le répertoire racine du domaine sur le dossier **`public/`** de Laravel (ex. `.../lotixam/public`).

**Secours** : si le domaine pointe vers la racine du projet, le fichier [`.htaccess`](../.htaccess) à la racine réécrit déjà les requêtes vers `public/`. Vérifie que `mod_rewrite` est activé.

## Checklist `.env` production

Voir [`.env.production.example`](../.env.production.example). Points clés :

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY` défini (`php artisan key:generate` une fois si besoin, si CLI disponible)
- `APP_URL` et `SITE_URL` en HTTPS si le site est en HTTPS
- `SESSION_SECURE_COOKIE=true` si tout le site est servi en HTTPS
- Base de données, mail, etc. selon LWS

## Permissions (mutualisé)

Le serveur web doit pouvoir écrire dans :

- `storage/` (logs, cache, sessions, vues compilées)
- `bootstrap/cache/`

Souvent : répertoires en `775` ou `755` selon les règles LWS ; propriétaire = utilisateur FTP ou `www-data` selon la doc hébergeur.

**Piège** : ne pas laisser un `bootstrap/cache/config.php` généré sur une autre machine (Docker, autre chemin) — il fige des chemins absolus. Sur mutualisé sans `php artisan config:cache`, supprime ce fichier s’il pose problème.

## Fichiers publics Laravel

Si tu utilises `storage/app/public` : exécuter une fois `php artisan storage:link` si le panneau / un script PHP le permet, pour créer `public/storage` → `storage/app/public`.

## Queues et tâches en arrière-plan

Sur mutualisé sans worker : garde `QUEUE_CONNECTION=sync` (défaut dans `.env.example`). Rien ne doit tourner en continu ; seul Apache/PHP répond aux requêtes.

## Liste rapide FTP (après build)

Référence minimale : [scripts/ftp-upload-after-build.txt](../scripts/ftp-upload-after-build.txt).

À synchroniser en plus du code habituel :

- `vendor/` (si pas de Composer sur le serveur)
- **`public/build/manifest.json`**
- **`public/build/assets/*`**
