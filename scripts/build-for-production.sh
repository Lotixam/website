#!/usr/bin/env bash
# Build artefacts pour déploiement mutualisé (LWS) : vendor optimisé + assets Vite.
# Prérequis : PHP + Composer, Node.js 20.19+ (voir package.json / Vite 8).
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

echo "==> composer install --no-dev --optimize-autoloader --no-interaction"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> npm ci && npm run build"
npm ci
npm run build

echo "==> OK. À uploader sur le serveur : tout le projet + vendor/ + public/build/ (voir docs/deployment-lws.md)"
