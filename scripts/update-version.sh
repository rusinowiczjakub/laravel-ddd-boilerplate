#!/bin/bash

# Script to extract version from CHANGELOG.md and update .env
# Works in Docker (no nano/vim required - uses sed)

set -e

CHANGELOG_FILE="CHANGELOG.md"
ENV_FILE=".env"

# Check if CHANGELOG.md exists
if [ ! -f "$CHANGELOG_FILE" ]; then
    echo "❌ CHANGELOG.md not found!"
    exit 1
fi

# Extract version from first ## [X.X.X] line in CHANGELOG.md (skip Unreleased)
# Example: ## [1.0.0] - 2025-01-23
VERSION_LINE=$(grep -m 1 "^## \[[0-9]" "$CHANGELOG_FILE" || echo "")

if [ -z "$VERSION_LINE" ]; then
    echo "⚠️  No version found in CHANGELOG.md"
    exit 0
fi

# Extract version number (e.g., "1.0.0")
VERSION=$(echo "$VERSION_LINE" | sed -n 's/^## \[\([^]]*\)\].*/\1/p')

# Extract release date (e.g., "2025-01-23")
RELEASE_DATE=$(echo "$VERSION_LINE" | sed -n 's/.*- \([0-9]\{4\}-[0-9]\{2\}-[0-9]\{2\}\).*/\1/p')

# Try to extract release name from next line if it exists
# Look for pattern like: > **Release Name: "Genesis"**
RELEASE_NAME=""
LINE_NUM=$(grep -n "^## \[$VERSION\]" "$CHANGELOG_FILE" | head -1 | cut -d: -f1)
if [ ! -z "$LINE_NUM" ]; then
    NEXT_LINE=$((LINE_NUM + 1))
    RELEASE_NAME_LINE=$(sed -n "${NEXT_LINE}p" "$CHANGELOG_FILE")
    # Extract text between quotes in the Release Name line
    if [[ "$RELEASE_NAME_LINE" =~ \"([^\"]+)\" ]]; then
        RELEASE_NAME="${BASH_REMATCH[1]}"
    fi
fi

echo "📦 Extracted from CHANGELOG.md:"
echo "   Version: $VERSION"
echo "   Date: $RELEASE_DATE"
[ ! -z "$RELEASE_NAME" ] && echo "   Name: $RELEASE_NAME"

# Check if .env exists
if [ ! -f "$ENV_FILE" ]; then
    echo "⚠️  .env file not found - creating from .env.example"
    cp .env.example "$ENV_FILE"
fi

# Function to update or add env variable
update_env_var() {
    local key=$1
    local value=$2

    if grep -q "^${key}=" "$ENV_FILE"; then
        # Update existing
        sed -i.bak "s|^${key}=.*|${key}=${value}|" "$ENV_FILE"
    else
        # Add new
        echo "${key}=${value}" >> "$ENV_FILE"
    fi
}

# Update .env variables
update_env_var "APP_VERSION" "$VERSION"
[ ! -z "$RELEASE_DATE" ] && update_env_var "APP_RELEASE_DATE" "$RELEASE_DATE"
[ ! -z "$RELEASE_NAME" ] && update_env_var "APP_RELEASE_NAME" "$RELEASE_NAME"

# Remove backup file created by sed
rm -f "${ENV_FILE}.bak"

echo "✅ Updated $ENV_FILE with version $VERSION"
echo "   (Config cache will be cleared during container startup)"

exit 0
