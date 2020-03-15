<?php

$http = new Swoole\Http\Server("0.0.0.0", 9501);
$http->on('request', function ($request, $response) {
    $data = json_encode([
        'server' => $request->server,
        'GET' => $request->get,
        'POST' => $request->post,
    ], JSON_UNESCAPED_UNICODE);
    $response->end($data);
});
$http->start();

// $server = new Swoole\WebSocket\Server("0.0.0.0", 9501);
// $server->on('open', function (Swoole\WebSocket\Server $server, $request) {
//     echo "[Open] handshake success with fd{$request->fd}\n";
// });
// $server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
//     echo "[Message] receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
//     $server->push($frame->fd, $frame->data);
// });
// $server->on('close', function ($ser, $fd) {
//     echo "[Close] client {$fd} closed\n";
// });

// $server->start();
