<?php //-->
/**
 * This file is part of a package designed for the CradlePHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

use Cradle\Framework\CommandLine;

require_once __DIR__ . '/src/events.php';

$this->addLogger(function($message) {
    //add logger only on SOCKET INSTANCE
    if (php_sapi_name() !== 'cli' || !defined('SOCKET_INSTANCE')) {
        return;
    }

    CommandLine::info('Message Received: ' . $message);
});
