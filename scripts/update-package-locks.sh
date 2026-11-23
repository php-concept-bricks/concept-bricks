#!/usr/bin/env bash
#
# Update the monorepo root composer.lock only.
# Per-package locks are not used (libraries; see concept-extensions/scripts/README.md).
#
# Usage (prefer inside Docker):
#   docker compose exec app ./scripts/update-package-locks.sh
#   docker compose exec app ./scripts/update-package-locks.sh --dry-run
#
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DRY_RUN=0

while [[ $# -gt 0 ]]; do
    case "$1" in
        --dry-run)
            DRY_RUN=1
            shift
            ;;
        -h|--help)
            sed -n '3,10p' "$0"
            exit 0
            ;;
        -*)
            echo "Unknown option: $1" >&2
            exit 1
            ;;
        *)
            echo "Unexpected argument: $1 (root lock only; no package filters)" >&2
            exit 1
            ;;
    esac
done

if ! command -v composer >/dev/null 2>&1; then
    echo "composer not found (run inside Docker: docker compose exec app ...)" >&2
    exit 1
fi

if [[ $DRY_RUN -eq 1 ]]; then
    echo "dry-run: composer update --no-install in monorepo root"
    exit 0
fi

echo "==> monorepo root"
(cd "$ROOT" && composer update --no-install --no-interaction)
echo "done"
