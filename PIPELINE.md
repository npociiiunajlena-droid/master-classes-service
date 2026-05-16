# PIPELINE.md

## Что реализовано

Пайплайн находится в файле `.github/workflows/ci.yml` и выполняет требования лабораторной:

1. Запускается на `push` и `pull_request` для долгоживущих веток:
- `dev`, `develop`
- `qa`, `uat`
- `main`, `master`

2. Стадии качества для долгоживущих веток:
- `Validate Environment Files` — параноидальная проверка `.env.dev`, `.env.uat`, `.env.prod`, `.env.ci` и обязательных ключей.
- `Tests With Coverage Gate` — тесты Laravel + генерация `coverage.xml` + gate `>= 50%`.
- `Static Analysis (Larastan)` — `phpstan`/`larastan` (при любой ошибке job падает).
- `Lint Check (Pint --test)` — проверка линтера в test-режиме без автоисправлений.

3. Для обычных (не долгоживущих) веток на `push`:
- `Lint Auto-Fix (non long-lived branches)` — запуск `Pint` в режиме автоформатирования.

4. Симуляция деплоя (только после успешных quality-стадий):
- `develop/dev` -> копируется `.env.dev` в `.env`, выводится `Deploying to DEV with .env.dev`
- `uat/qa` -> копируется `.env.uat` в `.env`, выводится `Deploying to UAT with .env.uat`
- `main/master` -> копируется `.env.prod` в `.env`, выводится `Deploying to PROD with .env.prod`

## Ручной аппрув для прод-ветки

Job `deploy-prod` использует GitHub Environment `production`.
Чтобы включить ручной аппрув:

1. Откройте репозиторий GitHub -> `Settings` -> `Environments`.
2. Создайте (или выберите) environment `production`.
3. Включите `Required reviewers` и добавьте проверяющих.

После этого job `deploy-prod` будет ожидать ручного подтверждения перед выполнением.

## Команды локальной проверки

- Тесты: `composer test`
- Тесты с покрытием: `composer test:coverage`
- Проверка порога покрытия: `composer check:coverage`
- Статанализ: `composer analyse`
- Линтер (check): `composer lint`
- Линтер (fix): `composer lint:fix`

## Примечания

- `.env` игнорируется через `.gitignore`.
- Файл `.env.ci` настроен на `sqlite` in-memory (`DB_DATABASE=:memory:`).
- `APP_KEY` в `.env.dev/.env.uat/.env.prod` оставлен пустым с пояснением, что ключ генерируется в целевой среде.
