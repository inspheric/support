<?php

namespace Inspheric\Support\Traits;

trait PackageProvider
{
    public function basePath($path = null)
    {
        $base = dirname(dirname(dirname((new \ReflectionClass($this))->getFileName())));
        return $base.'/'.trim($path,'/');
    }
}
