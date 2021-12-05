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
            ['start_date', 'validateDates'],

            [['vehicle_id'], 'integer'],
            [['vehicle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehicle::className(), 'targetAttribute' => ['vehicle_id' => 'id']],
        ];
    }

    public function validateDates()
    {
        Reservation::find()
            ->joinWith("vehicle")
            ->where(['vehicle_id' => $this->vehicle_id])
            ->andWhere(['between', 'from', $this->from, $this->until])
            ->andWhere(['between', 'until', $this->from, $this->until])
            ->count();
        if (strtotime($this->end_date) <= strtotime($this->start_date)) {
            $this->addError('from', 'Please give correct Start and End dates');
            $this->addError('until', 'Please give correct Start and End dates');
        }
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
