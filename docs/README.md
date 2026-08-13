# Документация `saloonphp-amocrm`

Каталог технической документации пакета — Saloon-обёртки над amoCRM REST API v4.

## Содержание

| Документ | О чём | Когда смотреть |
|---|---|---|
| [package-api-map.md](package-api-map.md) | Карта реализованного API пакета: все Request-классы, их параметры, Response-классы и их методы, по модулям + сводка покрытия | Нужно понять, что умеет пакет и как им пользоваться |
| [audit.md](audit.md) | Аудит качества: падающий тест, баги (блокеры + логические), пробелы инфраструктуры и дизайна API, приоритетный план | Планирование исправлений и техдолга |
| [official-library-study.md](official-library-study.md) | Разбор официального SDK `amocrm/amocrm-api-library` 1.15.0 + gap-таблица «SDK → обёртка» | Роадмап портирования, что ещё реализовать |
| [amocrm-api-abilities.md](amocrm-api-abilities.md) | Возможности официального API amoCRM (полный обзор платформы по докам разработчика) | Справка по самому API amoCRM |
| [superpowers/specs/2026-06-07-amocrm-v1-release-design.md](superpowers/specs/2026-06-07-amocrm-v1-release-design.md) | Дизайн релиза v1.0: полный паритет с SDK 1.15, фазы, ломающие изменения, гейты | Планирование и реализация v1.0 |
| [folder-structure.md](folder-structure.md) | Целевая структура папок `src/` под v1.0: модули сущностей, коллекции, инфраструктура | Ориентир по раскладке при реализации |
| [query-traits-application.md](query-traits-application.md) | Аудит применимости Query-трейтов к текущим запросам + карта трейтов для новых сущностей | Подключение page/limit/order/filter/with по сущностям |

## Краткая навигация по задачам

- **«Какой запрос вызвать?»** → [package-api-map.md](package-api-map.md)
- **«Что починить в первую очередь?»** → [audit.md](audit.md) → раздел «Приоритетный план»
- **«Что ещё портировать из официального SDK?»** → [official-library-study.md](official-library-study.md) → раздел «Роадмап портирования»
- **«Что вообще умеет API amoCRM?»** → [amocrm-api-abilities.md](amocrm-api-abilities.md)

## Статус документов

| Документ | Источник данных | Дата |
|---|---|---|
| package-api-map.md | Разбор `src/` (95 файлов) | 2026-06-07 |
| audit.md | PHPStan + PHP-CS-Fixer + PHPUnit + ручной разбор | 2026-06-07 |
| official-library-study.md | Разбор `vendor/amocrm/amocrm-api-library` 1.15.0 | 2026-06-07 |
| superpowers/specs/2026-06-07-amocrm-v1-release-design.md | Согласованный дизайн релиза v1.0 | 2026-06-07 |
| amocrm-api-abilities.md | Портал разработчика amoCRM | — |
