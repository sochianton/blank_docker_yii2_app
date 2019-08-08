<?php
/**
 * Created by PhpStorm.
 * User: alexsh
 * Date: 29.10.18
 * Time: 15:56
 */

namespace scl\yii\push;


use GuzzleHttp\Exception\RequestException;
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Notification;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Recipient\Recipient;
use Yii;
use yii\helpers\ArrayHelper;

final class Push
{
    /** @var string $apiKey */
    private $apiKey;
    /** @var string $errorCategory */
    private $errorCategory;
    /** @var int $pushLimit */
    private $pushLimit;
    /** @var PushRepoInterface $pushRepo */
    private $pushRepo;

    const dataDefault = [
        'title' => 'Default message from server',
        'body' => 'Default message form server',
    ];

    // Error data from official docs
    // https://firebase.google.com/docs/cloud-messaging/admin/errors
    const STATE_FOR_DELETED = ['NotRegistered', 'InvalidRegistration'];
    const ERRORS = [
        '200' => [
            'MissingRegistration' => 'Missing Registration Token',
            'InvalidRegistration' => 'Invalid Registration Token',
            'NotRegistered' => 'Unregistered Device',
            'InvalidPackageName' => 'Invalid Package Name',
            'MismatchSenderId' => 'Mismatched Sender',
            'MessageTooBig' => 'Message Too Big',
            'InvalidDataKey' => 'Invalid Data Key',
            'InvalidTtl' => 'Invalid Time to Live',
            'Unavailable' => 'Timeout',
            'InternalServerError' => 'Internal Server Error',
            'DeviceMessageRateExceeded' => 'Device Message Rate Exceeded',
            'TopicsMessageRateExceeded' => 'Topics Message Rate Exceeded',
            'InvalidApnsCredential' => 'Invalid APNs credentials',
        ],
        '400' => [
            'default' => 'Invalid JSON',
            'InvalidParameters' => 'Invalid Parameters',
        ],
        '401' => [
            'default' => 'Authentication Error',
        ],
    ];

    /**
     * Push constructor.
     * @param PushRepoInterface $pushRepo
     * @param string $apiKey
     * @param string $errorCategory
     * @param int $pushLimit
     */
    public function __construct(
        PushRepoInterface $pushRepo,
        string $apiKey,
        string $errorCategory = 'FCM PushSender',
        int $pushLimit = 1000
    ) {
        $this->apiKey = $apiKey;
        $this->errorCategory = $errorCategory;
        $this->pushLimit = $pushLimit;
        if ($pushLimit > 1000) {
            throw new \InvalidArgumentException('FCM does not support more then 1000 addresses per request');
        }
        $this->pushRepo = $pushRepo;
    }


    /**
     * Send push for any users
     * @param int[] $uidList
     * @param int $usersType
     * @param Notification $note
     * @param array $data
     */
    public function sendToUsers(array $uidList, int $usersType, Notification $note, array $data = []): void
    {
        $tokens = $this->pushRepo->getPushTokenListById($uidList, $usersType);
        /** @var Device[] $recipients */
        $recipients = [];
        foreach ($tokens as $token) {
            if (empty($token)) {
                continue;
            }
            $recipients[] = new Device($token);
        }

        $this->send($recipients, $note, $data);
    }

    /**
     * Send push for any device tokens
     * @param string[] $tokens
     * @param Notification $note
     * @param array $data
     */
    public function sendToTokens(array $tokens, Notification $note, array $data = []): void
    {
        /** @var Device[] $recipients */
        $recipients = [];
        foreach ($tokens as $token) {
            if (empty($token)) {
                continue;
            }

            $recipients[] = new Device($token);
        }

        $this->send($recipients, $note, $data);
    }

    /**
     * Send pushes
     * @param Notification $note
     * @param array $data
     * @param Recipient[] $recipients
     */
    private function send(array $recipients, Notification $note, array $data = []): void
    {
        $currentPage = 0;
        while (true) {
            $currentRecipients = array_slice($recipients, $this->pushLimit * $currentPage, $this->pushLimit);
            if (count($currentRecipients) == 0) {
                break;
            }

            $client = new Client();
            $client->setApiKey($this->apiKey);
            $client->injectHttpClient(new \GuzzleHttp\Client());

            $message = new Message();
            foreach ($currentRecipients as $recipient) {
                $message->addRecipient($recipient);
            }

            /* for custom push with sounds */
            $jsonData = $note->jsonSerialize();
            $message->setData([
                'title' => $jsonData['title'],
                'body' => $jsonData['body']
            ]);
            /*    */

            try {
                $response = $client->send($message);
                $responseBody = json_decode($response->getBody()->getContents());
                if (ArrayHelper::getValue($responseBody, 'failure', 0) > 0) {
                    $this->handleFailureResult($responseBody, $currentRecipients);
                }
            } catch (RequestException $e) {
                $response = $e->getResponse();
                Yii::error(
                    ArrayHelper::getValue(self::ERRORS[$response->getStatusCode()], 'default', $e->getMessage()),
                    $this->errorCategory
                );
            } catch (\Exception $e) {
                Yii::error($e->getMessage() . ' ' . $e->getTraceAsString(), $this->errorCategory);
            }
            $currentPage++;
        }
    }

    /**
     * Error handler
     * @param $responseBody
     * @param Recipient[] $recipients
     */
    protected function handleFailureResult($responseBody, array $recipients): void
    {
        $deletePushTokens = [];
        foreach (ArrayHelper::getValue($responseBody, 'results', []) as $i => $status) {
            if ($responseBody->failure <= count($deletePushTokens)) {
                break;
            }

            if (!property_exists($status, 'error')) {
                continue;
            }

            $errorStatus = $status->error;

            /** @var Recipient $recipient */
            $recipient = $recipients[$i] ?? null;
            if ($recipient === null) {
                continue;
            }

            $token = $recipient->getIdentifier();
            Yii::warning(
                ArrayHelper::getValue(self::ERRORS['200'], $errorStatus, $errorStatus) . ' ' . $token,
                $this->errorCategory
            );

            if (!in_array($errorStatus, self::STATE_FOR_DELETED)) {
                continue;
            }
            $deletePushTokens[] = $token;

        }

        // Remove wrong tokens from storage
        if (!empty($deletePushTokens)) {
            $result = $this->pushRepo->removeTokensById($deletePushTokens);
            if (!$result) {
                Yii::error('Cant remove token list' . json_encode($deletePushTokens));
            }
        }
    }
}
