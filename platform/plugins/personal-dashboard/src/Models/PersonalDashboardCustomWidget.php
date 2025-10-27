<?php

namespace Botble\PersonalDashboard\Models;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\BaseModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class PersonalDashboardCustomWidget extends BaseModel
{
    protected $table = 'personal_dashboard_custom_widgets';

    protected $fillable = [
        'key',
        'title',
        'description',
        'icon',
        'color',
        'column_class',
        'view_path',
        'handler',
        'ajax_route',
        'has_load_callback',
        'is_active',
        'sort_order',
        'settings',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'settings' => 'array',
        'has_load_callback' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getTitle(?string $locale = null): string
    {
        $locale = $locale ?: App::getLocale();

        $title = Arr::get($this->title, $locale, Arr::first($this->title));

        return (string) $title;
    }

    public function getDescription(?string $locale = null): ?string
    {
        $locale = $locale ?: App::getLocale();

        $description = Arr::get($this->description, $locale, Arr::first($this->description ?? []));

        return $description ? BaseHelper::clean($description) : null;
    }
}
