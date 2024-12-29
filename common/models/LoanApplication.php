<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * LoanApplication model
 *
 * @property integer $id
 * @property integer $user_id
 * @property float $amount
 * @property integer $term_months
 * @property string $purpose
 * @property float $monthly_income
 * @property string $status
 * @property float $monthly_payment
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $user
 */
class LoanApplication extends ActiveRecord
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FINISHED = 'finished';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%loan_applications}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'value' => new \yii\db\Expression('CURRENT_TIMESTAMP'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'amount', 'term_months', 'purpose', 'monthly_income'], 'required'],
            [['user_id', 'term_months'], 'integer'],
            [['amount', 'monthly_income', 'monthly_payment'], 'number'],
            [['purpose'], 'string'],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_REJECTED]],
            [['term_months'], 'integer', 'min' => 6, 'max' => 60],
            [['amount'], 'number', 'min' => 1000],
            [['monthly_income'], 'number', 'min' => 0],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * Gets query for [[User]]
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}