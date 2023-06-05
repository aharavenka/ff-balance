<?php

use app\models\services\BalanceService;
use app\models\User;
use yii\db\StaleObjectException;

class BalanceServiceCest
{
    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function testCredit(FunctionalTester $I): void
    {
        $balanceService = new BalanceService();

        $user = new User();
        $user->balance = 100;
        $user->save();

        $userId = $user->id;
        $amount = 50;
        $result = $balanceService->credit($userId, $amount);

        $I->assertEquals('success', $result[0]);
        $I->assertEmpty($result[1]);

        $user = User::findOne($userId);
        $I->assertEquals(50, $user->balance);

        $user->delete();
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function testDebit(FunctionalTester $I): void
    {
        $balanceService = new BalanceService();

        $user = new User();
        $user->balance = 50;
        $user->save();

        $userId = $user->id;
        $amount = 25;
        $result = $balanceService->debit($userId, $amount);

        $I->assertEquals('success', $result[0]);
        $I->assertEmpty($result[1]);

        $user = User::findOne($userId);
        $I->assertEquals(75, $user->balance);

        $user->delete();
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function testTransfer(FunctionalTester $I): void
    {
        $balanceService = new BalanceService();

        $fromUser = new User();
        $fromUser->balance = 100;
        $fromUser->save();

        $toUser = new User();
        $toUser->balance = 50;
        $toUser->save();

        $fromUserId = $fromUser->id;
        $toUserId = $toUser->id;
        $amount = 50;
        $result = $balanceService->transfer($fromUserId, $toUserId, $amount);

        $I->assertEquals('success', $result[0]);
        $I->assertEmpty($result[1]);

        $fromUser = User::findOne($fromUserId);
        $toUser = User::findOne($toUserId);
        $I->assertEquals(50, $fromUser->balance);
        $I->assertEquals(100, $toUser->balance);

        $fromUser->delete();
        $toUser->delete();
    }
}
