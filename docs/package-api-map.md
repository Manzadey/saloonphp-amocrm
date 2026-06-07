# Карта реализованного API пакета

> Карта запросов (Requests), их параметров, ответов (Responses) и параметров
> ответов, реализованных в пакете `manzadey/saloonphp-amocrm`.
>
> Документ описывает **что покрыто кодом пакета** (в отличие от
> [amocrm-api-abilities.md](amocrm-api-abilities.md), где описано всё API amoCRM).

## Архитектура

```
Client
├── connector()  → MainConnector   → https://{domain}/api/v4   (Bearer-токен, AlwaysThrowOnErrors)
└── oAuth2()     → OAuth2Connector  → https://{domain}/oauth2   (JSON body, client credentials)
```

`Client` — точка входа. Авторизация (`authorize()` / `refreshAccessToken()`)
вызывается ленивым `getAuth()` при первом запросе и при истечении токена.

Точки входа: `account()`, `leads()`, `contacts()`, `tasks()`, `users()`,
`oAuth2()`.

---

## 1. OAuth2 — `$client->oAuth2()`

| Request | Method | Endpoint | Параметры тела | Response |
|---|---|---|---|---|
| `AccessToken` | POST | `/oauth2/access_token` | `client_id`, `client_secret`, `redirect_uri` (из коннектора) + `grant_type`, `code`/`refresh_token` | `Saloon\Response` |

Хелперы запроса: `exchangeAuthCode(code)` → `grant_type=authorization_code` + `code`;
`refreshAccessToken(token)` → `grant_type=refresh_token` + `refresh_token`.
Сеттеры: `setAuthCode`, `setRefreshToken`, `setGrandType(GrandTypeEnum)`.

---

## 2. Account — `$client->account(?array $with)`

| Request | Method | Endpoint | Query | Response |
|---|---|---|---|---|
| `AccountRequest` | GET | `/account` | `with` (csv) | `AccountResponse` |

**Параметры `with`** (`AccountWithQueryEnum`): `amojo_id`, `amojo_rights`,
`users_groups`, `task_types`, `version`, `entity_names`, `datetime_settings`,
`drive_url`, `is_api_filter_enabled`, `invoices_settings`.
Методы: `with(enum|string)`, `withAll()`.

**`AccountResponse`:** `getId`, `getName`, `getSubdomain`, `getCreatedAt`,
`getCreatedBy`, `getUpdatedAt`, `getUpdatedBy`, `getCurrentUserId`,
`getCountry`, `getCurrency`, `getCurrencySymbol`.

---

## 3. Leads — `$client->leads()`

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list()` | `LeadListRequest` | GET | `/leads` | order, search, with, filter, page, limit | `LeadListResponse` |
| `search($q)` | `LeadListRequest` | GET | `/leads` | `query=$q` | `LeadListResponse` |
| `item($id)` | `LeadItemRequest` | GET | `/leads/{id}` | with, `limit=1` | `LeadItemResponse` |
| `create(?LeadModel)` | `LeadCreateRequest` | POST | `/leads` | body: массив сделок (`add()`) | `LeadAddResponse` |
| `update()` | `LeadUpdateRequest` | PATCH | `/leads` | body: `addLead()`/`addLeads()` | `LeadUpdateResponse` (пустой) |
| `customFields()` | `LeadCustomFieldsListRequest` | GET | `/leads/custom_fields` | order, filter, page, limit | `LeadCustomFieldsListResponse` |
| `notes()` | → `NoteReferences('leads')` | | | | см. §6 |
| `tags()` | → `TagReference('leads')` | | | | см. §8 |

**`with` сделки** (`HasLeadWithQuery`): `withCatalogElements`,
`withIsPriceModifiedByRobot`, `withLossReason`, `withContacts`,
`withOnlyDeleted`, `withSourceId`.

**`LeadFilter`** (+ общие из `AbstractFilter`, см. §10): `price(from,to)`,
`statuses(array)`, `addStatus(id, pipelineId)`, `pipelineId(id)`,
`closedAt(from,to)`.

**`LeadModel` (тело):** `setId`, `setName`/`removeName`, `setPrice`/`removePrice`,
`setPipelineId`/`remove`, `setResponsibleUserId`/`remove`, `setStatusId`,
`setRequestId`. Read: `id`, `link`, `groupId`, `lossReasonId`, `createdBy/At`,
`updatedBy/At`, `closedAt`, `closedTaskAt`, `isDeleted`, `score`, `accountId`.
Трейты: `HasContacts`, `HasTags`, `HasCustomFieldsValues`.

**Responses:** `LeadListResponse` → `leads()`, `lead()`, `isEmpty/isNotEmpty()`
+ `page()`, `*PageUrl()`. `LeadAddResponse` → `leads()`, `lead()`.
`LeadItemResponse` → `lead()`. `LeadCustomFieldsListResponse` → `fields()`.

---

## 4. Contacts — `$client->contacts()`

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list()` | `ContactListRequest` | GET | `/contacts` | with, page, limit, search, filter, order | `ContactListResponse` |
| `search($q)` | `ContactListRequest` | GET | `/contacts` | `query=$q` | `ContactListResponse` |
| `create(?ContactModel)` | `ContactCreateRequest` | POST | `/contacts` | body: `add()` → `save()` | `ContactCreateResponse` |
| `customFields()` | `ContactCustomFieldsListRequest` | GET | `/contacts/custom_fields` | — | `Saloon\Response` |

