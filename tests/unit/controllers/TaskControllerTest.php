<?php

namespace tests\unit\controllers;

use Yii;
use yii\web\Application;
use yii\caching\ArrayCache;
use app\controllers\TaskController;
use app\models\Task;

class TaskControllerTest extends \PHPUnit\Framework\TestCase
{
    private $app;

    protected function setUp(): void
    {
        parent::setUp();

        $config = require __DIR__ . '/../../../config/test.php';

        // Добавляем компонент кэша для тестовой среды,
        // чтобы вызовы TagDependency::invalidate() не падали на null cache
        $config['components']['cache'] = [
            'class' => ArrayCache::class,
        ];

        // Create web application
        new Application($config);
        $this->app = Yii::$app;
    }

    public function testActionCreate()
    {
        $params = [
            "title" => "Тестовая задача для Create",
            "note" => "Тестовая заметка",
            "status" => "new"
        ];
        Yii::$app->request->setBodyParams($params);

        $controller = new TaskController('task', $this->app);
        $result = $controller->actionCreate();

        $this->assertInstanceOf(Task::class, $result);
        $this->assertNotEmpty($result->id);
        $this->assertEquals("Тестовая задача для Create", $result->title);

        // Очистка
        $controller->actionDelete($result->id);
    }

    public function testActionGet()
    {
        // Создаем задачу для теста
        $createParams = [
            "title" => "Тестовая задача для Get",
            "note" => "Тестовая заметка",
            "status" => "new"
        ];
        Yii::$app->request->setBodyParams($createParams);
        $controller = new TaskController('task', $this->app);
        $createdTask = $controller->actionCreate();

        // Тестируем получение задачи
        $result = $controller->actionGet('Тестовая задача для Get');

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertEquals("Тестовая задача для Get", $result[0]->title);

        // Очистка
        $controller->actionDelete($createdTask->id);
    }

    public function testActionUpdate()
    {
        // Создаем задачу для обновления
        $createParams = [
            "title" => "Задача для обновления",
            "note" => "Исходная заметка",
            "status" => "new"
        ];
        Yii::$app->request->setBodyParams($createParams);
        $controller = new TaskController('task', $this->app);
        $createdTask = $controller->actionCreate();
        $taskId = $createdTask->id;

        // Обновляем задачу
        $updateParams = [
            "note" => "Обновление тестовой задачи"
        ];
        Yii::$app->request->setBodyParams($updateParams);
        $result = $controller->actionUpdate($taskId);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals("Обновление тестовой задачи", $result->note);

        // Очистка
        $controller->actionDelete($taskId);
    }

    public function testActionDelete()
    {
        // Создаем задачу для удаления
        $createParams = [
            "title" => "Задача для удаления",
            "note" => "Тестовая заметка",
            "status" => "new"
        ];
        Yii::$app->request->setBodyParams($createParams);
        $controller = new TaskController('task', $this->app);
        $createdTask = $controller->actionCreate();
        $taskId = $createdTask->id;

        // Удаляем задачу (soft delete)
        $result = $controller->actionDelete($taskId);

        $this->assertInstanceOf(Task::class, $result);
        $this->assertEquals(1, $result->delete);
    }

    protected function tearDown(): void
    {
        if ($this->app !== null) {
            Yii::$app = null;
            $this->app = null;
        }
        parent::tearDown();
    }
}
