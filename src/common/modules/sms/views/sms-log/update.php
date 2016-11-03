<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\modules\sms\models\SmsLog */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Sms Log',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sms Logs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="sms-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
