<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $email
 * @property string $password_hash
 * @property string $first_name
 * @property string $last_name
 * @property string $date_of_birth
 * @property string $passport_number
 * @property string $passport_expiry_date
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Document[] $documents
 * @property LoanApplication[] $loanApplications
 */
class User extends ActiveRecord implements IdentityInterface
{
    public const ROLE_USER = 'user';
    public const ROLE_ADMIN = 'admin';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%users}}';
    }

    public function getIsAdmin(): bool
    {
        return Yii::$app->authManager->checkAccess($this->id, self::ROLE_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
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
    public function rules(): array
    {
        return [
            [['email', 'first_name', 'last_name', 'date_of_birth', 'passport_number', 'passport_expiry_date'], 'required'],
            [['email'], 'email'],
            [['date_of_birth', 'passport_expiry_date'], 'date', 'format' => 'php:Y-m-d'],
            [['passport_number'], 'string', 'min' => 6, 'max' => 20],
            [['email', 'passport_number'], 'unique'],
            [['first_name', 'last_name'], 'string', 'max' => 255],
        ];
    }

    public function getDocuments(): ActiveQuery
    {
        return $this->hasMany(Document::class, ['user_id' => 'id']);
    }

    public function getLoanApplications(): ActiveQuery
    {
        return $this->hasMany(LoanApplication::class, ['user_id' => 'id']);
    }

    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        try {
            $decoded = Yii::$app->jwt->getParser()->parse((string) $token);

            if (!$decoded->verify(new \Lcobucci\JWT\Signer\Hmac\Sha256(), Yii::$app->jwt->key) ||
                $decoded->isExpired(new \DateTimeImmutable())) {
                return null;
            }

            return static::findOne($decoded->claims()->get('uid'));
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): string
    {
        return Yii::$app->security->generateRandomString();
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @throws Exception
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}