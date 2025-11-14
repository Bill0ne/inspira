<?php

namespace Botble\InspiraCancellation\Tables\Actions;

use Botble\Table\Actions\Action;
use Closure;

class ConditionalAction extends Action
{
    protected ?Closure $displayIf = null;

    public function displayIf(Closure $callback): static
    {
        $this->displayIf = $callback;

        return $this;
    }

    public function shouldDisplay(): bool
    {
        if (! $this->displayIf) {
            return true;
        }

        return (bool) call_user_func($this->displayIf, $this);
    }

    public function toHtml(): string
    {
        if (! $this->shouldDisplay()) {
            return '';
        }

        return parent::toHtml();
    }
}
