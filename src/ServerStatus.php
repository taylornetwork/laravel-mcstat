<?php


namespace TaylorNetwork\LaravelMcStat;

use Illuminate\Support\Facades\Config;
use randomhost\Minecraft\Status;
use TaylorNetwork\LaravelMcStat\Models\Server;

class ServerStatus
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * @var Status
     */
    protected $wrapper;

    protected $forceLegacy = false;

    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->getStatusWrapper();
    }

    public function useLegacy(): self
    {
        $this->forceLegacy = true;
        return $this;
    }

    public function getStatusWrapper(bool $newInstance = false): Status
    {
        if(!isset($this->wrapper) || $newInstance) {
            $this->wrapper = new Status($this->server->getHostname(), $this->server->getPort());
        }

        return $this->wrapper;
    }

    public function getStatus(): array
    {
        $legacy = Config::get('mcstat.use_legacy_protocol', false);

        if($this->forceLegacy) {
            $legacy = true;
        } else {
            if(isset($this->server->legacy)) {
                $legacy = $this->server->legacy;
            }
        }

        $response = $this->getStatusWrapper()->ping($legacy);

        if($response) {
            return $response;
        }

        return [];
    }

    public static function server(Server $server): self
    {
        return new static($server);
    }

    public function __call($name, $arguments)
    {
        return $this->getStatusWrapper()->$name(...$arguments);
    }


}
