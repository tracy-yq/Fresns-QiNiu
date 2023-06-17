<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Helpers\CacheHelper;
use App\Utilities\FileUtility;
use Fresns\DTO\DTO;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class LogicalDeletionFiles extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'fileIdsOrFids' => ['array', 'required'],
        ];
    }

    public function delete()
    {
        FileUtility::logicalDeletionFiles($this->fileIdsOrFids);

        // 删除缓存
        CacheHelper::forgetFresnsFileUsage($this->fileIdsOrFids);
        CacheHelper::forgetFresnsKeys([
            "qiniu_file_antilink_{$this->fileIdsOrFids}",
        ], [
            'fresnsPlugins',
            'pluginQiNiu',
        ]);

        return true;
    }
}
