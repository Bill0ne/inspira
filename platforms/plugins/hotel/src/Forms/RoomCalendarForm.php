<?php

namespace Botble\Hotel\Forms;

use Botble\Base\Forms\Fields\HiddenField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Hotel\Http\Requests\RoomCalendarRequest;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Models\RoomCalendar;

class RoomCalendarForm extends FormAbstract
{
    public function setup(): void
    {
        $room = Room::query()->findOrFail(request()->route('id'));

        $this
            ->setupModel(new RoomCalendar())
            ->setValidatorClass(RoomCalendarRequest::class)
            ->disablePermalinkField()
            ->add('room_id', HiddenField::class, [
                'value' => $room->id,
            ])
            ->add('name', TextField::class, [
                'label' => trans('plugins/hotel::ical.calendar_name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/hotel::ical.calendar_name_placeholder'),
                ],
            ])
            ->add('url', TextareaField::class, [
                'label' => trans('plugins/hotel::ical.calendar_url'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/hotel::ical.calendar_url_placeholder'),
                    'rows' => 3,
                ],
            ]);

        $this->setBreakFieldPoint('status');

        // Add existing calendars
        $calendars = $room->calendars;

        if ($calendars->isNotEmpty()) {
            $this->addMetaBoxes([
                'existing_calendars' => [
                    'title' => trans('plugins/hotel::ical.existing_calendars'),
                    'content' => view('plugins/hotel::ical.calendars', compact('calendars', 'room'))->render(),
                    'priority' => 0,
                ],
            ]);
        }

        // Add export calendar info
        $this->addMetaBoxes([
            'export_calendar' => [
                'title' => trans('plugins/hotel::ical.export_calendar'),
                'content' => view('plugins/hotel::ical.export', compact('room'))->render(),
                'priority' => 1,
            ],
        ]);
    }
}
