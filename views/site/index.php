<?php

/* @var $this yii\web\View */

use app\models\Vehicle;
use app\models\VehicleType;
use kartik\datecontrol\DateControl;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $form yii\widgets\ActiveForm */

$this->title = 'My Yii Application';
?>
<style>
    .calendar-week-bg {
        background-color: #19A7B4;
        color: white;
        font-weight: bold;
    }
    .vehicle-type-bg {
        color: #495057;
        /*color: white;*/
        background-color: #e9ecef;
        /*background-color: #ffffff;*/
        font-weight: bold;
    }
    .vehicle-bg {
        color: #495057;
        background-color: #e9ecef;
        font-weight: bold;
    }

    .form-group {
        margin-bottom: 0.2rem;
    }
    .container {
        max-width: 100%;
    }
</style>
<div class="site-index">

    <h2>Aktueller Wochenplan</h2>

    <table class="table table-sm table-bordered">

        <tr class="calendar-week-bg">
            <th>
                KW <?= $period->getStartDate()->format('W') ?>
            </th>
            <?php foreach ($period as $dt): ?>
                <th><?= $dt->format('Y-m-d') ?></th>
            <?php endforeach; ?>

        </tr>

        <?php foreach ($reservations as $vehicleTypeId => $vehicleTypeGroup): ?>
            <?php $vehicleType = VehicleType::findOne($vehicleTypeId); ?>
            <tr class="vehicle-type-bg">
                <td colspan="7">
                    <?= $vehicleType->name ?>
                </td>
            </tr>
            <?php foreach ($vehicleTypeGroup as $vehicleId => $reservationGroup): ?>
                <?php $vehicle = Vehicle::findOne($vehicleId); ?>
                <tr>
                    <td class="vehicle-bg">
                        <?= $vehicle->license_plate ?>
                    </td>
                    <?php foreach ($reservationGroup as $date => $reservation): ?>
                        <td>
                            <?php $form = ActiveForm::begin([]); ?>

                            <?= $form->field($reservation, 'request_date')->hiddenInput()->label(false) ?>

                            <?= $form->field($reservation, 'location')->textInput(['placeholder' => 'Ort'])->label(false) ?>

                            <?= $form->field($reservation, 'start_time')->widget(DateControl::class, [
                                'type' => DateControl::FORMAT_TIME,
                                'options' => [
                                    'placeholder' => 'Startzeit auswählen'
                                ],
                            ])->label(false) ?>

                            <?= $form->field($reservation, 'vehicle_id')->dropDownList(
                                ArrayHelper::map($vehicleType->vehicles, "id", "license_plate"),
                                ['prompt' => 'Fahrzeug auswählen']
                            )->label(false) ?>

                            <?= $form->field($reservation, 'thermo')->checkbox(['placeholder' => 'Thermo'])->label(false) ?>

                            <?php ActiveForm::end(); ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>

    </table>


</div>
