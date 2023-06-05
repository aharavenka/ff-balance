<?php

use app\components\RabbitMQService;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQServiceCest
{
    const TEST_QUEUE = 'test-queue';
    const TEST_MESSAGE = 'Test message';
    public function testSend(FunctionalTester $I): void
    {
        $rabbitMQ = Yii::$app->rabbitmq;

        $rabbitMQ->send(self::TEST_QUEUE, self::TEST_MESSAGE);

        // Проверяем, что сообщение было отправлено успешно
        $I->seeInRabbitMQQueue($rabbitMQ, self::TEST_QUEUE, self::TEST_MESSAGE);
    }

    public function testReceive(FunctionalTester $I): void
    {
        $rabbitMQ = Yii::$app->rabbitmq;
        try {
            $rabbitMQ->receive(self::TEST_QUEUE, function ($msg) use (&$I) {
                $I->assertEquals(self::TEST_MESSAGE, $msg->getBody());
                $msg->ack();
            }, 1);
        } catch (Throwable) {}

        $I->dontSeeInRabbitMQQueue($rabbitMQ, self::TEST_QUEUE, self::TEST_MESSAGE);
    }
}
