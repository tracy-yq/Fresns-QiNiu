<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Services;

use App\Helpers\CacheHelper;
use App\Helpers\FileHelper;
use App\Helpers\StrHelper;
use App\Models\File;
use Fresns\DTO\DTO;
use Illuminate\Validation\Rule;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class AntiLinkFileInfo extends DTO
{
    use QiNiuStorageTrait;

    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', Rule::in(array_keys(File::TYPE_MAP))],
            'fileIdOrFid' => ['string', 'required'],
        ];
    }

    public function getAntiLinkFileInfo()
    {
        /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter $storage */
        $storage = $this->getAdapter();

        if (is_null($storage)) {
            return null;
        }

        if (! $this->isEnabledAntiLink()) {
            return null;
        }

        $cacheKey = 'qiniu_file_antilink_'.$this->fileIdOrFid;
        $cacheTags = ['fresnsPlugins', 'pluginQiNiu'];

        $fileInfo = CacheHelper::get($cacheKey, $cacheTags);

        if (empty($fileInfo)) {
            $file = $this->getFile();
            if (is_null($file)) {
                return null;
            }

            $fileInfo = $file->getFileInfo();

            $deadline = $this->getExpireSeconds();

            $keys = [
                'imageConfigUrl', 'imageRatioUrl', 'imageSquareUrl', 'imageBigUrl',
                'videoPosterUrl', 'videoUrl',
                'audioUrl',
                'documentPreviewUrl',
            ];

            foreach ($keys as $key) {
                if ($key == 'documentPreviewUrl') {
                    $documentUrl = $file->getFileUrl();

                    $antiLinkUrl = $this->getAntiLinkUrl($documentUrl, $deadline);

                    $fileInfo[$key] = FileHelper::fresnsFileDocumentPreviewUrl($antiLinkUrl, $file->fid, $file->extension);

                    continue;
                }

                if (empty($fileInfo[$key])) {
                    continue;
                }

                $fileInfo[$key] = $this->getAntiLinkUrl($fileInfo[$key], $deadline);
            }

            $cacheTime = CacheHelper::fresnsCacheTimeByFileType($this->getType());

            CacheHelper::put($fileInfo, $cacheKey, $cacheTags, null, $cacheTime);
        }

        return $fileInfo;
    }

    public function getFile()
    {
        if (StrHelper::isPureInt($this->fileIdOrFid)) {
            $file = File::where('id', $this->fileIdOrFid)->first();
        } else {
            $file = File::where('fid', $this->fileIdOrFid)->first();
        }

        return $file;
    }
}
