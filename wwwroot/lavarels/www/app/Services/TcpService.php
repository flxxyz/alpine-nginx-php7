<?php

namespace App\Services;

use Hhxsv5\LaravelS\Swoole\Socket\TcpSocket;
use Illuminate\Support\Facades\Log;
use Swoole\Server;

class TcpService extends TcpSocket
{
    public function onConnect(Server $server, $fd, $reactorId)
    {
        Log::info('[Tcp] [CONNECT]', [$fd]);
        $server->send($fd, 'Welcome to LaravelS' . PHP_EOL . '> ');
    }

    public function onReceive(Server $server, $fd, $reactorId, $data)
    {
        Log::info('[Tcp] [RECEIVE] Received data: ', [$fd, $data]);
        $server->send($fd, '> Received data: ' . $data . ($data === "quit\r\n" ? '' : '> '));
        if ($data === "quit\r\n") {
            $server->close($fd);
        }
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        Log::info('[Tcp] [CLOSE]', [$fd]);
        $server->send($fd, '> goodbye' . PHP_EOL);
    }
}