# Разбор официального SDK `amocrm/amocrm-api-library`

> Версия: **1.15.0** (установлена в `require-dev`).
> Назначение в проекте: **референс** при портировании API на Saloon.
> В коде обёртки `saloonphp-amocrm` пакет напрямую не используется.

## Что это

Официальный PHP-SDK amoCRM от команды amoCRM (автор — Nikita Bessudnov). Полное
объектное покрытие REST API v4: **418 классов** в `src/AmoCRM`. Стиль
сравнительно старый — поддержка PHP **7.1+**, doc-блочные типы, без
`declare(strict_types)`. HTTP — Guzzle, OAuth — `league/oauth2-client` +
`amocrm/oauth2-amocrm`.

```
require: php >=7.1 || >=8.0
         guzzlehttp/guzzle 6|7
         amocrm/oauth2-amocrm ^3
         lcobucci/jwt        (disposable-токены чатов/ботов)
         nesbot/carbon, ramsey/uuid, symfony/dotenv, fig/http-message-util
```

## Архитектура (3 слоя)

```
AmoCRMApiClient (фасад, ~50 методов-входов)
        │  $client->leads(), ->contacts(), ->tasks(), ->notes($type) ...
        ▼
EntitiesServices/*  (38 сервисов; 49 файлов с базовыми/интерфейсами/трейтами) extends BaseEntity
        │  CRUD: get / getOne / add / addOne / update / updateOne / syncOne
        ▼
Models + Collections + Filters  (243 + 56 + 31 классов)
```

### Базовый контракт сервисов — `BaseEntity`

| Метод | Назначение |
|---|---|
| `get(?BaseEntityFilter, array $with)` | список → `BaseApiCollection` |
| `getOne($id, array $with)` | один элемент → `BaseApiModel` |
| `add(Collection)` / `addOne(Model)` | создание |
| `update(Collection)` / `updateOne(Model)` | обновление |
| `syncOne(Model, $with)` | дозагрузка модели с сервера |

Сервис объявляет только `$method` (endpoint), `$collectionClass`, `ITEM_CLASS`.

Доп. контракты-миксины:
- `HasPageMethodsInterface` / `PageMethodsTrait` — пагинация (`nextPage()` / `prevPage()`)
- `HasLinkMethodInterface` / `LinkMethodsTrait` — связи (`link()` / `unlink()`)
- `HasDeleteMethodInterface` — удаление
- `HasParentEntity` / `WithParentEntityMethodsTrait` — вложенные ресурсы

## OAuth (`AmoCRMOAuth`)

Заметно богаче, чем в обёртке:

- `getAuthorizeUrl()`, `getOAuthButton()` — готовая HTML-кнопка авторизации
- `getAccessTokenByCode()`, `getAccessTokenByRefreshToken()`
- `exchangeApiKey()` — миграция со старого API-ключа
- `getAccountDomain()` / `getAccountDomainByRefreshToken()` — определение домена аккаунта
- `setAccessTokenRefreshCallback()`
- `getResourceOwner()`, `getAuthorizationHeaders()`, `setProtocol()`, `setBaseDomain()`
- **disposable-токены** для чатов/ботов: `parseDisposableToken()`,
  `parseBotDisposableToken()` (через `lcobucci/jwt`)

## Покрытие сущностей (38 сервисов)

> 49 файлов в `EntitiesServices/`, из них 11 — базовые классы, интерфейсы и трейты;
> реальных сервисов сущностей — **38**.

- **Сущности:** Leads (+ `addComplex` — сделка с контактами/компаниями за один
  запрос), Contacts, Companies, Customers (+ Statuses, Transactions, BonusPoints),
  Tasks, Notes, Tags, Events / EventTypes.
- **Каталоги:** Catalogs, CatalogElements, Products, Currencies.
- **Воронки:** Pipelines, Statuses, LossReasons.
- **Поля:** CustomFields, CustomFieldGroups.
- **Связи / файлы:** Links, EntityFiles, Files (Drive API), ShortLinks, EntityTags,
  EntitySubscriptions.
- **Инфраструктура аккаунта:** Account, Users, Roles, Segments, Webhooks, Widgets,
  Sources (+ WebsiteButtons), Unsorted, Calls, Talks, Chats / Templates.

## Доменный слой и особенности

- **Models (243):** богатые модели с типизированными значениями кастом-полей
  (`CustomFieldsValues/*` под каждый тип), фабрики (`Factories`), интерфейсы, трейты,
  спец-модели токенов/доменов.
- **Collections (56):** типобезопасные коллекции (`LeadsCollection` и т.д.),
  проверяющие соответствие ключей ответа.
- **Filters (31):** отдельный фильтр-класс на сущность (`LeadsFilter`, `TasksFilter`,
  `ContactsFilter`…) + `BaseRangeFilter`.
- **Exceptions (19):** детальная иерархия —
  `AmoCRMApiTooManyRequestsException`, `AmoCRMApiNoContentException`,
  `AmoCRMApiPageNotAvailableException`,
  `CollectionAndResponseKeysNotIdenticalException`, ошибки disposable-токенов и др.
