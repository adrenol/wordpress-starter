#!/bin/bash
source "$(dirname "$0")/utils.sh"

START_TIME=$(date +%s)

# ── Collect input ──────────────────────────────────────────────

read -p "Project name: " PROJECT
[ -z "$PROJECT" ] && error "Project name is required" && exit 1

read -p "Domain: " DOMAIN
[ -z "$DOMAIN" ] && error "Domain is required" && exit 1

read -p "Theme name: " THEME
[ -z "$THEME" ] && error "Theme name is required" && exit 1

read -p "Plugin to activate (leave empty to skip): " PLUGIN_ACTIVATE

REMOTE_PATH="/opt/clients/$PROJECT"
URL="https://$PROJECT.$DOMAIN"
THEME_DIR="wp-content/themes/$THEME"

echo ""
log "Deploying project: $PROJECT"
log "Domain: $URL"
log "Remote path: $REMOTE_PATH"
echo ""

# ── Build ──────────────────────────────────────────────────────

build_assets() {
  step "Building Tailwind styles..."
  cd "$THEME_DIR" &&
  npm run build &&
  cd - >/dev/null || exit 1
}

# ── Sync ───────────────────────────────────────────────────────

sync_files() {
  step "Creating remote directory..."
  ssh myserver "mkdir -p '$REMOTE_PATH'"

  step "Syncing files..."
  rsync -az \
    --delete --delete-excluded \
    --exclude-from=".rsyncignore" \
    --exclude=".env" \
    --exclude="$THEME_DIR/node_modules/" \
    --exclude="**/node_modules/" \
    --exclude="AGENTS.md" \
    . "myserver:$REMOTE_PATH/"
}

# ── Permissions ────────────────────────────────────────────────

fix_permissions() {
  step "Fixing permissions..."
  ssh myserver "
mkdir -p '$REMOTE_PATH/wp-content/uploads' &&
chmod -R 775 '$REMOTE_PATH/wp-content' &&
chown -R 33:33 '$REMOTE_PATH/wp-content'
" || true
}

# ── Docker ─────────────────────────────────────────────────────

start_containers() {
  step "Starting containers..."
  ssh myserver "
printf 'PROJECT=%s\nDOMAIN=%s\n' '$PROJECT' '$DOMAIN' > '$REMOTE_PATH/.env' &&
cd '$REMOTE_PATH' &&
docker compose up -d --force-recreate
"
}

# ── WordPress setup ────────────────────────────────────────────

setup_wordpress() {
  step "Configuring WordPress..."
  ssh myserver "
cd '$REMOTE_PATH' &&

echo '[1/5] Checking WordPress installation...' &&

if ! docker compose run --rm -T cli wp core is-installed --url='$URL' --allow-root >/dev/null 2>&1; then
  echo '[install] WordPress is not installed, installing...' &&
  docker compose run --rm -T cli wp core install \
    --url='$URL' \
    --title='$PROJECT' \
    --admin_user='admin' \
    --admin_password='admin' \
    --admin_email='admin@example.com' \
    --skip-email \
    --allow-root
else
  echo '[ok] WordPress is already installed'
fi &&

echo '[2/5] Installing ru-RU language...' &&
(
  docker compose run --rm -T cli wp language core is-installed ru_RU --url='$URL' --allow-root >/dev/null 2>&1 ||
  docker compose run --rm -T cli wp language core install ru_RU --url='$URL' --allow-root
) &&

echo '[3/5] Switching language...' &&
docker compose run --rm -T cli wp site switch-language ru_RU --url='$URL' --allow-root &&

echo '[4/5] Activating custom theme if found...' &&
THEME=\$(docker compose run --rm -T cli wp theme list --field=name --format=csv | grep -v '^twentytwenty' | head -n 1 || true) &&

if [ -n \"\$THEME\" ]; then
  docker compose run --rm -T cli wp theme activate \"\$THEME\" --url='$URL' --allow-root
else
  echo '[warn] No custom theme found, keeping current theme'
fi &&

echo '[5/5] Activating requested plugin...' &&

if [ -n \"$PLUGIN_ACTIVATE\" ]; then
  echo \"[plugin] Activating $PLUGIN_ACTIVATE...\" &&
  docker compose run --rm -T cli wp plugin activate $PLUGIN_ACTIVATE --url='$URL' --allow-root || true
else
  echo '[info] Skipped plugin activation (none specified)'
fi
"
}

# ── Run ────────────────────────────────────────────────────────

build_assets
sync_files
fix_permissions
start_containers
setup_wordpress

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo ""
success "--------------------------------------"
success "Deploy completed successfully"
success "$URL"
success "Duration: ${DURATION}s"
success "--------------------------------------"
