<?php

use app\models\Vehicle;
use kartik\date\DatePicker;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Reservation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reservation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'from')->widget(DateTimePicker::class, [
        'options' => ['placeholder' => 'Startdatum auswählen'],
        'pluginOptions' => [
            'autoclose' => true
        ]
    ]) ?>

    <?= $form->field($model, 'until')->widget(DateTimePicker::class, [
        'options' => [
            'placeholder' => 'Enddatum auswählen'
        ],
        'pluginOptions' => [
            'autoclose' => true,
        ]
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
