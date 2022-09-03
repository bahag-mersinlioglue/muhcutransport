<?php

/* @var $this yii\web\View */

use app\models\Customer;
use app\models\Employee;
use app\models\EmployeeSearch;
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
</style>
<?php

$this->registerJs("
$('#close-clipboard').click(function(){
    $('#clipboard-wrapper').hide();
});
    $('.copy-summary').click(function(data) {
         var elm = $(this);
         elm.removeClass('fa-copy');
         elm.addClass('fa-spinner fa-spin');
         $.ajax({
            url: 'index.php?r=reservation/day-summary&date=' + $(this).data('date'),
            type: 'GET',
            success: function (data) {
            
//                console.log(navigator.userAgent);
//                navigator.userAgent.match(/ipad|iphone/i);
            
                $('#clipboard').val(data);
                $('#clipboard-wrapper').show();
                $('.clipboard-js-init').click();
            },
            error: function () {
                alert('Something went wrong');
            },
            complete: function() {
                elm.addClass('fa-copy');
                elm.removeClass('fa-spinner fa-spin');
            }
        });
    });

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
                alert('Something went wrong');
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
    
    $('.take-over-for-next-day').dblclick(function() {
//    $('.take-over-for-next-day').click(function() {
        var currentElm = $(this).closest('td');
        var nextElm = currentElm.next();
        
        var customerSelector = '[name=\"Reservation[customer_name]\"]';
        nextElm.find(customerSelector).val(currentElm.find(customerSelector).val());
    
        var locationSelector = '[name=\"Reservation[location]\"]';
        nextElm.find(locationSelector).val(currentElm.find(locationSelector).val());
    
        var startTimeSelector = '[name=\"Reservation[start_time]\"]';
        nextElm.find(startTimeSelector).val(currentElm.find(startTimeSelector).val());
        nextElm.find(startTimeSelector).prev().val(currentElm.find(startTimeSelector).val());
    
        var thermoSelector = '[name=\"Reservation[thermo]\"]';
        nextElm.find(thermoSelector).prop('checked', currentElm.find(thermoSelector).is(':checked'));
        if (currentElm.find(thermoSelector).is(':checked')) {
            nextElm.addClass('thermo');
        } else {
            nextElm.removeClass('thermo');
        }
    
        var driverSelector = '[name=\"Reservation[driver_id]\"]';
        nextElm.find(driverSelector).val(currentElm.find(driverSelector).val());
        
        nextElm.find('form').submit();
    });
"
);
?>

<div id="clipboard-wrapper" style="display: none; margin-bottom: 2rem; padding-bottom: 2rem;">
    <textarea id="clipboard" rows="10" cols="60">
        Example text
    </textarea>
    <?= \supplyhog\ClipboardJs\ClipboardJsWidget::widget([
        'inputId' => "#clipboard",
    ]) ?>
    <button class="btn btn-primary" id="close-clipboard">Schließen</button>
</div>

<div class="reservation-overview">

    <h2 class="text-center">

        <a href="<?= 'index.php?r=reservation/overview&date=' . $period->getStartDate()->modify('-1week')->getTimestamp() ?>">
            <i class="fa fa-arrow-left"></i>
        </a>

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

        <a href="<?= 'index.php?r=reservation/overview&date=' . $period->getStartDate()->modify('+1week')->getTimestamp() ?>">
            <i class="fa fa-arrow-right"></i>
        </a>
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
                    <i class="fa fa-copy copy-summary" data-date="<?= $dt->format('Y-m-d') ?>"></i>
                </th>
            <?php endforeach; ?>
            <th>
                Standard-Fahrer
            </th>
        </tr>
        </thead>

        <?php foreach ($reservations as $vehicleTypeId => $vehicleTypeGroup): ?>
            <?php $vehicleType = VehicleType::findOne($vehicleTypeId); ?>
            <tr class="vehicle-type-bg">
                <td colspan="8">
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
                                'action' => ['reservation/save'],
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
                                    'mask' => '99:99',
                                    'options' => [
                                        'class' => 'form-control',
                                    ],
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

                            <div class="row">
                                <div class="col">
                                    <?= $form->field($reservation, 'thermo')->checkbox(['placeholder' => 'Thermo', 'class' => 'thermo-control'])->label(false) ?>
                                </div>
                                <div class="col text-center">
                                    <i class="fa fa-arrow-circle-right take-over-for-next-day"></i>
                                </div>
                            </div>

                            <?= $form->field($reservation, 'driver_id')
                                ->dropDownList(
                                    ArrayHelper::map(EmployeeSearch::findAllNotDeleted(),'id','fullName'),
                                    ['placeholder' => 'Fahrer', 'prompt' => 'Fahrer']
                                )
                                ->label(false)
                            ?>

                            <div class="form-group hidden" style="display: none;">
                                <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                            </div>

                            <?php ActiveForm::end(); ?>
                        </td>
                    <?php endforeach; ?>
                    <td>
                        <?= $vehicle->getDriverName() ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>

    </table>



</div>
