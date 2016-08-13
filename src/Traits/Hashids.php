<?php

namespace Inspheric\Support\Traits;

use Vinkla\Hashids as BaseHashids;

trait Hashids {

    public function getRouteKeyName()
    {
        return isset($this->routeKeyName) ? $this->routeKeyName : 'hashid';
    }

    public function getHashidAttribute()
    {
        return static::encodeHashid($this->id);
    }

    public function scopeWhereHashid($query, $id)
    {
        return $query->where('id', static::decodeHashid($id));
    }

    public function findByHashid($id, $columns = ['*'])
    {
        return parent::find(static::decodeHashid($id), $columns);
    }

    public function findManyByHashid($ids, $columns = ['*'])
    {
        return parent::findMany(static::decodeHashid($ids), $columns);
    }

    public function findByHashidOrFail($id, $columns = ['*'])
    {
        return parent::findOrFail(static::decodeHashid($id), $columns);
    }

    public function findByHashidOrNew($id, $columns = ['*'])
    {
        return parent::findOrNew(static::decodeHashid($id), $columns);
    }

    public static function decodeHashid($hashid)
    {

        if (is_numeric($hashid)) {
            return $hashid;
        }

        $ids = BaseHashids::decode($hashid);

        if (count($ids) == 0) {
            $ids = $hashid;
        }
        elseif (count($ids) == 1) {
            $ids = head($ids);
        }

        return $ids;
    }

    public static function encodeHashid($id)
    {
        return BaseHashids::encode($id);
    }

}
