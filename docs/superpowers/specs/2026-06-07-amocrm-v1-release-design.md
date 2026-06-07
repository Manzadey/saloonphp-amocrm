# Дизайн: релиз v1.0 пакета `saloonphp-amocrm`

> Дата: 2026-06-07
> Статус: согласован, ожидает финального ревью спеки перед планом реализации.
> Связанные документы: [audit.md](../../audit.md), [package-api-map.md](../../package-api-map.md),
> [official-library-study.md](../../official-library-study.md).

## Цель

Сделать пакет публично доступным и выпустить первую мажорную версию **v1.0.0** —
стабильную, корректную, с согласованным типобезопасным API и **полным паритетом
сущностей с официальным SDK `amocrm/amocrm-api-library` 1.15**.

## Принцип

**v1.0 = заморозка публичного API по SemVer.** Все ломающие изменения (рефактор
конструкторов, унификация `send`/`with`, типизация фильтров, срез старых имён,
введение коллекций) выполняются **до тега**. Полный паритет сущностей — основной
объём работ перед заморозкой.

## Зафиксированные решения

| Вопрос | Решение |
|---|---|
| Объём 1.0 | **Полный паритет с SDK 1.15** (~38 сервисов сущностей) |
| OAuth | **Без паритета** — текущий code/refresh |
| Доменный слой | **Типобезопасные коллекции + фабрики** (паттерн SDK) |
| Рефактор создания запросов | Обязательные параметры → в конструктор; опциональный query остаётся fluent, но типизируется |
| Иммутабельность Requests | **Частичная**: обязательные параметры — `readonly`-свойства конструктора, сеттеров на них нет; опциональный query — fluent (per-send изоляцию обеспечивает Saloon `PendingRequest`). Полная PSR-7-иммутабельность отклонена как противоречащая архитектуре Saloon |
| Старые имена (`save()` и пр.) | **Чистый срез**, без `@deprecated`-алиасов |
| PHPStan | **Прогрессивный ратчет до level 9** (целевой гейт 1.0), привязанный к фазам — см. раздел «Качество». Только через настоящую типизацию, без кастов/baseline. `max` (10) — опционально после 1.0 |
| Методология | **TDD** (тест → реализация); **каждый раздел — отдельный коммит** |
| CI | GitHub Actions, матрица PHP 8.1–8.4 |
| Публикация | Регистрация на Packagist + auto-update webhook |
| База веток | Режем от `dev`; релизный мёрж `dev → main` + тег |

## Вынужденные исключения из паритета

Прямое следствие решения «без OAuth-паритета»:

1. **Disposable-flow Chats/Talks** (боты/чаты через JWT-токены `parseDisposableToken`
   / `parseBotDisposableToken`) — **в 1.x**. Bearer-auth части Talks/Chats/Templates
   портируются.
2. **OAuth-фичи SDK**: домен-детект, OAuth-кнопка/URL, `exchangeApiKey`,
   `getResourceOwner` — **в 1.x**.
3. **Deprecated `Customers/Transactions`** — не портируется (помечено deprecated в
   самом SDK). Документируется как исключение.

## Не входит в 1.0 (Non-goals)

- Расширение OAuth-слоя (см. исключения выше).
- Сущности/методы, отсутствующие в SDK 1.15.

---

## Доменный слой (новое в v1.0)

Вводим паттерн SDK для типобезопасности:

- **`BaseCollection`** + коллекция на сущность (`LeadCollection`, `ContactCollection`,
  …) — типобезопасные итерируемые наборы моделей.
- **Фабрики моделей** — единое создание модели из массива ответа.
- **Response** возвращает типизированную коллекцию вместо «сырого» массива моделей.

Это затрагивает и существующие 7 сущностей (их Response-классы переводятся на
коллекции) — поэтому слой вводится **до** массового портирования (Фаза 3), чтобы
новые сущности сразу следовали финальному паттерну.

---

## Инвентарь паритета (что портируем)

Легенда: ✓ есть · 🟡 частично · ➕ новое

