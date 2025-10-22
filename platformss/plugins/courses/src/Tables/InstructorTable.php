<?php

namespace Botble\Courses\Tables;

use Botble\Courses\Models\Instructor;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\EmailColumn;
use Botble\Table\Columns\PhoneColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class InstructorTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Instructor::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('instructor.create'))
            ->addActions([
                EditAction::make()->route('instructor.edit'),
                DeleteAction::make()->route('instructor.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                NameColumn::make()->route('instructor.edit'),
                EmailColumn::make(),
                PhoneColumn::make(),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('instructor.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select([
                    'id',
                    'name',
                    'created_at',
                    'status',
                    'email',
                    'phone',
                ]);
            });
    }
}
