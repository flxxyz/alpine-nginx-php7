<?php

function ping($ip){
  $ip_port = explode(':', $ip);
  if( filter_var( $ip_port[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ){        //IPv6
    $socket = socket_create(AF_INET6, SOCK_STREAM, SOL_TCP);
  }elseif( filter_var( $ip_port[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ){    //IPv4
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
  }else{
    return FALSE;
  }
      
  if( !isset($ip_port[1]) ){        //没有写端口则指定为80
    $ip_port[1] = '80';
  }
  $ok = socket_connect($socket, $ip_port[0], $ip_port[1]);
  socket_close($socket);
  return $ok;
} 

$host = $_GET['host'];

if (ping($host)) {
  echo '与'.$host.'通信正常';
}
