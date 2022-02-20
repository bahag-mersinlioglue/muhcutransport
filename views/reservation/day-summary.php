<?php /** @var Reservation $reservation */

use app\models\Reservation; ?>
<?php foreach ($reservations as $reservation): ?>
    ###### <?= $reservation->request_date ?> <br/>
    <?= $reservation->getDriverName() ?><br/>
    <?= $reservation->customer ? $reservation->customer->company_name : '--Firma' ?> <br/>
    <?php if($reservation->start_time): ?>
        <?= $reservation->start_time ?> <br/>
    <?php endif; ?>
    <?= $reservation->location ? $reservation->location : '--Ort' ?> <br/>
    <?= $reservation->vehicle->license_plate ?> <br/>
    <br/>
<?php endforeach; ?>