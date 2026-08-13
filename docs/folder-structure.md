# Целевая структура папок (v1.0)

> Планируемая раскладка `src/` после реализации [спеки релиза v1.0](superpowers/specs/2026-06-07-amocrm-v1-release-design.md):
> полный паритет с SDK 1.15, типобезопасные коллекции + фабрики, типизированные
> фильтры и `with`-enum. Документ — ориентир; точные имена уточняются при реализации
> каждой фазы.
>
> Легенда: ✓ есть сейчас · ➕ новое в v1.0

## Принципы раскладки

- **Модуль на сущность.** Всё, что относится к сущности, лежит в
  `src/Modules/<Entity>/` (модель, коллекция, фабрика, фильтр, `with`-enum,
  Reference, Requests/, Responses/). Сохраняем текущую конвенцию проекта.
- **Общая инфраструктура** — в корне `src/` (Client, Connectors, Configs, Query,
  Filters, Collections, Responses, Enum, Exceptions, Contracts).
- **Базовые классы** — рядом с одноимённой папкой: `Modules/Model.php`,
  `Filters/AbstractFilter.php`, `Collections/BaseCollection.php`.
- **Вложенность** — только для настоящих «родитель → потомок» (Pipelines → Statuses
  → LossReasons; Customers → Statuses/BonusPoints; Sources → WebsiteButtons; Chats →
  Templates). Остальное — плоско.

---

## Канонический модуль (шаблон сущности)

Каждая сущность повторяет эту анатомию (на примере `Lead`):

```
src/Modules/Lead/
├── LeadReference.php          ✓  точка входа: list/item/create/update/delete (+ sub-ref)
├── LeadModel.php              ✓  доменная модель (ArrayStore)
├── LeadCollection.php         ➕ типобезопасная коллекция моделей
├── LeadFactory.php            ➕ фабрика модели из массива ответа
├── LeadFilter.php             ✓  типизированный фильтр (наружу вместо строкового filter())
├── LeadWith.php               ➕ enum параметров ?with= (вместо magic-строк)
├── Requests/
│   ├── AbstractLeadRequest.php   ✓
│   ├── LeadListRequest.php       ✓
│   ├── LeadItemRequest.php       ✓
│   ├── LeadCreateRequest.php     ✓  (модель — обязательный параметр конструктора)
│   ├── LeadUpdateRequest.php     ✓
│   └── LeadDeleteRequest.php     ➕ где применимо (по HasDeleteMethodInterface SDK)
└── Responses/
    ├── LeadListResponse.php      ✓  возвращает LeadCollection
    ├── LeadItemResponse.php      ✓
    ├── LeadCreateResponse.php    ✓
    └── LeadUpdateResponse.php    ✓
```

> Не у всех сущностей есть полный набор операций — состав `Requests/`/`Responses/`
> определяется паритетом с соответствующим сервисом SDK.

---

## Полная раскладка `src/`

