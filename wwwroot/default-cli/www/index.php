<?php

$server = new Swoole\WebSocket\Server("0.0.0.0", 9001);
$server->set([
    'daemonize' => false,
    'open_websocket_close_frame' => true,
]);
$server->on('open', function ($serv, $request) {
    echo "[Open   ] handshake success with fd{$request->fd}\n";
});
$server->on('message', function ($serv, $frame) {
    if ($frame->opcode == 0x08) {
        echo "[Close  ] receive from {$frame->fd},opcode:{$frame->code},reason:{$frame->reason}\n";
    } else {
        echo "[Message] receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $serv->push($frame->fd, $frame->data);
    }
});
$server->on('close', function ($ser, $fd) {
});

$httpPort = $server->listen('0.0.0.0', 9000, SWOOLE_SOCK_TCP);
$httpPort->set([
    'open_http_protocol' => true,
    'open_websocket_protocol' => false,
]);
$httpPort->on('request', function ($request, $response) use ($server) {
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        $response->end();
        return;
    }

    if (isset($request->get['message'])) {
        if (is_string($request->get['message']) && $request->get['message'] != '') {
            foreach ($server->connections as $fd) {
                // 需要先判断是否是正确的websocket连接，否则有可能会push失败
                if ($server->isEstablished($fd)) {
                    $server->push($fd, $request->get['message']);
                }
            }
            echo "全员推送成功！\n";
        }
    }

    $response->end('hello world!');
});


$server->start();
