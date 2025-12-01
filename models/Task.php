<?php

namespace app\models;

use yii\db\ActiveRecord;

class Task extends ActiveRecord
{
    public const STATUS_NEW            = 'new';
    public const STATUS_IN_PROGRESS    = 'in_progress';
    public const STATUS_COMPLETED      = 'completed';
    public const STATUS_FAILED         = 'failed';
    public const STATUS_CANCELED       = 'canceled';

    public const STATUS_LIST = [
        self::STATUS_NEW => 'New',
        self::STATUS_IN_PROGRESS => 'In progress',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_FAILED => 'Failed',
        self::STATUS_CANCELED => 'Canceled',
    ];
    public function rules(): array
    {
        return [
            [['title', 'status'], 'required'],
            [['title','note'], 'string'],
            ['status', 'in', 'range' => array_keys(self::STATUS_LIST),
                'message' => '"Статус" может принимать только одно из значений:' .
                                implode("; ", array_keys(self::STATUS_LIST))
            ],
            [['created_at', 'updated_at'],  'datetime', 'format' => 'php:Y-m-d H:i:s',
                'message' => 'Неверный формат даты. Надо Y-m-d H:i:s'],
        ];
    }

    public static function tableName()
    {
        return '{{task}}';
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'Идентификатор',
            'title' => 'Название задачи (уникальный)',
            'note' => 'Подробности по задаче',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата внесения изменений',
            'status' => 'Статус выполнения',
        ];
    }
}
