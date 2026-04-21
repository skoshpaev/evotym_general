#!/bin/sh
set -eu

PROJECT_NAME="${1:?Project name is required}"
HOST_WORKSPACE="${HOST_WORKSPACE:?HOST_WORKSPACE is required}"
PROJECT_DIR="$HOST_WORKSPACE/$PROJECT_NAME"
COMPOSE_FILE="$PROJECT_DIR/docker-compose.yml"

cleanup() {
    if [ -f "$COMPOSE_FILE" ]; then
        docker compose -f "$COMPOSE_FILE" down >/dev/null 2>&1 || true
    fi
}

trap cleanup INT TERM EXIT

/bin/sh "$HOST_WORKSPACE/scripts/bootstrap-projects.sh" "$PROJECT_NAME"

if [ ! -f "$COMPOSE_FILE" ]; then
    echo "[orchestrator] Missing compose file for $PROJECT_NAME" >&2
    exit 1
fi

docker compose -f "$COMPOSE_FILE" up -d --build

while :; do
    if ! docker compose -f "$COMPOSE_FILE" ps --services --filter status=running | grep -q .; then
        echo "[orchestrator] $PROJECT_NAME stack is not running" >&2
        exit 1
    fi

    sleep 15 &
    wait $!
done
