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
`pipelines()`, `webhooks()`, `oAuth2()`.

---

## 1. OAuth2 — `$client->oAuth2()`

| Request | Method | Endpoint | Параметры тела | Response |
|---|---|---|---|---|
| `AccessToken` | POST | `/oauth2/access_token` | `client_id`, `client_secret`, `redirect_uri` (из коннектора) + `grant_type`, `code`/`refresh_token` | `Saloon\Response` |

Хелперы запроса: `exchangeAuthCode(code)` → `grant_type=authorization_code` + `code`;
`refreshAccessToken(token)` → `grant_type=refresh_token` + `refresh_token`.
Сеттеры: `setAuthCode`, `setRefreshToken`, `setGrantType(GrantTypeEnum)`.

---

## 2. Account — `$client->account(?array $with)`

| Request | Method | Endpoint | Query | Response |
|---|---|---|---|---|
| `AccountRequest` | GET | `/account` | `with` (csv) | `AccountResponse` |

**Параметры `with`** (`AccountWith`): `amojo_id`, `amojo_rights`,
`users_groups`, `task_types`, `version`, `entity_names`, `datetime_settings`,
`drive_url`, `is_api_filter_enabled`, `invoices_settings`.
Методы: `with(list<AccountWith>)`, `addWith(AccountWith)`, `withAll()`.

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
| `create(LeadModel)` | `LeadCreateRequest` | POST | `/leads` | body: массив сделок (`add()`) | `LeadAddResponse` |
| `update()` | `LeadUpdateRequest` | PATCH | `/leads` | body: `add()`/`addMany()` | `LeadUpdateResponse` (пустой) |
| `customFields()` | `LeadCustomFieldsListRequest` | GET | `/leads/custom_fields` | order, filter, page, limit | `CustomFieldsListResponse` |
| `notes()` | → `NoteReferences('leads')` | | | | см. §6 |
| `tags()` | → `TagReference('leads')` | | | | см. §8 |

**`with` сделки** (`HasLeadWithQuery` → `LeadWith`): `withCatalogElements`,
`withIsPriceModifiedByRobot`, `withLossReason`, `withContacts`,
`withOnlyDeleted`, `withSourceId`, `withSource`.

**Сортировка** (`LeadOrderField`): `created_at`, `updated_at`, `id`.
**Фильтр:** `addFilter(LeadFilter)`.

**`LeadFilter`** (+ общие из `AbstractFilter`, см. §10): `price(from,to)`,
`statuses(array)`, `addStatus(id, pipelineId)`, `pipelineId(id)`,
`closedAt(from,to)`.

**`LeadModel` (тело):** `setId`, `setName`/`removeName`, `setPrice`/`removePrice`,
`setPipelineId`/`remove`, `setResponsibleUserId`/`remove`, `setStatusId`,
`setRequestId`. Read: `id`, `link`, `groupId`, `lossReasonId`, `createdBy/At`,
`updatedBy/At`, `closedAt`, `closedTaskAt`, `isDeleted`, `score`, `accountId`.
Трейты: `HasContacts`, `HasTags`, `HasCustomFieldsValues`.

**Responses:** `LeadListResponse` → `leads(): ModelCollection<LeadModel>`
+ `page()`, `*PageUrl()`. `LeadAddResponse` → `leads()`.
`LeadItemResponse` → `lead(): ?LeadModel`. `CustomFieldsListResponse` → `fields()`.

---

