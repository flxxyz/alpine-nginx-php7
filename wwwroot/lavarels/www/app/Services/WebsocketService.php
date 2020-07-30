<?php

namespace App\Services;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\WebSocket\Server;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;

class WebsocketService implements WebSocketHandlerInterface
{
    public function __construct() {}
    
    public function onOpen(Server $server, Request $request)
    {
        Log::info('[Websocket] [OPEN]', [$request->fd]);
        $server->push($request->fd, 'Welcome to LaravelS');
    }

    public function onMessage(Server $server, Frame $frame)
    {
        Log::info('[Websocket] [MESSAGE]', [$frame->fd, $frame->data]);
        $date = now();
        $server->push($frame->fd, $date->format('Y-m-d H:i:s'));
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        Log::info('[Websocket] [CLOSE]', [$fd]);
    }
}
