<?php

namespace app\commands;

use app\models\services\BalanceService;
use Yii;
use yii\console\Controller;

class WorkerController extends Controller
{
    private BalanceService $balanceService;

    public function __construct($id, $module, BalanceService $balanceService, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->balanceService = $balanceService;
    }

    public function actionRun(string $queueName): void
    {
        Yii::$app->rabbitmq->receive($queueName, function ($msg) {
            $payload = json_decode($msg->body, true);

            [$result, $message] = $this->processOperation($payload);

            // генерация события на основе result и message - здесь возвращаем в очередь результат сообщения
            $payload['result'] = $result;
            $payload['message'] = $message;
            Yii::$app->rabbitmq->send($_ENV['RABBITMQ_RESULT_QUEUE'], json_encode($payload));

            // Освобождаем ресурсы после каждой итерации
            unset($payload, $result, $message);

            $msg->ack();
        });
    }

    private function processOperation(array $payload): array
    {
        $result = 'exception';
        $message = 'Operation not found';

        switch ($payload['operation']) {
            case 'debit':
                [$result, $message] = $this->balanceService->debit($payload['user_id'], $payload['amount']);
                break;
            case 'credit':
                [$result, $message] = $this->balanceService->credit($payload['user_id'], $payload['amount']);
                break;
            case 'transfer':
                [$result, $message] = $this->balanceService->transfer($payload['sender_user_id'], $payload['receiver_user_id'], $payload['amount']);
                break;
        }

        return [$result, $message];
    }

}
