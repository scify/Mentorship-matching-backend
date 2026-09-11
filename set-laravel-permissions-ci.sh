#!/bin/bash

# Usage: ./set-laravel-permissions-ci.sh /path/to/laravel
# In GitHub Actions, just point to the Laravel app root (default: current directory)

set -e

LARAVEL_ROOT="${1:-.}"

echo "🔧 Setting Laravel file permissions in CI environment at: $LARAVEL_ROOT"

# Ensure the home directory is traversable by the web server
# (www-data needs execute permission on every directory in the path to reach storage/)
echo "📁 Ensuring home directory is traversable..."
chmod 755 "$HOME" 2>/dev/null || true

# General project-wide permissions
# Note: Some files may be owned by www-data (runtime uploads) - skip those gracefully
echo "📁 Setting directory permissions to 755..."
find "$LARAVEL_ROOT" -type d -exec chmod 755 {} \; 2>/dev/null || true

echo "📄 Setting file permissions to 644..."
find "$LARAVEL_ROOT" -type f -exec chmod 644 {} \; 2>/dev/null || true

# Writable directories: storage & bootstrap/cache
# Note: Using -perm to skip files we don't have permission to modify (e.g., runtime uploads owned by www-data)
echo "📝 Making storage/ and bootstrap/cache/ writable..."
find "$LARAVEL_ROOT/storage" "$LARAVEL_ROOT/bootstrap/cache" -type d -exec chmod 775 {} \; 2>/dev/null || true
find "$LARAVEL_ROOT/storage" "$LARAVEL_ROOT/bootstrap/cache" -type f -exec chmod 664 {} \; 2>/dev/null || true

# Make public directory writable for symlinks and uploads
echo "📝 Making public/ directory group-writable..."
chmod 775 "$LARAVEL_ROOT/public"

# Only chgrp files/dirs the deploy user owns — skips www-data-owned runtime files (sessions etc.)
echo "👥 Setting group ownership to www-data for deploy-user-owned files..."
find "$LARAVEL_ROOT/storage" "$LARAVEL_ROOT/bootstrap/cache" -user "$(whoami)" -exec chgrp www-data {} \; 2>/dev/null || true
chgrp www-data "$LARAVEL_ROOT/public" 2>/dev/null || true

# Set the setgid bit so new files inherit www-data group
echo "🔒 Setting setgid bit on writable directories..."
find "$LARAVEL_ROOT/storage" "$LARAVEL_ROOT/bootstrap/cache" -type d -exec chmod g+s {} \; 2>/dev/null || true

echo "✅ CI permissions set successfully."
