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
| CI | GitHub Actions, матрица PHP 8.2–8.4 (8.1 отпал с переходом на `php: ^8.2` + saloon 4) |
| Публикация | Packagist + auto-update webhook — настроено, проверено на `v0.8.0` |
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

**Статус на 2026-08-13** (актуализировано после `v0.8.0`):

| Фаза | Статус | Итог |
|---|---|---|
| Pre-flight | ✅ | `dev` синхронизирован с `main` |
| 0 — Стабилизация | ✅ | B1–B3, инфраструктура, CI, level 3 |
| 1 — Логические баги | ✅ | L1–L8, AF1, AF2, level 5, `CHANGELOG` → **`v0.8.0`** |
| 2 — Унификация API | ✅ | level 6 (ратчет — отдельным PR) |
| 3 — Доменный слой | ⏳ | level 7 |
| 4 — Паритет сущностей | ⏳ | level 8, 12 групп `4a`–`4l` |
| 5 — Полировка | ⏳ | level 9 |
| 6 — Релиз | ⏳ | тег `v1.0.0` (Packagist уже настроен) |

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

### Фаза 1 — Логические баги (`fix/logic-bugs`) — ✅ выполнена, `v0.8.0`
- **L1–L8, AF1, AF2** закрыты по TDD (тест-репродьюсер → фикс), коммит на баг.
  Корень L3/L4/L6 — `array_merge_recursive`. L2, L8, AF2 сверены с офиц. докой amoCRM.
- **L8 и AF2 сделаны здесь, а не в Фазе 2**, как планировалось: оба ломающие, и одна
  BC-волна дешевле двух. Из плана Фазы 2 вычтены.
- Побочные результаты сверки с докой:
  - `QueryOrderFieldEnum` был неправ в обе стороны — `SORT` лишний, а `COMPLETE_TILL`
    (валидное поле сортировки задач) отсутствовал. Добавлен.
  - Аудит ошибочно приписывал задачам фильтр `created_by` — в `tasks-api` его нет,
    в урезанной базе не оставлен.
- Попутно: `newest()` после фикса L7 стал дублём `latest()` — удалён, добавлен
  `oldest()` (ASC). Заведён `CHANGELOG.md` (пункт из Фазы 5).
- **Ратчет:** phpstan **level 5** — выполнено.
- **Гейт:** ✅ 105 тестов / 211 assertions, CI зелёный (PHP 8.2–8.4), phpstan level 5.

> ~~Осталось от этого круга задач: `QueryOrderFieldEnum` — объединение полей по всем
> сущностям.~~ — ✅ закрыто в Фазе 2 разделением на пер-сущностные енамы. Там же
> вскрылось, что удаление `SORT` было ошибкой для кастом-полей — исправлено патчем
> `v0.8.1`.

### Фаза 2 — Унификация и типизация API (`refactor/api-consistency`) — ✅ BREAKING

Разбита на два PR: ломающая унификация и отдельно ратчет level 6 — правки почти не
пересекаются, а один PR на ~45 файлов нечитаем.

**Перед планированием сверены доки каждой сущности отдельно.** Это дало матрицу,
которая расходилась и с планом, и с кодом:

| Сущность | `order` | `with` | `filter` |
|---|---|---|---|
| сделки | `created_at`, `updated_at`, `id` | 7 (+`source`, не было) | 13 |
| контакты | `updated_at`, `id` (**без `created_at`**) | 3 | 9 |
| задачи | `created_at`, `complete_till`, `id` | — | 7 |
| примечания | `updated_at`, `id` (не было) | `is_pinned` (не было) | 4 |
| кастом-поля | `sort`, `id` | — | `type` |
| теги | **не поддерживается** | — | `name`, `id` |
| пользователи | не поддерживается | 6 | не поддерживается |

Сделано (коммит на раздел):

- **Имена.** `ContactCreateRequest::save()`, `LeadUpdateRequest::addLead()`/
  `addLeads()`, `TagCreateRequest::tag()`, `TagAttachRequest::model()`,
  `TagReference::updateLead()` срезаны → `send()`, `add()`, `addMany()`, `add()`,
  `update()`. Попутно `CustomFieldListRequest` получил `send()`.
- **`with` на енамах.** `HasWithQuery` стал generic по `Query\WithField`; енамы
  `LeadWith`/`ContactWith`/`UserWith`/`NoteWith`/`AccountWith`. `AccountRequest`
  переведён на трейт, `withAll()` живёт в `HasAccountWithQuery` (перебор `cases()`
  требует знания енама). `HasContactWithQuery` переехал в `Requests/Traits/`.
