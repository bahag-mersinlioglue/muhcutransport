<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reservation".
 *
 * @property int $id
 * @property string $from
 * @property string $until
 * @property string $location
 * @property int $vehicle_id
 *
 * @property Vehicle $vehicle
 */
class Reservation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reservation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from', 'until', 'vehicle_id'], 'required'],
            [['from', 'until', 'location'], 'safe'],
            [['from', 'until',], 'date', 'format' => 'php:Y-m-d H:i'],
            ['from', 'compare', 'compareAttribute' => 'until', 'operator' => '<', 'enableClientValidation' => true],
            ['from', 'validateFromDate'],
            ['until', 'validateUntilDate'],

            [['vehicle_id'], 'integer'],
            [['vehicle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehicle::className(), 'targetAttribute' => ['vehicle_id' => 'id']],
        ];
    }

    public function validateFromDate()
    {
        $q1 = self::find()
            ->joinWith("vehicle")
            ->where(['vehicle_id' => $this->vehicle_id])
            ->andWhere(['between', 'from', $this->from, $this->until])
        ;
        if ($q1->count()) {
            /** @var Reservation $reservation */
            foreach ($q1->all() as $reservation) {
                $this->addError('from', $this->formatDateValidationMessage($reservation->from, $reservation->until));
            }
        }
    }

    public function validateUntilDate() {
        $q2 = self::find()
            ->joinWith("vehicle")
            ->where(['vehicle_id' => $this->vehicle_id])
            ->andWhere(['between', 'until', $this->from, $this->until]);
        if ($q2->count()) {
            /** @var Reservation $reservation */
            foreach ($q2->all() as $reservation) {
                $this->addError('until', $this->formatDateValidationMessage($reservation->from, $reservation->until));
            }
        }
    }

    private function formatDateValidationMessage($d1, $d2) {
        return 'Reserviert: vom <b>' .Yii::$app->formatter->asDatetime($d1) . '</b> bis <b>' . Yii::$app->formatter->asDatetime($d2) . '</b>';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from' => 'Von',
            'until' => 'Bis',
            'location' => 'Ort',
            'vehicle_id' => 'Fahrzeug',
        ];
    }

    /**
     * Gets query for [[Vehicle]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehicle()
    {
        return $this->hasOne(Vehicle::className(), ['id' => 'vehicle_id']);
    }
}
