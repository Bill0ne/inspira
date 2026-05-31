<?php

namespace Botble\Community\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Community\Forms\CommunityMemberForm;
use Botble\Community\Http\Requests\CommunityMemberRequest;
use Botble\Community\Models\CommunityMember;
use Botble\Community\Tables\CommunityMemberTable;

class CommunityMemberController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/community::community.name'), route('community.index'));
    }

    public function index(CommunityMemberTable $table)
    {
        $this->pageTitle(trans('plugins/community::community.members'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/community::community.create'));

        return CommunityMemberForm::create()->renderForm();
    }

    public function store(CommunityMemberRequest $request)
    {
        $form = CommunityMemberForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('community.index'))
            ->setNextUrl(route('community.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(CommunityMember $community)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $community->name]));

        return CommunityMemberForm::createFromModel($community)->renderForm();
    }

    public function update(CommunityMember $community, CommunityMemberRequest $request)
    {
        CommunityMemberForm::createFromModel($community)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('community.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(CommunityMember $community)
    {
        return DeleteResourceAction::make($community);
    }
}
