# Аудит пакета `manzadey/saloonphp-amocrm`

> Дата: 2026-06-07
> Объём анализа: 95 файлов в `src/`, 5 тестовых файлов (40 тестов).
> Инструменты: PHPStan 2.1, PHP-CS-Fixer 3.95, PHPUnit 10.5 + ручной разбор кода.

## Вердикт

Пакет работоспособен в «счастливом пути», но содержит **падающий тест**, несколько
**реальных багов с порчей данных / тихими ошибками** и **дыры в инфраструктуре
качества** (нет конфигов PHPStan/PHPUnit, нет `autoload-dev`). В текущем виде не
готов к релизу как стабильный.

### Результаты инструментов

| Инструмент | Результат |
|---|---|
| PHPUnit | `Tests: 40, Assertions: 96, Errors: 1` — **1 падающий тест** |
| PHPStan | 12 ошибок @ level 5, 89 @ level 8, 296 @ level max |
| PHP-CS-Fixer | ✅ чисто (нарушений нет) |

---

## 🔴 Блокеры (ломают сборку / CI)

> **✅ Все B1–B3 исправлены в Фазе 0** (ветка `fix/audit-blockers`).

| # | Что | Где | Статус |
|---|---|---|---|
| B1 | **Падает тест** `testRequestId`. `requestId()` объявлен `array\|int\|null`, а `setRequestId(string)` пишет строку → `TypeError` | `src/Modules/Lead/LeadModel.php` | ✅ `requestId(): ?string` |
| B2 | `TagAttachRequest::model()` вызывает `TagsContract::all()`, но интерфейс этот метод не объявляет | `src/Modules/Tag/Requests/TagAttachRequest.php` | ✅ `all()` объявлен в `TagsContract` |
| B3 | `TaskModel::setEntity()` — `match` не покрывает все ветки `class-string<TaskContract>` (нет `default`) → `UnhandledMatchError` для неизвестной сущности | `src/Modules/Task/TaskModel.php` | ✅ `default` → `InvalidArgumentException` + `TaskModelTest` |

---

## 🟠 Логические баги (тихая порча данных / неверное поведение)

> **✅ L1–L7 и AF1 исправлены в Фазе 1** (ветка `fix/logic-bugs`, по TDD, каждый —
> отдельный коммит с регрессионным тестом). **L8** (удаление `QueryOrderFieldEnum::SORT`)
> остаётся на Фазу 2 как ломающее. L2 сверено с офиц. докой amoCRM (`filter[updated_at]`).

| # | Что | Где | Серьёзность |
|---|---|---|---|
| L1 | `appendToTagsToAdd()`: в цикле `$tagsToAdd[$i] = $tag->all()` использует **параметр** `$tag` вместо элемента цикла `$tagToAdd`. При `array`-аргументе → fatal `->all() on array`; при `TagModel` → все теги перезаписываются последним добавленным | `src/Modules/Tag/Requests/HasTags.php:60-72` | high |
| L2 | `updatedAt()` пишет ключ фильтра `'updated'`, тогда как соседи — `created_at`, `closest_task_at`. Несогласованно с остальными; фильтр по обновлению, вероятно, игнорируется amoCRM | `src/Filters/AbstractFilter.php:84` | high ⚠️ |
| L3 | `Model::__construct` использует `array_merge_recursive($data, $defaults)`: при пересечении ключей скаляр превращается в массив (`field_code` → `['X','REFERRER']`), и default перебивает явное значение. Ломает `ReferrerFieldModel` / `YclIdFieldModel` | `src/Modules/Model.php:21` | medium |
| L4 | `HasContacts::setContacts` через `array_merge_recursive` дублирует контакты при повторном `addContact()` (в `HasTags::setTags` сделано правильно — присваиванием) | `src/Modules/Contact/Requests/HasContacts.php:30` | medium |
| L5 | `NoteItemResponse::note()` объявлен `?NoteModel`, но всегда возвращает модель даже из пустого JSON. Остальные Item-ответы (`lead/task/user`) корректно возвращают `null` | `src/Modules/Note/Responses/NoteItemResponse.php:12` | medium |
| L6 | `HasFilterQuery::filter` через `array_merge_recursive`: повторный вызов с тем же скалярным ключом аккумулирует значение в массив. `TaskReference::list()` пресидит `entity_type` — повторный пользовательский фильтр сломает запрос | `src/Query/HasFilterQuery.php:16` | low-med |
| L7 | `newest()` → `ASC`, `latest()` → `DESC`: семантически противоречиво (newest обычно DESC). Тихо возвращает не тот порядок сортировки. **Решено удалением `newest()`**: остались `latest()` (DESC) и новый `oldest()` (ASC) — вместо двух дублирующихся методов пара с явной семантикой | `src/Query/HasOrderQuery.php:25` | low (спорно) |
| L8 | `QueryOrderFieldEnum::SORT = 'sort'` — **не валидное поле сортировки**. Дока amoCRM v4 для `order` перечисляет только `created_at` / `updated_at` / `id`; `sort` — свойство позиции (воронки/этапы/поля), не ключ сортировки списков. `order[sort]=…` будет проигнорирован/ошибка | `src/Enum/QueryOrderFieldEnum.php:15` | medium ✓ |
| AF1 | `AbstractFilter::range()` использует `array_filter(compact('from','to'))` **без колбэка** → отбрасывает не только `null`, но и `0`/любые falsy. Вред: `LeadFilter::price(0, 1000)` теряет границу `from=0` (`['to'=>1000]`); то же для нулевых таймстампов. Нужно `fn($v) => $v !== null` | `src/Filters/AbstractFilter.php:16` | medium |

