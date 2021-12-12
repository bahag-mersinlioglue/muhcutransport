<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Reservation;

/**
 * ReservationSearch represents the model behind the search form of `app\models\Reservation`.
 */
class ReservationSearch extends Reservation
{
    public $license_plate;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'vehicle_id'], 'integer'],
            [['request_date', 'start_time', 'location', 'license_plate'], 'safe'],
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
        $query = Reservation::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['license_plate'] = [
            'asc' => ['vehicle.license_plate' => SORT_ASC],
            'desc' => ['vehicle.license_plate' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith(['vehicle']);

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'request_date' => $this->request_date,
            'start_time' => $this->start_time,
            'location' => $this->location,
            'vehicle.license_plate' => $this->license_plate,
        ]);

        return $dataProvider;
    }
}
