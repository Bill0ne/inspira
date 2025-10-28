<?php

namespace Theme\Riorelax\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\Fields\RepeaterField;
use Botble\Base\Forms\FieldOptions\RepeaterFieldOption;
use Botble\Hotel\Facades\HotelHelper;
use App;

class ThemeBookingForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->withCustomFields()

            ->add('slots', RepeaterField::class,
                RepeaterFieldOption::make()
                    ->label(false)
                    ->fields([
                        [
                            'type' => 'text',
                            'label' => __('Check In Date'),
                            'attributes' => [
                                'name' => 'start_date',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapperAttributes' => ['class' => 'col-md-6'],
                        ],
                        [
                            'type' => 'text',
                            'label' => __('Check Out Date'),
                            'attributes' => [
                                'name' => 'end_date',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'wrapperAttributes' => ['class' => 'col-md-6'],
                        ],
                    ])
                    ->value(old('slots', []))
            )
            ->setFormOption('method', 'POST');
    }
}
