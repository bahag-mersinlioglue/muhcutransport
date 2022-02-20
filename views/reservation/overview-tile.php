<?php

/* @var $this yii\web\View */

use app\models\Customer;
use app\models\Employee;
use app\models\Vehicle;
use app\models\VehicleType;
use kartik\date\DatePicker;
use kartik\datecontrol\DateControl;
use kartik\typeahead\TypeaheadBasic;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $form yii\widgets\ActiveForm */

$this->title = 'Wochenplan';
?>
<style>

    .copy-summary {
        cursor: pointer;
    }

    .calendar-week-bg {
        background-color: #19A7B4;
        color: white;
        font-weight: bold;
    }

    .vehicle-type-bg {
        color: white;
        background-color: #767676;
        font-weight: bold;
    }

    .vehicle-bg {
        color: #495057;
        /*background-color: #e9ecef;*/
        font-weight: bold;
    }

    .form-group {
        margin-bottom: 0.2rem;
    }

    .container {
        max-width: 100%;
    }

    .table tbody tr:nth-child(2n+2) {
        background-color: #e9ecef !important;
    }

    .thermo {
        background-color: #fdfdcc;
    }

    td {
        border: 1px solid #cecece;
    }
    td.red {
        background-color: #ff6363;
    }
    td.yellow {
        background-color: #ffff00;
    }
    td.green {
        background-color: #e5ffc9;
    }
</style>
<?php

$this->registerJs("
"
);
?>
<div class="reservation-overview">

    <h2>
        <span style="display: inline-block;">
            <?php
            echo DatePicker::widget([
                'name' => 'dp_5',
                'type' => DatePicker::TYPE_BUTTON,
                'value' => $period->getStartDate()->format('Y-m-d'),
                'options' => [
                    'style' => 'float: left;'
                ],
                'pluginOptions' => [
                    'format' => 'yyyy-mm-dd',
                    'autoclose' => true,
                ],
                'pluginEvents' => [
                    "changeDate" => "function(e) {  
                        location.href = 'index.php?r=reservation/overview&date=' + (e.date.getTime() / 1000); 
                    }",
                ]
            ]);
            ?>
        </span>
        <small>
            <?= Yii::$app->formatter->asDate($period->getStartDate()) ?>
            bis <?= Yii::$app->formatter->asDate($period->getEndDate()) ?>
        </small>
    </h2>

    <table class="table table-sm table-bordered">

        <thead>
        <tr class="calendar-week-bg">
            <th>
                KW <?= $period->getStartDate()->format('W') ?>
            </th>
            <?php foreach ($period as $dt): ?>
                <th>
                    <?= $dt->format('d.m.Y') ?>
                </th>
            <?php endforeach; ?>
        </tr>
        </thead>

        <?php foreach ($reservations as $vehicleTypeId => $vehicleTypeGroup): ?>
            <?php $vehicleType = VehicleType::findOne($vehicleTypeId); ?>
            <tr class="vehicle-type-bg">
                <td colspan="7">
                    <?= $vehicleType->name ?>
                </td>
            </tr>
            <?php foreach ($vehicleTypeGroup as $vehicleId => $reservationGroup): ?>
                <?php $vehicle = Vehicle::findOne($vehicleId); ?>
                <tr style="<?= $vehicleId % 2 == 0 ? '' : '' ?>">
                    <td class="vehicle-bg">
                        <?= $vehicle->license_plate ?>
                    </td>
                    <?php foreach ($reservationGroup as $date => $reservation): ?>
                        <td style="min-height: 50px;" class="<?= $reservation->getTileOverviewClass() ?>">

                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>

    </table>


    <div style="height: 1px; overflow: hidden;">
        <div id="clipboard"></div>
        <?= \supplyhog\ClipboardJs\ClipboardJsWidget::widget([
            'inputId' => "#clipboard",
        ]) ?>
    </div>

</div>
