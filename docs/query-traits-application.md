# Применение Query-трейтов

> Аудит применимости трейтов `src/Query/*` к текущим запросам + карта, какие трейты
> нужны новым сущностям паритета (v1.0). Поддержка query-параметров сверена с
> официальным SDK `amocrm/amocrm-api-library` (фильтры, `HasPageMethodsInterface`,
> `OrderTrait`, `filter['query']`), а не на память.

## Трейты `src/Query` (6)

| Трейт | Публичные методы | Query-параметр |
|---|---|---|
| `HasPageQuery` | `page(int)` | `page` |
| `HasLimitQuery` | `limit(int)` | `limit` |
| `HasSearchQuery` | `querySearch(string\|int)` | `query` (поиск) |
| `HasFilterQuery` | `filter(key, value)` | `filter[...]` |
| `HasOrderQuery` | `order()`, `newest()`, `latest()`, `removeOrder()` | `order[...]` |
| `HasWithQuery` | `with(array)`, `addWith(string)` | `with` |

> Связанные находки аудита: L6 (`filter` аккумулирует скаляры), L7 (`newest/latest`
> инвертированы), L8 (`QueryOrderFieldEnum::SORT` невалиден), `with` (5 реализаций).
> См. [audit.md](audit.md).

---

## Что реально поддерживает amoCRM (по SDK)

| Параметр | Сущности, поддерживающие его |
|---|---|
| **page / limit** | Leads, Contacts, Companies, Customers, Tasks, Notes, Tags, Users, CustomFields, Events, Files, Catalogs, CatalogElements, Currencies, Segments, Roles, Widgets, Unsorted, EntitySubscriptions, Chats/Templates |
| **order** | Leads, Contacts, Companies, Customers, Tasks, **Notes**, CustomFields, Unsorted |
| **query** (поиск) | Leads, Contacts, Companies, Customers, Tags, CatalogElements |
| **filter** | Leads, Contacts, Companies, Customers, Tasks, Notes, Tags, CustomFields, Events, Files, Links, Catalogs, CatalogElements, Currencies, Unsorted, Webhooks, Sources |
| **with** | Leads, Contacts, Companies, Customers, Users, Account, CatalogElements *(точный набор — на этапе реализации группы)* |

---

## 1. Аудит текущего применения

Легенда: ✅ применён верно · ⚠️ пробел (трейт применим, но не подключён) · 🟡 работает, но не через трейт

| Запрос | page | limit | query | filter | order | with | Вердикт |
|---|:--:|:--:|:--:|:--:|:--:|:--:|---|
| `LeadListRequest` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | корректно |
| `LeadItemRequest` | — | — | — | — | — | ✅ | корректно |
| `LeadCustomFieldsListRequest` | ✅ | ✅ | — | ✅ | ✅ | — | корректно |
| `ContactListRequest` | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | корректно |
| `ContactCustomFieldsListRequest` | ⚠️ | ⚠️ | — | ⚠️ | ⚠️ | — | **пробел** + дубль `CustomFieldListRequest` |
| `TaskListRequest` | ✅ | ✅ | — | ✅ | ✅ | — | корректно (query у задач нет — верно) |
| `NoteListRequest` | ✅ | ✅ | — | ✅ | ⚠️ | — | **пробел: нет `HasOrderQuery`** (Notes поддерживают order) |
| `TagListRequest` | ✅ | ✅ | ✅ | ✅ | — | — | корректно (Tags поддерживают query — проверено) |
| `CustomFieldListRequest` | ✅ | ✅ | — | ✅ | ✅ | — | корректно |
| `UserListRequest` | ✅ | ✅ | — | — | — | ✅ | корректно |
| `UserItemRequest` | — | — | — | — | — | ✅ | корректно |
| `AccountRequest` | — | — | — | — | — | 🟡 | `with` своя реализация, не через трейт |

### Находки

