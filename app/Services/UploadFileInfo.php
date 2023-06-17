<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Models\File;
use App\Utilities\FileUtility;
use Fresns\DTO\DTO;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class UploadFileInfo extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'platformId' => ['integer', 'required'],
            'usageType' => ['integer', 'required'],
            'tableName' => ['string', 'required'],
            'tableColumn' => ['string', 'required'],
            'tableId' => ['integer', 'nullable'],
            'tableKey' => ['string', 'nullable'],
            'aid' => ['string', 'nullable'],
            'uid' => ['integer', 'nullable'],
            'type' => ['integer', 'required'],
            'fileInfo' => ['array', 'required'],
        ];
    }

    public function process()
    {
        $this->resetQiNiuConfig();

        $bodyInfo = [
            'platformId' => $this->platformId,
            'usageType' => $this->usageType,
            'tableName' => $this->tableName,
            'tableColumn' => $this->tableColumn,
            'tableId' => $this->tableId,
            'tableKey' => $this->tableKey,
            'aid' => $this->aid ?: null,
            'uid' => $this->uid ?: null,
            'type' => $this->type,
            'fileInfo' => $this->fileInfo,
        ];

        $uploadFileInfo = FileUtility::uploadFileInfo($bodyInfo);

        // 如果是视频，处理封面图
        // 七牛云直接将参数拼接在 URL 后面即可输出封面图，无需单独生成物理文件
        // $newFileInfo = [];
        // foreach ($uploadFileInfo as $fileInfo) {
        //     if ($fileInfo['type'] == File::TYPE_VIDEO) {
        //         // files->video_poster_path
        //     }

        //     $newFileInfo[] = $fileInfo;
        // }

        return $uploadFileInfo;
    }
}
