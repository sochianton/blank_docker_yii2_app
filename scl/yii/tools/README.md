# yii-tools

## REQUIREMENTS

- You should generally follow [Yii 2 requirements](https://github.com/yiisoft/yii2/blob/master/README.md).
- The minimum is that your Web server supports PHP 7.1.
- include scl/tools subtree. Example: `git subtree add --squash --prefix=scl/yii/tools git@gitlab.icerockdev.com:scl/scl-yii/yii-tools.git master`


## INSTALLATION

### Install via subtree

Include like subtree to scl/tools directory of project

git subtree add --squash --prefix=scl/yii/tools git@gitlab.icerockdev.com:scl/scl-yii/yii-tools.git tag_name

Example: `git subtree add --squash --prefix=scl/tools git@gitlab.icerockdev.com:scl/scl-yii/yii-tools.git master`

## USAGE

### Обработчик ошибок

Подключение в конфиг REST API `main.php`:

````php
    'components' => [
        'errorHandler' => [
            'class' => ApiErrorHandler::class,
            'errorAction' => 'site/error',
        ],
    ],
````

### Типизированный ввод

Пример типизированного Request для Yii. Отвечает за получение данных и первоначальную валидацию

````php
class CreateRequest extends Request
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
}

````

Использование в контроллерах

````php
    $request = new CreateRequest($this->input);
    if ($request->validate()) {
        return $request;
    }
````

### Вывод
Класс для формирования ответа клиенту

Пример структуры файла:
````php
use scl\tools\rest\interfaces\Response;


class View implements Response
{
    /**
     * View constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->id = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->imageUrl = $product->getImageUrl();
        $this->price = $product->getPrice();
        $this->modifiers = $product->modifiers;
        $this->defaultSelection = $product->default_selection
        $this->outOfStock = $product->isOutOfStock();
        $this->availableTime = $product->getAvailableTime();
    }
}
````

Использование в контроллерах

````php
    return new ViewResponse($response, $this->productDiscountService);
````

Базовый RestController

содержит базовую конфигурацию для типового rest контроллера в рамках yii2
````php
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => [],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['contentNegotiator']['languages'] = ['ru', 'en'];
        $behaviors['access']['class'] = AccessControl::class;
        $behaviors['access']['rules'][] = [
            'actions' => [],
            'allow' => true,
            'roles' => ['@'],
        ];
        // Отключён фильтр RateLimiter
        unset($behaviors['rateLimiter']);
        return $behaviors;
    }
````

## Authors
- Alex Shvedov <alexeii.shvedov@gmail.com>
- Dmitry Veremeiko <d.veremeiko@gmail.com>
