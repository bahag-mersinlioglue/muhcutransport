<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\VehicleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Vehicles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="vehicle-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Vehicle', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'license_plate',
            [
                'attribute' => 'employee_first_name',
                'value' => function ($model) {
                    return $model->employee ? $model->employee->first_name : '';
                }
            ],
            [
                'attribute' => 'employee_last_name',
                'value' => function ($model) {
                    return $model->employee ? $model->employee->last_name : '';
                }
            ],
            [
                'value' => function ($model) {
                    return $model->employee ? $model->employee->phonenumber : '';
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
