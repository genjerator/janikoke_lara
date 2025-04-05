<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Mapper;
class LatLngJsonField extends Field
{
    protected string $view = 'forms.components.lat-lng-json-field';

    public function validateJson(): self
    {
        return $this->rule('json')->rule('nullable');
    }

}
