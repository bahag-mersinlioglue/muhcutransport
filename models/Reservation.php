<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reservation".
 *
 * @property int $id
 * @property string $customer
 * @property string $request_date
 * @property string $start_time
 * @property string $from
 * @property string $until
 * @property string $location
 * @property int $thermo
 * @property int $vehicle_id
 * @property int $customer_id
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
            [['request_date', 'vehicle_id'], 'required'],
            [['request_date', 'start_time', 'location', 'customer'], 'safe'],
            [['request_date'], 'date', 'format' => 'php:Y-m-d'],
            [['start_time'], 'date', 'format' => 'php:H:i'],

            [['vehicle_id', 'thermo', 'customer_id'], 'integer'],
            [['vehicle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehicle::class, 'targetAttribute' => ['vehicle_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer' => 'Kunde',
            'customer_id' => 'Kunde-ID',
            'request_date' => 'Datum',
            'start_time' => 'Startzeit',
            'location' => 'Ort',
            'thermo' => 'Thermo',
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

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Customer::class, ['id' => 'customer_id']);
    }
}
