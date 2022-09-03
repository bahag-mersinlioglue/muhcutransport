<?php /** @var Reservation $reservation */

use app\models\Reservation; ?>
<?php // echo $reservation->request_date ?>
<?php foreach ($reservations as $reservation): ?>
######
<?= $reservation->getDriverName() . PHP_EOL ?>
<?= $reservation->customer ? $reservation->customer->company_name : '--Firma' ?>
<?= $reservation->location ? $reservation->location : '--Ort' ?>
<?php if($reservation->start_time): ?>
<?= $reservation->start_time ?> Uhr
<?php endif; ?>
<?= $reservation->vehicle->license_plate ?>

<?php endforeach; ?>