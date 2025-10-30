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
                    $q->orWhereHas('instructor', function ($relation) use ($search) {
                        $relation->where('name', 'like', "%{$search}%");
                    });
                }
            });
        }

        // 📂 Kategorie
        if ($category = $request->get('category')) {
            $column = $type === 'courses' ? 'category_id' : 'room_category_id';

            if (self::has($query, $column)) {
                $query->where($column, $category);
            }
        }

        // 👩‍🏫 Coach / Trainer (nur Kurse)
        if ($type === 'courses' && ($trainer = $request->get('trainer'))) {
            $column = 'instructor_id';

            if (self::has($query, $column)) {
                $query->where($column, $trainer);
            }
        }

        // 🗓️ Datum
        $date = self::parseDate($request->get('date'));
        if ($date) {
            if ($type === 'courses') {
                if (self::has($query, 'start_date')) {
                    $query->whereDate('start_date', '<=', $date);
                }

                if (self::has($query, 'end_date')) {
                    $query->where(function (Builder $builder) use ($date) {
                        $builder
                            ->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $date);
                    });
                }
            } else {
                $query->where(function (Builder $roomQuery) use ($date) {
                    $roomQuery
                        ->whereDoesntHave('activeRoomDates')
                        ->orWhereHas('activeRoomDates', function ($dates) use ($date) {
                            $dates
                                ->whereDate('start_date', '<=', $date)
                                ->where(function ($range) use ($date) {
                                    $range
                                        ->whereNull('end_date')
                                        ->orWhereDate('end_date', '>=', $date);
                                });
                        });
                });
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

    public static function count(Request $request, string $type): int
    {
        if ($type === 'courses') {
            $query = \Botble\Courses\Models\Course::query()->wherePublished();
        } else {
            $query = \Botble\Hotel\Models\Room::query()->wherePublished();
        }

        return self::apply($request, $query, $type)->count();
    }

    protected static function parseDate(?string $val): ?string
    {
        if (!$val) return null;
        $val = trim($val);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) return $val;

        try {
            return Carbon::createFromFormat('d.m.Y', $val)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
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
