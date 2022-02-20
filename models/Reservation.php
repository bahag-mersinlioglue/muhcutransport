<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reservation".
 *
 * @property int $id
 * @property string $customer_name
 * @property string $request_date
 * @property string $start_time
 * @property string $from
 * @property string $until
 * @property string $location
 * @property int $thermo
 * @property int $vehicle_id
 * @property int $customer_id
 * @property int $driver_id
 *
 * @property Vehicle $vehicle
 * @property Employee $driver
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
            [['request_date', 'start_time', 'location', 'customer_name'], 'safe'],
            [['request_date'], 'date', 'format' => 'php:Y-m-d'],
            [['start_time'], 'date', 'format' => 'php:H:i'],

            [['vehicle_id', 'thermo', 'customer_id', 'driver_id'], 'integer'],
            [['vehicle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehicle::class, 'targetAttribute' => ['vehicle_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Customer::class, 'targetAttribute' => ['customer_id' => 'id']],
            [['driver_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['driver_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_name' => 'Kunde',
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

    /**
     * Gets query for [[Driver]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDriver()
    {
        return $this->hasOne(Employee::class, ['id' => 'driver_id']);
    }

    public function getDriverName() {
        if ($this->driver_id) {
            $driver = $this->driver;
        } else {
            $driver = $this->vehicle->employee;
        }
        return $driver ? $driver->getFullName() : 'Kein Fahrer';
    }

    public function getTileOverviewClass() {
//        Rot -> Fest verplant
//        Gelb -> Kundenname existiert && Adresse fehlt (Reserviert)
//        Grün -> Kundenname & Adresse fehlen

        $class = 'green';
//        if (empty($this->customer_id) && empty($this->location)) {
//            $class = 'green';
//        }
        if ($this->customer_id && empty($this->location)) {
            $class = 'yellow';
        }
        if ($this->customer_id && $this->location) {
            $class = 'red';
        }
        return $class;
    }
}