## 4. Contacts — `$client->contacts()`

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list()` | `ContactListRequest` | GET | `/contacts` | with, page, limit, search, filter, order | `ContactListResponse` |
| `search($q)` | `ContactListRequest` | GET | `/contacts` | `query=$q` | `ContactListResponse` |
| `create(ContactModel)` | `ContactCreateRequest` | POST | `/contacts` | body: `add()` | `ContactCreateResponse` |
| `customFields()` | `ContactCustomFieldsListRequest` | GET | `/contacts/custom_fields` | order, filter, page, limit | `CustomFieldsListResponse` |

**`with` контакта** (`HasContactWithQuery` → `ContactWith`): `withCatalogElements`,
`withLeads`, `withCustomers`.

**Сортировка** (`ContactOrderField`): `updated_at`, `id` — `created_at` amoCRM у
контактов не принимает. **Фильтр:** `addFilter(ContactFilter)`.

**`ContactModel` (тело):** `setId`, `setName`, `setFirstName`, `setLastName`,
`setResponsibleUserId`, `setGroupId`, `setCreatedBy/By`,
`setCreatedAt/UpdatedAt`, `setClosestTaskAt`, `setIsDeleted`, `setIsMain`,
`setAccountId`. Read: `isUnsorted`, `isMain`, `link`.

**Responses:** `ContactListResponse` → `contacts(): ModelCollection<ContactModel>`,
page/links. `ContactCreateResponse` → `contacts()`, `contactsIds(): list<int>`.
`CustomFieldsListResponse` → `fields()`.

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

**Responses:** `TaskListResponse` → `tasks(): ModelCollection<TaskModel>`, page/links.
`TaskItemResponse` → `task(): ?TaskModel`.
`TaskItemResponse` → `task()`.

---

## 6. Notes — `$client->leads()->notes()`

`entityType` фиксируется ссылкой `NoteReferences`. Доступно только из `leads()`.

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list(?entityId)` | `NoteListRequest` | GET | `/{entity}[/{entityId}]/notes` | page, limit, filter, order, with | `NoteListResponse` |
| `item($id,?entityId)` | `NoteItemRequest` | GET | `/{entity}[/{entityId}]/notes/{id}` | — | `NoteItemResponse` |
| `create(?entityId)` | `NotesCreateRequest` | POST | `/{entity}[/{entityId}]/notes` | body: `add()`/`addCommonNote()` | `NoteCreateResponse` |

**Фильтр:** `addFilter(NoteFilter)` — `id()`, `entityId()`, `noteType()`,
`updatedAt()`. **Сортировка** (`NoteOrderField`): `updated_at`, `id`.
**`with`** (`HasNoteWithQuery` → `NoteWith`): `withIsPinned`.

**`NoteTypeEnum`:** `common`, `call_in`, `call_out`, `service_message`,
`message_cashier`, `geolocation`, `sms_in`, `sms_out`,
`extended_service_message`, `attachment`.

**`NoteModel` (тело):** `setEntityId`, `setNoteType(enum)`, `setParams(array)`,
`setResponsibleUserId`, `setCreatedBy`, `setRequestId`,
`setIsNeedToTriggerDigitalPipeline`, `commonNote(text)`,
`callIn(CallInNoteModel)`.

**Responses:** `NoteListResponse` / `NoteCreateResponse` → `notes(): ModelCollection<NoteModel>`;
`NoteItemResponse` → `note(): ?NoteModel`.

---

