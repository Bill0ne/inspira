<?php

namespace Theme\Riorelax\Helpers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class FilterHelper
{
    /**
     * Wendet zentrale Filterlogik auf Query an.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type 'courses' oder 'rooms'
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function apply(Request $request, Builder $query, string $type)
    {
        // 🔍 Suche
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search, $type) {
                $q->where('name', 'like', "%$search%");
                if ($type === 'courses') {
                    $q->orWhereHas('trainer', fn($t) => $t->where('name', 'like', "%$search%"));
                }
            });
        }

        // 📂 Kategorie
        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        // 👩‍🏫 Trainer (nur Kurse)
        if ($type === 'courses' && ($trainer = $request->get('trainer'))) {
            $query->where('trainer_id', $trainer);
        }

        // 🗓️ Datum / Zeitraum
        if ($type === 'courses' && ($date = $request->get('date'))) {
            $query->whereDate('session_date', $date);
        }

        if ($type === 'rooms') {
            $start = $request->get('start');
            $end   = $request->get('end');
            if ($start && $end) {
                $query->whereBetween('available_from', [$start, $end]);
            }
        }

        // 💶 Preisfilter (nur Rooms)
        if ($type === 'rooms' && ($price = $request->get('price'))) {
            match ($price) {
                'low'    => $query->where('price', '<', 50),
                'medium' => $query->whereBetween('price', [50, 100]),
                'high'   => $query->where('price', '>', 100),
            };
        }

        // 🔽 Sortierung
        $sort = $request->get('sort');
        switch ($sort) {
            case 'popular':
                $query->orderBy('views', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
        }

        return $query;
    }
}
