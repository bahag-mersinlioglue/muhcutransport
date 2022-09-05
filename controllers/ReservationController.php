<?php

namespace app\controllers;

use app\models\Customer;
use app\models\Reservation;
use app\models\ReservationSearch;
use app\models\Vehicle;
use app\models\VehicleSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ReservationController implements the CRUD actions for Reservation model.
 */
class ReservationController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Reservation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReservationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Reservation model.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Reservation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Reservation();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Reservation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Reservation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionOverview()
    {
        $date = new \DateTime();
        $selectedDate = intval(Yii::$app->getRequest()->getQueryParam('date'));
        if ($selectedDate > 0) {
            $selectedDate += (60*60*24);
        }
        if (!empty($selectedDate)) {
            $date = new \DateTime('@' . $selectedDate);
        }

        $year = $date->format('Y');
        $week = $date->format('W');

        $firstWeekDay = (new \DateTime())->setISODate($year, $week);
        $lastWeekDay = (new \DateTime())->setISODate($year, $week, 7);

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($firstWeekDay, $interval, $lastWeekDay);

        $reservations = [];
        foreach ($period as $dt) {
            /** @var Vehicle $vehicle */
            foreach (VehicleSearch::findAllNotDeleted() as $vehicle) {
                $reservation = Reservation::findOne(['vehicle_id' => $vehicle->id, 'request_date' => $dt->format('Y-m-d')]);
                if (!$reservation) {
                    $reservation = new Reservation();
                    $reservation->request_date = $dt->format('Y-m-d');
                    $reservation->vehicle_id = $vehicle->id;
                } else {
                    if ($reservation->customer) {
                        $reservation->customer_name = $reservation->customer->company_name;
                    }
                }
                $reservations[$vehicle->vehicleType->id][$vehicle->id][$dt->format('Y-m-d')] = $reservation;
            }
        }

        return $this->render('overview', [
            'reservations' => $reservations,
            'period' => $period,
            'week' => $week,
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionOverviewTile()
    {
        $date = new \DateTime();
        $selectedDate = intval(Yii::$app->getRequest()->getQueryParam('date'));
        if ($selectedDate > 0) {
            $selectedDate += (60*60*24);
        }
        if (!empty($selectedDate)) {
            $date = new \DateTime('@' . $selectedDate);
        }

        $year = $date->format('Y');
        $week = $date->format('W');

        $firstWeekDay = (new \DateTime())->setISODate($year, $week);
        $lastWeekDay = (new \DateTime())->setISODate($year, $week, 7);

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($firstWeekDay, $interval, $lastWeekDay);

        $reservations = [];
        foreach ($period as $dt) {
            /** @var Vehicle $vehicle */
            foreach (VehicleSearch::findAllNotDeleted() as $vehicle) {
                $reservation = Reservation::findOne(['vehicle_id' => $vehicle->id, 'request_date' => $dt->format('Y-m-d')]);
                if (!$reservation) {
                    $reservation = new Reservation();
                    $reservation->request_date = $dt->format('Y-m-d');
                    $reservation->vehicle_id = $vehicle->id;
                } else {
                    if ($reservation->customer) {
                        $reservation->customer_name = $reservation->customer->company_name;
                    }
                }
                $reservations[$vehicle->vehicleType->id][$vehicle->id][$dt->format('Y-m-d')] = $reservation;
            }
        }

        return $this->render('overview-tile', [
            'reservations' => $reservations,
            'period' => $period,
            'week' => $week,
        ]);
    }

    public function actionSave()
    {
        $request = \Yii::$app->getRequest();

        $data = Yii::$app->request->post('Reservation');
        $requestDate = $data['request_date'];
        $vehicleId = $data['vehicle_id'];
        $customerName = $data['customer_name'];

        $model = Reservation::findOne([
            'request_date' => $requestDate,
            'vehicle_id' => $vehicleId,
        ]);
        if (!$model) {
            $model = new Reservation();
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load($request->post())) {
            if (strlen($customerName)) {
                $customer = Customer::findOne(['company_name' => $customerName]);
                if (!$customer) {
                    $customer = new Customer();
                    $customer->company_name = $customerName;
                    $customer->save();
                }
                $model->customer_id = $customer->id;
            } else {
                $model->customer_id = null;
            }
            return ['success' => $model->save()];
        } else {
            return ['error' => $model->getErrors()];
        }
    }

    /**
     * @return string
     */
    public function actionDaySummary($date)
    {
        $reservations = Reservation::find()
            ->joinWith(['vehicle'])
            ->where(['request_date' => $date])
            ->orderBy([Vehicle::tableName() . '.license_plate' => SORT_ASC])
            ->all();

        $result = '';
        $lineEnding = PHP_EOL;
        foreach ($reservations as $reservation) {
            $result .= "######" . $lineEnding;
            $result .= $reservation->getDriverName() . $lineEnding;
            $result .= ($reservation->customer ? $reservation->customer->company_name : '--Firma') . $lineEnding;
            $result .= ($reservation->location ? $reservation->location : '--Ort') . $lineEnding;
            if ($reservation->start_time) {
                $result .= $reservation->start_time . ' Uhr' . $lineEnding;
            }
            $result .= $reservation->vehicle->license_plate . $lineEnding . $lineEnding;
        }

        return $result;
    }

    /**
     * Finds the Reservation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Reservation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Reservation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