- **Q-GAP1 — `NoteListRequest`**: Notes поддерживают `order` (SDK: `NotesFilter` → `OrderTrait`), но трейт не подключён. Добавить `HasOrderQuery`.
- ~~**Q-GAP2 — `UserListRequest`**: Users поддерживают пагинацию и `with` (роли/группы), но запрос без трейтов вовсе. Добавить `HasPageQuery`, `HasLimitQuery`, `with`.~~ Закрыто: подключены `HasPageQuery`, `HasLimitQuery`, `HasUserWithQuery`.
- **Q-GAP3 — `ContactCustomFieldsListRequest`**: custom_fields поддерживают page/limit/order/filter, но запрос «голый»; к тому же дублирует `CustomFieldListRequest` — унифицировать.
- **Q-STYLE — `AccountRequest`**: `with` не через `HasWithQuery` — своя сигнатура на одно значение (уже зафиксировано в спеке, Фаза 2). `UserItemRequest` переведён на `HasUserWithQuery`.
- **Над-применения не найдено**: ранее спорные `TagListRequest::querySearch` и `CustomFieldListRequest::order` — **валидны** (подтверждено SDK).

---

## 2. Какие трейты нужны новым сущностям (паритет)

Карта для Фазы 4 спеки. Легенда: ✅ нужен · — не поддерживается эндпоинтом · ❓ сверить при реализации группы.

| Новая сущность | page/limit | order | query | filter | with |
|---|:--:|:--:|:--:|:--:|:--:|
| Company | ✅ | ✅ | ✅ | ✅ | ✅ |
| Pipeline | — | — | — | — | — |
| Status (этапы) | — | — | — | — | — |
| LossReason | — | — | — | — | — |
| CustomFieldGroup | — | — | — | — | — |
| Link | — | — | — | ✅ | — |
| EntitySubscription | ✅ | — | — | — | — |
| ShortLink | — | — | — | — | — |
| File (Drive) | ✅ | — | — | ✅ | — |
| EntityFile | ❓ | — | — | ❓ | — |
| Unsorted | ✅ | ✅ | — | ✅ | — |
| Event | ✅ | — | — | ✅ | — |
| EventType | — | — | — | — | — |
| Customer | ✅ | ✅ | ✅ | ✅ | ✅ |
| Customer/Status | — | — | — | — | — |
| Customer/BonusPoint | — | — | — | — | — |
| Segment | ✅ | — | — | ❓ | — |
| Catalog | ✅ | — | — | ✅ | — |
| CatalogElement | ✅ | — | ✅ | ✅ | ❓ |
| Product | ✅ | — | ❓ | ❓ | — |
| Currency | ✅ | — | — | ✅ | — |
| Role | ✅ | — | — | — | ❓ |
| Webhook | — | — | — | ✅ | — |
| Widget | ✅ | — | — | — | — |
| Source | — | — | — | ✅ | — |
| WebsiteButton | — | — | — | — | — |
| Call | — | — | — | — | — |
| Talk | ❓ | — | — | — | — |
| Chat/Template | ✅ | — | — | — | — |

> ❓-ячейки (`with`, часть `filter`) уточняются по живой документации amoCRM при
> реализации соответствующей группы Фазы 4 — не угадывать (правило проекта).

---

## Привязка к плану

- **Q-GAP1 / Q-GAP2 / Q-GAP3** — аддитивные (не ломающие), выполнить в **Фазе 2**
  (при унификации запросов) либо в **Фазе 4** для соответствующих сущностей.
- **Q-STYLE** — **Фаза 2** (унификация `with` на `HasWithQuery`).
- **Карта новых сущностей** — ориентир для **Фазы 4** (каждой группе — свой набор
  трейтов из таблицы выше).

Источники: `vendor/amocrm/amocrm-api-library/src/AmoCRM/{Filters/*,EntitiesServices/*}`
(`HasPageMethodsInterface`, `OrderTrait`, `filter['query']`),
[amoCRM v4 — API сделок](https://www.amocrm.ru/developers/content/crm_platform/leads-api).
