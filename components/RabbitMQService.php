<?php

namespace app\components;

use Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use yii\base\Component;

class RabbitMQService extends Component
{
    public string $host;
    public string $port;
    public string $username;
    public string $password;

    private AMQPStreamConnection $connection;

    /**
     * @throws Exception
     */
    public function init(): void
    {
        parent::init();

        $this->connection = new AMQPStreamConnection(
            $this->host,
            $this->port,
            $this->username,
            $this->password
        );
    }

    public function send(string $queue, string $message): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $msg = new AMQPMessage($message);
        $channel->basic_publish($msg, '', $queue);
        $channel->close();
    }

    public function receive(string $queue, callable $callback, int $timeout = 0): void
    {
        $channel = $this->connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->basic_qos(0, 1, 0);
        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait(timeout: $timeout);
        }

        $channel->close();
    }

    /**
     * @throws Exception
     */
    public function __destruct()
    {
        $this->connection->close();
    }
}
