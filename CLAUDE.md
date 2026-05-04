# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

`manzadey/saloonphp-amocrm` — PHP SDK for the AmoCRM REST API (`/api/v4`) built on top of [Saloon v3](https://docs.saloon.dev). PHP `^8.1`, PSR-4 autoload (`Manzadey\SaloonAmoCrm\` → `src/`, `Manzadey\SaloonAmoCrm\Tests\` → `tests/`). Comments and PHPDoc are written in Russian — preserve language when editing existing docblocks.

## Commands

No `phpunit.xml` / `phpunit.xml.dist` is committed (they're gitignored), so PHPUnit is invoked directly against the `tests/` directory. PHPUnit 12 is used, so test methods need either the `test` prefix or the `#[\PHPUnit\Framework\Attributes\Test]` attribute — recent commits standardised on `#[Test]`.

```bash
composer install

# Run the full suite
vendor/bin/phpunit tests

# Single file / class / method
vendor/bin/phpunit tests/Connectors/MainConnectorTest.php
vendor/bin/phpunit --filter MainConnectorTest tests
vendor/bin/phpunit --filter testResolveBaseUrl tests
```

There is no linter, static analyser, or CI configuration in the repo — don't invent one.

## Architecture

The SDK is a thin object-oriented facade over Saloon. Three things drive the design:

**`Client` is the composition root** (`src/Client.php`). It holds two Saloon connectors and exposes module references:

- `MainConnector` (`/api/v4`) — used by every business request. Authenticated via a `Closure` returned from `Client::getAuth()` that the connector calls on every request. The closure pulls the current token via `TokenConfig::callbackGetAccessToken`, calls `authorize()` if there is no token, calls `refreshAccessToken()` if `AccessToken::hasExpired()`, and finally returns a `Saloon\Http\Auth\AccessTokenAuthenticator`. Token persistence is delegated to user-supplied closures on `TokenConfig` (`callbackGetAccessToken`, `callbackAuthorizeCode`, `callbackRefreshAccessToken`) — the SDK itself never touches storage.
- `OAuth2Connector` (`/oauth2`) — used only by `Requests/OAuth2/AccessToken` for the auth-code exchange and refresh-token flows.

`Client::leads()`, `contacts()`, `tasks()`, `users()`, `account()` return module **References**, which are factories for that module's Requests.

**Module layout** (`src/Modules/<Entity>/`) is uniform and worth matching when adding a new entity:

```
<Entity>Reference.php        — factory returning Request instances (list/item/create/update/...)
<Entity>Model.php            — extends Modules\Model (which extends Saloon ArrayStore + Makeable),
                               getter/setter/remover triplets over a flat array payload
<Entity>Filter.php           — extends Filters\AbstractFilter for ?filter[...] query params
Requests/Abstract<Entity>Request.php  — sets $endpoint, holds MainConnector, overrides send()
Requests/<Entity>{List,Item,Create,Update,...}Request.php
Requests/Traits/Has<Entity>WithQuery.php  — entity-specific `?with=` flags built on Query\HasWithQuery
Responses/<Entity>{List,Item,Add,Update}Response.php  — typed wrappers, hydrate Models
```

`Model` (`src/Modules/Model.php`) is the canonical payload object: it extends `Saloon\Repositories\ArrayStore`, supports dotted-key reads via overridden `get()`, and merges per-class `$defaults` into incoming data. Don't fight the array-store pattern — request bodies are built by calling `$model->all()` and writing it onto `$this->body()`.

**Cross-cutting query traits** in `src/Query/` (`HasFilterQuery`, `HasLimitQuery`, `HasOrderQuery`, `HasPageQuery`, `HasSearchQuery`, `HasWithQuery`) are mixed into List requests to compose query string params; entity-specific `with` traits (e.g. `HasLeadWithQuery`) wrap `HasWithQuery` to expose typed `withContacts()`-style helpers.

**Cross-entity contracts/traits** in `src/Contracts/` and per-module trait directories (`Modules/Tag/Requests/HasTags`, `Modules/CustomField/Requests/HasCustomFieldsValues`, `Modules/Contact/Requests/HasContacts`) are composed into multiple Models so behaviours like tag/custom-field manipulation stay shared. When a model needs one of these capabilities, `implement` the contract and `use` the trait — see `LeadModel` for the canonical example.

**Errors**: `MainConnector` uses `AlwaysThrowOnErrors`, so any non-2xx becomes a Saloon exception. The OAuth2 flow handles errors manually in `Client::authorize()` / `refreshAccessToken()` and rethrows as `AmoCrmExchangeAuthCodeException` / `AmoCrmRefreshAccessTokenException`.

## Conventions when extending

- Every PHP file starts with `declare(strict_types=1);`.
- Requests must extend the module's `Abstract<Entity>Request` (which already plumbs the connector and overrides `send()` to return the typed Response).
- Responses extending `Saloon\Http\Response` mix in `HasPageResponse` / `HasLinksResponse` from `src/Responses/` for paginated list responses.
- New tests mirror the `src/` namespace under `tests/` (e.g. `tests/Connectors/`, `tests/Configs/`) and use the `#[Test]` attribute rather than the `test` method-name prefix — see recent commits.
