#!/bin/bash
set -euo pipefail

GREEN="\033[0;32m"
YELLOW="\033[1;33m"
RED="\033[0;31m"
BLUE="\033[0;34m"
NC="\033[0m"

START_TIME=$(date +%s)

read -p "Project name: " PROJECT
if [ -z "$PROJECT" ]; then
  echo -e "${RED}Project name is required${NC}"
  exit 1
fi

read -p "Domain: " DOMAIN
if [ -z "$DOMAIN" ]; then
  echo -e "${RED}Domain is required${NC}"
  exit 1
fi

REMOTE_PATH="/opt/clients/$PROJECT"

echo ""
echo -e "${BLUE}Deploying project:${NC} ${PROJECT}"
echo -e "${BLUE}Domain:${NC} https://$PROJECT.$DOMAIN"
echo -e "${BLUE}Remote path:${NC} $REMOTE_PATH"
echo ""

echo -e "${YELLOW}→ Creating remote directory...${NC}"
ssh myserver "mkdir -p $REMOTE_PATH"

echo -e "${YELLOW}→ Syncing files...${NC}"
rsync -az --delete --exclude-from='.rsyncignore' . myserver:$REMOTE_PATH/

echo -e "${YELLOW}→ Writing .env and restarting containers...${NC}"
ssh myserver "
printf 'PROJECT=%s\nDOMAIN=%s\n' '$PROJECT' '$DOMAIN' > $REMOTE_PATH/.env &&
cd $REMOTE_PATH &&
docker compose up -d --force-recreate &&
docker compose ps
"

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo ""
echo -e "${GREEN}--------------------------------------${NC}"
echo -e "${GREEN}✅ Deploy completed successfully${NC}"
echo -e "${GREEN}🌐 https://$PROJECT.$DOMAIN${NC}"
echo -e "${GREEN}⏱ Duration: ${DURATION}s${NC}"
echo -e "${GREEN}--------------------------------------${NC}"