# Scripts — concept-bricks

Утиліти монорепо. Composer-команди краще ганяти в Docker (`docker compose exec app …`).

Карта пакетів → split-репо: [`../packages.json`](../packages.json).  
Extensions sibling: `../concept-extensions` (теги звідти — дефолт для `extension-*`).

| Скрипт | Роль |
|--------|------|
| `align-package-deps.sh` | Вирівняти `require` у `composer.json` (`brick-*` + `extension-*`) |
| `update-package-locks.sh` | Оновити **root** `composer.lock` |
| `create-split-repos.sh` | Один раз: створити порожні GitHub-репо в org |
| `split-packages.sh` | Локальний fallback split (звичайний реліз — через CI) |

### Locks

| Файл | У git? |
|------|--------|
| Root `composer.lock` | Так — один граф для Docker/CI монорепо |
| `packages/*/composer.lock` | Ні — libraries; споживачам не потрібні |

`php-concept/stack` / `core-2` скрипт align **не** чіпає.

---

## Порядок відносно extensions

1. Спочатку extensions: align cross-deps → tag → CI Split (у тегу всередині вже `^N`, не старі majors).
2. Потім bricks: align `brick-*` + `extension-*` → tag → CI Split.
3. Потім apps / stack bump.

---

## Реліз (один прохід)

Packagist/apps читають **`composer.json` з тегу**, не lock. Версія пакета = git-тег (поля `"version"` у json немає).

```
1. align-package-deps.sh   # за потреби з --bricks / --extensions
2. update-package-locks.sh # root (path-репо — можна до тега)
3. commit + git push origin main
4. git tag vX.Y.Z && git push origin vX.Y.Z
   → CI (.github/workflows/split.yml) сплітить усі brick-* з тим самим тегом
```

`split-packages.sh` **не обовʼязковий** (fallback).

---

## `align-package-deps.sh`

**Що робить**

- У `packages/*/composer.json`: `php-concept/brick-*` і `php-concept/extension-*` → один constraint на префікс.
- У кореневому `composer.json`: ті самі `require` + pin у path `repositories[].options.versions` (`^X.Y` → `X.Y.0`).
- Composer / locks не чіпає.

**Звідки constraint**

| Префікс | Дефолт |
|---------|--------|
| `brick-*` | останній git-тег **цього** репо (`v2.1.1` → `^2.1`) |
| `extension-*` | останній тег `../concept-extensions` |

Стрибок major (останній тег ще старий) — передати явно:

```bash
./scripts/align-package-deps.sh --bricks ^3.0 --extensions ^3.0
```

**Запуск**

```bash
./scripts/align-package-deps.sh
./scripts/align-package-deps.sh --dry-run
./scripts/align-package-deps.sh --bricks ^3.0 --extensions ^3.0
./scripts/align-package-deps.sh --set php-concept/brick-=^3.0
./scripts/align-package-deps.sh http    # один пакет (dirname у packages/)
```

---

## `update-package-locks.sh`

Лише **root** `composer.lock` (`composer update --no-install`).

```bash
docker compose exec app ./scripts/update-package-locks.sh
docker compose exec app ./scripts/update-package-locks.sh --dry-run
```

Після align (нові root `require` / path versions) — одразу ок: root резолвить через path, не Packagist.

---

## `create-split-repos.sh`

Одноразово: репо в org з `packages.json`, якщо ще немає.

```bash
./scripts/create-split-repos.sh
```

---

## `split-packages.sh` (fallback)

Звичайний шлях — **CI** на `push` тега `v*`.

Локально:

```bash
./scripts/split-packages.sh --tag vX.Y.Z
./scripts/split-packages.sh --tag vX.Y.Z --dry-run
./scripts/split-packages.sh --tag vX.Y.Z brick-http
```

---

## Типові сценарії

### Перейти на bricks `v3.0.0` + extensions `^3.0`

Передумова: extensions уже опубліковані з вирівняними cross-deps (`v3.0.x`, де всередині `^3.0`).

```bash
cd /var/www/concept/concept-bricks

./scripts/align-package-deps.sh --bricks ^3.0 --extensions ^3.0
docker compose exec app ./scripts/update-package-locks.sh

# review (package locks removal / scripts — у той самий commit, якщо ще ні)
git add -A
git status
git commit -m "Align brick and extension constraints to ^3.0."
git push origin main

git tag v3.0.0
git push origin v3.0.0
# CI Split green → apps / stack bump на brick-*:^3.0
```

Без `--bricks ^3.0` скрипт візьме constraint з поточного тега (`v2.1.1` → `^2.1`) — для стрибка major цього мало.

### Звичайний реліз без зміни constraint-префікса

```bash
git push origin main
git tag vX.Y.Z && git push origin vX.Y.Z
# CI Split
```

### Новий пакет

1. `packages/<name>/` + запис у `packages.json`
2. `./scripts/create-split-repos.sh`
3. Commit → tag/push → CI Split

---

## Чого не робити

| ❌ | ✅ |
|----|----|
| Комітити `packages/*/composer.lock` | Лише root lock |
| Тегати bricks `^3`, поки extensions split ще зі старими cross-deps | Спочатку extensions release |
| Align major без `--bricks` / `--extensions`, коли останній тег ще старий major | Явні `--bricks ^N.0 --extensions ^N.0` |
| Завжди ганяти `split-packages.sh` руками | `git push origin vX.Y.Z` → CI |
