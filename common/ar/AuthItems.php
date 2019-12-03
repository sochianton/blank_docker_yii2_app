<?php

namespace common\ar;

use kartik\grid\ActionColumn;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\rbac\DbManager;


/**
 * Class AuthItems
 * @package common\ar
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 *
 * @property AuthItems $childrenRl
 */
class AuthItems extends ActiveRecord
{

    const SCENARIO_SEARCH = 'search';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $children; // Для подсчета количества вложенных объектов в SQL запросе
    public $parent; // Если выборка для родителя
    public $childrenForm=null; // Для установки дочерних ролей в форме

    public function getParseData()
    {

        if($this->data){
            $res = unserialize($this->data);

            if($res){
                return var_export($res, true);
            }
        }

        return $this->data;

    }

    //============ ~~~~~~~~~~~~~~~~~ =========================

    public static function tableName()
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;
        return $auth->itemTable;
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'name' => Yii::t('app', 'Name'),
            'childrenForm' => Yii::t('app', 'Children'),
            'description' => Yii::t('app', 'Description'),
            'rule_name' => Yii::t('app', 'Rule name'),
            'type' => Yii::t('app', 'Type'),
            'data' => Yii::t('app', 'Data'),
        ]);
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class
            ],

        ];
    }

    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_CREATE => ['name', 'type', 'description', 'childrenForm'],
            self::SCENARIO_UPDATE => ['description', 'childrenForm'],
            self::SCENARIO_SEARCH => ['name', 'type', 'description'],
        ]);
    }

    public function rules()
    {
        $item = $this;

        return [

            [['name', 'type'], 'required', 'except' => [
                self::SCENARIO_SEARCH
            ]],
            [['name'], 'unique'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['rule_name', 'data'], 'default', 'value' => null],
            [['rule_name'], 'in', 'range' => self::getRulesNameList() ],
            [['description'], 'string', 'max' => 500],
            [['type'], 'in', 'range' => array_keys(self::getRoleTypesList())],

            ['childrenForm', 'default', 'value' => []],
            ['childrenForm', 'each', 'rule' => [
                'exist',
                'targetAttribute' => 'name'
            ]],

            ['childrenForm', 'each', 'rule' => [function ($attribute, $params, $validator) {

                $val = $this->$attribute;
                $children = AuthItems::findOne($val)->getAuthItem();

                if($this->isNewRecord){
                    if($this->type == 1){
                        $parent = Yii::$app->authManager->createRole($this->name);
                    }
                    else{
                        $parent = Yii::$app->authManager->createPermission($this->name);
                    }

                }
                else{
                    $parent = $this->getAuthItem();
                }

                if(!Yii::$app->authManager->canAddChild($parent,$children)){
                    $this->addError($attribute, 'Can not add item '.$val.' as a child to '.$this->name);
                }

            }]],



            [['data'], 'filter', 'filter' => function($val){

                if($val){
                    try{
                        eval('$res = '.$val.';');
                        $res = serialize($res);
                    }
                    catch (Exception $e){
                        $res=null;
                    }
                    return $res;
                }
                return $val;

            } ],


        ];
    }

    public function beforeDelete()
    {

        if(parent::beforeDelete()){

            return true;

        }
        else{
            return false;
        }
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \yii\db\Exception
     */
    public function search($params){

        $query = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query
                ->alias('item')
                ->leftJoin([
                    'ch' => (new Query())
                        ->select('parent, COUNT(parent) count')
                        ->from(Yii::$app->authManager->itemChildTable)
                        ->groupBy(['parent'])
                ], 'ch.parent = item.name')
                ->leftJoin(
                    ['par' => Yii::$app->authManager->itemChildTable], 'par.child = item.name')
                ->select(['item.*', 'children' => 'ch.count', 'parent' => 'par.parent', ])
            ,
            'key' => 'name',
            'sort' => [
                'defaultOrder' => [
                    'type' => SORT_ASC,
                    'name' => SORT_ASC,
                ]
            ]
        ]);


        $this->load($params);



        if (!$this->validate()) {
            throw new \yii\db\Exception('Errors in validation', $this->errors);
        }


        $query
            ->andFilterWhere([
                'type' => $this->type,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['=', 'par.parent', $this->parent])
        ;

        return $dataProvider;

    }

    public function afterFind()
    {
        parent::afterFind();

        $this->childrenForm = ArrayHelper::getColumn($this->childrenRl, 'name');

    }

    public function getAuthItem(){
        if($this->type == 1) return Yii::$app->authManager->getRole($this->name);
        elseif ($this->type == 2) return Yii::$app->authManager->getPermission($this->name);
    }

    //=============== RELATIONS ============================================================

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getChildrenRl()
    {
        return $this->hasMany(self::class, ['name' => 'child'])->viaTable(\common\ar\AuthItemChild::tableName(), ['parent' => 'name']);
    }

    //=============== STATIC ============================================================

    /**
     * @param null $id
     * @return array|mixed
     */
    static function getRoleTypesList($id=null){
        $res = [
            1 => Yii::t('app', 'Role'),
            2 => Yii::t('app', 'Permission'),
        ];
        if($id !== null AND isset($res[$id])){
            return $res[$id];
        }
        else{
            return $res;
        }
    }

    static function getRulesNameList($id=null){

        $res = [];
        foreach(Yii::$app->authManager->getRules() as $k=>$rule){
            $res[$k] = $rule->name;
        }

//        foreach (FileHelper::findFiles(Yii::getAlias('@common/rbac')) as $file){
//
//            $parts = explode('/', $file);
//            $part = $parts[count($parts)-1];
//            $parts = explode('.', $part);
//
//            try{
//                $class = 'common\\rbac\\'.$parts[0];
//                $class=new $class();
//            }
//            catch (ErrorException $e){
//                $class = null;
//                continue;
//            }
//
//            $res[$class->name] = $class->name;
//
//        }

        if($id !== null AND isset($res[$id])){
            return $res[$id];
        }
        else{
            return $res;
        }

    }

    /**
     * @param null $except
     * @return \yii\db\ActiveQuery
     */
    static function getAllList($except=null){

        return self::find()
            ->andFilterWhere(['!=', 'name', $except])
            ;

    }

    /**
     * @param self $model
     * @return \common\widgets\AppGridView
     *
     * @throws \yii\db\Exception
     */
    static function getGrid(self $model){
        return new \common\widgets\AppGridView([
            'id' => 'GridRoles',
            'tableOptions' => [
                'id' => 'GridRoles_table'
            ],
            'dataProvider' => $model->search([]),
            // 'filterModel' => null,
            'box' => [
                'no-padding' => true,
            ],
            'menu' => [
                'options' => [
                    'tag' => null,
                ],
                'itemOptions' => [
                    'tag' => null,
                ],
                'encodeLabels' => false,
                'linkTemplate' => '<a class="btn btn-default" href="{url}">{label}</a>',
                'items' => [
                    [
                        'label' => '<i class="fa fa-plus"></i> '.Yii::t('app', 'Add'),
                        'url' => ['create-role'],
                        'visible' => Yii::$app->user->ch('/user/create-role')
                    ]
                ]
            ],
            'resizableColumns'=>true,
            'responsive'=>true,
            'responsiveWrap'=>true,
            'columns' => [
                'name',
                'description',
                [
                    'class' => ActionColumn::class,
                    'template' => '{update} {delete}',
                    'buttons' => [
                        'update' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update-role', 'id' => $key, ], [
                                'title' => Yii::t('app', 'Update item'),
                            ]);
                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete-role', 'id' => $key, ], [
                                'title' => Yii::t('app', 'Delete item'),
                            ]);
                        },
                    ],
                    'visibleButtons' => [
                        'delete' => function ($model) {
                            return Yii::$app->user->ch('/user/delete-role');
                        },
                        'update' => function ($model) {
                            return Yii::$app->user->ch('/user/update-role');
                        },
                    ]
                ],
            ],
        ]);
    }

}
