<?php /** @var Reservation $reservation */

use app\models\Reservation; ?>
<?php // echo $reservation->request_date ?>
<?php foreach ($reservations as $reservation): ?>
######
<?= $reservation->getDriverName() ?><br/>
<?= $reservation->customer ? $reservation->customer->company_name : '--Firma' ?> <br/>
<?= $reservation->location ? $reservation->location : '--Ort' ?> <br/>
<?php if($reservation->start_time): ?>
<?= $reservation->start_time ?> Uhr<br/>
<?php endif; ?>
<?= $reservation->vehicle->license_plate ?> <br/>
<br/>
<?php endforeach; ?>