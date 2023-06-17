<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\QiNiu\Http\Controllers;

use App\Fresns\Api\Traits\ApiResponseTrait;
use App\Helpers\CacheHelper;
use App\Models\File;
use App\Models\PluginCallback;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Plugins\QiNiu\Http\Requests\UploadFileInfoDTO;
use Plugins\QiNiu\Traits\QiNiuStorageTrait;

class QiNiuApiController extends Controller
{
    use ApiResponseTrait;
    use QiNiuStorageTrait;

    public function callback(string $ulid)
    {
        // 接收到七牛请求
        $data = \request()->all();

        $pluginCallback = PluginCallback::query()->where('ulid', $ulid)->first();

        if (! $pluginCallback) {
            return $this->failure(3e4, '未找到 callback 信息 '.$ulid);
        }

        if ($pluginCallback->content['sence'] != 'transcoding') {
            $pluginCallback->update([
                'is_used' => true,
            ]);

            return;
        }

        $fid = $pluginCallback->content['file']['fid'] ?? null;
        $file = File::where('fid', $fid)->first();

        if (empty($fid) || empty($file)) {
            return;
        }

        // 失败
        if ($data['code'] == 3) {
            $file->update([
                'transcoding_state' => File::TRANSCODING_STATE_FAILURE,
                'transcoding_reason' => $data['items'][0]['error'] ?? null,
            ]);

            return;
        }

        // 成功
        if ($data['code'] == 0) {
            $file->update([
                'original_path' => $file->path,
            ]);

            $diskPath = $pluginCallback->content['save_path'];

            /** @var \Overtrue\Flysystem\Qiniu\QiniuAdapter $adapter */
            $adapter = $this->setType($file->type)->getAdapter();
            [$stat, $error] = $adapter->getBucketManager()->stat($this->getBucketName(), $diskPath);

            $meta = [];
            if (! $error) {
                $meta = array_merge([
                    'mime' => $stat['mimeType'],
                    'extension' => pathinfo($diskPath, PATHINFO_EXTENSION),
                    'size' => $stat['fsize'],
                    'md5' => $stat['md5'],
                    'sha' => $stat['hash'],
                    'sha_type' => 'hash',
                ]);
            }

            $file->update(array_merge([
                'path' => $diskPath,
                'transcoding_state' => File::TRANSCODING_STATE_DONE,
            ], $meta));

            CacheHelper::forgetFresnsFileUsage($file->id);
        }

        $pluginCallback->update([
            'is_used' => true,
        ]);

        return $this->success(null, '操作 '.$ulid);
    }

    public function uploadFileInfo(Request $request)
    {
        $dtoRequest = new UploadFileInfoDTO($request->all());

        $bodyInfo = [
            'platformId' => $dtoRequest->platformId,
            'usageType' => $dtoRequest->usageType,
            'tableName' => $dtoRequest->tableName,
            'tableColumn' => $dtoRequest->tableColumn,
            'tableId' => $dtoRequest->tableId ?? null,
            'tableKey' => $dtoRequest->tableKey ?? null,
            'aid' => $dtoRequest->aid,
            'uid' => $dtoRequest->uid,
            'type' => (int) $dtoRequest->type,
            'fileInfo' => $dtoRequest->fileInfo,
        ];

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->uploadFileInfo($bodyInfo);

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->errorResponse();
        }

        return $this->success($fresnsResp->getData());
    }
}