## 7. Users — `$client->users()`

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list()` | `UserListRequest` | GET | `/users` | page, limit, with | `UserListResponse` |
| `item($id)` | `UserItemRequest` | GET | `/users/{id}` | with | `UserItemResponse` |

**`with` пользователя** (`HasUserWithQuery`): `withRole`, `withGroup`, `withUuid`,
`withAmojoId`, `withUserRank`, `withPhoneNumber` — поверх общего `HasWithQuery`
(`with(array)` / `addWith(string)`, склейка в csv).

**`UserModel`:** `setId`, `setName`, `setEmail`, `setLang`, `setRights(array)`.
Read: `name` (строка). Responses: `UserListResponse` → `users(): ModelCollection<UserModel>`,
page/links. `UserItemResponse` → `user(): ?UserModel`.

---

## 8. Tags — `$client->leads()->tags()`

`entityType` из ссылки `TagReference`. Доступно только из `leads()`.

| Ref-метод | Request | Method | Endpoint | Параметры | Response |
|---|---|---|---|---|---|
| `list()` | `TagListRequest` | GET | `/{entity}/tags` | page, limit, search; `addFilter(TagFilter)` | `TagListResponse` |
| `create($tag)` | `TagCreateRequest` | POST | `/{entity}/tags` | body: `add(TagModel)` | `TagCreateResponse` |
| `update(TagsContract)` | `TagAttachRequest` | PATCH | `/{entity}` | body: `add()` (привязка тегов к сущности) | `Saloon\Response` |

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
Read: `name`, `type`, `accountId`, `sort`, `isApiOnly`, `link`. `id`/`name`/
`code`/`type` читают и формат `custom_fields_values` (`field_id`/`field_name`/
`field_code`/`field_type`), и формат справочника `/{entity}/custom_fields`
(`id`/`name`/`code`/`type`) — второй в приоритете, если оба ключа отсутствуют
в первом.

**Трейт `HasCustomFieldsValues`:** `customFieldsValues()`,
`setCustomFieldsValues()`, `addCustomFieldsValue()`.

---

## 10. Pipelines — `$client->pipelines()`

| Ref-метод | Request | Method | Endpoint | Response |
|---|---|---|---|---|
| `list()` | `PipelineListRequest` | GET | `/leads/pipelines` | `PipelineListResponse` |

Пустой аккаунт отдаёт **204 без тела** — `ModelCollection::of()` превращает такое
тело в пустую коллекцию.

**`PipelineModel`:** `id`, `name`, `sort`, `isMain`, `isUnsortedOn`,
`isArchive`, `accountId`, `statuses()` → `list<PipelineStatusModel>` из
`_embedded.statuses`.

**`PipelineStatusModel`** (`Modules\Pipeline\Status`): `id`, `name`, `sort`,
`isEditable`, `pipelineId`, `color`, `type`, `accountId`.

**Response:** `PipelineListResponse` → `pipelines(): ModelCollection<PipelineModel>`.

---

## 11. Webhooks — `$client->webhooks()`

| Ref-метод | Request | Method | Endpoint | Тело | Response |
|---|---|---|---|---|---|
| `list()` | `WebhookListRequest` | GET | `/webhooks` | — | `WebhookListResponse` |
| `subscribe($destination, $settings)` | `WebhookSubscribeRequest` | POST | `/webhooks` | `destination`, `settings` | `WebhookResponse` |
| `unsubscribe($destination)` | `WebhookUnsubscribeRequest` | DELETE | `/webhooks` | `destination` | `WebhookUnsubscribeResponse` (пустой) |

Подписка на уже существующий `destination` **обновляет** хук, а не дублирует
его. Аккаунт без подписок отдаёт **204 без тела** — как и у pipelines, лишней
обработки не требуется.

**`WebhookModel`:** `id`, `accountId`, `destination`/`setDestination`,
`settings`/`setSettings(array)`, `sort`, `createdBy`, `createdAt`, `updatedAt`,
`isDisabled` (`true`, когда amoCRM сам отключил хук после серии неудачных
доставок).

**Responses:** `WebhookListResponse` → `webhooks(): ModelCollection<WebhookModel>`;
`WebhookResponse` → `webhook(): ?WebhookModel`.
`WebhookResponse` → `webhook()`.

---

## 12. Общие query-трейты, фильтры, enum

**Query-трейты** (`src/Query`):

- `HasPageQuery` → `page(int)`
- `HasLimitQuery` → `limit(int)`
- `HasSearchQuery` → `querySearch(string|int)`
- `HasFilterQuery<TFilter>` → `addFilter(TFilter)`; `filter()` — `protected`
- `HasOrderQuery<TField>` → `order(TField, QueryOrderEnum)`, `removeOrder()`
- `HasWithQuery<TWith>` → `with(list<TWith>)`, `addWith(TWith)`

`latest()` / `oldest()` — в пер-сущностных обёртках (`HasLeadOrderQuery` и т.п.):
их дефолт `::ID` конкретного енама generic-параметром невыразим.

**`AbstractFilter`** — только то, что принимает каждая сущность с фильтрами:
`range(name,from,to)`, `id()`, `responsibleUserId()`, `updatedAt(from,to)`.
Общее для сделок и контактов — трейт `HasCommonEntityFilters`: `name()`,
`createdBy()`, `updatedBy()`, `createdAt()`, `closestTaskAt()`,
`customFieldsValues()`. `NoteFilter` и `TagFilter` наследуют `ArrayStore` напрямую.

**Enums:** `QueryOrderEnum` (`asc`/`desc`); поля сортировки — по сущности
(`LeadOrderField`, `ContactOrderField`, `TaskOrderField`, `NoteOrderField`,
`CustomFieldOrderField`); `GrantTypeEnum`
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
| Pipelines | ✅ | — | — | — | — | — | — |
| Webhooks | ✅ | — | ✅ (subscribe) | — | — | — | — |

¹ `NoteReferences`/`TagReference` универсальны по `entityType`, но в
`ContactReference` не проброшены — доступны только через `LeadReference`.

**Заметные пробелы:** нет item/update у Contacts, нет update у Tasks/Notes,
нет delete нигде, нет companies/catalogs/events. Из «нетипизированных» остался
только `TagAttachRequest`: PATCH `/{entity}` отдаёт `_embedded.{сущность}`, форма
зависит от типа сущности — типизируется вместе с её update-запросом.
