<?php


namespace TaylorNetwork\LaravelMcStat\Models;

use Illuminate\Database\Eloquent\Model;

class StatusUpdate extends Model
{
    const STATUS_KEYS = [
        'player_count',
        'player_max',
        'server_version',
        'protocol_version',
        'latency',
    ];

    protected $fillable = [
        'player_count',
        'player_max',
        'server_version',
        'protocol_version',
        'latency',
        'json_response',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function getResponseAttribute()
    {
        return json_decode($this->json_response, true);
    }
}