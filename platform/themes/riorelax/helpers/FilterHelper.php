<?php

namespace Theme\Riorelax\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class FilterHelper
{
    public static function apply(Request $request, Builder $query, string $type): Builder
    {
        // 🔍 Suche
        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search, $type) {
                $q->where('name', 'like', "%{$search}%");
                if ($type === 'courses') {
                    $q->orWhereHas('trainer', fn($t) => $t->where('name', 'like', "%{$search}%"));
                }
            });
        }

        // 📂 Kategorie
        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        // 👩‍🏫 Coach / Trainer (nur Kurse)
        if ($type === 'courses' && ($trainer = $request->get('trainer'))) {
            $query->where('trainer_id', $trainer);
        }

        // 🗓️ Datum (ein Feld „Wann“)
        $date = self::parseDate($request->get('date'));
        if ($date) {
            if ($type === 'courses') {
                // passe Feldnamen an dein Schema an (session_date/start_date)
                $query->whereDate('session_date', $date);
            } else {
                // Rooms: Beispiel-Logik – an dein Schema anpassen (available_from/to)
                if (self::has($query, 'available_from')) {
                    $query->whereDate('available_from', '<=', $date);
                }
                if (self::has($query, 'available_to')) {
                    $query->whereDate('available_to', '>=', $date);
                }
            }
        }

        // 🔽 Sortierung
        switch ((string) $request->get('sort')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_asc':
                if (self::has($query, 'price')) $query->orderBy('price', 'asc');
                else $query->orderBy('created_at', 'desc');
                break;
            case 'price_desc':
                if (self::has($query, 'price')) $query->orderBy('price', 'desc');
                else $query->orderBy('created_at', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }

    /** Ergebnisanzahl berechnen (für das Filter-Partial) */
    public static function count(Request $request, string $type): int
    {
        if ($type === 'courses') {
            $query = \Botble\Courses\Models\Course::query();
        } else {
            $query = \Botble\Hotel\Models\Room::query();
        }

        return self::apply($request, $query, $type)->count();
    }

    /** d.m.Y oder Y-m-d -> Y-m-d */
    protected static function parseDate(?string $val): ?string
    {
        if (!$val) return null;
        $val = trim($val);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;
        try { return Carbon::createFromFormat('d.m.Y', $val)->format('Y-m-d'); } catch (\Throwable) { return null; }
    }

    protected static function has(Builder $query, string $column): bool
    {
        try {
            $table = $query->getModel()->getTable();
            return Schema::hasColumn($table, $column);
        } catch (\Throwable) {
            return false;
        }
    }
}

if (! class_exists('Theme\\Rlorenak\\Helpers\\FilterHelper')) {
    class_alias(FilterHelper::class, 'Theme\\Rlorenak\\Helpers\\FilterHelper');
}
