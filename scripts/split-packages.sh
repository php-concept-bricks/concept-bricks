#!/usr/bin/env bash
#
# Push each packages/* subtree to its split repo in php-concept-bricks org.
#
# Usage:
#   ./scripts/split-packages.sh              # all packages
#   ./scripts/split-packages.sh brick-http   # one repo name from packages.json
#   ./scripts/split-packages.sh --dry-run
#
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ORG="php-concept-bricks"
BRANCH="${SPLIT_BRANCH:-main}"
DRY_RUN=0
FILTER=""

while [[ $# -gt 0 ]]; do
    case "$1" in
        --dry-run)
            DRY_RUN=1
            shift
            ;;
        -h|--help)
            echo "Usage: split-packages.sh [--dry-run] [repo-name...]"
            exit 0
            ;;
        *)
            FILTER="$1"
            shift
            ;;
    esac
done

if ! git -C "$ROOT" rev-parse HEAD >/dev/null 2>&1; then
    echo "Monorepo needs at least one commit before split." >&2
    exit 1
fi

cd "$ROOT"

while IFS=$'\t' read -r prefix repo; do
    if [[ -n "$FILTER" && "$repo" != "$FILTER" ]]; then
        continue
    fi

    split_branch="split/$repo"
    remote="git@github.com:$ORG/$repo.git"

    echo "==> $prefix -> $ORG/$repo"

    if [[ $DRY_RUN -eq 1 ]]; then
        echo "  git subtree split --prefix=$prefix -b $split_branch"
        echo "  git push $remote $split_branch:$BRANCH"
        continue
    fi

    git branch -D "$split_branch" 2>/dev/null || true
    git subtree split --prefix="$prefix" -b "$split_branch"
    git push "$remote" "$split_branch:$BRANCH" --force
done < <(php -r '
    $map = json_decode(file_get_contents("packages.json"), true)["packages"];
    foreach ($map as $path => $repo) {
        echo $path, "\t", $repo, "\n";
    }
')

echo "Done."
