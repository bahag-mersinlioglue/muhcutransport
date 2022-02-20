<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehicle".
 *
 * @property int $id
 * @property string $license_plate
 * @property int|null $employee_id
 * @property int|null $vehicle_type_id
 *
 * @property Employee $employee
 * @property VehicleType $vehicleType
 */
class Vehicle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehicle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['license_plate'], 'required'],
            [['employee_id'], 'integer'],
            [['license_plate'], 'string', 'max' => 50],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['vehicle_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => VehicleType::class, 'targetAttribute' => ['vehicle_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'license_plate' => 'Kennzeichen',
            'employee_id' => 'Mitarbeiter',
            'vehicle_type_id' => 'Fahrzeug-Typ',
        ];
    }

    /**
     * Gets query for [[Employee]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * Gets query for [[VehicleType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleType()
    {
        return $this->hasOne(VehicleType::class, ['id' => 'vehicle_type_id']);
    }

    public function getDriverName() {
        return $this->employee ? $this->employee->getFullName() : '-';
    }
}
