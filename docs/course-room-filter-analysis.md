# Course and Room Filter Flow

This document captures how the Riorelax theme builds and applies the shared filter for both courses and rooms.

## High-level flow

1. The filter UI (`platform/themes/riorelax/partials/filters.blade.php`) renders the shared bar with fields for search, category, optional coach, date, and sort order.
2. User selections are submitted as query parameters to the listing endpoints (courses or rooms).
3. Controllers invoke `Theme\Riorelax\Helpers\FilterHelper::apply()` to mutate an Eloquent query builder based on those parameters.
4. The helper optionally scopes the query with additional relations (e.g., instructor lookups for courses, active date windows for rooms).
5. The same helper can compute a count via `FilterHelper::count()` for display beside the filter bar.

## Filter UI inputs

The Blade partial determines context using the current route and loads fallback data if the controller does not provide it:

- Categories are loaded from `CourseCategory` for courses or `RoomCategory` for rooms.
- Coaches are loaded from `Instructor` for courses; rooms do not show a trainer dropdown.
- The filter bar auto-submits when dropdowns or the date picker change, while the search field submits on Enter.
- Total results are pulled from the paginator (`$courses` or `$rooms`) when available; otherwise the helper count can be used.

## Helper application logic

`FilterHelper::apply()` processes each supported criterion in sequence:

1. **Search**
   - Applies a `name` `LIKE` clause.
   - When filtering courses, also searches the related `instructor` names with `orWhereHas`.

2. **Category**
   - Uses `category_id` for courses or `room_category_id` for rooms.
   - Guards each column with `Schema::hasColumn()` to avoid invalid SQL on models without the field.

3. **Trainer (courses only)**
   - Filters by `instructor_id` when present on the model.

4. **Date (single "Wann" field)**
   - Dates are normalized via `parseDate()`, accepting either `Y-m-d` or `d.m.Y` formats.
   - For courses: ensures `start_date <= date` and `(end_date IS NULL OR end_date >= date)` if those columns exist.
   - For rooms: keeps rooms with no `activeRoomDates` or any active date range overlapping the requested date.

5. **Sort order**
   - Supports `newest`, `oldest`, `price_asc`, and `price_desc`, falling back to `created_at DESC` when price is unavailable.

The helper returns the modified builder so controllers can continue chaining (e.g., `->paginate()`).

## Counting filtered results

`FilterHelper::count()` builds a fresh query (`Course::query()` or `Room::query()`) and passes it through `apply()` to compute the total number of matches for the current request parameters.

## Schema guards and utilities

- `has()` wraps `Schema::hasColumn()` to verify a column exists on the model table before applying a filter.
- `parseDate()` tolerates German `d.m.Y` input by attempting a Carbon conversion when the string is not already ISO-formatted.

## Integration touchpoints

- Controllers that render course or room listings should call `FilterHelper::apply(request(), $query, 'courses'|'rooms')` before retrieving results.
- The Blade partial expects `$categories`, `$trainers`, and `$courses`/`$rooms` collections when available, but falls back gracefully to internal lookups.

With the namespace corrected (`namespace Theme\Riorelax\Helpers;`), Composer can autoload `FilterHelper` through the theme classmap entry, ensuring the helper is available wherever the filter logic is needed.
