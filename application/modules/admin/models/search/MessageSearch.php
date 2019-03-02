<?php

namespace app\modules\admin\models\search;

use app\models\Message;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @author Alexander Kononenko <contact@hauntd.me>
 * @package app\modules\admin\models\search
 */
class MessageSearch extends Message
{
    /**
     * @var string|int
     */
    public $fromUser;
    /**
     * @var string|int
     */
    public $toUser;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['fromUser', 'toUser', 'text'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find()->joinWith(['sender', 'receiver', 'sender.profile as senderProfile', 'receiver.profile as receiverProfile']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if (is_numeric($this->fromUser)) {
            $query->andFilterWhere(['from_user_id' => $this->fromUser]);
        } elseif (!empty($this->fromUser)) {
            $query->andWhere(['or',
                ['like', 'sender.username', $this->fromUser],
                ['like', 'sender.email', $this->fromUser],
                ['like', 'senderProfile.name', $this->fromUser],
            ]);
        }

        if (is_numeric($this->toUser)) {
            $query->andFilterWhere(['to_user_id' => $this->toUser]);
        } elseif (!empty($this->fromUser)) {
            $query->andWhere(['or',
                ['like', 'receiver.username', $this->toUser],
                ['like', 'receiver.email', $this->toUser],
                ['like', 'receiverProfile.name', $this->toUser],
            ]);
        }

        $query->andFilterWhere(['like', 'lower(text)', strtolower($this->text)]);

        return $dataProvider;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'fromUser' => Yii::t('app', 'Sender'),
            'toUser' => Yii::t('app', 'Receiver'),
        ]);
    }
}
