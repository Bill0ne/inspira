<?php

namespace Botble\Courses\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Courses\Models\CourseReview;
use Botble\Courses\Tables\CourseReviewTable;

class ReviewController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/hotel::hotel.name'));
    }

    public function index(CourseReviewTable $dataTable)
    {
        $this->pageTitle(trans('plugins/hotel::review.name'));

        Assets::addStylesDirectly('vendor/core/plugins/hotel/css/review.css');

        return $dataTable->renderTable();
    }

    public function destroy(CourseReview $courseReview)
    {
        return DeleteResourceAction::make($courseReview);
    }
}