```
src/
├── Client.php                         ✓  фасад: leads(), contacts(), tasks(), ...
│
├── Connectors/                        ✓
│   ├── MainConnector.php              ✓  https://{domain}/api/v4 (Bearer)
│   └── OAuth2Connector.php            ✓  https://{domain}/oauth2
│
├── Configs/                           ✓
│   ├── Config.php                     ✓
│   └── TokenConfig.php                ✓
│
├── Contracts/                         ✓  интерфейсы (TagsContract, TaskContract, ...)
│
├── Requests/
│   └── OAuth2/
│       └── AccessToken.php            ✓  code/refresh (без OAuth-паритета)
│
├── Query/                             ✓  общие query-трейты
│   ├── HasPageQuery.php               ✓
│   ├── HasLimitQuery.php              ✓
│   ├── HasSearchQuery.php             ✓
│   ├── HasOrderQuery.php              ✓
│   ├── HasFilterQuery.php             ✓  (станет internal; наружу — фильтр-объекты)
│   └── HasWithQuery.php               ✓  (единый механизм на enum)
│
├── Filters/                           ✓
│   └── AbstractFilter.php             ✓  база типизированных фильтров
│
├── Collections/                       ➕ доменный слой коллекций
│   └── BaseCollection.php             ➕ база типобезопасных коллекций
│
├── Responses/                         ✓  общие response-трейты
│   ├── HasPageResponse.php            ✓
│   └── HasLinksResponse.php           ✓
│
├── Enum/                              ✓  общие enum
│   ├── QueryOrderEnum.php             ✓
│   ├── QueryOrderFieldEnum.php        ✓
│   └── GrandTypeEnum.php              ✓
│
├── Exceptions/                        ✓
│   └── AmoCrm/                        ✓
│
└── Modules/
    ├── Model.php                      ✓  база всех моделей
    │
    │  ── Ядро CRM ──────────────────────────────
    ├── Account/                       ✓
    ├── Lead/                          ✓
    ├── Contact/                       ✓  (+ item/update/delete ➕)
    ├── Company/                       ➕
    ├── Task/                          ✓  (+ update/delete ➕)
    ├── Note/                          ✓  (+ update/delete ➕)
    ├── Tag/                           ✓
    ├── User/                          ✓
    │
    │  ── Воронки ───────────────────────────────
    ├── Pipeline/                      ✓  (только list; create/update/delete ➕)
    │   ├── Status/                    ✓  этапы воронки (только чтение из `_embedded.statuses`)
    │   └── LossReason/                ➕  причины отказа
    │
    │  ── Кастом-поля ───────────────────────────
    ├── CustomField/                   ✓  (доукомплектовать)
    └── CustomFieldGroup/              ➕  группы полей (табы карточки)
    │
    │  ── Связи / подписки / ссылки ─────────────
    ├── Link/                          ➕  связи сущностей (link/unlink)
    ├── EntitySubscription/            ➕
    ├── ShortLink/                     ➕
    │
    │  ── Файлы ─────────────────────────────────
    ├── File/                          ➕  Drive API (отдельный домен, бинарная загрузка)
    ├── EntityFile/                    ➕  связь файлов с сущностями
    │
    │  ── Неразобранное ─────────────────────────
    ├── Unsorted/                      ➕  list + accept/reject
    │
    │  ── События ───────────────────────────────
    ├── Event/                         ➕
    ├── EventType/                     ➕
    │
    │  ── Покупатели ────────────────────────────
    ├── Customer/                      ➕
    │   ├── Status/                    ➕  статусы покупателей
    │   └── BonusPoint/                ➕  начисление/списание баллов
    ├── Segment/                       ➕  сегменты покупателей
    │
    │  ── Каталоги / товары ─────────────────────
    ├── Catalog/                       ➕
    ├── CatalogElement/                ➕
    ├── Product/                       ➕
    ├── Currency/                      ➕
    │
    │  ── Инфраструктура аккаунта ───────────────
    ├── Role/                          ➕
    ├── Webhook/                       ✓  (list/subscribe/unsubscribe)
    ├── Widget/                        ➕
    ├── Source/                        ➕
    │   └── WebsiteButton/             ➕
    │
    │  ── Коммуникации ──────────────────────────
    ├── Call/                          ➕
    ├── Talk/                          ➕  (Bearer-часть; disposable-flow → 1.x)
    └── Chat/                          ➕
        └── Template/                  ➕  (Bearer-часть; disposable-flow → 1.x)
```

Исключено из паритета: `Customers/Transactions` (deprecated в SDK),
OAuth-зависимые disposable-flow Chats/Talks (→ 1.x).

---

## Раскладка вне `src/`

```
.
├── src/                              (см. выше)
├── tests/                            ➕ зеркалит src/ (юнит + интеграция per Request/Response/Filter)
│   ├── Modules/
│   │   ├── Lead/ ...
│   │   └── ...
│   ├── Query/
│   ├── Filters/
│   └── Collections/
│
├── docs/                            ✓ (карта API, аудит, разбор SDK, спека, эта структура)
│
├── .github/
│   └── workflows/
│       └── ci.yml                    ➕ матрица PHP 8.1–8.4: phpstan(8) + cs-fixer + phpunit
│
├── phpstan.neon                      ➕ level 8
├── phpunit.xml                       ➕
├── .php-cs-fixer.dist.php            ✓
├── .gitattributes                    ➕ export-ignore для tests/, docs/, конфигов
├── CHANGELOG.md                      ➕ Keep a Changelog
├── README.md                        ✓ (переписать: install/usage + бейджи)
└── composer.json                    ✓ (+ autoload-dev, scripts, keywords, support)
```

---

## Соответствие фазам реализации

| Что появляется | Фаза (спека) |
|---|---|
| `phpstan.neon`, `phpunit.xml`, `.github/workflows/ci.yml`, `autoload-dev` | 0 |
| `with`-enum, типизированные фильтры наружу, переименования | 2 |
| `Collections/BaseCollection.php`, `<Entity>Collection`, `<Entity>Factory` | 3 |
| Новые модули `Modules/<Entity>/` (➕) | 4a–4l |
| `.gitattributes`, `CHANGELOG.md`, README, метаданные composer | 5 |
