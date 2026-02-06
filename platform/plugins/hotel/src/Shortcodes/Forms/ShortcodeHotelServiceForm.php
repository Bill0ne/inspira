<?php

namespace Botble\Hotel\Shortcodes\Forms;

use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Hotel\Models\Service;
use Botble\Shortcode\Forms\ShortcodeForm;
use Illuminate\Support\Arr;

class ShortcodeHotelServiceForm extends ShortcodeForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/hotel::hotel.shortcodes.title'))
                    ->toArray()
            )
            ->add(
                'service_ids',
                SelectField::class,
                SelectFieldOption::make()
                    ->choices(
                        Service::query()
                        ->wherePublished()
                        ->pluck('name', 'id')
                        ->toArray()
                    )
                    ->label(trans('plugins/hotel::hotel.shortcodes.choose_services'))
                    ->searchable()
                    ->multiple()
                    ->toArray()
            )
            ->add(
                'show_price',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/hotel::hotel.shortcodes.show_price'))
                    ->value(Arr::get($this->getModel(), 'show_price', true))
            );
    }
}