- **Клиент:** `withContextUserId()` (запрос от имени пользователя для админ-токенов,
  иммутабельный `clone`), `setCheckHttpStatusCallback()`, `setUserAgent()`,
  `LongLivedAccessToken`, `AmoCRMApiClientFactory`.

---

## Gap-таблица: официальный SDK → обёртка `saloonphp-amocrm`

Легенда: ✅ есть · 🟡 частично · ❌ нет

### Сводка по слоям

| Аспект | Официальный SDK 1.15 | Обёртка `saloonphp-amocrm` |
|---|---|---|
| Сущностей-сервисов | 38 (49 файлов) | 7 |
| Базовый CRUD | get / getOne / add / update / sync / delete / link | 🟡 частичный, без delete / link / sync |
| Сложное добавление | `addComplex` (сделка + контакты) | ❌ |
| Пагинация | итераторы `nextPage()` / `prevPage()` | 🟡 только `page()` + ссылки в ответе |
| OAuth | домен-детект, disposable, кнопка, exchangeApiKey | 🟡 базовый code / refresh |
| Стиль | PHP 7.1, docblock-типы, Guzzle | PHP 8.1, нативные типы, Saloon |
| Коллекции / фабрики | ✅ | ❌ (ArrayStore-модели) |
| Контекст пользователя | `withContextUserId()` | ❌ |

### Покрытие сущностей

| Сущность / сервис | SDK | Обёртка | Комментарий |
|---|:--:|:--:|---|
| Account | ✅ | ✅ | в обёртке только чтение |
| Leads | ✅ | 🟡 | нет delete; есть list/item/create/update/customFields |
| Leads `addComplex` | ✅ | ❌ | сделка + контакты/компании одним запросом |
| Contacts | ✅ | 🟡 | нет item / update / delete |
| Companies | ✅ | ❌ | — |
| Customers (+ Statuses/Transactions/BonusPoints) | ✅ | ❌ | — |
| Tasks | ✅ | 🟡 | нет update / delete |
| Notes / EntityNotes | ✅ | 🟡 | нет update / delete; доступны только из `leads()` |
| Tags / EntityTags | ✅ | 🟡 | нетипизированные ответы; доступны только из `leads()` |
| Users | ✅ | 🟡 | list / item, без update |
| Roles | ✅ | ❌ | — |
| Events / EventTypes | ✅ | ❌ | — |
| Pipelines / Statuses / LossReasons | ✅ | ❌ | — |
| CustomFields | ✅ | 🟡 | только list |
| CustomFieldGroups | ✅ | ❌ | — |
| Catalogs / CatalogElements / Products | ✅ | ❌ | — |
| Currencies | ✅ | ❌ | — |
| Links (связи сущностей) | ✅ | ❌ | — |
| Files / EntityFiles (Drive API) | ✅ | ❌ | — |
| ShortLinks | ✅ | ❌ | — |
| EntitySubscriptions | ✅ | ❌ | — |
| Segments | ✅ | ❌ | — |
| Webhooks | ✅ | ❌ | — |
| Widgets | ✅ | ❌ | — |
| Sources / WebsiteButtons | ✅ | ❌ | — |
| Unsorted (неразобранное) | ✅ | ❌ | — |
| Calls | ✅ | ❌ | — |
| Talks / Chats / Templates | ✅ | ❌ | — |

### Инфраструктура / OAuth

| Возможность | SDK | Обёртка |
|---|:--:|:--:|
| Авторизация по коду | ✅ | ✅ |
| Обновление по refresh-токену | ✅ | ✅ |
| Колбэк обновления токена | ✅ | ✅ |
| Определение домена аккаунта | ✅ | ❌ |
| OAuth-кнопка / URL авторизации | ✅ | ❌ |
| `exchangeApiKey` (миграция API-ключа) | ✅ | ❌ |
| Disposable-токены (чаты/боты) | ✅ | ❌ |
| Контекст пользователя (`withContextUserId`) | ✅ | ❌ |
| Кастомная обработка HTTP-статуса | ✅ | 🟡 (через Saloon middleware) |
| Типобезопасные коллекции | ✅ | ❌ |
| Фабрики моделей | ✅ | ❌ |

---

## Выводы для роадмапа портирования

Обёртка `saloonphp-amocrm` — современная, тонкая и типобезопасная (PHP 8.1 + Saloon),
но покрывает малую долю API. Официальный SDK служит картой того, что ещё предстоит
портировать. Приоритеты на основе таблицы:

1. **Закрыть базовый CRUD** существующих сущностей: `delete`, `item`/`update` для
   Contacts, `update` для Tasks/Notes.
2. **Пробросить** `notes()` / `tags()` в Contacts и Tasks (сервисы уже универсальны).
3. **Сложное добавление** `addComplex` для сделок (частый сценарий интеграций).
4. **Новые сущности** по востребованности: Companies, Pipelines/Statuses,
   CustomFieldGroups, Links, Unsorted, Catalogs/CatalogElements.
5. **Расширить OAuth:** определение домена аккаунта, OAuth-кнопка/URL.
6. **Доменные удобства:** типобезопасные коллекции и итераторы пагинации.