- **Пер-сущностные поля сортировки.** `QueryOrderFieldEnum` удалён; `HasOrderQuery`
  generic по `Query\OrderField`, `latest()`/`oldest()` — в пер-сущностных обёртках
  (дефолт `::ID` конкретного енама generic-параметром невыразим).
- **`readonly` + обязательная модель.** Гейт держит reflection-тест: любой
  promoted-параметр конструктора запроса обязан быть `readonly`, поэтому новый
  запрос не сможет добавить изменяемый.
- **Фильтры.** `filter()` → `protected`, `addFilter()` — один generic-метод в трейте
  вместо восьми копий. Новые `ContactFilter`, `NoteFilter`, `TagFilter`; шесть общих
  для сделок и контактов ключей — в трейте `HasCommonEntityFilters` (в Фазе 1 они
  ждали второго потребителя — им стал `ContactFilter`).
- **`GrandTypeEnum` → `GrantTypeEnum`.**
- **Пробелы Query-трейтов** закрыты: `NoteListRequest` получил order и `with`;
  `UserListRequest` и `ContactCustomFieldsListRequest` были закрыты ещё до Фазы 2.

> Типизация проверена на отрицательных примерах, а не только на зелёных тестах:
> `TaskListRequest::latest(LeadOrderField::UPDATED_AT)` и
> `LeadListRequest::addFilter(NoteFilter::make())` отвергаются PHPStan на level 6,
> а первое — ещё и PHP в рантайме.

- **Ратчет:** phpstan **level 6** — PR `chore/phpstan-level-6`. Замер: 65 ошибок,
  64 из них `missingType.iterableValue` в моделях, ответах и контрактах, т.е. с
  ломающими правками пересечения почти нет (потому и отдельный PR).
- **Гейт:** старые имена срезаны, публичный API типизирован, **phpstan level 6**.

> Осталось от этого круга: дубль `ContactCustomFieldsListRequest` /
> `CustomFieldListRequest` — сводить в Фазе 4c вместе с остальными кастом-полями.
> Теги по-прежнему отдают сырой `Saloon\Response` — типизированные ответы требуют
> новых Response-классов, это Фаза 3.

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
  license). ~~`CHANGELOG.md` (Keep a Changelog)~~ — ✅ заведён в Фазе 1, здесь только
  дописывать секции по фазам.
- `composer.json`: `description`, `keywords`, `support`, `homepage`.
- `.gitattributes`: `export-ignore` для `tests/`, `docs/`, конфигов.
- BC-policy: документ о политике совместимости в 1.x.
- Финальная проверка покрытия по всем сущностям.
- **Ратчет (финал):** поднять phpstan до **level 9** (целевой гейт 1.0) — добить
  остаточный `mixed` через аксессоры/фабрики, **без кастов/baseline**.
- **Гейт:** документация полная, CI зелёный, **phpstan level 9**.

### Фаза 6 — Релиз
- Мёрж `dev → main`; тег `v1.0.0`.
- ~~Регистрация на Packagist + auto-update webhook (GitHub)~~ — ✅ работает, проверено
  на `v0.8.0`: версия появилась в p2-метаданных через секунды после пуша тега.
- GitHub Release с описанием из CHANGELOG.

---

## Детализация ломающих изменений (для CHANGELOG)

| Было | Стало | Фаза |
|---|---|---|
| `requestId(): array\|int\|null` | корректный тип (B1) | 0 |
| `ContactCreateRequest::save()` | `send()` | **2** ✅ |
| `addLead` / `addLeads` / `tag` / `model` / `updateLead` | `add()` / `addMany()` / `update()` | **2** ✅ |
| 5 разных `with(...)`, magic-строки | единый generic-трейт `HasWithQuery<TWith>` + енамы по сущности | **2** ✅ |
| `AccountRequest::with()`/`withAll()` — своя реализация | трейт `HasWithQuery` | **2** ✅ |
| `AccountWithQueryEnum` | `AccountWith` | **2** ✅ |
| `filter(string $key, $value)` публичный | `protected`; наружу `addFilter(TFilter)` | **2** ✅ |
| `create()` с опциональной моделью | модель обязательна | **2** ✅ |
| `QueryOrderFieldEnum::SORT` | удалён (L8 — невалидное поле) | **1** ✅ |
| `HasOrderQuery::newest()` | удалён; `latest()` (DESC) + `oldest()` (ASC) | **1** ✅ |
| `TaskFilter` наследует `name`/`createdBy`/`updatedBy`/`createdAt`/`closestTaskAt`/`customFieldsValues` | не наследует; методы у `LeadFilter` (AF2) | **1** ✅ |
| `filter()` аккумулирует повторный скалярный ключ | last-wins (L6) | **1** ✅ |
| `GrandTypeEnum` | `GrantTypeEnum` (опечатка) | **2** ✅ |
| `QueryOrderFieldEnum` — объединение полей всех сущностей | енам на сущность + generic `HasOrderQuery` | **2** ✅ |
| `NoteListRequest::filterId`/`filterEntityId`/`filterNoteType`, `TagListRequest::filterName`/`filterId` | `NoteFilter` / `TagFilter` | **2** ✅ |
| сортировка контактов по `created_at` | невыразима — amoCRM её не поддерживал | **2** ✅ |
| Response → массив моделей | Response → типобезопасная коллекция | 3 |

