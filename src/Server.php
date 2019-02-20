<?php //-->
/**
 * This file is part of a package designed for the CradlePHP Project.
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Cradle\WebSocket;

use Exception;
use SplObjectStorage;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

/**
 * Web Socket Message
 *
 * @vendor   Cradle
 * @package  WebSocket
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Server implements MessageComponentInterface
{
    /**
     * @var SplObjectStorage $clients
     */
    protected $clients = null;

    /**
     * @var FrameworkHandler $cradle
     */
    protected $cradle = null;

    /**
     * Sets the Client Spool
     */
    public function __construct()
    {
        $this->clients = new SplObjectStorage;
        $this->cradle = cradle();
    }

    /**
     * What to do when a connection is open
     *
     * @param *ConnectionInterface $conn
     *
     * @return Message
     */
    public function onOpen(ConnectionInterface $connection)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($connection);

        $this->cradle->log(sprintf('New Connection (%s)', $connection->resourceId));

        return $this;
    }

    /**
     * What to do when a connection is open
     *
     * @param *ConnectionInterface $from
     * @param *string              $msg
     *
     * @return Message
     */
    public function onMessage(ConnectionInterface $from, $message)
    {
        $count = count($this->clients) - 1;
        $this->cradle->log(sprintf(
            'Connection %d sending message "%s" to %d other connections',
            $from->resourceId,
            $message,
            $count
        ));

        foreach ($this->clients as $client) {
            if ($from === $client) {
                continue;
            }

            // The sender is not the receiver, send to each client connected
            $client->send($message);
        }

        return $this;
    }

    /**
     * What to do when a connection is closed
     *
     * @param *ConnectionInterface $conn
     *
     * @return Message
     */
    public function onClose(ConnectionInterface $connection)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($connection);

        $this->cradle->log(sprintf('Disconnection (%s)', $connection->resourceId));

        return $this;
    }

    /**
     * What to do when there is an error
     *
     * @param *ConnectionInterface $conn
     *
     * @return Message
     */
    public function onError(ConnectionInterface $connection, Exception $e)
    {
        $this->cradle->log(sprintf(
            'An error has occurred: (%s)',
            $e->getMessage()
        ));

        $connection->close();

        return $this;
    }
}
