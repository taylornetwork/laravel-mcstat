<?php


namespace TaylorNetwork\LaravelMcStat\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use TaylorNetwork\LaravelMcStat\ServerStatus;

class Server extends Model
{
    protected $guarded = [];

    public function statusUpdates()
    {
        return $this->hasMany(StatusUpdate::class)->orderBy('created_at', 'desc');
    }

    public function getStatusAttribute(): StatusUpdate
    {
        return $this->status();
    }

    public function status()
    {
        if($this->statusUpdates->count() === 0 || !Config::get('mcstat.wait_for_refresh', true)) {
            return $this->refreshStatus();
        }

        $add = Config::get('mcstat.refresh_wait_time', [ 'unit' => 'hour', 'amount' => 1]);

        if($this->refreshedAt->addUnit($add['unit'], $add['amount']) < Carbon::now()) {
            return $this->refreshStatus();
        }

        return $this->statusUpdates->first();
    }

    public function getRefreshedAtAttribute(): Carbon
    {
        return $this->statusUpdates->first()->created_at;
    }

    public function getHostname(): string
    {
        return $this->hostname ?? $this->ip_address;
    }

    public function getPort(): int
    {
        return $this->port ?? 25565;
    }

    protected function refreshStatus()
    {
        $status = ServerStatus::server($this)->getStatus();

        if($status) {
            return $this->statusUpdates()->create(array_merge(Arr::only($status, StatusUpdate::STATUS_KEYS),
                ['json_response' => json_encode($status)]));
        }

        return false;
    }

}
