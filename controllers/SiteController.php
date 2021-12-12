<?php

namespace app\controllers;

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
                $reservation = Reservation::find()
                    ->where(['vehicle_id' => $vehicle->id, 'request_date' => $dt->format('Y-m-d')])->one();
                if (!$reservation) {
                    $reservation = new Reservation();
                    $reservation->request_date = $dt->format('Y-m-d');
                }
                $reservations[$vehicle->vehicleType->id][$vehicle->id][$dt->format('Y-m-d')] = $reservation;
            }
        }

        return $this->render('index', [
            'reservations' => $reservations,
            'period' => $period,
        ]);
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
