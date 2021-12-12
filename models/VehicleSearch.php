<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Vehicle;

/**
 * VehicleSearch represents the model behind the search form of `app\models\Vehicle`.
 */
class VehicleSearch extends Vehicle
{
    public $employee_first_name;
    public $employee_last_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'employee_id'], 'integer'],
            [['license_plate', 'employee_first_name', 'employee_last_name'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Vehicle::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['employee_first_name'] = [
            'asc' => ['employee.first_name' => SORT_ASC],
            'desc' => ['employee.first_name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->joinWith(['employee', 'vehicleType']);
        $query->andFilterWhere([
            'id' => $this->id,
//            'employee_id' => $this->employee_id,
        ]);

        $query->andFilterWhere(['like', 'license_plate', $this->license_plate]);
        $query->andFilterWhere(['like', 'employee.first_name', $this->employee_first_name]);
        $query->andFilterWhere(['like', 'employee.last_name', $this->employee_last_name]);

        return $dataProvider;
    }
}
