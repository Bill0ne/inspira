<?php

use Botble\Community\Models\CommunityMember;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Botble\Slug\Services\SlugService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        if (! class_exists(SlugService::class)) {
            return;
        }

        $slugService = new SlugService();
        $prefix = SlugHelper::getPrefix(CommunityMember::class, '', false);

        CommunityMember::query()->orderBy('id')->each(function (CommunityMember $member) use ($slugService, $prefix): void {
            $exists = Slug::query()
                ->where('reference_type', CommunityMember::class)
                ->where('reference_id', $member->getKey())
                ->exists();

            if ($exists) {
                return;
            }

            $base = Str::slug((string) $member->name) ?: (string) $member->getKey();

            Slug::query()->create([
                'key' => $slugService->create($base, 0, CommunityMember::class),
                'reference_type' => CommunityMember::class,
                'reference_id' => $member->getKey(),
                'prefix' => $prefix,
            ]);
        });
    }

    public function down(): void
    {
        Slug::query()->where('reference_type', CommunityMember::class)->delete();
    }
};