| Группа | Сервисы SDK | Статус |
|---|---|---|
| **Ядро CRM** | Account ✓, Leads ✓, Contacts ✓, Companies ➕, Tasks ✓, Notes ✓, Tags ✓, Users ✓ | дораб./новое |
| **Воронки** | Pipelines ➕, Statuses ➕, LossReasons ➕ | новое |
| **Кастом-поля** | CustomFields 🟡, CustomFieldGroups ➕ | дораб./новое |
| **Связи/подписки** | Links ➕, EntitySubscriptions ➕, ShortLinks ➕ | новое |
| **Файлы** | Files (Drive) ➕, EntityFiles ➕ | новое |
| **Неразобранное** | Unsorted ➕ | новое |
| **События** | Events ➕, EventTypes ➕ | новое |
| **Покупатели** | Customers ➕, Customers/Statuses ➕, Customers/BonusPoints ➕, Segments ➕ | новое |
| **Каталоги/товары** | Catalogs ➕, CatalogElements ➕, Products ➕, Currencies ➕ | новое |
| **Инфраструктура** | Roles ➕, Webhooks ➕, Widgets ➕, Sources ➕, Sources/WebsiteButtons ➕ | новое |
| **Коммуникации** | Calls ➕, Talks 🟡, Chats/Templates 🟡 | новое (без disposable) |

Исключено: `Customers/Transactions` (deprecated).

---

## Фазы реализации

Каждая фаза = отдельная ветка от `dev`, внутри — TDD, коммит на раздел. Фаза
завершается зелёным CI (phpstan на целевом уровне ратчета этой фазы — см. «Качество»)
и мёржем в `dev`.

### Pre-flight (до Фазы 0)
- **Синхронизировать `dev` с `main`.** На 2026-06-07 `dev` отставал от `main` на
  коммит PSR-12 + php-cs-fixer + phpstan (`26a831b`). Выполнено: `dev` приведён к
  `main` через fast-forward (`dev == main`, запушено). Все рабочие ветки v1.0 режутся
  от обновлённого `dev`.
- На будущее: перед стартом каждой фазы проверять, что `dev` не отстал от `main`.

### Фаза 0 — Стабилизация (`fix/audit-blockers`)
- Починить падающий тест `testRequestId` + блокеры **B1–B3** (audit.md).
- Добавить `phpstan.neon` (**level 3**), `phpunit.xml`, `autoload-dev`, `composer scripts`.
- Поднять CI (GitHub Actions): матрица PHP 8.1–8.4, гейты phpstan + cs-fixer + phpunit.
- **Гейт:** CI зелёный, **phpstan level 3**.

### Фаза 1 — Логические баги (`fix/logic-bugs`)
- Починить **L1–L7** из audit.md по TDD (тест-репродьюсер → фикс), каждый отдельным
  коммитом. Корень L3/L4/L6 — заменить `array_merge_recursive`. L2 — сверить ключ
  `updated_at` с документацией amoCRM.
- **L8** (удаление `QueryOrderFieldEnum::SORT`) — ломающее, выполняется в Фазе 2
  вместе с прочими enum-правками.
- **Ратчет:** поднять phpstan до **level 5** (почти бесплатно, +3 поверх 4).
- **Гейт:** баги покрыты тестами, CI зелёный, **phpstan level 5**.

### Фаза 2 — Унификация и типизация API (`refactor/api-consistency`) — BREAKING
- `send()` везде; **удалить** `ContactCreateRequest::save()`.
- Имена создания: `add` / `addLead` / `tag` / `model` → канонические `add()` /
  `addMany()`.
- Единый `with` на enum (`LeadWith`, `ContactWith`, …) вместо 5 реализаций и
  magic-строк. **Единый механизм — трейт `Query\HasWithQuery`** (сделать
  enum-aware). Мигрировать на него:
  - `AccountRequest::with()` / `withAll()` — сейчас **переопределяют** логику, трейт
    не подключают; перевести на трейт + `AccountWith` enum.
  - `UserItemRequest::with()` — inline-дубликат, перевести на трейт.
  - `HasLeadWithQuery` / `HasContactWithQuery` — оставить как тонкие обёртки над
    трейтом с типизированными методами-ярлыками.
  - Переименовать `AccountWithQueryEnum` → `AccountWith` (консистентность с
    `LeadWith` / `ContactWith`).
