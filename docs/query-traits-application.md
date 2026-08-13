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
| `HasFilterQuery<TFilter>` | `addFilter(TFilter)`; `filter()` — `protected` | `filter[...]` |
| `HasOrderQuery<TField>` | `order(TField, QueryOrderEnum)`, `removeOrder()` | `order[...]` |
| `HasWithQuery<TWith>` | `with(list<TWith>)`, `addWith(TWith)` | `with` |

Три трейта generic по своему типу. Пер-сущностные обёртки поверх них дают
типизированные ярлыки и дефолты, которые generic-параметром не выразить:

| Сущность | order-трейт (+ `latest()`/`oldest()`) | with-трейт | Фильтр |
|---|---|---|---|
| Сделки | `HasLeadOrderQuery` → `LeadOrderField` | `HasLeadWithQuery` → `LeadWith` | `LeadFilter` |
| Контакты | `HasContactOrderQuery` → `ContactOrderField` | `HasContactWithQuery` → `ContactWith` | `ContactFilter` |
| Задачи | `HasTaskOrderQuery` → `TaskOrderField` | — | `TaskFilter` |
| Примечания | `HasNoteOrderQuery` → `NoteOrderField` | `HasNoteWithQuery` → `NoteWith` | `NoteFilter` |
| Кастом-поля | `HasCustomFieldOrderQuery` → `CustomFieldOrderField` | — | `CustomFieldFilter` |
| Теги | — (order не поддерживается) | — | `TagFilter` |
| Пользователи | — | `HasUserWithQuery` → `UserWith` | — |
| Аккаунт | — | `HasAccountWithQuery` → `AccountWith` (+ `withAll()`) | — |

> Связанные находки аудита: L6 (`filter` аккумулирует скаляры), L7 (`newest()` был
> инвертирован — удалён в пользу `latest()`/`oldest()`), L8 (`SORT` невалиден у сделок
> и задач, но валиден у кастом-полей — см. регрессию `v0.8.0`), `with` (5 реализаций).
> Все закрыты. См. [audit.md](audit.md).

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
| `ContactCustomFieldsListRequest` | ✅ | ✅ | — | ✅ | ✅ | — | трейты на месте; остаётся дубль `CustomFieldListRequest` |
| `TaskListRequest` | ✅ | ✅ | — | ✅ | ✅ | — | корректно (query у задач нет — верно) |
| `NoteListRequest` | ✅ | ✅ | — | ✅ | ✅ | ✅ | корректно (order и `with=is_pinned` добавлены в Фазе 2) |
| `TagListRequest` | ✅ | ✅ | ✅ | ✅ | — | — | корректно (Tags поддерживают query — проверено) |
| `CustomFieldListRequest` | ✅ | ✅ | — | ✅ | ✅ | — | корректно |
| `UserListRequest` | ✅ | ✅ | — | — | — | ✅ | корректно |
| `UserItemRequest` | — | — | — | — | — | ✅ | корректно |
| `AccountRequest` | — | — | — | — | — | ✅ | корректно (переведён на трейт в Фазе 2) |

### Находки

- ~~**Q-GAP1 — `NoteListRequest`**: Notes поддерживают `order`, но трейт не подключён.~~ Закрыто в Фазе 2: подключён `HasNoteOrderQuery` (`updated_at`, `id` — сверено с докой) и `with=is_pinned`, которого в пакете не было вовсе.
- ~~**Q-GAP2 — `UserListRequest`**: Users поддерживают пагинацию и `with` (роли/группы), но запрос без трейтов вовсе. Добавить `HasPageQuery`, `HasLimitQuery`, `with`.~~ Закрыто: подключены `HasPageQuery`, `HasLimitQuery`, `HasUserWithQuery`.
- **Q-GAP3 — `ContactCustomFieldsListRequest`**: ~~custom_fields поддерживают page/limit/order/filter, но запрос «голый»~~ — закрыто. Ответы Lead/Contact сведены к общему `CustomField\Responses\CustomFieldsListResponse`; сам запрос всё ещё дублирует обобщённый `CustomFieldListRequest` — унифицировать в Фазе 4c.
- ~~**Q-STYLE — `AccountRequest`**: `with` не через `HasWithQuery` — своя сигнатура на одно значение.~~ Закрыто в Фазе 2: единственная механика — `HasWithQuery`, значения — енамы `WithField`. `withAll()` остался в `HasAccountWithQuery`: перебор `cases()` требует знания конкретного енама.
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

- ~~**Q-GAP1 / Q-GAP2 / Q-GAP3**~~ — Q-GAP1 и Q-GAP2 закрыты; от Q-GAP3 остался только
  дубль `ContactCustomFieldsListRequest` / `CustomFieldListRequest` — **Фаза 4c**.
- ~~**Q-STYLE**~~ — закрыто в Фазе 2.
- **Наборы `order` и `with` — пер-сущностные**, и это не косметика: до Фазы 2 общий
  енам пропускал сортировку задач по `updated_at`. Для новой сущности набор берётся
  из её страницы доки, а не с соседней (урок регрессии `v0.8.0`).
- **Карта новых сущностей** — ориентир для **Фазы 4** (каждой группе — свой набор
  трейтов из таблицы выше).

Источники: `vendor/amocrm/amocrm-api-library/src/AmoCRM/{Filters/*,EntitiesServices/*}`
(`HasPageMethodsInterface`, `OrderTrait`, `filter['query']`),
[amoCRM v4 — API сделок](https://www.amocrm.ru/developers/content/crm_platform/leads-api).
