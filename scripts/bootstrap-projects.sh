#!/bin/sh
set -eu

ROOT_DIR="/workspace"

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

    if [ "$project_name" = "order" ]; then
        git -C "$target_dir" remote set-url origin "git@github.com:skoshpaev/evotym_order.git" >/dev/null 2>&1 || true
    fi
}

if [ "$#" -eq 0 ]; then
    set -- product order
fi

for project_name in "$@"; do
    case "$project_name" in
        product)
            bootstrap_project "product" "https://github.com/skoshpaev/evotym_product.git"
            ;;
        order)
            bootstrap_project "order" "https://github.com/skoshpaev/evotym_order.git"
            ;;
        *)
            echo "[bootstrap] Unknown project: $project_name" >&2
            exit 1
            ;;
    esac
done