> ⚠️ L2 требует сверки с актуальной документацией amoCRM по ключу фильтра
> (`updated_at`) перед правкой.
>
> ✓ L8 — сверено с [докой amoCRM v4 (параметр `order`)](https://www.amocrm.ru/developers/content/crm_platform/leads-api)
> и фильтрами SDK (`HasOrderInterface`, `*Filter.php`): поля `created_at` / `updated_at` / `id`,
> направления `asc` / `desc`; `sort` отсутствует.

**Общий корень L3 / L4 / L6** — `array_merge_recursive` применён к ассоциативным
картам, где нужен `array_replace` / прямое присваивание.

---

## 🟡 Инфраструктура качества

> **Фаза 0 закрыла большинство пунктов** (ветка `fix/audit-blockers`).

- ~~**AF3 (косметика):** докблоки `@return $this` при `: static`~~ — ✅ **исправлено**
  (Фаза 0): избыточные `@return $this` убраны, src чист на PHPStan level 3.
- ~~**Нет `phpstan.neon`**~~ — ✅ **добавлен** (`phpstan.neon`, level 3; ратчет до 9 по фазам).
- ~~**Нет `phpunit.xml` / `.dist` и `autoload-dev`**~~ — ✅ **добавлены** (`phpunit.xml.dist`,
  `autoload-dev: Manzadey\tests`).
- ~~**Нет секции `scripts`**~~ — ✅ **добавлены** (`test` / `stan` / `cs` / `cs-fix`).
- ~~**Нет CI-workflow**~~ — ✅ **добавлен** (`.github/workflows/ci.yml`, матрица PHP 8.1–8.4).
- **Покрытие тестами** — пока низкое (Request/Response/фильтры/Query-трейты в основном
  не покрыты). Поднимается по ходу Фаз 1–5 (TDD). Частично начато: `TaskModelTest`.
- ~~`composer.json`: `"version": "0.1.0"` захардкожен, `"description"` — плейсхолдер~~
  — ✅ **исправлено**: поле `version` удалено, версия выводится из git-тегов
  (актуальный — `v0.7.0`); `description` заполнен осмысленным текстом.
- ~~`amocrm/amocrm-api-library` в `require-dev`, но в коде не используется (мёртвая
  dev-зависимость)~~ — ✅ **удалена** (Фаза 0): её транзитивная `lcobucci/clock` не
  поддерживала PHP 8.3/8.4 и ломала CI-матрицу; разбор сохранён в
  [official-library-study.md](official-library-study.md).
- Игнор 3 advisory saloon — это осознанное решение мейнтейнера (зафиксировано в
  памяти проекта), учтено как принятый риск, а не как находка.

---

## 🔵 Дизайн и согласованность публичного API

- **`send()` vs `save()`**: `ContactCreateRequest::save()` — единственное исключение,
  у всех остальных `send()`. (`src/Modules/Contact/Requests/ContactCreateRequest.php:38`)
- **Имена добавления модели** разнятся: `add()` / `addLead()` / `tag()` / `model()`.
- **5 реализаций `with`**: `HasWithQuery`, `HasLeadWithQuery`, `HasContactWithQuery`,
  `AccountRequest::with` (enum), `UserItemRequest::with` (inline-дубль). Унифицируемо.
- **Нетипизированные ответы** (сырой `Saloon\Response`): все Tag-запросы,
  `CustomFieldListRequest`, `UserListRequest`, `ContactCustomFieldsListRequest`.
- **Пробелы доступности**: `notes()` / `tags()` проброшены только в `LeadReference`,
  хотя `NoteReferences` / `TagReference` универсальны по `entityType` —
  Contacts/Tasks их лишены без причины.
- **Отсутствуют операции**: Contacts без `item()` / `update()`; Tasks/Notes/Users без
  `update()`; `delete()` нет нигде; нет batch-операций.
- **Опечатка в публичном enum**: `GrandTypeEnum` → должно быть `GrantTypeEnum`
  (`grant_type`). Значения (`authorization_code` / `refresh_token`) верны, но имя
  класса при заморозке 1.0 фиксируется — переименовать до тега.
  (`src/Enum/GrandTypeEnum.php`)
- **AF2 — протекающая база фильтров.** `AbstractFilter` плоско раздаёт подклассам
  ключи, невалидные для их сущности: `TaskFilter extends AbstractFilter` наследует
  `createdAt()` / `updatedBy()` / `name()` / `closestTaskAt()` / `customFieldsValues()`,
  которых задачи amoCRM не поддерживают (по SDK у Tasks только
  `id`/`updated_at`/`responsible_user_id`/`created_by` + свои). Метод «валиден» по
  типам, но API его игнорирует. Тот же класс проблемы, что L8. При переходе на
  типизированные фильтры (Фаза 2 спеки) базовый набор резать до реально
  поддерживаемого каждой сущностью. (`src/Filters/AbstractFilter.php`)

---

## Приоритетный план

1. **Сейчас (блокеры):** B1 (тип `requestId`), B2 (`TagsContract::all()`),
   B3 (`default` в `match`). → зелёный CI.
2. **Баги данных:** L1, L3, L4, L6 (убрать `array_merge_recursive`), L2 (сверить
   ключ `updated_at` по докам), L5 (null-guard), L8 (убрать `SORT` из
   `QueryOrderFieldEnum`), AF1 (колбэк в `range()`).
3. **Инфраструктура:** добавить `phpstan.neon` (прогрессивный ратчет до **level 9** по
   фазам — см. спеку, раздел «Качество»), `phpunit.xml`,
   `autoload-dev`, `scripts`, CI. Дописать тесты на Request/Response/фильтры.
4. **Дизайн (отдельный major):** унифицировать `send` / `with`, типизировать ответы,
   пробросить `notes`/`tags`, добавить недостающие CRUD-операции, переименовать
   `GrandTypeEnum` → `GrantTypeEnum`.
