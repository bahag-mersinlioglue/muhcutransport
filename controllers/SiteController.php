<?php

namespace app\controllers;

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

//        die('id: '. print_r(Yii::$app->request->post('Reservation'),1));
        $id = Yii::$app->request->post('Reservation')['id'];
        $requestDate = Yii::$app->request->post('Reservation')['request_date'];
        $vehicleId = Yii::$app->request->post('Reservation')['vehicle_id'];
//        echo "id: $id";
        print_r(Yii::$app->request->post('Reservation'));
        if ($found = Reservation::findOne([
            'request_date' => $requestDate,
            'vehicle_id' => $vehicleId,
        ])) {
            $model = $found;
        }

        \Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load($request->post())) {
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
