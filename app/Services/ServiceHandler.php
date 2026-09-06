<?php

namespace App\Services;
use App\Models\ServiceStorage;
use Illuminate\Support\Facades\Auth;

trait ServiceHandler
{
    private string $id = "592bf658-22d6-496f-a2c7-0fc55c88fc70";
    private string $keyUniqFlag = "uniq-handle";

    public function getItem(string $key)
    {
        return ServiceStorage::where([
            ['service', '=', $this->id],
            ['key', '=', $key]
        ])->first()['value'] ?? null;
    }

    public function setItem(string $key, string $value): void
    {
        ServiceStorage::updateOrCreate([
            'service' => $this->id,
            'key' => $key
        ], ['value' => $value]);
    }

    public function isNewDay():bool
    {
        $l = $this->getItem($this->keyUniqFlag);
        $d = now()->format('d.m.Y');
        if (!$l || $l != $d) {
            $this->setItem($this->keyUniqFlag, $d);
            return true;
        } else return false;
    }

    public function getHomeViewPage():string
    {
        return (Auth::user()->isManager()) ? 'biz.warehouse' : 'exp.index';
    }

}
