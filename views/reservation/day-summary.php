<?php /** @var Reservation $reservation */

use app\models\Reservation; ?>
<?php foreach ($reservations as $reservation): ?>
    ###### <?= $reservation->request_date ?> <br/>
    @--Fahrername--<br/>
    <?= $reservation->customer ? $reservation->customer->company_name : '--Firma' ?> <br/>
    <?= $reservation->start_time ? $reservation->start_time : '--Startzeit' ?> <br/>
    <?= $reservation->location ? $reservation->location : '--Ort' ?> <br/>
    <?= $reservation->vehicle->license_plate ?> <br/>
    <br/>
<?php endforeach; ?>