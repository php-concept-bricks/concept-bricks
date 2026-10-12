# Concept Bricks

Monorepo for first-party Concept stack bricks (`php-concept/brick-*`).

| Role | Repo |
|------|------|
| **Monorepo (source of truth)** | [php-concept-bricks/concept-bricks](https://github.com/php-concept-bricks/concept-bricks) |
| **Split packages** | [php-concept-bricks/brick-*](https://github.com/php-concept-bricks) |

Composer package names: `php-concept/brick-http`, `php-concept/brick-logger-monolog`, …

Work only in this monorepo. Split repos are published automatically from `packages/*` (Symfony-style: **one release tag everywhere**).

---

## Layout

```text
concept-bricks/
├── packages/
│   ├── http/                 → php-concept/brick-http
│   ├── logger-monolog/       → php-concept/brick-logger-monolog
│   └── …
├── packages.json             # monorepo path → split repo name
├── scripts/
│   ├── create-split-repos.sh # once: create empty GitHub repos
│   └── split-packages.sh     # push subtree (+ optional tag) to splits
└── docker-compose.yml
```

Each package keeps the usual trio plus an entry brick:

```text
*Builder.php
*Options.php
*StackProvider.php
*Brick.php              # implements BrickInterface / BindableBrickInterface
```

---

## Local development (Docker only)

Sibling repos must exist next to this one:

```text
../concept-core-2
../concept-stack
../concept-extensions
```

```bash
cd /var/www/concept-bricks

docker compose up -d --build
docker compose exec app bash

# inside container:
composer install
composer phpstan
```

Do not run Composer/PHPStan on the host.

---

## Packages

| Path | Entry | `NAME` | Composer |
|------|-------|--------|----------|
| `packages/casting-valinor` | `CastingBrick` | `casting-valinor` | `php-concept/brick-casting-valinor` |
| `packages/components` | `ComponentsBrick` | `components` | `php-concept/brick-components` |
| `packages/console-symfony` | `ConsoleBrick` | `console-symfony` | `php-concept/brick-console-symfony` |
| `packages/database-eloquent` | `DatabaseBrick` | `database-eloquent` | `php-concept/brick-database-eloquent` |
| `packages/data-masker` | `MaskingBrick` | `data-masker` | `php-concept/brick-data-masker` |
| `packages/error-handler-whoops` | `ErrorHandlingBrick` | `error-handler-whoops` | `php-concept/brick-error-handler-whoops` |
| `packages/event` | `EventBrick` | `event` | `php-concept/brick-event` |
| `packages/http` | `HttpBrick` | `http` | `php-concept/brick-http` |
| `packages/logger-monolog` | `LoggingBrick` | `logger-monolog` | `php-concept/brick-logger-monolog` |
| `packages/session-symfony` | `SessionBrick` | `session-symfony` | `php-concept/brick-session-symfony` |
| `packages/telemetry` | `TelemetryBrick` | `telemetry` | `php-concept/brick-telemetry` |
| `packages/validation-rakit` | `ValidationBrick` | `validation-rakit` | `php-concept/brick-validation-rakit` |
| `packages/view` | `ViewBrick` | `view` | `php-concept/brick-view` |

Root `composer.json` name `php-concept/bricks` is **dev-only** (path-requires all packages). It is not the Packagist product.

---

## Release (tag → push → split)

Same version on monorepo and every split repo (like Symfony components).

### 1. Commit & push monorepo

```bash
git add -A
git status
git commit -m "Describe the change"
git push origin main
```

### 2. Tag the monorepo

```bash
git tag v1.0.1
git push origin v1.0.1
```

Use semver: `vMAJOR.MINOR.PATCH`.

### 3. Split into package repos (+ same tag)

```bash
./scripts/split-packages.sh --tag v1.0.1
```

What this does for each `packages/*`:

1. `git subtree split` for that path  
2. Force-push to `php-concept-bricks/<brick-*>` `main`  
3. Push the **same** tag `v1.0.1` on the split commit  

Dry-run:

```bash
./scripts/split-packages.sh --dry-run --tag v1.0.1
```

One package only:

```bash
./scripts/split-packages.sh --tag v1.0.1 brick-http
```

If `HEAD` already has exactly one `v*` tag, you can omit `--tag`:

```bash
./scripts/split-packages.sh
```

### 4. Packagist

Packagist watches the **split** repos. After the tag exists on GitHub, update versions there (auto webhook or “Update”).

Consumers:

```json
"php-concept/brick-http": "^1.0"
```

---

## CI split (optional)

Workflow: `.github/workflows/split.yml`

- Triggers on push of tags `v*` (and `workflow_dispatch`)
- Needs repo secret **`SPLIT_PUSH_TOKEN`** (PAT with write access to all `brick-*` repos)

With the secret set, step 3 can be skipped locally: tagging + pushing `v*` on the monorepo is enough.

---

## First-time setup (already done for this org)

Create empty split repos (skips existing):

```bash
./scripts/create-split-repos.sh
```

---

## Stack API (short)

Contracts live in `php-concept/stack`:

- `BrickInterface` — `name()`, `requires()`, `providers()`
- `BindableBrickInterface` — + `bind()` for bricks that call `$stack->require()` from `with*()`
- `BindsToStack` trait

Capability id = package kebab = `*Brick::NAME` (e.g. `ValidationBrick::NAME` → `validation-rakit`).  
Cross-brick `require()` uses those constants; composer then lists the other `brick-*` package.

Options validation runs inside `*StackProvider`, not on the brick contract.

```php
$stack->add(new HttpBrick())
    ->builder()
    ->setRoutes([...])
    ->withFormRequests();

$stack->add(new CastingBrick())
    ->builder()
    ->setCacheDir(...)
    ->setDebug($debug);
```

---

## Checklist per release

- [ ] Changes committed on `main`
- [ ] `composer phpstan` green (in Docker)
- [ ] `git push origin main`
- [ ] `git tag vX.Y.Z` && `git push origin vX.Y.Z`
- [ ] `./scripts/split-packages.sh --tag vX.Y.Z` (or CI)
- [ ] Packagist shows the new version on split packages
