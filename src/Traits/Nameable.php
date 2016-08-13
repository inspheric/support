<?php

namespace Inspheric\Support\Traits;

trait Nameable
{
    public function getAtNameAttribute() {
        return '@'.$this->name;
    }

    public function getDisplayNameAttribute($display_name)
    {
        return $display_name ?: $this->name;
    }

    public function getRouteKeyName()
    {
        return 'name';
    }
}
