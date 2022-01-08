<?php

/* @var $this yii\web\View */

use app\models\Customer;
use app\models\Vehicle;
use app\models\VehicleType;
use kartik\date\DatePicker;
use kartik\datecontrol\DateControl;
use kartik\typeahead\TypeaheadBasic;
use kartik\widgets\DateTimePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $form yii\widgets\ActiveForm */

$this->title = 'Wochenplan';
?>
<style>
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
</style>
<?php

$this->registerJs("

    $('.thermo-control').click(function() {
        var form = $(this).parents('td');
        if (this.checked) {
            form.addClass('thermo');
        } else {
            form.removeClass('thermo');
        }
    });
    
    $('form').on('beforeSubmit', function(e) {
        var form = $(this);
        var formData = form.serialize();
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: formData,
            success: function (data) {
//                alert('Success');
            },
            error: function () {
//                alert('Error');
            }
        });
    })
    .on('submit', function(e){
        e.preventDefault();
    })
    .on('change', function() {
        $(this).find('.btn').click();
    })
    ;
"
);
?>
<div class="site-index">

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
                        console.log(e);
                        
                        location.href = 'index.php?r=site/index&date=' + (e.date.getTime() / 1000); 
                    }",
                ]
            ]);
            ?>
        </span>
        <?= Yii::$app->formatter->asDate($period->getStartDate()) ?>
        bis <?= Yii::$app->formatter->asDate($period->getEndDate()) ?>
    </h2>

    <table class="table table-sm table-bordered">

        <thead>
        <tr class="calendar-week-bg">
            <th>
                KW <?= $period->getStartDate()->format('W') ?>
            </th>
            <?php foreach ($period as $dt): ?>
                <th><?= $dt->format('Y-m-d') ?></th>
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
                        <td class="<?= $reservation->thermo ? 'thermo' : '' ?>">
                            <?php $form = ActiveForm::begin([
                                'action' => ['site/save'],
                                'enableClientValidation' => true,
                                'enableAjaxValidation' => false,
                            ]); ?>

                            <?= $form->field($reservation, 'id')->hiddenInput()->label(false) ?>
                            <?= $form->field($reservation, 'request_date')->hiddenInput()->label(false) ?>

                            <?= $form->field($reservation, 'customer_name')->widget(TypeaheadBasic::class, [
                                'data' => ArrayHelper::map(Customer::find()->all(), 'id', 'company_name'),
                                'options' => ['placeholder' => 'Kunde', 'id' => rand(0, 500)],
                                'pluginOptions' => ['highlight' => true],
                            ])->label(false) ?>

                            <?= $form->field($reservation, 'location')->textInput(['placeholder' => 'Ort'])->label(false) ?>

                            <?= $form->field($reservation, 'start_time')->widget(DateControl::class, [
                                'type' => DateControl::FORMAT_TIME,
                                'autoWidget' => false,
                                'widgetClass' => 'yii\widgets\MaskedInput',
                                'widgetOptions' => [
                                    'mask' => '99:99'
                                ],
                                'options' => [
                                    'id' => 'time-' . $reservation->request_date . '-' . $reservation->vehicle_id,
                                    'placeholder' => 'Startzeit auswählen'
                                ],
                            ])->label(false) ?>
                            <?php
                            //                            var_dump($reservation->start_time);
                            ?>

                            <?php
                            //                            $form->field($reservation, 'vehicle_id')->dropDownList(
                            //                                ArrayHelper::map($vehicleType->vehicles, "id", "license_plate"),
                            //                                ['prompt' => 'Fahrzeug auswählen']
                            //                            )->label(false)
                            ?>

                            <?= $form->field($reservation, 'vehicle_id')->hiddenInput()->label(false) ?>

                            <?= $form->field($reservation, 'thermo')->checkbox(['placeholder' => 'Thermo', 'class' => 'thermo-control'])->label(false) ?>

                            <div class="form-group hidden" style="display: none;">
                                <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>

    </table>


</div>
