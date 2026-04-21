#!/bin/sh
set -eu

PROJECT_NAME="${1:?Project name is required}"
ROOT_DIR="/workspace"
PROJECT_DIR="$ROOT_DIR/$PROJECT_NAME"

mkdir -p "$PROJECT_DIR"

/bin/sh "$ROOT_DIR/scripts/bootstrap-projects.sh" "$PROJECT_NAME"

if [ ! -f "$PROJECT_DIR/entrypoint.sh" ]; then
    echo "[runner] Missing entrypoint for $PROJECT_NAME" >&2
    exit 1
fi

chmod +x "$PROJECT_DIR/entrypoint.sh" || true
cd "$PROJECT_DIR"

exec "$PROJECT_DIR/entrypoint.sh" php-fpm
