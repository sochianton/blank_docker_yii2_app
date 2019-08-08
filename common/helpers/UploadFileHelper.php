<?php

namespace common\helpers;

use scl\tools\rest\exceptions\SafeException;
use Yii;

/**
 * Class UploadFileHelper
 * @package common\helpers
 */
class UploadFileHelper
{
    /**
     * @param string $mimeType
     * @param bool $withDot
     * @return string
     * @throws SafeException
     */
    public static function getExtensionByMime(string $mimeType, bool $withDot = true): string
    {
        switch ($mimeType) {
            case 'image/png':
                $extension = 'png';
                break;
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/jpeg':
                $extension = 'jpeg';
                break;
            case 'image/jpg':
                $extension = 'jpg';
                break;
            case 'application/msword':
                $extension = 'doc';
                break;
            case 'application/vndopenxmlformats-officedocumentwordprocessingmldocument':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $extension = 'docx';
                break;
            case 'application/pdf':
                $extension = 'pdf';
                break;
            case 'application/excel':
            case 'application/vnd.ms-excel':
            case 'application/x-excel':
            case 'application/x-msexcel':
                $extension = 'xls';
                break;
            case 'application/vndopenxmlformats-officedocumentspreadsheetmlsheet':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'application/vndopenxmlformats-officedocumentspreadsheetmltemplate':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.template':
                $extension = 'xlsx';
                break;
            case 'application/vnd.ms-excel.sheet.macroEnabled.12':
                $extension = 'xlsm';
                break;

            default:
                throw new SafeException(422, Yii::t('errors', 'File type not recognized'));
                break;
        }

        return $withDot ? '.' . $extension : $extension;
    }

    /**
     * @param string $extension
     * @param string $rootDir
     * @return string
     */
    public static function generateFileName(string $extension, string $rootDir = ''): string
    {
        do {
            /** @var string $name */
            $name = md5(microtime() . rand(0, 9999)) . rand(0, 9999);
            /** @var string $dirName */
            $dirName = $rootDir ? $rootDir . DIRECTORY_SEPARATOR : '';
            $dirName .= substr($name, 0, 3);
            /** @var string $fileName */
            $fileName = $dirName . DIRECTORY_SEPARATOR . $name . '.' . $extension;
        } while (file_exists(self::getFilePath($fileName)));

        $dirPath = self::getPath() . $dirName;
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        return $fileName;
    }

    /**
     * @return string
     */
    public static function getPath(): string
    {
        return Yii::getAlias('@storage') . DIRECTORY_SEPARATOR;
    }

    /**
     * @param null|string $fileName
     * @return string
     */
    public static function getFilePath(?string $fileName): ?string
    {
        if ($fileName === null) {
            return null;
        }
        return self::getPath() . $fileName;
    }

    /**
     * @param string|null $filePath
     * @return string|null
     */
    public static function getDirPath(?string $filePath): ?string
    {
        if ($filePath === null) {
            return null;
        }
        return pathinfo($filePath, PATHINFO_DIRNAME);
    }

    /**
     * @param string $path
     * @return void
     */
    public static function createDir(string $path): void
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    /**
     * @param null|string $filePath
     */
    public static function deleteFile(?string $filePath): void
    {
        if ($filePath === null) {
            return;
        }
        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
        $dirPath = dirname($filePath);
        if (!file_exists($dirPath) || !is_dir($dirPath)) {
            return;
        }

        if (count(array_diff(scandir($dirPath), array('..', '.'))) === 0) {
            rmdir($dirPath);
        }
    }

    /**
     * @param string $filePath
     * @param string $base64Sting
     * @return null|string
     */
    public static function createFileFromBase64(string $filePath, string $base64Sting): ?string
    {
        if (is_file($filePath)) {
            return null;
        }

        /** @var string $base64Sting */
        $base64Sting = substr($base64Sting, strpos($base64Sting, ','));
        if (file_put_contents(self::getFilePath($filePath), base64_decode($base64Sting))) {
            return $filePath;
        }

        return null;
    }
}
