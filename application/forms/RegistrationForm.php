<?php

namespace app\forms;

use app\models\Account;
use app\models\Profile;
use app\models\User;
use app\traits\CaptchaRequired;
use Yii;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\forms
 */
class RegistrationForm extends \yii\base\Model
{
    use CaptchaRequired;

    /**
     * @var string User email address
     */
    public $email;
    /**
     * @var string Username
     */
    public $username;
    /**
     * @var string Password
     */
    public $password;
    /**
     * @var string
     */
    public $name;
    /**
     * @var
     */
    public $sex;
    /**
     * @var string
     */
    public $country;
    /**
     * @var int
     */
    public $city;
    /**
     * @var string
     */
    public $dob;
    /**
     * @var string
     */
    public $dobDay;
    /**
     * @var string
     */
    public $dobMonth;
    /**
     * @var string
     */
    public $dobYear;
    /**
     * @var string
     */
    public $captcha;
    /**
     * @var User
     */
    protected $user = null;
    /**
     * @var Account
     */
    protected $account = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        /** @var User $user */
        $user = Yii::createObject(User::class);
        $sexOptions = (new Profile())->getSexOptions();

        $rules = [
            // username rules
            'usernameTrim' => ['username', 'trim'],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 255],
            'usernamePattern' => ['username', 'match', 'pattern' => $user::$usernameRegexp],
            'usernameRequired' => ['username', 'required'],
            'usernameUnique' => [
                'username',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('app', 'This username has already been taken')
            ],
            // email rules
            'emailTrim' => ['email', 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern' => ['email', 'email'],
            'emailUnique' => [
                'email',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('app', 'This email address has already been taken')
            ],
            // password rules
            'passwordRequired' => ['password', 'required',],
            'passwordLength' => ['password', 'string', 'min' => 6, 'max' => 72],

            // profile rules
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'trim'],
            [['sex'], 'required'],
            [['sex'], 'in', 'range' => array_keys($sexOptions)],

            // birth date
            ['dob', 'date', 'format' => 'php:Y-m-d'],
            ['dob', 'validateBirthday'],
            [['dobDay', 'dobMonth', 'dobYear'], 'validateBirthdayParts'],
            [['dobDay'], 'integer', 'min' => 1, 'max' => 31],
            [['dobMonth'], 'integer', 'min' => 1, 'max' => 12],
            [['dobYear'], 'integer', 'min' => 1920, 'max' => date('Y') - 18],

            // country
            ['country', 'string', 'min' => 2, 'max' => 2],
            ['country', function ($attribute, $params) {
                return Yii::$app->geographer->isValidCountryCode($this->$attribute);
            }],

            // city
            ['city', 'integer', 'min' => 0],
            ['city', function ($attribute, $params) {
                return Yii::$app->geographer->isValidCityCode($this->$attribute);
            }],
        ];

        if ($this->isCaptchaRequired()) {
            $rules['captcha'] = ['captcha', 'captcha'];
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'name' => Yii::t('app', 'Full name'),
            'sex' => Yii::t('app', 'Sex'),
            'country' => Yii::t('app', 'Country'),
            'city' => Yii::t('app', 'City'),
            'dob' => Yii::t('app', 'Birthdate'),
            'captcha' => Yii::t('app', 'Captcha'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'register-form';
    }

    /**
     * @param bool $connect
     * @return bool
     * @throws \Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function create($connect = false)
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var User $user */
        $user = Yii::createObject(User::class);
        $user->setScenario($connect ? 'connect' : 'register');
        $user->setAttributes($this->attributes);

        if (!$user->register()) {
            return false;
        }

        $user->profile->sex = $this->sex;
        $user->profile->name = $this->name;
        $user->profile->country = $this->country;
        $user->profile->city = $this->city;
        $user->profile->dob = $this->dob;
        $user->profile->save();

        $this->user = $user;

        return true;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateBirthday($attribute, $params)
    {
        $date = date_create_from_format('Y-m-d', sprintf("%d-12-31", date('Y') - 18));
        $maxDate = date_format($date, 'Y-m-d');
        date_sub($date, date_interval_create_from_date_string('100 years'));
        $minDate = date_format($date, 'Y-m-d');

        if ($this->$attribute > $maxDate) {
            $this->addError($attribute, Yii::t('app', 'Date is too small.'));
        } elseif ($this->$attribute < $minDate) {
            $this->addError($attribute, Yii::t('app', 'Date is too big.'));
        }
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validateBirthdayParts($attribute, $params)
    {
        $date = new \DateTime();
        $date->setDate($this->dobYear, $this->dobMonth, $this->dobDay);
        $this->dob = sprintf('%d-%02d-%02d', $this->dobYear, $this->dobMonth, $this->dobDay);
        return $this->validateBirthday('dob', $params);
    }

    /**
     * @return array
     */
    public function getDobDayOptions()
    {
        return array_combine(range(1,31), range(1,31));
    }

    /**
     * @return array
     */
    public function getDobMonthOptions()
    {
        return [
            1 => Yii::t('app', 'January'),
            2 => Yii::t('app', 'February'),
            3 => Yii::t('app', 'March'),
            4 => Yii::t('app', 'April'),
            5 => Yii::t('app', 'May'),
            6 => Yii::t('app', 'June'),
            7 => Yii::t('app', 'July'),
            8 => Yii::t('app', 'August'),
            9 => Yii::t('app', 'September'),
            10 => Yii::t('app', 'October'),
            11 => Yii::t('app', 'November'),
            12 => Yii::t('app', 'December'),
        ];
    }

    /**
     * @return array
     */
    public function getDobYearOptions()
    {
        $years = range(date('Y') - 18, 1920);
        return array_combine($years, $years);
    }
}
