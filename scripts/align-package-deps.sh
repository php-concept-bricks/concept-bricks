#!/usr/bin/env bash
#
# Align php-concept/brick-* and php-concept/extension-* require constraints in
# packages/*/composer.json (and monorepo root require + path versions).
# Does not touch locks.
#
# Defaults from git tags:
#   brick-*      → this repo's latest tag
#   extension-*  → sibling ../concept-extensions latest tag
#
# Sequential release (after extensions tag is already out):
#   1. ./scripts/align-package-deps.sh
#   2. optional: ./scripts/update-package-locks.sh  (root lock only)
#   3. commit → push main → tag/push v* (CI Split)
#
# Usage:
#   ./scripts/align-package-deps.sh
#   ./scripts/align-package-deps.sh --dry-run
#   ./scripts/align-package-deps.sh --extensions ^4.0 --bricks ^2.2
#   ./scripts/align-package-deps.sh --set php-concept/brick-=^2.2
#
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
DRY_RUN=0
DO_ROOT=0
SETS=()
FILTERS=()
EXTENSIONS_CONSTRAINT=""
BRICKS_CONSTRAINT=""

while [[ $# -gt 0 ]]; do
    case "$1" in
        --dry-run)
            DRY_RUN=1
            shift
            ;;
        --root)
            DO_ROOT=1
            shift
            ;;
        --extensions)
            [[ $# -ge 2 ]] || { echo "--extensions needs a constraint" >&2; exit 1; }
            EXTENSIONS_CONSTRAINT="$2"
            shift 2
            ;;
        --bricks)
            [[ $# -ge 2 ]] || { echo "--bricks needs a constraint" >&2; exit 1; }
            BRICKS_CONSTRAINT="$2"
            shift 2
            ;;
        --set)
            [[ $# -ge 2 ]] || { echo "--set needs prefix=constraint" >&2; exit 1; }
            SETS+=("$2")
            shift 2
            ;;
        -h|--help)
            sed -n '3,18p' "$0"
            exit 0
            ;;
        -*)
            echo "Unknown option: $1" >&2
            exit 1
            ;;
        *)
            FILTERS+=("$1")
            shift
            ;;
    esac
done

if ! command -v php >/dev/null 2>&1; then
    echo "php not found (run inside Docker: docker compose exec app ...)" >&2
    exit 1
fi

tag_to_caret() {
    local tag="$1"
    tag="${tag#v}"
    local major minor
    IFS=. read -r major minor _ <<<"$tag"
    if [[ -z "${minor:-}" ]]; then
        echo "^${major}"
    else
        echo "^${major}.${minor}"
    fi
}

normalize_constraint() {
    local c="$1"
    c="${c#v}"
    if [[ "$c" =~ ^[0-9] ]]; then
        tag_to_caret "v$c"
    else
        echo "$c"
    fi
}

constraint_from_repo() {
    local repo="$1"
    local tag
    tag="$(git -C "$repo" describe --tags --abbrev=0 2>/dev/null || true)"
    if [[ -z "$tag" ]]; then
        return 1
    fi
    tag_to_caret "$tag"
}

if [[ ${#SETS[@]} -eq 0 ]]; then
    if [[ -z "$BRICKS_CONSTRAINT" ]]; then
        BRICKS_CONSTRAINT="$(constraint_from_repo "$ROOT" || true)"
        if [[ -z "$BRICKS_CONSTRAINT" ]]; then
            echo "No bricks git tag; pass --bricks ^X.Y" >&2
            exit 1
        fi
    else
        BRICKS_CONSTRAINT="$(normalize_constraint "$BRICKS_CONSTRAINT")"
    fi

    if [[ -z "$EXTENSIONS_CONSTRAINT" ]]; then
        EXTENSIONS_CONSTRAINT="$(constraint_from_repo "$ROOT/../concept-extensions" || true)"
        if [[ -z "$EXTENSIONS_CONSTRAINT" ]]; then
            echo "No sibling concept-extensions tag; pass --extensions ^X.Y" >&2
            exit 1
        fi
    else
        EXTENSIONS_CONSTRAINT="$(normalize_constraint "$EXTENSIONS_CONSTRAINT")"
    fi

    SETS=(
        "php-concept/brick-=${BRICKS_CONSTRAINT}"
        "php-concept/extension-=${EXTENSIONS_CONSTRAINT}"
    )
fi

matches_filter() {
    local name="$1"
    if [[ ${#FILTERS[@]} -eq 0 ]]; then
        return 0
    fi
    local f
    for f in "${FILTERS[@]}"; do
        if [[ "$name" == "$f" || "$name" == "brick-$f" || "brick-$name" == "$f" ]]; then
            return 0
        fi
    done
    return 1
}

apply_composer_json() {
    local file="$1"
    local dry="$2"
    ALIGN_SETS="${SETS[*]}" ALIGN_DRY_RUN="$dry" php -r '
$sets = [];
foreach (preg_split("/\s+/", getenv("ALIGN_SETS") ?: "") as $item) {
    if ($item === "" || !str_contains($item, "=")) { continue; }
    [$prefix, $constraint] = explode("=", $item, 2);
    $sets[] = [$prefix, $constraint];
}
$path = $argv[1];
$data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
$require = $data["require"] ?? null;
if (!is_array($require)) { exit(0); }
$notes = [];
foreach ($require as $key => $current) {
    if (!is_string($key) || !is_string($current) || !str_starts_with($key, "php-concept/")) {
        continue;
    }
    foreach ($sets as [$prefix, $constraint]) {
        if (str_starts_with($key, $prefix) && $current !== $constraint) {
            $require[$key] = $constraint;
            $notes[] = $key . ": " . $current . " → " . $constraint;
            break;
        }
    }
}
if ($notes === []) { exit(0); }
if (getenv("ALIGN_DRY_RUN") !== "1") {
    $data["require"] = $require;
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
}
echo implode("\n", $notes), "\n";
' "$file"
}

apply_root_composer_json() {
    local file="$1"
    local dry="$2"
    ALIGN_SETS="${SETS[*]}" ALIGN_DRY_RUN="$dry" php -r '
$sets = [];
foreach (preg_split("/\s+/", getenv("ALIGN_SETS") ?: "") as $item) {
    if ($item === "" || !str_contains($item, "=")) { continue; }
    [$prefix, $constraint] = explode("=", $item, 2);
    $major = ltrim($constraint, "^~v");
    $parts = explode(".", $major);
    $pathVersion = ($parts[0] ?? "0") . "." . ($parts[1] ?? "0") . ".0";
    $sets[] = [$prefix, $constraint, $pathVersion];
}
$path = $argv[1];
$data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
$notes = [];

$require = $data["require"] ?? [];
if (is_array($require)) {
    foreach ($require as $key => $current) {
        if (!is_string($key) || !is_string($current) || !str_starts_with($key, "php-concept/")) {
            continue;
        }
        foreach ($sets as [$prefix, $constraint, $_pv]) {
            if (str_starts_with($key, $prefix) && $current !== $constraint) {
                $require[$key] = $constraint;
                $notes[] = "require $key: $current → $constraint";
                break;
            }
        }
    }
    $data["require"] = $require;
}

$repos = $data["repositories"] ?? [];
if (is_array($repos)) {
    foreach ($repos as $i => $repo) {
        if (!is_array($repo) || ($repo["type"] ?? "") !== "path") {
            continue;
        }
        $versions = $repo["options"]["versions"] ?? null;
        if (!is_array($versions)) {
            continue;
        }
        foreach ($versions as $key => $current) {
            if (!is_string($key) || !is_string($current) || !str_starts_with($key, "php-concept/")) {
                continue;
            }
            foreach ($sets as [$prefix, $_c, $pathVersion]) {
                if (str_starts_with($key, $prefix) && $current !== $pathVersion) {
                    $versions[$key] = $pathVersion;
                    $notes[] = "path versions $key: $current → $pathVersion";
                    break;
                }
            }
        }
        $repos[$i]["options"]["versions"] = $versions;
    }
    $data["repositories"] = $repos;
}

if ($notes === []) { exit(0); }
if (getenv("ALIGN_DRY_RUN") !== "1") {
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
}
echo implode("\n", $notes), "\n";
' "$file"
}

echo "Align sets:"
for s in "${SETS[@]}"; do
    echo "  $s"
done

shopt -s nullglob
for composer_json in "$ROOT"/packages/*/composer.json; do
    pkg_dir="$(dirname "$composer_json")"
    pkg_name="$(basename "$pkg_dir")"
    if ! matches_filter "$pkg_name"; then
        continue
    fi

    mapfile -t notes < <(apply_composer_json "$composer_json" "$DRY_RUN" || true)
    if [[ ${#notes[@]} -eq 0 ]]; then
        continue
    fi

    if [[ $DRY_RUN -eq 1 ]]; then
        echo "==> packages/$pkg_name: dry-run would update"
    else
        echo "==> packages/$pkg_name: updated"
    fi
    for note in "${notes[@]}"; do
        echo "    - $note"
    done
done

if [[ $DO_ROOT -eq 1 || ${#FILTERS[@]} -eq 0 ]]; then
    mapfile -t root_notes < <(apply_root_composer_json "$ROOT/composer.json" "$DRY_RUN" || true)
    if [[ ${#root_notes[@]} -gt 0 ]]; then
        if [[ $DRY_RUN -eq 1 ]]; then
            echo "==> monorepo root: dry-run would update"
        else
            echo "==> monorepo root: updated"
        fi
        for note in "${root_notes[@]}"; do
            echo "    - $note"
        done
    fi
fi

if [[ $DRY_RUN -eq 1 ]]; then
    echo "dry-run: done"
    exit 0
fi

echo
echo "JSON aligned. Next: optional ./scripts/update-package-locks.sh → commit → push main → tag/push v* (CI Split)"
echo "done"
