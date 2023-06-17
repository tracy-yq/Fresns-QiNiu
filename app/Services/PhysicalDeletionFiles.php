<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Helpers\CacheHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use App\Models\FileUsage;
use Fresns\DTO\DTO;
use Illuminate\Validation\Rule;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class PhysicalDeletionFiles extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', Rule::in(array_keys(File::TYPE_MAP))],
            'fileIdsOrFids' => ['array', 'required'],
        ];
    }

    public function delete()
    {
        /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter */
        $storage = $this->getAdapter();
        if (is_null($storage)) {
            return null;
        }

        foreach ($this->fileIdsOrFids as $id) {
            if (StrHelper::isPureInt($id)) {
                $file = File::where('id', $id)->first();
            } else {
                $file = File::where('fid', $id)->first();
            }

            if (empty($file)) {
                continue;
            }

            FileUsage::where('file_id', $file->id)->delete();

            $storage->delete($file->path);

            $file->update([
                'physical_deletion' => true,
            ]);

            $file->delete();

            // 删除缓存
            CacheHelper::forgetFresnsFileUsage($file->id);
            CacheHelper::forgetFresnsKeys([
                "qiniu_file_antilink_{$file->id}",
                "qiniu_file_antilink_{$file->fid}",
            ], [
                'fresnsPlugins',
                'pluginQiNiu',
            ]);
        }

        return true;
    }
}
