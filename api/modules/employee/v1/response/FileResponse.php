<?php

namespace api\modules\employee\v1\response;

use OpenApi\Annotations as OA;

/**
 * Class FileResponse
 * @package api\modules\employee\v1\response
 * @OA\Schema(schema="EmployeeFileResponse")
 */
class FileResponse
{
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $url;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $path;
    /**
     * @var string
     * @OA\Property(type="string")
     */
    public $name;

    /**
     * FileListResponse constructor.
     * @param array $file
     */
    public function __construct(array $file = [])
    {
        $this->url = (string)($file['url'] ?? '');
        $this->path = (string)($file['path'] ?? '');
        $this->name = (string)($file['name'] ?? '');
    }
}
