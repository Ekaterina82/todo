<?php

use yii\db\Migration;

class m251129_090655_insert_task_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(\app\models\Task::tableName(), [
            'title' => 'Сделать метод GET',
            'note' => null,
            'status' => \app\models\Task::STATUS_IN_PROGRESS,
        ]);

        $this->insert(\app\models\Task::tableName(), [
            'title' => 'Сделать метод POST',
            'note' => null,
            'status' => \app\models\Task::STATUS_NEW,
        ]);

        $this->insert(\app\models\Task::tableName(), [
            'title' => 'Сделать метод UPDATE',
            'note' => null,
            'status' => \app\models\Task::STATUS_NEW,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251129_090655_insert_task_table cannot be reverted.\n";

        return false;
    }
}