**`with` контакта** (`HasContactWithQuery`): `withCatalogElements`, `withLeads`,
`withCustomers`.

**`ContactModel` (тело):** `setId`, `setName`, `setFirstName`, `setLastName`,
`setResponsibleUserId`, `setGroupId`, `setCreatedBy/By`,
`setCreatedAt/UpdatedAt`, `setClosestTaskAt`, `setIsDeleted`, `setIsMain`,
`setAccountId`. Read: `isUnsorted`, `isMain`, `link`.

**Responses:** `ContactListResponse` → `contacts()`, `isEmpty/isNotEmpty()`,
page/links. `ContactCreateResponse` → `contacts()`, `contactsIds()`.

---

## 5. Tasks — `$client->tasks(?string $entityType)`

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list()` | `TaskListRequest` | GET | `/tasks` | order, filter, page, limit (+ авто `filter[entity_type]`) | `TaskListResponse` |
| `item($id)` | `TaskItemRequest` | GET | `/tasks/{id}` | — | `TaskItemResponse` |
| `create(?TaskModel)` | `TaskCreateRequest` | POST | `/tasks` | body: `add()` | `TaskCreateResponse` (пустой) |

**`TaskFilter`** (+ общие): `isCompleted(bool)`, `taskType(int|TaskTypeEnum|array)`,
`entityType(string)`, `entityId(id)`. `TaskTypeEnum`: `CALL=1`, `MEETING=2`.

**`TaskModel` (тело):** `setText`, `setCompleteTill`,
`setTaskTypeId`/`typeCall()`/`typeMeeting()`,
`setEntity(TaskContract)`/`setEntityId`/`setEntityType`, `setResponsibleUserId`,
`setIsCompleted`, `setDuration`, `setResult(array)`, `setGroupId`,
`setCreated/UpdatedBy/At`, `setAccountId`.

**Responses:** `TaskListResponse` → `tasks()`, `task()`, page/links.
`TaskItemResponse` → `task()`.

---

## 6. Notes — `$client->leads()->notes()`

`entityType` фиксируется ссылкой `NoteReferences`. Доступно только из `leads()`.

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list(?entityId)` | `NoteListRequest` | GET | `/{entity}[/{entityId}]/notes` | page, limit, filter | `NoteListResponse` |
| `item($id,?entityId)` | `NoteItemRequest` | GET | `/{entity}[/{entityId}]/notes/{id}` | — | `NoteItemResponse` |
| `create(?entityId)` | `NotesCreateRequest` | POST | `/{entity}[/{entityId}]/notes` | body: `add()`/`addCommonNote()` | `NoteCreateResponse` |

**Фильтры списка:** `filterId(id)`, `filterEntityId(array)`,
`filterNoteType(NoteTypeEnum)`.

**`NoteTypeEnum`:** `common`, `call_in`, `call_out`, `service_message`,
`message_cashier`, `geolocation`, `sms_in`, `sms_out`,
`extended_service_message`, `attachment`.

**`NoteModel` (тело):** `setEntityId`, `setNoteType(enum)`, `setParams(array)`,
`setResponsibleUserId`, `setCreatedBy`, `setRequestId`,
`setIsNeedToTriggerDigitalPipeline`, `commonNote(text)`,
`callIn(CallInNoteModel)`.

**Responses:** `notes()`, `note()`.

---

