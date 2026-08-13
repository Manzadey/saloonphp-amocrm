# Changelog

All notable changes to this package are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
Releases before `v0.7.0` predate this file — see the git history and the GitHub
releases page for those.

## [Unreleased]

Phase 2 of the road to 1.0: the public API is unified and typed. Magic strings and
the union enums they leaned on are gone — every `with`, `order` and `filter` value
is now an enum or filter object belonging to a single entity.

The sets were checked against the amoCRM docs **page by entity**, not by
generalising one page. That is how three of the changes below were found, and it is
the same mistake that produced the 0.8.1 regression.

### Added

- Per-entity sort fields: `LeadOrderField`, `ContactOrderField`, `TaskOrderField`,
  `NoteOrderField`, `CustomFieldOrderField`.
- Per-entity `with` values: `LeadWith`, `ContactWith`, `UserWith`, `NoteWith`,
  `AccountWith`.
- Filter objects: `ContactFilter`, `NoteFilter`, `TagFilter`. `addFilter()` now
  exists on every list request.
- `LeadWith::SOURCE` — the leads docs list both `source_id` and `source`; only the
  first was available.
- `NoteListRequest` gained sorting (`updated_at`, `id`) and `with=is_pinned`,
  neither of which the package supported.
- `CustomFieldListRequest::send()` — it was the only list request you had to send
  through the connector by hand.

### Removed

- **`Enum\QueryOrderFieldEnum`** — split per entity. A single enum could not reject
  sorting tasks by `updated_at` or leads by `sort`; the API rejected them at runtime.
- **`ContactCreateRequest::save()`**, **`LeadUpdateRequest::addLead()`/`addLeads()`**,
  **`TagCreateRequest::tag()`**, **`TagAttachRequest::model()`**,
  **`TagReference::updateLead()`** — one action, one name.
- **`NoteListRequest::filterId()`/`filterEntityId()`/`filterNoteType()`** and
  **`TagListRequest::filterName()`/`filterId()`** — replaced by filter objects.

### Changed

- **`filter(string $key, $value)` is no longer public.** It could build a key the
  entity does not accept, which amoCRM silently ignores while returning the whole
  list. Use `addFilter()` with the filter object of that entity.
- **`with()` takes a list of enums**, `addWith()` a single enum. `AccountRequest`
  lost its own `with()`/`withAll()` and goes through the shared trait;
  `AccountWithQueryEnum` is now `AccountWith`.
- **Sorting contacts by `created_at` is no longer expressible** — amoCRM never
  supported it. Contacts accept `updated_at` and `id`.
- **Required parameters are `readonly`** and `create()` requires its model:
  `LeadReference`, `ContactReference`, `TaskReference`, `TagReference::create()` and
  `TagReference::update()`. Calling them without a model used to produce a request
  with an empty body that amoCRM rejects.
- `Enum\GrandTypeEnum` → `Enum\GrantTypeEnum`, `AccessToken::setGrandType()` →
  `setGrantType()` (typo; the `grant_type` values are unchanged).
- The six filter keys shared by leads and contacts moved from `LeadFilter` into the
  `HasCommonEntityFilters` trait. Signatures unchanged.
- `HasContactWithQuery` moved to `Modules\Contact\Requests\Traits`.

### Upgrading

| If you called | Do this instead |
|---|---|
| `filter('name', 'X')` on a list request | `addFilter(LeadFilter::make()->name('X'))` — filter object per entity |
| `with(['contacts'])` | `with([LeadWith::CONTACTS])` |
| `AccountRequest::with('version')` | `addWith(AccountWith::VERSION)` |
| `latest(QueryOrderFieldEnum::CREATED_AT)` | `latest(LeadOrderField::CREATED_AT)` — the enum of that entity |
| `ContactCreateRequest::save()` | `send()` |
| `LeadUpdateRequest::addLead()` / `addLeads()` | `add()` / `addMany()` |
| `TagCreateRequest::tag()`, `TagAttachRequest::model()` | `add()` |
| `TagReference::updateLead($model)` | `update($model)` |
| `NoteListRequest::filterNoteType($t)` | `addFilter(NoteFilter::make()->noteType($t))` |
| `TagListRequest::filterName('X')` | `addFilter(TagFilter::make()->name('X'))` |
| `leads()->create()` then `add($model)` | `leads()->create($model)` — the model is required |
| `GrandTypeEnum`, `setGrandType()` | `GrantTypeEnum`, `setGrantType()` |

Sort fields and `with` values differ per entity, and the types now enforce it:
`TaskListRequest::latest(LeadOrderField::UPDATED_AT)` fails to type-check instead of
being rejected by the API at runtime.

## [0.8.1] - 2026-08-13

### Fixed

- **`QueryOrderFieldEnum::SORT` is back.** Removing it in 0.8.0 was right for leads
  and tasks but wrong for custom fields, where `sort` is a documented sort field
  (`/api/v4/leads/custom_fields?order[sort]=asc`). Its removal left
  `LeadCustomFieldsListRequest`, `ContactCustomFieldsListRequest` and
  `CustomFieldListRequest` with no meaningful field to sort by. Sorting leads or
  tasks by `SORT` is still rejected by the API — the enum is a union across
  entities, not a per-entity set.

