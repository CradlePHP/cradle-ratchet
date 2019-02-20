<?php //-->
/**
 * This file is part of a package designed for the CradlePHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

require_once __DIR__ . '/Server.php';

use Cradle\Framework\CommandLine;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Cradle\WebSocket\Server;

/**
 * CLI socket-server - bin/cradle queue auth-verify auth_slug=<email>
 *
 * @param Request $request
 * @param Response $response
 *
 * @return string
 */
$this->on('socket-server', function($request, $response) {
    $port = 8080;
    if ($request->hasStage('port')) {
        $port = $request->getStage('port');
    }

    $server = new Server();
    $wsServer = new WsServer($server);
    $httpServer = new HttpServer($wsServer);
    $ioServer = IoServer::factory($httpServer, $port);

    define('SOCKET_INSTANCE', md5(uniqid()));

    CommandLine::info('Server starting on port: ' . $port);

    $ioServer->run();
});
