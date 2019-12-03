<?php


namespace common\repositories;


use common\ar\EmployeeQualification;
use common\ar\User;
use common\ar\UserWork;
use common\interfaces\BaseRepositoryInterface;
use common\models\Customer;
use common\traits\RepositoryTrait;
use yii\db\Expression;

class UserRep implements BaseRepositoryInterface
{

    static $class = User::class;

    use RepositoryTrait;

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    static function getAllCustomerList(){
        return $q = User::findAll([
            'type' => User::TYPE_CUSTOMER,
            'status' => User::STATUS_ACTIVE
        ]);
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    static function getAllEmployeeList(){
        return $q = User::findAll([
            'type' => User::TYPE_EMPLOYEE,
            'status' => User::STATUS_ACTIVE
        ]);
    }

    /**
     * @param string $email
     * @param null $type
     * @return User|null
     */
    static function getByEmail(string $email, $type = null): ?User
    {
        $q = User::find();

        $q->andWhere(['email' => $email]);

        if($type){
            $q->andWhere(['type' => $type]);
        }

        return $q->one();
    }

    /**
     * @param int userId
     * @param array $qualificationIds
     * @throws \Exception
     */
    static function insertQualificationArray(int $userId, array $qualificationIds): void
    {
        $model = new EmployeeQualification();
        try {

            $employeeQualifications = [];
            foreach ($qualificationIds as $qualificationId) {
                $employeeQualifications[] = [
                    'employee_id' => $userId,
                    'qualification_id' => $qualificationId,
                ];
            }

            \Yii::$app->db->createCommand()->batchInsert(
                EmployeeQualification::tableName(),
                $model->attributes(),
                $employeeQualifications
            )->execute();
        } catch (\Exception $exception) {
            throw new \Exception(\Yii::t('errors', 'Can\'t create employee qualifications'));
        }
    }

    /**
     * @param int $userId
     * @param array $workIds
     * @throws \Exception
     */
    static function insertWorkArray(int $userId, array $workIds): void
    {
        $model = new UserWork();
        try {

            $userWorks = [];
            foreach ($workIds as $workId) {
                $userWorks[] = [
                    'work_id' => $workId,
                    'user_id' => $userId,
                ];
            }

            \Yii::$app->db->createCommand()->batchInsert(
                UserWork::tableName(),
                $model->attributes(),
                $userWorks
            )->execute();
        } catch (\Exception $exception) {
            throw new \Exception(\Yii::t('errors', 'Can\'t create user work'));
        }
    }

    /**
     * @param array $userId
     */
    static function deleteQualificationArray(array $userIds): void
    {
        EmployeeQualification::deleteAll(['employee_id' => $userIds]);
    }

    /**
     * @param array $userId
     */
    static function deleteWorkArray(array $userIds): void
    {
        UserWork::deleteAll(['user_id' => $userIds]);
    }

    /**
     * @param $userId
     * @return array
     */
    static function getQualificationIds($userId){

        return \common\models\EmployeeQualification::find()
            ->where(['employee_id' => $userId])
            ->select('qualification_id')
            ->column();

    }

    /**
     * @param $userId
     * @return array
     */
    static function getWorksIds($userId){

        return UserWork::find()
            ->where(['user_id' => $userId])
            ->select('work_id')
            ->column();

    }

    /**
     * @param User $customer
     * @param string $photo
     * @return Customer|null
     */
    static function updatePhoto(User $user, string $photo): ?User
    {
        $user->updateAttributes(['photo' => $photo]);

        return $user;
    }

    static function getByType($id, $type=null): ?User{
        $q = User::find();

        $q->andWhere(['id' => $id]);

        if($type){
            $q->andWhere(['type' => $type]);
        }

        return $q->one();
    }

    /**
     * @param int $id
     * @return bool
     */
    static function isBlocked(int $id): bool
    {
        return (bool)User::find()
            ->where(['id' => $id])
            ->andWhere(['status' => User::STATUS_DELETED])
            ->count();
    }

    /**
     * @param array $qualificationIds
     * @return array
     */
    static function getEmployeeIdsByQualifications(array $qualificationIds): array
    {
        return EmployeeQualification::find()
            ->select(['employee_id'])
            ->where(['qualification_id' => $qualificationIds])
            ->column();
    }

    /**
     * @param array $includedEmployeeIds
     * @param array $excludedEmployeeIds
     * @return User[]|null
     */
    static function getAllAvailable(array $includedEmployeeIds, array $excludedEmployeeIds): ?array
    {
        return User::find()
            ->where(['id' => $includedEmployeeIds])
            ->andFilterWhere(['NOT IN', 'id', $excludedEmployeeIds])
            ->andWhere(['!=', 'status', User::STATUS_DELETED])
            ->orderBy(new Expression('random()'))
            ->all();
    }

    /**
     * @param array $workIds
     * @param array $excludedUserIds
     * @param int|null $type
     * @return array|null
     */
    static function getAllByWork(array $workIds, array $excludedUserIds, int $type=null): ?array
    {

        return User::find()
            ->alias('u')
            ->leftJoin(['uw' => UserWork::tableName()], "uw.user_id=u.id")
            ->andFilterWhere(['NOT IN', 'u.id', $excludedUserIds])
            ->andFilterWhere(['=', 'u.type', $type])
            ->andWhere(['!=', 'u.status', User::STATUS_DELETED])
            ->andWhere(['IN', 'uw.work_id', $workIds])
            ->orderBy(new Expression('random()'))
            ->all();
    }

}