## Качество (гейты CI)

- **PHPStan — прогрессивный ратчет до level 9**, привязанный к фазам. Каждый шаг
  поднимается **только через настоящую типизацию**, без кастов/`@phpstan-ignore`/baseline.

  | Фаза | Целевой level | Стоимость (ошибок поверх предыдущего) | Драйвер |
  |---|:--:|:--:|---|
  | 0 | 3 | ~9 | инфраструктура, мелочи ✅ |
  | 1 | 5 | +3 | заодно с багами L1–L8 ✅ |
  | 2 | 6 | +65 (обрыв 5→6; замер на `v0.8.1`) ✅ | `missingType.iterableValue` в моделях/ответах/контрактах |
  | 3 | 7 | +2 | коллекции + типизированные аксессоры/фабрики |
  | 4 | 8 | +12 | новый код пишется чисто; narrowing `mixed` через фабрики |
  | 5 | **9** | +174 (обрыв 8→9) | добивание `mixed` из `json()`/`ArrayStore` через аксессоры |

  > `max` (level 10, +33 поверх 9) — опционально после 1.0. Замеры на момент 2026-06-07
  > (на чистом текущем коде); по мере рефактора фактические числа уменьшаются.

- ~~**PHP-CS-Fixer** — `--dry-run` чисто.~~ — зависимость и скрипты `cs`/`cs-fix`
  удалены из `composer.json`; гейта больше нет.
- **PHPUnit** — зелёно на матрице PHP 8.2 / 8.3 / 8.4 (8.1 отпал вместе с переходом
  на `php: ^8.2` и saloon 4).
- Тесты пишутся **до** реализации (TDD).

## Риски

- **Объём.** Полный паритет (~38 сервисов + коллекции/фабрики) — большой объём;
  реализуется по группам (Фаза 4), каждая группа — отдельный план.
- ~~**L2 (`updated_at`)** — сверить с живой документацией amoCRM до фикса~~ — сверено
  в Фазе 1. Правило подтвердило себя: сводка аудита разошлась с докой по двум пунктам
  (`created_by` у задач, отсутствующий `complete_till`). Сверять доку, а не аудит.
- **Сверять доку каждой сущности, а не одну страницу.** В Фазе 1 вывод по `leads-api`
  («`sort` — не поле сортировки») был обобщён на весь енам и сломал сортировку
  кастом-полей — регрессия жила до `v0.8.1`. В Фазе 2 сверка по странице на сущность
  нашла ещё три расхождения: `source` у сделок, `is_pinned` и order у примечаний,
  отсутствие `created_at` у контактов.
- **Files/Drive** — бинарная загрузка, отдельный домен; самый нетривиальный пункт.
- **Talks/Chats** — частичный паритет (disposable-flow в 1.x); зафиксировать в
  README/CHANGELOG, чтобы не вводить в заблуждение.
- **Существующие ветки** (`feature/create-tests`, `fix/account-module-bugs`,
  `refactoring/strict-types`, `chore/psr-compliance`) — свериться перед каждой фазой.

## Метрика готовности 1.0

- [ ] CI зелёный, **phpstan level 9** (целевой гейт 1.0). — CI зелёный, level **6**.
- [x] Баги B1–B3, L1–L8, AF1, AF2 закрыты тестами. — Фазы 0–1, `v0.8.0`.
- [x] Публичный API унифицирован, типизирован, старые имена срезаны. — Фаза 2.
- [ ] Доменный слой: коллекции + фабрики, Response типизированы.
- [ ] Паритет сущностей по инвентарю (с задокументированными исключениями).
- [ ] README + CHANGELOG + метаданные composer готовы. — `CHANGELOG` и `description`
      есть; остаются README (usage/бейджи), `keywords`/`support`/`homepage`,
      `.gitattributes`.
- [x] Пакет на Packagist, webhook настроен. — проверено на `v0.8.0`.
