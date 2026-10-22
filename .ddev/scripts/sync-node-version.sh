#!/usr/bin/env bash
#
# sync-node-version.sh
#
# Keeps DDEV's nodejs_version in sync with `.nvmrc` (source of truth).
# Runs inside the DDEV web container as a post-start exec hook.
#
# Add to .ddev/config.yaml:
#
#   hooks:
#     post-start:
#       - exec: "bash .ddev/scripts/sync-node-version.sh"
#

set -euo pipefail

# ---------------------------------------------------------------------------
# Paths (container-relative)
# ---------------------------------------------------------------------------
PROJECT_ROOT="/var/www/html"
NVMRC_FILE="${PROJECT_ROOT}/.nvmrc"
DDEV_CONFIG="${PROJECT_ROOT}/.ddev/config.yaml"

# ---------------------------------------------------------------------------
# Colours
# ---------------------------------------------------------------------------
YELLOW='\033[33m'
RESET='\033[0m'

# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

# Print a coloured message: warn <colour> <message>
warn() {
    printf "${1}  %s${RESET}\n" "$2"
}

# Portable in-place sed (no -i flag quirks between GNU/BSD):
#   sed_replace <pattern> <file>
sed_replace() {
    sed "$1" "$2" > "${2}.tmp" && mv "${2}.tmp" "$2"
}

# ---------------------------------------------------------------------------
# Main
# ---------------------------------------------------------------------------

# Only run inside a DDEV container.
if [[ "${IS_DDEV_PROJECT:-}" != "true" ]]; then
    echo "This script is meant to run inside a DDEV container (as a post-start hook)."
    exit 1
fi

# No .nvmrc or no DDEV config? Nothing to enforce.
[[ ! -f "$NVMRC_FILE" ]] && exit 0
[[ ! -f "$DDEV_CONFIG" ]] && exit 0

NVMRC_VERSION=$(tr -d 'v \n' < "$NVMRC_FILE")
DDEV_VERSION=$(grep '^nodejs_version:' "$DDEV_CONFIG" | head -1 | sed 's/^nodejs_version:[[:space:]]*//' | tr -d '"')

[[ "$NVMRC_VERSION" == "$DDEV_VERSION" ]] && exit 0

printf "\n"
warn "$YELLOW" "Node version mismatch detected!"
warn "$YELLOW" ".nvmrc:       $NVMRC_VERSION"
warn "$YELLOW" "DDEV config:  $DDEV_VERSION"
printf "\n"
warn "$YELLOW" "Updating DDEV config to match .nvmrc..."

sed_replace "s/^nodejs_version:.*/nodejs_version: \"${NVMRC_VERSION}\"/" "$DDEV_CONFIG"

warn "$YELLOW" "Done. Run 'ddev restart' to apply the new Node version."
printf "\n"