## [0.8.0] - 2026-08-13

Phase 1 of the road to 1.0: the logic bugs found by the package audit
(`docs/audit.md`, findings L1–L7 and AF1), each fixed with a regression test.

Several fixes change behaviour that used to be wrong but observable — see
[Upgrading](#upgrading) below before pulling this in.

### Added

- `HasOrderQuery::oldest()` — orders least-recent-first (`ASC`), the counterpart
  to `latest()`.
- `QueryOrderFieldEnum::COMPLETE_TILL` — tasks can be sorted by `complete_till`,
  which the enum was missing.

### Removed

- **`HasOrderQuery::newest()`.** It became identical to `latest()` once its
  direction was corrected (see Fixed → sorting), so the pair was collapsed into
  `latest()` / `oldest()`.
- **`QueryOrderFieldEnum::SORT`.** `sort` is a position property of pipelines,
  statuses and fields — not a list sort key. `order[sort]=…` was never honoured.
- **Six filter methods no longer inherited by `TaskFilter`:** `name()`,
  `createdBy()`, `updatedBy()`, `createdAt()`, `closestTaskAt()` and
  `customFieldsValues()` moved from `AbstractFilter` down to `LeadFilter`. amoCRM
  accepts none of them on tasks, so calling them built a filter the API ignored.
  `AbstractFilter` now carries only what every entity accepts — `id()`,
  `responsibleUserId()`, `updatedAt()` and the `range()` helper.

### Changed

- **`HasFilterQuery::filter()` no longer accumulates repeated keys.** Calling it
  twice with the same key now overwrites instead of merging the values into an
  array. Multi-value filters still work by passing an array as the value.
- **`AbstractFilter::updatedAt()` now emits `filter[updated_at]`** instead of
  `filter[updated]`, so requests sent with this filter start being filtered by
  amoCRM rather than silently returning everything.
- **`Model` defaults no longer override explicit data.** A value passed to the
  constructor now wins over the subclass default for the same key, and stays a
  scalar instead of being merged into an array. Affects `ReferrerFieldModel` and
  `YclIdFieldModel`.
- PHPStan is enforced at level 5 (was level 3) — internal quality gate, no
  runtime effect.

### Fixed

- `HasTags::appendToTagsToAdd()` used the method argument instead of the current
  loop element when normalising `TagModel` instances. A second append overwrote
  every previously queued tag with the last one, and passing an array raised a
  fatal error.
- `HasContacts::setContacts()` re-appended already-stored contacts on every call,
  so a second `addContact()` produced duplicates.
- `NoteItemResponse::note()` is declared `?NoteModel` but built a model even from
  an empty body; it now returns `null`, matching the lead, task and user
  responses.
- `AbstractFilter::range()` dropped zero bounds along with unset ones, so calls
  like `LeadFilter::price(0, 1000)` lost the lower bound. Only `null` bounds are
  dropped now.
- Sorting: `newest()` ordered `ASC` while `latest()` ordered `DESC`, despite both
  names meaning most-recent-first. Most-recent-first is now `latest()` (`DESC`)
  and least-recent-first is `oldest()` (`ASC`).

### Upgrading

| If you called | Do this instead |
|---|---|
| `newest()` expecting newest-first | `latest()` — same result, the method is gone |
| `newest()` and relied on the old `ASC` order | `oldest()` |
| `filter($key, …)` twice to build a multi-value filter | `filter($key, [$a, $b])` — one call, array value |
| `QueryOrderFieldEnum::SORT` | nothing — the field was never honoured; sort by `ID`, `CREATED_AT`, `UPDATED_AT` or `COMPLETE_TILL` |
| `TaskFilter::name()` / `createdBy()` / `updatedBy()` / `createdAt()` / `closestTaskAt()` / `customFieldsValues()` | nothing — amoCRM ignores these on tasks; the calls were silently doing nothing. Still available on `LeadFilter` |

Sort fields are per-entity and the enum is a union of all of them: leads accept
`created_at` / `updated_at` / `id`, tasks accept `created_at` / `complete_till` /
`id`. Sorting tasks by `UPDATED_AT` (or leads by `COMPLETE_TILL`) is rejected by
the API — the enum cannot catch that for you.

Two changes need no code edit but do change the requests you send: `updatedAt()`
starts being honoured by amoCRM (result sets get narrower), and `Model`
subclasses with defaults now respect the values you pass in.

[Unreleased]: https://github.com/Manzadey/saloonphp-amocrm/compare/v0.8.1...HEAD
[0.8.1]: https://github.com/Manzadey/saloonphp-amocrm/compare/v0.8.0...v0.8.1
[0.8.0]: https://github.com/Manzadey/saloonphp-amocrm/compare/v0.7.0...v0.8.0