## 7. Users — `$client->users()`

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list()` | `UserListRequest` | GET | `/users` | — | `Saloon\Response` |
| `item($id)` | `UserItemRequest` | GET | `/users/{id}` | `with` (csv через `with(array)`) | `UserItemResponse` |

**`UserModel`:** `setId`, `setName`, `setEmail`, `setLang`, `setRights(array)`.
Response: `user()`.

---

## 8. Tags — `$client->leads()->tags()`

`entityType` из ссылки `TagReference`. Доступно только из `leads()`.

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list()` | `TagListRequest` | GET | `/{entity}/tags` | page, limit, filter, search; `filterName()`, `filterId()` | `Saloon\Response` |
| `create(?tag)` | `TagCreateRequest` | POST | `/{entity}/tags` | body: `tag(TagModel)` | `Saloon\Response` |
| `update(?TagsContract)` / `updateLead(LeadModel)` | `TagAttachRequest` | PATCH | `/{entity}` | body: `model()` (привязка тегов к сущности) | `Saloon\Response` |

**`TagModel`:** `setId`, `setName`, `setColor(TagColorEnum|string)`.
`TagColorEnum` — 23 предопределённых HEX-цвета.

**Трейт `HasTags`** (на моделях сущностей): `tags()`, `setTags()`, `addTag()`,
`clearTags()`, `tagsToAdd()`, `appendToTagsToAdd()`.

---

## 9. CustomFields (универсальный) — `CustomFieldListRequest`

| Request | Method | Endpoint | Параметры |
|---|---|---|---|
| `CustomFieldListRequest` | GET | `/{entity}/custom_fields` | page, limit, filter, order |

**`CustomFieldFilter`:** `type(CustomFieldTypeEnum|string)`.
`CustomFieldTypeEnum` — 24 типа (`text`, `numeric`, `select`, `multiselect`,
`date`, `url`, `price`, `monetary`, `file`, `linked_entity`, `chained_list` и др.).

**`CustomFieldModel`:** `setId`, `setCode`, `setValues(array)`, `addValue()`.
Read: `name`, `type`, `accountId`, `sort`, `isApiOnly`, `link`.

**Трейт `HasCustomFieldsValues`:** `customFieldsValues()`,
`setCustomFieldsValues()`, `addCustomFieldsValue()`.

---

## 10. Общие query-трейты, фильтры, enum

**Query-трейты** (`src/Query`):

- `HasPageQuery` → `page(int)`
- `HasLimitQuery` → `limit(int)`
- `HasSearchQuery` → `querySearch(string|int)`
- `HasFilterQuery` → `filter(key, value)`
- `HasOrderQuery` → `order(field, dir)`, `newest()`, `latest()`, `removeOrder()`
- `HasWithQuery` → `with(array)`, `addWith(string)`

**`AbstractFilter`** (база всех фильтров): `range(name,from,to)`,
`customFieldsValues(array)`, `id()`, `name()`, `createdBy()`, `updatedBy()`,
`responsibleUserId()`, `createdAt(from,to)`, `updatedAt(from,to)`,
`closestTaskAt(from,to)`.

**Enums:** `QueryOrderEnum` (`asc`/`desc`); `QueryOrderFieldEnum`
(`id`, `created_at`, `updated_at`, `sort`); `GrandTypeEnum`
(`authorization_code`/`refresh_token`).

**Response-трейты:** `HasPageResponse` → `page()`; `HasLinksResponse` →
`links()`, `selfPageUrl()`, `nextPageUrl()`, `firstPageUrl()`, `prefPageUrl()`.

---

## Сводка покрытия API

| Сущность | List | Item | Create | Update | CustomFields | Notes | Tags |
|---|:-:|:-:|:-:|:-:|:-:|:-:|:-:|
| Leads | ✅ | ✅ | ✅ | ✅ (PATCH) | ✅ | ✅ | ✅ |
| Contacts | ✅ | ❌ | ✅ | ❌ | ✅ | ⚠️¹ | ⚠️¹ |
| Tasks | ✅ | ✅ | ✅ | ❌ | — | — | — |
| Notes | ✅ | ✅ | ✅ | ❌ | — | — | — |
| Users | ✅ | ✅ | ❌ | ❌ | — | — | — |
| Account | — | ✅ | — | — | — | — | — |
| Tags | ✅ | ❌ | ✅ | ✅ (attach) | — | — | — |

¹ `NoteReferences`/`TagReference` универсальны по `entityType`, но в
`ContactReference` не проброшены — доступны только через `LeadReference`.

**Заметные пробелы:** нет item/update у Contacts, нет update у Tasks/Notes,
нет delete нигде, нет companies/pipelines/catalogs/events. Все
«нетипизированные» запросы (Contacts customFields, Users list, все
Tag-запросы) возвращают сырой `Saloon\Response` без модели-обёртки.
