#!/bin/bash
source "$(dirname "$0")/source/utils.sh"

START_TIME=$(date +%s)

read -p "Project name: " PROJECT
[ -z "$PROJECT" ] && error "Project name is required" && exit 1

read -p "Domain: " DOMAIN
[ -z "$DOMAIN" ] && error "Domain is required" && exit 1

REMOTE_PATH="/opt/clients/$PROJECT"

echo ""
log "Deploying project: $PROJECT"
log "Domain: https://$PROJECT.$DOMAIN"
log "Remote path: $REMOTE_PATH"
echo ""

step "Creating remote directory..."
ssh myserver "mkdir -p '$REMOTE_PATH'"

step "Syncing files..."
rsync -az --delete --exclude-from='.rsyncignore' . "myserver:$REMOTE_PATH/"

step "Starting containers..."
ssh myserver "
printf 'PROJECT=%s\nDOMAIN=%s\n' '$PROJECT' '$DOMAIN' > '$REMOTE_PATH/.env' &&
cd '$REMOTE_PATH' &&
docker compose up -d --force-recreate
"

step "Configuring WordPress..."
ssh myserver "
cd '$REMOTE_PATH' &&

for i in \$(seq 1 30); do
  if docker compose run --rm cli wp core is-installed --allow-root >/dev/null 2>&1; then
    break
  fi
  sleep 2
done &&

docker compose run --rm cli wp core is-installed --allow-root >/dev/null 2>&1 &&

(
  docker compose run --rm cli wp language core is-installed ru_RU --allow-root >/dev/null 2>&1 ||
  docker compose run --rm cli wp language core install ru_RU --allow-root
) &&

docker compose run --rm cli wp site switch-language ru_RU --allow-root &&

(
  docker compose run --rm cli wp user get admin --allow-root >/dev/null 2>&1 ||
  docker compose run --rm cli wp user create admin admin@example.com \
    --role=administrator \
    --user_pass=admin \
    --allow-root
)
"

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo ""
success "--------------------------------------"
success "Deploy completed successfully"
success "https://$PROJECT.$DOMAIN"
success "Duration: ${DURATION}s"
success "--------------------------------------"