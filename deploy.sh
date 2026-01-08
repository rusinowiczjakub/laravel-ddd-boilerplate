#!/bin/bash
set -e

echo "🚀 Starting RepairFlow deployment..."
echo ""

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}📋 Step 0/4: Updating version from CHANGELOG.md...${NC}"
if [ -f "scripts/update-version.sh" ]; then
    bash scripts/update-version.sh
else
    echo -e "${YELLOW}⚠️  Version update script not found, skipping...${NC}"
fi

echo ""
echo -e "${BLUE}📦 Step 1/4: Building new Docker images...${NC}"
docker compose -f docker-compose.prod.yml build --no-cache

echo ""
echo -e "${YELLOW}⚠️  Step 2/4: Restarting containers (brief downtime)...${NC}"
docker compose -f docker-compose.prod.yml down
docker compose -f docker-compose.prod.yml up -d

echo ""
echo -e "${BLUE}⏳ Step 3/4: Waiting for application to be ready...${NC}"
echo "   - Application will enter maintenance mode"
echo "   - Database migrations will run"
echo "   - Caches will be optimized"
echo "   - Application will exit maintenance mode"
echo ""

# Wait for containers to be healthy
sleep 5

# Check if application container is running
if docker compose -f docker-compose.prod.yml ps | grep -q "repairflow_app.*Up"; then
    echo -e "${GREEN}✅ Application container is running${NC}"
else
    echo -e "${YELLOW}⚠️  Warning: Application container may not be running properly${NC}"
fi

# Check if nginx container is running
if docker compose -f docker-compose.prod.yml ps | grep -q "repairflow_nginx.*Up"; then
    echo -e "${GREEN}✅ Nginx container is running${NC}"
else
    echo -e "${YELLOW}⚠️  Warning: Nginx container may not be running properly${NC}"
fi

echo ""
echo -e "${GREEN}✅ Deployment complete!${NC}"
echo ""
echo "📊 Container status:"
docker compose -f docker-compose.prod.yml ps

echo ""
echo "💡 Useful commands:"
echo "   - View logs: docker compose -f docker-compose.prod.yml logs -f"
echo "   - View app logs: docker compose -f docker-compose.prod.yml logs -f application"
echo "   - Restart: docker compose -f docker-compose.prod.yml restart"
echo "   - Stop: docker compose -f docker-compose.prod.yml down"
