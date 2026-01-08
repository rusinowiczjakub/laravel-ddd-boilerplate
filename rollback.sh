#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
COMPOSE_FILE="docker-compose.prod.yml"
BACKUP_DIR="backups"

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_prompt() {
    echo -e "${BLUE}[PROMPT]${NC} $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    log_error "Please do not run this script as root"
    exit 1
fi

# Check if backup directory exists
if [ ! -d "$BACKUP_DIR" ]; then
    log_error "Backup directory not found!"
    exit 1
fi

# List available backups
log_info "Available database backups:"
echo ""
backups=($(ls -t ${BACKUP_DIR}/db_backup_*.sql 2>/dev/null))

if [ ${#backups[@]} -eq 0 ]; then
    log_error "No backups found!"
    exit 1
fi

# Display backups with numbers
for i in "${!backups[@]}"; do
    backup_date=$(basename "${backups[$i]}" | sed 's/db_backup_//;s/.sql//')
    readable_date=$(echo $backup_date | sed 's/_/ /;s/\([0-9]\{4\}\)\([0-9]\{2\}\)\([0-9]\{2\}\)/\1-\2-\3/')
    echo "  $((i+1)). ${backups[$i]} ($readable_date)"
done

echo ""
log_prompt "Select backup number to restore (1-${#backups[@]}) or 'q' to quit:"
read -r selection

# Validate input
if [ "$selection" = "q" ] || [ "$selection" = "Q" ]; then
    log_info "Rollback cancelled"
    exit 0
fi

if ! [[ "$selection" =~ ^[0-9]+$ ]] || [ "$selection" -lt 1 ] || [ "$selection" -gt ${#backups[@]} ]; then
    log_error "Invalid selection"
    exit 1
fi

selected_backup="${backups[$((selection-1))]}"

log_warn "You are about to restore database from: $selected_backup"
log_warn "This will OVERWRITE the current database!"
log_prompt "Are you sure? (yes/no):"
read -r confirm

if [ "$confirm" != "yes" ]; then
    log_info "Rollback cancelled"
    exit 0
fi

# Load environment variables
source .env

# Step 1: Enable maintenance mode
log_info "Enabling maintenance mode..."
docker-compose -f $COMPOSE_FILE exec -T application php artisan down --retry=60

# Step 2: Create a backup of current state before rollback
log_info "Creating backup of current database before rollback..."
ROLLBACK_DATE=$(date +%Y%m%d_%H%M%S)
docker-compose -f $COMPOSE_FILE exec -T mysql mysqldump -u${DB_USERNAME} -p${DB_PASSWORD} ${DB_DATABASE} > "${BACKUP_DIR}/db_before_rollback_${ROLLBACK_DATE}.sql"
log_info "Current database backed up to ${BACKUP_DIR}/db_before_rollback_${ROLLBACK_DATE}.sql"

# Step 3: Restore database
log_info "Restoring database from backup..."
if docker-compose -f $COMPOSE_FILE exec -T -e MYSQL_PWD=${DB_PASSWORD} mysql mysql -u${DB_USERNAME} ${DB_DATABASE} < "$selected_backup"; then
    log_info "Database restored successfully!"
else
    log_error "Failed to restore database!"
    log_error "Your current database backup is at: ${BACKUP_DIR}/db_before_rollback_${ROLLBACK_DATE}.sql"
    exit 1
fi

# Step 4: Clear caches
log_info "Clearing application caches..."
docker-compose -f $COMPOSE_FILE exec -T application php artisan cache:clear
docker-compose -f $COMPOSE_FILE exec -T application php artisan config:clear
docker-compose -f $COMPOSE_FILE exec -T application php artisan route:clear
docker-compose -f $COMPOSE_FILE exec -T application php artisan view:clear

# Step 5: Re-cache
log_info "Re-caching configuration..."
docker-compose -f $COMPOSE_FILE exec -T application php artisan config:cache
docker-compose -f $COMPOSE_FILE exec -T application php artisan route:cache
docker-compose -f $COMPOSE_FILE exec -T application php artisan view:cache

# Step 6: Restart queue workers
log_info "Restarting queue workers..."
docker-compose -f $COMPOSE_FILE exec -T application php artisan queue:restart

# Step 7: Disable maintenance mode
log_info "Disabling maintenance mode..."
docker-compose -f $COMPOSE_FILE exec -T application php artisan up

log_info "✓ Rollback completed successfully!"
log_info ""
log_info "Restored from: $selected_backup"
log_info "Pre-rollback backup saved at: ${BACKUP_DIR}/db_before_rollback_${ROLLBACK_DATE}.sql"