- Обязательные параметры → конструктор как **`readonly`-свойства** (частичная
  иммутабельность; `create()` принимает модель обязательно; `id`/`entityType` —
  только через конструктор, сеттеров на них нет).
- Фильтры: скрыть «строковый» `filter()` из публичного API, наружу — только
  типизированные фильтр-объекты. Чинит L6. **AF2:** при переходе на типизированные
  фильтры базовый набор `AbstractFilter` резать до ключей, реально поддерживаемых
  каждой сущностью (не наследовать `createdAt`/`name`/`closestTaskAt`/… туда, где
  amoCRM их не принимает — напр. Tasks).
- **Enum-исправления (см. audit.md):**
  - `QueryOrderFieldEnum` — **удалить** `case SORT` (L8: невалидное поле сортировки,
    сверено с докой amoCRM v4 + SDK).
  - `GrandTypeEnum` → **переименовать** в `GrantTypeEnum` (опечатка `grant_type`;
    значения не меняются).
- **Пробелы Query-трейтов** (см. [query-traits-application.md](../../query-traits-application.md)):
  `NoteListRequest` → добавить `HasOrderQuery`; `UserListRequest` → page/limit/with;
  `ContactCustomFieldsListRequest` → page/limit/order (+ унифицировать с
  `CustomFieldListRequest`).
- **Ратчет:** поднять phpstan до **level 6** (типизация фильтров/`with`/enum закрывает
  обрыв 5→6: `missingType.iterableValue`, generics).
- **Гейт:** старые имена срезаны, публичный API типизирован, **phpstan level 6**.

### Фаза 3 — Доменный слой: коллекции + фабрики (`refactor/typed-collections`) — BREAKING
- Ввести `BaseCollection`, коллекции и фабрики; перевести Response существующих
  сущностей на типобезопасные коллекции.
- **Enabler для level 8–9:** типизированные аксессоры модели (`getString`/`getInt`/
  `getArray`/…) и фабрики как единая точка narrowing `mixed` из `json()`/`ArrayStore`.
  Это убирает основную массу `return.type`/`argument.type` **без кастов**.
- **Ратчет:** поднять phpstan до **level 7** (хвост 8–9 — на Фазах 4–5 по мере того,
  как фабрики покрывают `mixed`).
- **Гейт:** все Response типизированы коллекциями, **phpstan level 7**.

### Фаза 4 — Паритет сущностей (`feat/<группа>` по группам инвентаря)
Каждая группа — своя ветка/коммиты, по TDD, в финальном стиле (конструкторы +
коллекции + типизированные фильтры). Набор Query-трейтов на каждую сущность — по
карте в [query-traits-application.md](../../query-traits-application.md):
- 4a — Companies + доукомплектование CRUD ядра (Contacts item/update, Tasks/Notes
  update, delete где применимо).
- 4b — Pipelines, Statuses, LossReasons.
- 4c — CustomFields (завершить) + CustomFieldGroups.
- 4d — Links, EntitySubscriptions, ShortLinks.
- 4e — Files (Drive) + EntityFiles.
- 4f — Unsorted.
- 4g — Events, EventTypes.
- 4h — Customers, Customers/Statuses, Customers/BonusPoints, Segments.
- 4i — Catalogs, CatalogElements, Products, Currencies.
- 4j — Roles, Webhooks, Widgets, Sources, WebsiteButtons.
- 4k — Calls.
- 4l — Talks, Chats/Templates (Bearer-часть; disposable — в 1.x).
- **Ратчет:** новый код пишется чисто на **level 8**; добивание `mixed` из json через
  фабрики сущностей.
- **Гейт:** каждая группа с тестами, **phpstan level 8**.

### Фаза 5 — Полировка релиза (`chore/release-prep`)
- README: install / usage / примеры + бейджи (Packagist version, downloads, CI,
  license). `CHANGELOG.md` (Keep a Changelog).
- `composer.json`: `description`, `keywords`, `support`, `homepage`.
- `.gitattributes`: `export-ignore` для `tests/`, `docs/`, конфигов.
- BC-policy: документ о политике совместимости в 1.x.
- Финальная проверка покрытия по всем сущностям.
- **Ратчет (финал):** поднять phpstan до **level 9** (целевой гейт 1.0) — добить
  остаточный `mixed` через аксессоры/фабрики, **без кастов/baseline**.
