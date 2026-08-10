# CLAUDE.md

Guidance for Claude Code working in this repository.

## What this is

**TKF CLM** — a Laravel 11 application for community-led monitoring (CLM) of an
immunization/polio program in Pakistan. It has two faces:

- **A Blade admin panel** (`/admin/*`) used by program staff on desktop.
- **A REST API** (`/api/v1/*`) consumed by a mobile app that field workers use to
  submit forms offline/online.

Geography is the spine of the domain: **District → Union Council (UC) → Fix Site /
Outreach Site**. Almost every record carries `district` + `uc`, and most reporting
aggregates along that axis.

## Stack

- PHP 8.2+ (8.3 locally), Laravel 11
- Blade + Tailwind 4 via Vite. **No JS framework** — pages are server-rendered,
  interactivity is vanilla JS in `<script>` blocks inside the Blade files.
- `laravel/sanctum` for API tokens
- `phpoffice/phpspreadsheet` for all Excel import/export
- MySQL in development/production (Laragon locally); SQLite `:memory:` in tests

## Commands

```bash
composer install && npm install
php artisan migrate
npm run dev          # vite watch
composer dev         # server + queue + pail + vite together
composer test        # config:clear then artisan test
vendor/bin/pint      # code style
```

Tests run against SQLite in-memory (see `phpunit.xml`) — they never touch the dev
database.

## Domain: the five "core forms"

These are the whole app. Each is a first-class model with its own table, its own
admin CRUD screens, and its own API submit endpoint. Forms are **hardcoded**, not
user-defined — there is no form builder (see *History* below).

| Form | Model | Table | ID prefix | Children |
|---|---|---|---|---|
| Child Line List | `ChildLineList` | `child_line_lists` | `CL` | — |
| FGDs – Community | `FgdsCommunity` | `fgds_community` | `FC` | `FgdsCommunityBarrier`, participants |
| FGDs – Health Workers | `FgdsHealthWorkers` | `fgds_health_workers` | `FH` | `FgdsHealthWorkersBarrier`, participants |
| Bridging The Gap | `BridgingTheGap` | `bridging_the_gaps` | `BG` | `BridgingTheGapActionPlan`, `BridgingTheGapTeamMember`, participants |
| Vaccination Records | `VaccinationRecord` | `vaccination_records` | `VR` | belongs to `CommunityMember` |

Each core form has a matching pair of controllers with the same name:
`App\Http\Controllers\Admin\XController` (screens, import/export) and
`App\Http\Controllers\Api\XController` (mobile submit/list/show).

Note `Api\FormIdController` (`POST /api/v1/form-id/generate`) belongs to the core
forms despite its name — it mints a `unique_id` so the mobile app can display it
before submitting. It is unrelated to the deleted form builder.

## Conventions that matter

### `HasUniqueFormId`

Every core-form model uses this trait. On `creating`, it stamps `unique_id` as
`{PREFIX}-{8 random uppercase chars}`. Prefixes are a hardcoded map in the trait
keyed by `class_basename`, including legacy names (`AreaMapping`, `DraftList`,
`ReligiousLeader`, `CommunityBarrier`, `HealthcareBarrier`) from before the
2026-01-07 rename migration. Anything unrecognised falls back to `FM`.

### Participants are polymorphic

`Participant` attaches via `morphMany(..., 'participantable')` to FGDs-Community,
FGDs-Health-Workers and Bridging The Gap. Consequences:

- **Participant counts come from actual `Participant` rows**, not from the
  `participants_males` / `participants_females` integer columns on the parent. Those
  columns are what the field worker typed; the rows are the real attendance list.
  Recent work deliberately moved reporting onto the rows — keep it that way.
- **Deleting a parent must clean up by hand.** There are no cascade constraints.
  `BridgingTheGapTeamMember` rows reference participants across forms, so
  `destroy`/`bulkDestroy` delete team-member references *first*, then participants,
  then the record. Copy that order in any new delete path.

### Barrier categories are a closed set

`BarrierCategory::CANONICAL` lists exactly 11 categories. Imports **must never
create a new category** — call `BarrierCategory::resolveForImport($rawLabel, $byName)`,
which resolves exact name → legacy alias → best keyword overlap → `FALLBACK`, and
always returns one of the 11. Pass the pre-built `keyBy(normalizeName)` collection
when importing in bulk to avoid a query per row.

Three migrations (`2026_06_02_000002`, `2026_06_02_000003`) exist purely to enforce
this list. If you change `CANONICAL`, change those too.

### UC consolidation lives in `DashboardController`

Field workers spell UCs inconsistently (`Muzafrabad`, `Muzafarabad-1`,
`Muzafarabad 01`, …). The canonical map is
`DashboardController::UC_CONSOLIDATION`, exposed via two **static** methods:

- `DashboardController::getConsolidatedUcName(?string $raw): ?string`
- `DashboardController::getUcVariants(string $consolidated): array`

These are called statically from `FgdsCommunityController`,
`FgdsHealthWorkersController`, `FixedSiteReportController` and
`Api\BridgingTheGapController`. It is domain logic sitting in a controller — if you
touch it, prefer extracting to a service over adding another static caller.

When querying by UC, filter on `whereIn('uc', $variants)`, never on a single string.

### Page-wide filters

