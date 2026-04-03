#!/bin/bash
source "$(dirname "$0")/utils.sh"

PROJECT=""

while [[ "$#" -gt 0 ]]; do
  case $1 in
    -p|--project) PROJECT="$2"; shift ;;
    -h|--help)
      echo "Usage: pull.sh [options]"
      echo "Options:"
      echo "  -p, --project <name>    Project name"
      exit 0
      ;;
    *) echo "Unknown parameter passed: $1"; exit 1 ;;
  esac
  shift
done

if [ -z "$PROJECT" ]; then
  read -p "Project name: " PROJECT
fi

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
rsync -az "myserver:$REMOTE_PATH" "$LOCAL_PATH"

echo ""
success "--------------------------------------"
success "wp-content pulled successfully"
success "Project: $PROJECT"
success "--------------------------------------"