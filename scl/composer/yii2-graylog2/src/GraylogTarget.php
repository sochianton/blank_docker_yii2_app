<?php

namespace scl\graylog;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\log\Target;
use yii\log\Logger;
use Gelf;
use Psr\Log\LogLevel;

/**
 * GraylogTarget sends log to Graylog2 (in GELF format)
 *
 */
class GraylogTarget extends Target
{
    public $defaultExcept = [
        'yii\web\HttpException:404',
        'yii\web\HttpException:403',
        'yii\web\HttpException:401',
        'graylog',
        'yii\db\Connection::open',
        'yii\db\Command::query',
        'yii\db\Command::execute',
        'yii\debug\Module*',
        'yii\web\Session::open',
    ];

    /**
     * @var string Graylog2 host
     */
    public $host = '127.0.0.1';

    /**
     * @var integer Graylog2 port
     */
    public $port = 12201;

    /**
     * @var string default facility name
     */
    public $facility = 'yii2-logs';

    /**
     * @var array default additional fields
     */
    public $additionalFields = [];

    /**
     * @var array throwable additional fields
     */
    public $throwable = [];

    public $shortLength = 100;
    public $contextLength = 500;
    public $contextDepth = 5;

    /**
     * @var bool flag for use api headers
     */
    public $logApiHeaders = false;
    /**
     * @var array map graylog field to request header
     */
    public $headerMap = [
        'device-model' => 'X-Device-Model',
        'platform' => 'X-Platform',
        'platform-version' => 'X-Platform-Version',
        'app-version' => 'X-App-Version',
        'app-build' => 'X-App-Build',
    ];

    public $logVars = ['_GET', '_POST', '_FILES'];


    private $apiHeaders = null;

    /**
     * @var array graylog levels
     */
    private $_levels = [
        Logger::LEVEL_TRACE => LogLevel::DEBUG,
        Logger::LEVEL_PROFILE_BEGIN => LogLevel::DEBUG,
        Logger::LEVEL_PROFILE_END => LogLevel::DEBUG,
        Logger::LEVEL_INFO => LogLevel::INFO,
        Logger::LEVEL_WARNING => LogLevel::WARNING,
        Logger::LEVEL_ERROR => LogLevel::ERROR,
    ];

    public function init()
    {
        parent::init();
        $additionalFields = [];
        // merge ignored exceptions
        $this->except = array_merge($this->except, $this->defaultExcept);
        $throwable = [
            'context' => function ($yii) {
                return $this->getLimitedContextMessage($this->contextLength, $this->contextDepth);
            },
        ];
        // logging responce headers
        if (!Yii::$app->getRequest()->isConsoleRequest) {
            $throwable['response-headers'] = function ($yii) {
                /** @var \yii\web\Application $yii */
                $headers = $yii->getResponse()->getHeaders()->toArray() ?? [];
                return json_encode($headers);
            };
            $throwable['url'] = function ($yii) {
                /** @var \yii\web\Application $yii */
                return $yii->getRequest()->absoluteUrl;
            };
            $additionalFields['user-ip'] = function ($yii) {
                /** @var \yii\web\Application $yii */
                return $yii->getRequest()->getUserIP();
            };
        }

        $this->throwable = array_merge($this->throwable, $throwable);
        $this->additionalFields = array_merge($this->additionalFields, $additionalFields);
    }

    private function getHeaders()
    {
        if (Yii::$app->getRequest()->isConsoleRequest) {
            return [];
        }

        if ($this->apiHeaders !== null) {
            return $this->apiHeaders;
        }

        $headers = Yii::$app->getRequest()->getHeaders() ?? [];
        foreach ($this->headerMap as $key => $val) {
            if (!is_string($key) || empty($key)) {
                continue;
            }
            // send fields only with data
            if (isset($headers[$val][0])) {
                $this->apiHeaders[$key] = $headers[$val][0];
            }
        }
        return $this->apiHeaders;
    }