On the FGDs index screens, one set of filters drives the table, the stat cards, the
barrier-category cards, the drill-down modal *and* the map. The pattern is a private
`applyBarrierListFilters($query, $request)` applied to a fresh query for each
consumer, plus a `$filteredIds` collection for the count queries. If you add a
filter, add it there once — don't special-case the table.

### Import/export

Two distinct mechanisms, don't mix them up:

- **CSV** — `export()` / `template()` / `import()` on the admin controllers, hand-rolled
  with `fputcsv`/`fgetcsv`.
- **Excel** — the child-record uploads (`uploadBarriers`, `uploadActionPlan`) and their
  `*Sample` templates, via PhpSpreadsheet.

Excel imports **replace** existing child rows for that record (delete-then-insert),
they don't merge. Map columns **by header name, not by position** — positional
mapping was a real bug fixed in `3b0a55c`.

#### The action-plan layout

Bridging The Gap action plans have one canonical column set, and it is declared
in three places that must stay in sync:

| Where | What |
|---|---|
| `BridgingTheGapController::resolveActionPlanColumns()` | header→field matching + positional fallback |
| `BridgingTheGapController::actionPlanSample()` | the downloadable template |
| `AP_FIELDS` in `bridging-the-gap/{index,show}.blade.php` | the manage-modal table, inline editor and save payload |

```
Problem | Sub Cause | Root Cause | Solution | Action Needed | Responsible | Timeline
```

Only `Problem` is required; a row with an empty problem is skipped. Every other
column may be absent — matching is by header name, so an older file without
`Sub Cause` still imports with the rest landing correctly.

`Sub Cause` and `Root Cause` both reduce to a string containing "cause" once
punctuation is stripped, so they are matched on their full prefixed forms and a
bare `Cause` header is reserved for root cause. Keep that in mind before adding
another "…Cause" column.

To add a column: migration → `BridgingTheGapActionPlan::$fillable` → the three
places above → `storeActionPlan`/`updateActionPlan` validation → the Fixed Site
report (`FixedSiteReportController` export + `reports/fixed-site.blade.php`).
The views are driven off `AP_FIELDS`, so they need one entry each, not a set of
shifted array indexes.

### Activity logging

Every authenticated route is wrapped in the `activity.log` middleware
(`App\Http\Middleware\ActivityLogger`) which writes to `activity_logs`. API requests
additionally go through `ApiRequestLogger`.

## Auth: three contexts

| Context | Guard | Provider | Used by |
|---|---|---|---|
| Admin panel | `web` (session) | `User` | staff, all `/admin/*` |
| Mobile field app | `sanctum` | `User` | core-form submit endpoints |
| CLM Tracker | `community` (sanctum) | `CommunityMember` | vaccination records, `/api/v1/clm/*` |

`community` is a second sanctum guard over a different model — when adding an API
route, be deliberate about which of the two it belongs to.

## Views and assets

`resources/views/admin/core-forms/{form}/{index,show,edit}.blade.php`, with shared
pieces in `core-forms/partials/`. The admin layout is split into
`layouts/partials/{sidebar,header,alerts,footer,scripts}.blade.php`.

**CSS lives in `resources/css/`, not in Blade.** `admin.css` holds the theme
tokens; `admin/layout.css` is the chrome loaded on every admin page; the
page-scoped sheets (`admin/core-forms.css`, `admin/uc-detail.css`,
`admin/fixed-site-report.css`) are separate Vite entries pulled in through
`@push('styles')` so each still loads only on its own page. Add a new stylesheet
to `vite.config.js` inputs and push it from the view — don't reintroduce inline
`<style>` blocks, and don't merge page sheets into `admin.css` (the class names
are not namespaced and will collide).

Page JavaScript that needs Blade interpolation (`route()`, `@json`) stays in a
Blade partial (`admin/uc/partials/scripts.blade.php`), not a `.js` asset. Note
that `@js()`, `@json()` and other directives are parsed by Blade **even inside JS
comments and string literals** — that caused a 500 in `bcdcae8`. When writing JS
in Blade, keep Blade directives out of comments.

## History

- **The generic form builder was removed** (Aug 2026). `Form`, `FormField`,
  `FormSubmission`, `FormSubmissionParticipant`, the `FormFieldType` enum, their
  controllers/policies/services/resources and the `admin/forms` + `admin/submissions`
  screens are gone, along with their tables. It was never adopted: production held
  one demo form and zero submissions. `spatie/laravel-medialibrary` went with it —
  it only ever backed the builder's file fields. Don't reintroduce either without
  a concrete need; new data collection should be a core form.
- The four `create_form*_table` migrations are kept for history and are undone by
  `2026_08_07_200000_drop_form_builder_tables`.

## Known rough edges

- The CSV `export()` / `template()` / `import()` methods on `FgdsCommunityController`
  and `FgdsHealthWorkersController` reference columns that no longer exist
  (`uc_name`, `session_date`, `epi_focal_person`, `barriers_identified`,
  `solutions_proposed`, `follow_up_actions`). Exports emit blanks for them and
  imports silently drop them via mass-assignment. Don't copy these as a pattern.
- `/debug` (`DebugController`) is reachable by any authenticated user, not just admins.
- There are no cascade deletes anywhere; deletion order is manual (see above).
