<?php

namespace app\controllers;

use app\models\Customer;
use app\models\Employee;
use app\models\Reservation;
use app\models\Vehicle;
use app\models\VehicleType;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $year = date('Y');
        $week = date('W');
        $firstWeekDay = (new \DateTime())->setISODate($year, $week);
        $lastWeekDay = (new \DateTime())->setISODate($year, $week, 7);

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($firstWeekDay, $interval, $lastWeekDay);

        $reservations = [];
        foreach ($period as $dt) {
            /** @var Vehicle $vehicle */
            foreach (Vehicle::find()->all() as $vehicle) {
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

        return $this->render('index', [
            'reservations' => $reservations,
            'period' => $period,
        ]);
    }

    public function actionSave() {
        $model = new Reservation();
        $request = \Yii::$app->getRequest();

        $data = Yii::$app->request->post('Reservation');
        $requestDate = $data['request_date'];
        $vehicleId = $data['vehicle_id'];
        $customerName = $data['customer_name'];

        if ($found = Reservation::findOne([
            'request_date' => $requestDate,
            'vehicle_id' => $vehicleId,
        ])) {
            $model = $found;
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
            }
            print_r($model->attributes);
            print_r($customer->attributes);
            return ['success' => $model->save()];
        } else {
            return ['error' => $model->getErrors()];
        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