- **Гейт:** документация полная, CI зелёный, **phpstan level 9**.

### Фаза 6 — Релиз
- Мёрж `dev → main`; тег `v1.0.0`.
- Регистрация на Packagist + auto-update webhook (GitHub).
- GitHub Release с описанием из CHANGELOG.

---

## Детализация ломающих изменений (для CHANGELOG)

| Было | Стало | Фаза |
|---|---|---|
| `requestId(): array\|int\|null` | корректный тип (B1) | 0 |
| `ContactCreateRequest::save()` | `send()` | 2 |
| `addLead` / `addLeads` / `tag` / `model` | `add()` / `addMany()` | 2 |
| 5 разных `with(...)`, magic-строки | единый трейт `HasWithQuery` + enum `LeadWith` / `ContactWith` / … | 2 |
| `AccountRequest::with()`/`withAll()`, `UserItemRequest::with()` — свои реализации | трейт `HasWithQuery` | 2 |
| `AccountWithQueryEnum` | `AccountWith` | 2 |
| `filter(string $key, $value)` публичный | только типизированные фильтр-объекты | 2 |
| `create()` с опциональной моделью | модель обязательна | 2 |
| `QueryOrderFieldEnum::SORT` | удалён (L8 — невалидное поле) | 2 |
| `GrandTypeEnum` | `GrantTypeEnum` (опечатка) | 2 |
| Response → массив моделей | Response → типобезопасная коллекция | 3 |

## Качество (гейты CI)

- **PHPStan — прогрессивный ратчет до level 9**, привязанный к фазам. Каждый шаг
  поднимается **только через настоящую типизацию**, без кастов/`@phpstan-ignore`/baseline.

  | Фаза | Целевой level | Стоимость (ошибок поверх предыдущего) | Драйвер |
  |---|:--:|:--:|---|
  | 0 | 3 | ~9 | инфраструктура, мелочи |
  | 1 | 5 | +3 | заодно с багами L1–L8 |
  | 2 | 6 | +65 (обрыв 5→6) | типизация фильтров / `with` / enum, generics |
  | 3 | 7 | +2 | коллекции + типизированные аксессоры/фабрики |
  | 4 | 8 | +12 | новый код пишется чисто; narrowing `mixed` через фабрики |
  | 5 | **9** | +174 (обрыв 8→9) | добивание `mixed` из `json()`/`ArrayStore` через аксессоры |

  > `max` (level 10, +33 поверх 9) — опционально после 1.0. Замеры на момент 2026-06-07
  > (на чистом текущем коде); по мере рефактора фактические числа уменьшаются.

- **PHP-CS-Fixer** — `--dry-run` чисто.
- **PHPUnit** — зелёно на матрице PHP 8.1 / 8.2 / 8.3 / 8.4.
- Тесты пишутся **до** реализации (TDD).

## Риски

- **Объём.** Полный паритет (~38 сервисов + коллекции/фабрики) — большой объём;
  реализуется по группам (Фаза 4), каждая группа — отдельный план.
- **L2 (`updated_at`)** — сверить с живой документацией amoCRM до фикса (правило
  проекта: не угадывать).
- **Files/Drive** — бинарная загрузка, отдельный домен; самый нетривиальный пункт.
- **Talks/Chats** — частичный паритет (disposable-flow в 1.x); зафиксировать в
  README/CHANGELOG, чтобы не вводить в заблуждение.
- **Существующие ветки** (`feature/create-tests`, `fix/account-module-bugs`,
  `refactoring/strict-types`, `chore/psr-compliance`) — свериться перед каждой фазой.

## Метрика готовности 1.0

- [ ] CI зелёный, **phpstan level 9** (целевой гейт 1.0).
- [ ] Баги B1–B3, L1–L7 закрыты тестами.
- [ ] Публичный API унифицирован, типизирован, старые имена срезаны.
- [ ] Доменный слой: коллекции + фабрики, Response типизированы.
- [ ] Паритет сущностей по инвентарю (с задокументированными исключениями).
- [ ] README + CHANGELOG + метаданные composer готовы.
- [ ] Пакет на Packagist, webhook настроен.
