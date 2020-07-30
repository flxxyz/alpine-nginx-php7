<?php

namespace App\Services;

use Hhxsv5\LaravelS\Swoole\Socket\UdpSocket;
use Illuminate\Support\Facades\Log;
use Swoole\Server;

class UdpService extends UdpSocket
{
    public function onPacket(Server $server, $data, array $clientInfo)
    {
        Log::info('[Udp] [PACKET] Received data: ', [$data, $clientInfo]);
        $server->sendto($clientInfo['address'], $clientInfo['port'], 'Received data: ' . $data . PHP_EOL);
    }
}