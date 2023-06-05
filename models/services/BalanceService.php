<?php

namespace app\models\services;

use app\models\User;
use Yii;

class BalanceService
{
    public function credit($userId, $amount): array
    {
        $transaction = Yii::$app->db->beginTransaction('SERIALIZABLE');
        try {
            $user = User::findOne($userId);
            if ($user->balance < $amount) {
                throw new \Exception('Insufficient funds');
            }
            $user->balance -= $amount;
            if (!$user->update()) {
                throw new \Exception('Failed to save user data');
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['error', $e->getMessage()];
        }

        return ['success', ''];
    }

    public function debit($userId, $amount): array
    {
        $transaction = Yii::$app->db->beginTransaction('SERIALIZABLE');
        try {
            $user = User::findOne($userId);
            $user->balance += $amount;
            if (!$user->update()) {
                throw new \Exception('Failed to save user data');
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['error', $e->getMessage()];
        }

        return ['success', ''];
    }

    public function transfer($fromUserId, $toUserId, $amount): array
    {
        $transaction = Yii::$app->db->beginTransaction('SERIALIZABLE');
        try {
            $fromUser = User::findOne($fromUserId);
            $toUser = User::findOne($toUserId);
            if ($fromUser->balance < $amount) {
                throw new \Exception('Insufficient funds');
            }
            $fromUser->balance -= $amount;
            $toUser->balance += $amount;
            if (!$fromUser->update() || !$toUser->update()) {
                throw new \Exception('Failed to save user data');
            }
            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return ['error', $e->getMessage()];
        }

        return ['success', ''];
    }
}
