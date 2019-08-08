<?php

namespace api\misc;

use Yii;
use yii\web\UploadedFile;

/**
 * Class UploadedFileBase64
 * @package api\misc
 */
class UploadedFileBase64 extends UploadedFile
{
    /**
     * @var string
     */
    public $content;

    /**
     * @param string $name
     * @return null|UploadedFileBase64
     */
    public static function getInstanceByName($name)
    {
        $file = Yii::$app->request->post($name, []);
        if (empty($file['content'])) {
            return null;
        }

        return new static([
            'name' => $file['name'] ?? '',
            'content' => $file['content'],
            'type' => self::getB64Type($file['content']),
            'size' => self::getB64Size($file['content']),
        ]);
    }

    /**
     * @param string $name
     * @return array|UploadedFileBase64[]
     */
    public static function getInstancesByName($name)
    {
        $files = Yii::$app->request->post($name, []);
        $results = [];
        foreach ($files as $file) {
            if (empty($file['content'])) {
                continue;
            }

            $results[] = new static([
                'name' => $file['name'] ?? '',
                'content' => $file['content'],
                'type' => self::getB64Type($file['content']),
                'size' => self::getB64Size($file['content']),
            ]);
        }

        return $results;
    }

    /**
     * @param string $content
     * @return string
     */
    public static function getB64Type(string $content): string
    {
        return substr($content, 5, strpos($content, ';') - 5);
    }

    /**
     * @param string $content
     * @return int Bytes
     */
    public static function getB64Size(string $content): int
    {
        $partials = explode(',', $content);
        return (int)(strlen(rtrim($partials[1] ?? '', '=')) * 3 / 4);
    }

    /**
     * @return bool
     */
    public function isValidBase64(): bool
    {
        $partials = explode(',', $this->content);
        if (empty($partials[1])) {
            return false;
        }
        return base64_encode(base64_decode($partials[1], true)) === $partials[1];
    }
}
