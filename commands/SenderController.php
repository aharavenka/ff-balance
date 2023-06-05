<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class SenderController extends Controller
{
    public function actionRun()
    {
        $operations = ['debit', 'credit', 'transfer'];
        $queueMappings = [
            'debit' => 'debit_queue',
            'credit' => 'credit_queue',
            'transfer' => 'transfer_queue',
        ];
        $i = 0;
        $dateId = (new \DateTime())->format('Ymd') . '_';

        while (true) {
            $operation = $operations[array_rand($operations)];
            $amount = mt_rand(10, 1000) / 100;

            $queueName = $queueMappings[$operation];

            $payload = [
                'operation_id' => $dateId . ++$i,
                'operation' => $operation,
                'amount' => $amount,
            ];

            if ($operation === 'transfer') {
                $senderUserId = mt_rand(1, 10);
                $receiverUserId = mt_rand(1, 10);

                $payload['sender_user_id'] = $senderUserId;
                $payload['receiver_user_id'] = $receiverUserId;
            } else {
                $userId = mt_rand(1, 10);
                $payload['user_id'] = $userId;
            }

            Yii::$app->rabbitmq->send($queueName, json_encode($payload));

            usleep($_ENV['RABBITMQ_CONSUMER_PAUSE']);
        }
    }
}
