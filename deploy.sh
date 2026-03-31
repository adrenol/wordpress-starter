#!/bin/bash
set -euo pipefail

read -p "Project name: " PROJECT

if [ -z "$PROJECT" ]; then
  echo "Project name is required"
  exit 1
fi

read -p "Domain: " DOMAIN

if [ -z "$DOMAIN" ]; then
  echo "Domain is required"
  exit 1
fi

REMOTE_PATH="/opt/clients/$PROJECT"

echo "Deploying to $REMOTE_PATH"

cat > .env <<EOF
PROJECT=$PROJECT
DOMAIN=$DOMAIN
EOF

ssh myserver "mkdir -p $REMOTE_PATH"

rsync -av --exclude-from='.rsyncignore' . myserver:$REMOTE_PATH/

ssh myserver "cd $REMOTE_PATH && docker compose up -d && docker compose ps"