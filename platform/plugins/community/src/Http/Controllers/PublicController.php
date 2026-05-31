<?php

namespace Botble\Community\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Community\Models\CommunityMember;
use Botble\Theme\Facades\Theme;
use Botble\SeoHelper\Facades\SeoHelper;

class PublicController
{
    public function index()
    {
        $members = CommunityMember::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with(['customer:id,first_name,last_name,avatar'])
            ->orderBy('name')
            ->get();

        SeoHelper::setTitle(trans('plugins/community::community.public.title'));

        Theme::breadcrumb()->add(trans('plugins/community::community.public.title'), route('public.community'));

        return Theme::scope('community.index', compact('members'))->render();
    }
}
