# Development tools

## REQUIREMENTS

The minimum is that your Web server supports PHP 7.0.


## Installation
Include like subtree to scl/tools directory of project

git subtree add --squash --prefix=scl/tools git@gitlab.icerockdev.com:scl/scl-yii/tools.git tag_name

Example: `git subtree add --squash --prefix=scl/tools git@gitlab.icerockdev.com:scl/scl-yii/tools.git master`

## Usage

Подключение в контроллер, с использованием SafeException для обработки штатных exception с выводом всей информации пользователю

````php
public function runAction($id, $params = [])
{
   try {
       return parent::runAction($id, $params);
   } catch (SafeException $exception) {
       return new ErrorResponse($exception);
   } catch (\Exception $exception) {
       \Yii::$app->getErrorHandler()->logException($exception);
       return new ErrorResponse($exception);
   }
}
````

Создание типизированного ответа. В поля этого класса добавляются все необходимые данные. 
В конструктор передаются любые необходимые данные

````php
class CreateResponse implements Response
{
    public function __construct()
    {
        Yii::$app->response->statusCode = 201;
    }
}
````

Пример типизированного Request для Yii. Отвечает за получение данных и первоначальную валидацию

````php
class CreateRequest extends Model implements Request
{
    /** @var integer $rating */
    public $rating;

    /** @var string $comment */
    public $comment;

    /** @var integer $orderId */
    public $orderId;

    /** @var integer $userId */
    public $userId;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['rating', 'orderId', 'userId'], 'required'],
            [['userId', 'rating'], 'integer'],
            [['comment'], 'string', 'max' => 300],
            [['orderId'], 'integer', 'min' => 1],
        ];
    }


    /**
     * @param $params
     * @return bool
     */
    public function fillFromRequest($params)
    {
        $this->rating = $params['rating'] ?? null;
        $this->comment = $params['comment'] ?? null;
        $this->orderId = $params['order_id'] ?? null;
        $this->userId = \Yii::$app->user->id ?? null;

        return $this->validate();
    }
}

````

Пример использования типизированного ввода-вывода в контроллере

````php
public function actionCreate()
{
    $request = new CreateRequest();

    if (!$request->fillFromRequest($this->input)) {
        return $request;
    }

    $result = $this->logicModel->create($request);

    return new CreateResponse($result);
}
````

Пример модели с логикой обработки

````php
/**
 * @param CreateRequest $request
 * @return bool
 * @throws SafeException
 */
public function create(CreateRequest $request)
{
    /** @var Review $review */
    $review = new Review();

    $this->fromRequestToData($request, $review);

    if ($this->checkReview($review)) {
        throw new SafeException('Review already published.');
    };

    $isSaved = $review->save();

    if (!$isSaved) {
        return $isSaved;
    }

    $review->refresh();
    $review->syncOrder();

    return $isSaved;
}
````

## Authors
- Alex Shvedov <alexeii.shvedov@gmail.com>
- Dmitry Veremeiko <d.veremeiko@gmail.com>
