<?php

namespace app\commands;

use app\models\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\BaseConsole;

class InitController extends Controller
{
    /**
     * @throws Exception
     * @throws InvalidRouteException|InvalidConfigException
     */
    public function actionIndex(): void
    {
        $this->actionMigrate();
        $this->actionGenerateUsers();
        $this->actionSetPermissions();
        $this->actionUpdateEnvFile();

        $this->stdout("Initialization completed successfully.\n", BaseConsole::FG_GREEN);
    }

    /**
     * @throws Exception
     * @throws InvalidRouteException|InvalidConfigException
     */
    public function actionMigrate(): void
    {
        \Yii::$app->runAction('migrate', ['migrationPath' => '@app/migrations/', 'interactive' => 0]);
        $this->stdout("Migrations applied successfully.\n", BaseConsole::FG_GREEN);
        $db = Yii::$app->db;
        Yii::$app->set('db', Yii::$app->db_test);
        \Yii::$app->runAction('migrate', ['migrationPath' => '@app/migrations/', 'interactive' => 0]);
        $this->stdout("Migrations for test DB applied successfully.\n", BaseConsole::FG_GREEN);
        Yii::$app->set('db', $db);
    }

    public function actionUpdateEnvFile(): void
    {
        $envFilePath = Yii::getAlias('@app/.env');
        $content = file_get_contents($envFilePath);

        if ($content !== false) {
            $lines = explode("\n", $content);
            if (isset($lines[0])) {
                $lines[0] = 'DB_HOST=mysql';
                $updatedContent = implode("\n", $lines);
                file_put_contents($envFilePath, $updatedContent);
                $this->stdout(".env file updated successfully.\n", BaseConsole::FG_GREEN);
                return;
            }
        }

        $this->stdout("Failed to update .env file.\n", BaseConsole::FG_RED);
    }

    public function actionGenerateUsers(): void
    {
        $this->stdout("Generating users...\n");

        $usersCount = User::find()->count();
        if ($usersCount > 0) {
            $this->stdout("Users already exist. Skipping user generation.\n", BaseConsole::FG_YELLOW);
            return;
        }

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->balance = mt_rand(100, 1000);
            $user->save();
        }

        $this->stdout("Users generated successfully.\n", BaseConsole::FG_GREEN);
    }

    public function actionSetPermissions(): void
    {
        $runtimePath = Yii::getAlias('@app/runtime');
        $webAssetsPath = Yii::getAlias('@app/web/assets');

        if (!file_exists($runtimePath)) {
            mkdir($runtimePath, 0777, true);
            $this->stdout("Runtime directory created successfully.\n", BaseConsole::FG_GREEN);
        }

        if (!file_exists($webAssetsPath)) {
            mkdir($webAssetsPath, 0777, true);
            $this->stdout("Web assets directory created successfully.\n", BaseConsole::FG_GREEN);
        }

        $this->runCommand('chmod', ['-R', '777', $runtimePath]);
        $this->runCommand('chmod', ['-R', '777', $webAssetsPath]);

        $this->stdout("Permissions set successfully.\n", BaseConsole::FG_GREEN);
    }

    private function runCommand(string $command, array $arguments = []): void
    {
        $arguments = array_map('escapeshellarg', $arguments);
        $command = $command . ' ' . implode(' ', $arguments);

        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

    }
}