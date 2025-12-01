<?php

namespace app\controllers;

use app\models\Task;
use yii\rest\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;
use yii\caching\TagDependency;

class TaskController extends Controller
{
    /**
     * API GET
     * @param  string  $title    Наименование задачи
     * @param  string  $status   Статус задачи
     * @return mixed
     */
    public function actionGet($title = '', $status = '')
    {
        $cacheTime = 120; //время сохранения в кэш (секунды)
        $result = [];
        $query  = Task::find()->andWhere(['delete' => false]);

        if ($title || $status) {
            $keyTwoFilter = 'taskCache' . $title . $status;
            $result = \Yii::$app->cache->getOrSet($keyTwoFilter, function () use ($query, $title, $status) {
                return $query->andFilterWhere(["title" => $title])
                      ->andFilterWhere(["status" => $status])
                      ->all();
            }, $cacheTime, new TagDependency(['tags' => 'db_task_cache']));
        }

        if (empty($title) && empty($status)) {
            $keyAll = 'taskCache_all';
            $result = \Yii::$app->cache->getOrSet($keyAll, function () use ($query) {
                return $query->all();
            }, $cacheTime, new TagDependency(['tags' => 'db_task_cache']));
        }

        return $result;
    }

    /**
     * API POST
     * @throws Exception
     */
    public function actionCreate()
    {
        $model = new Task();
        $postData = Yii::$app->request->post();
        if (!$model->load($postData, '')) {
            throw new \Exception("Ошибка загрузки входных параметров");
        }

        if (!$model->validate()) {
            throw new \Exception(Json::encode($model->getErrors()));
        }

        if (!$model->save()) {
            throw new \Exception(Json::encode($model->getErrors()));
        }

        // чистим кэш
        TagDependency::invalidate(\Yii::$app->cache, 'db_task_cache');
        return $model;
    }

    /**
     * API PUT
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        if (($model = Task::findOne($id)) == null) {
            throw new \Exception(Yii::t('app', "Задача с идентификатором {0} не найдена.", $id));
        }

        $params = Yii::$app->request->getBodyParams();

        if (!$model->load($params, '')) {
            throw new \Exception("Ошибка загрузки входных параметров");
        }

        if (!$model->validate()) {
            throw new \Exception(Json::encode($model->getErrors()));
        }

        $model->updated_at = date('Y-m-d H:i:s');
        if (!$model->save()) {
            throw new \Exception(Json::encode($model->getErrors()));
        }

        // чистим кэш
        TagDependency::invalidate(\Yii::$app->cache, 'db_task_cache');

        return $model;
    }

    /**
     * API DELETE
     * @throws Exception
     */
    public function actionDelete($id)
    {
        if (($model = Task::findOne($id)) == null) {
            throw new \Exception(Yii::t('app', "Задача с идентификатором {0} не найдена.", $id));
        }

        $model->updated_at = date('Y-m-d H:i:s');
        $model->delete = true;
        if (!$model->save()) {
            throw new \Exception(Json::encode($model->getErrors()));
        }

        // чистим кэш
        TagDependency::invalidate(\Yii::$app->cache, 'db_task_cache');

        return $model;
    }
}
