<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    public function seeInRabbitMQQueue($rabbitmq, $queue, $message): void
    {
        $messages = [];
        try {
            $rabbitmq->receive($queue, function ($msg) use (&$messages) {
                $messages[] = $msg->getBody();
                $msg->nack();
            }, 1);
        } catch (Throwable) {}
        // Проверяем, содержит ли очередь заданное сообщение
        $this->assertTrue(in_array($message, $messages), "Message '$message' not found in the '$queue' queue.");
    }

    public function dontSeeInRabbitMQQueue($rabbitmq, $queue, $message): void
    {
        $messages = [];
        try {
            $rabbitmq->receive($queue, function ($msg) use (&$messages) {
                $messages[] = $msg->getBody();
                $msg->ack();
            }, 1);
        } catch (Throwable) {}

        // Проверяем, содержит ли очередь заданное сообщение
        $this->assertFalse(in_array($message, $messages), "Message '$message' not found in the '$queue' queue.");
    }

}
