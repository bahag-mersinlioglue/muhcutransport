<?php

use app\models\Vehicle;
use kartik\date\DatePicker;
use kartik\datecontrol\DateControl;
use kartik\time\TimePicker;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Reservation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reservation-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'errorOptions' => [
                'encode' => false,
                'class' => 'help-block'
            ],
        ],
    ]); ?>

    <?= $form->field($model, 'request_date')->widget(DateControl::class, [
        'type' => DateControl::FORMAT_DATE,
        'options' => ['placeholder' => 'Tag auswählen',],
//        'pluginOptions' => [
//            'autoclose' => true
//        ]
    ]) ?>

    <?= $form->field($model, 'start_time')->widget(DateControl::class, [
        'type' => DateControl::FORMAT_TIME,
        'options' => [
            'placeholder' => 'Startzeit auswählen'
        ],
//        'pluginOptions' => [
//            'autoclose' => true,
//        ]
    ]) ?>

    <?= $form->field($model, 'location')->textInput() ?>

    <?= $form->field($model, 'vehicle_id')->dropDownList(
        ArrayHelper::map(Vehicle::find()->all(), "id", "license_plate"),
        ['prompt' => 'Fahrzeug auswählen']
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
