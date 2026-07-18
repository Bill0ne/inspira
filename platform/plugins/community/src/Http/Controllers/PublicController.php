<?php

namespace Botble\Community\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Community\Models\CommunityMember;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Str;

class PublicController
{
    public function index()
    {
        $members = CommunityMember::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with(['customer:id,first_name,last_name,avatar'])
            ->orderBy('created_at')
            ->get();

        SeoHelper::setTitle(trans('plugins/community::community.public.title'));

        Theme::breadcrumb()->add(trans('plugins/community::community.public.title'), route('public.community'));

        return Theme::scope('community.index', compact('members'))->render();
    }

    public function getMember(string $key)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(CommunityMember::class));

        abort_unless($slug, 404);

        $member = CommunityMember::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with(['customer:id,first_name,last_name,avatar'])
            ->findOrFail($slug->reference_id);

        SeoHelper::setTitle($member->name)
            ->setDescription(Str::words(strip_tags((string) ($member->short_description ?: $member->description)), 40));

        Theme::breadcrumb()
            ->add(trans('plugins/community::community.public.title'), route('public.community'))
            ->add($member->name, $member->url);

        return Theme::scope('community.member', compact('member'))->render();
    }
}
