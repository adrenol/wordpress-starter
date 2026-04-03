#!/bin/bash
source "$(dirname "$0")/utils.sh"

read -p "Project name: " PROJECT
if [ -z "$PROJECT" ]; then
  error "Project name is required"
  exit 1
fi

REMOTE_PATH="/opt/clients/$PROJECT/wp-content/"
LOCAL_PATH="./wp-content/"

echo ""
log "Pulling wp-content from server"
log "Remote: myserver:$REMOTE_PATH"
log "Local:  $LOCAL_PATH"
echo ""

step "Syncing wp-content..."
rsync -az \
  --delete-excluded \
  --exclude-from=".rsyncignore" \
  "myserver:$REMOTE_PATH" \
  "$LOCAL_PATH"

echo ""
success "--------------------------------------"
success "wp-content pulled successfully"
success "Project: $PROJECT"
success "--------------------------------------"