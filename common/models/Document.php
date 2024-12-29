<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Document model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $file_path
 * @property string $original_name
 * @property string $mime_type
 * @property integer $size
 * @property string $created_at
 *
 * @property User $user
 */
class Document extends ActiveRecord
{
    public const TYPE_PASSPORT = 'passport';
    public const TYPE_INCOME = 'income';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%documents}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'file_path', 'original_name', 'mime_type', 'size'], 'required'],
            [['user_id', 'size'], 'integer'],
            [['type'], 'in', 'range' => [self::TYPE_PASSPORT, self::TYPE_INCOME]],
            [['file_path', 'original_name', 'mime_type'], 'string', 'max' => 255],
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