    /**
     * Sends log messages to Graylog2 input
     */
    public function export()
    {
        $transport = new Gelf\Transport\UdpTransport($this->host, $this->port,
            Gelf\Transport\UdpTransport::CHUNK_SIZE_WAN);
        $publisher = new Gelf\Publisher($transport);

        $requestType = (Yii::$app->getRequest()->isConsoleRequest) ? 'cli' : 'web';
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            $gelfMsg = new Gelf\Message;
            // Set base parameters
            $gelfMsg->setLevel(ArrayHelper::getValue($this->_levels, $level, LogLevel::INFO))
                ->setTimestamp($timestamp)
                ->setFacility($this->facility)
                ->setAdditional('category', $category)
                ->setAdditional('requestType', $requestType)
                ->setFile('unknown')
                ->setLine(0);

            // For string log message set only shortMessage
            if (is_string($text)) {
                $gelfMsg->setShortMessage(substr($text, 0, $this->shortLength));
                $gelfMsg->setFullMessage($text);
            } elseif ($text instanceof \Throwable) {
                $throwable = true;
                $gelfMsg->setShortMessage('Throwable ' . get_class($text) . ': ' . $text->getMessage());
                $gelfMsg->setFullMessage($text->getTraceAsString());
                $gelfMsg->setLine($text->getLine());
                $gelfMsg->setFile($text->getFile());

            } else {
                // If log message contains special keys 'short', 'full' or 'add', will use them as shortMessage, fullMessage and additionals respectively
                $short = ArrayHelper::remove($text, 'short');
                $full = ArrayHelper::remove($text, 'full');
                $add = ArrayHelper::remove($text, 'add');
                // If 'short' is set
                if ($short !== null) {
                    $gelfMsg->setShortMessage(substr((string)$short, 0, $this->shortLength));
                    // All remaining message is fullMessage by default
                    $gelfMsg->setFullMessage(VarDumper::dumpAsString($text));
                } else {
                    $msg = VarDumper::dumpAsString($text);
                    $gelfMsg->setShortMessage(substr($msg, 0, $this->shortLength));
                    $gelfMsg->setFullMessage($msg);
                }
                // If 'full' is set will use it as fullMessage (note that all other stuff in log message will not be logged, except 'short' and 'add')
                if ($full !== null) {
                    $gelfMsg->setFullMessage(VarDumper::dumpAsString($full));
                }
                // Process additionals array (only with string keys)
                if (is_array($add)) {
                    foreach ($add as $key => $val) {
                        if (is_string($key)) {
                            if (!is_string($val)) {
                                $val = VarDumper::dumpAsString($val);
                            }
                            $gelfMsg->setAdditional($key, $val);
                        }
                    }
                }
            }

            // Set 'file', 'line' and additional 'trace', if log message contains traces array
            if (isset($message[4]) && is_array($message[4])) {
                $traces = [];
                foreach ($message[4] as $index => $trace) {
                    $traces[] = "{$trace['file']}:{$trace['line']}";
                    if ($index === 0) {
                        $gelfMsg->setFile($trace['file']);
                        $gelfMsg->setLine($trace['line']);
                    }
                }
                $gelfMsg->setAdditional('trace', implode("\n", $traces));
            }

            // Add any additional fields the user specifies
            $fielsds = $this->additionalFields;
            if (isset($throwable)) {
                $fielsds = array_merge($fielsds, $this->throwable);
            }

            foreach ($fielsds as $key => $value) {
                if (is_string($key) && !empty($key)) {
                    if (is_callable($value)) {
                        $value = $value(Yii::$app);
                    }
                    if (empty($value)) {
                        continue;
                    }
                    if (!is_string($value)) {
                        $value = VarDumper::dumpAsString($value);
                    }
                    $gelfMsg->setAdditional($key, $value);
                }
            }

            // add request headers
            if ($this->logApiHeaders || isset($throwable)) {
                foreach ($this->getHeaders() as $key => $val) {
                    $gelfMsg->setAdditional($key, $val);
                }
            }
            // Publish message
            try {
                $publisher->publish($gelfMsg);
            } catch (\Throwable $e) {
                Yii::error([
                    'err' => $e->getMessage(),
                    'trace' => $e->getTrace(),
                    'msg' => $message,
                ], 'graylog');
            }
        }
    }

    /**
     * Processes the given log messages.
     * This method will filter the given messages with [[levels]] and [[categories]].
     * And if requested, it will also export the filtering result to specific medium (e.g. email).
     * @param array $messages log messages to be processed. See [[Logger::messages]] for the structure
     * of each message.
     * @param bool $final whether this method is called at the end of the current application
     */
    public function collect($messages, $final)
    {
        $this->messages = array_merge($this->messages,
            static::filterMessages($messages, $this->getLevels(), $this->categories, $this->except));
        $count = count($this->messages);
        if ($count > 0 && ($final || $this->exportInterval > 0 && $count >= $this->exportInterval)) {
            // set exportInterval to 0 to avoid triggering export again while exporting
            $oldExportInterval = $this->exportInterval;
            $this->exportInterval = 0;
            $this->export();
            $this->exportInterval = $oldExportInterval;

            $this->messages = [];
        }
    }

    protected function getLimitedContextMessage($limit, $depth)
    {
        $context = ArrayHelper::filter($GLOBALS, $this->logVars);
        $result = [];
        foreach ($context as $key => $value) {
            $result[] = "\${$key} = " . VarDumper::dumpAsString($value, $depth);
        }

        return substr(implode("\n\n", $result), 0, $limit);
    }
}
