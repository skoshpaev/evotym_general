#!/bin/sh
set -eu

ROOT_DIR="${HOST_WORKSPACE:?HOST_WORKSPACE is required}"

ensure_dependencies() {
    if ! command -v git >/dev/null 2>&1; then
        apk add --no-cache git openssh-client >/dev/null
    fi
}

bootstrap_project() {
    project_name="$1"
    remote_url="$2"
    target_dir="$ROOT_DIR/$project_name"

    mkdir -p "$target_dir"

    if [ -d "$target_dir/.git" ] || [ -f "$target_dir/composer.json" ] || [ -f "$target_dir/entrypoint.sh" ]; then
        echo "[bootstrap] $project_name already exists"
        return 0
    fi

    if find "$target_dir" -mindepth 1 -maxdepth 1 -print -quit | grep -q .; then
        echo "[bootstrap] $project_name directory is not empty, skipping clone"
        return 0
    fi

    clone_url="$remote_url"
    if [ -n "${GITHUB_TOKEN:-}" ]; then
        clone_url="https://x-access-token:${GITHUB_TOKEN}@github.com/skoshpaev/evotym_${project_name}.git"
        echo "[bootstrap] Cloning $project_name with token authentication"
    else
        echo "[bootstrap] Cloning $project_name"
    fi

    git clone "$clone_url" "$target_dir"
    git -C "$target_dir" remote set-url origin "git@github.com:skoshpaev/evotym_${project_name}.git" >/dev/null 2>&1 || true
}

prepare_runtime_image() {
    project_name="$1"
    target_dir="$ROOT_DIR/$project_name"
    image_name="$project_name-app"

    if [ ! -f "$target_dir/Dockerfile" ]; then
        echo "[bootstrap] Missing Dockerfile for $project_name" >&2
        exit 1
    fi

    echo "[bootstrap] Building $image_name image"
    docker build -t "$image_name" "$target_dir"

    echo "[bootstrap] Initializing $project_name runtime"
    docker run --rm \
        -e COMPOSER_ALLOW_SUPERUSER=1 \
        -v "$target_dir:/workspace/$project_name" \
        "$image_name" \
        true
}

run_database_migrations() {
    project_name="$1"
    target_dir="$ROOT_DIR/$project_name"
    image_name="$project_name-app"
    network_name="evotym_${project_name}_internal"

    case "$project_name" in
        product)
            database_url="mysql://product:product@mysql:3306/product?serverVersion=8.0&charset=utf8mb4"
            ;;
        order)
            database_url="mysql://order:order@mysql:3306/order?serverVersion=8.0.36&charset=utf8mb4"
            ;;
        *)
            echo "[bootstrap] Unknown project for migrations: $project_name" >&2
            exit 1
            ;;
    esac

    if [ ! -f "$target_dir/bin/console" ] || [ ! -d "$target_dir/migrations" ]; then
        echo "[bootstrap] No Symfony migrations for $project_name, skipping"
        return 0
    fi

    if ! find "$target_dir/migrations" -type f -name 'Version*.php' -print -quit | grep -q .; then
        echo "[bootstrap] No migration files for $project_name, skipping"
        return 0
    fi

    echo "[bootstrap] Running database migrations for $project_name"
    docker run --rm \
        --network "$network_name" \
        -e COMPOSER_ALLOW_SUPERUSER=1 \
        -e DATABASE_URL="$database_url" \
        -v "$target_dir:/workspace/$project_name" \
        "$image_name" \
        php bin/console doctrine:migrations:migrate --no-interaction
}

if [ "$#" -eq 0 ]; then
    set -- product order
fi

ensure_dependencies

for project_name in "$@"; do
    case "$project_name" in
        product)
            bootstrap_project "product" "https://github.com/skoshpaev/evotym_product.git"
            prepare_runtime_image "product"
            run_database_migrations "product"
            ;;
        order)
            bootstrap_project "order" "https://github.com/skoshpaev/evotym_order.git"
            prepare_runtime_image "order"
            run_database_migrations "order"
            ;;
        *)
            echo "[bootstrap] Unknown project: $project_name" >&2
            exit 1
            ;;
    esac
done
