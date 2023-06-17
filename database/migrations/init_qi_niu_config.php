<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

use App\Models\FileUsage;
use App\Utilities\SubscribeUtility;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $fresnsWordBody = [
        'type' => SubscribeUtility::TYPE_TABLE_DATA_CHANGE,
        'fskey' => 'QiNiu',
        'cmdWord' => 'audioVideoTranscoding',
        'subject' => FileUsage::class,
    ];

    // Run the migrations.
    public function up(): void
    {
        \FresnsCmdWord::plugin()->addSubscribeItem($this->fresnsWordBody);
    }

    // Reverse the migrations.
    public function down(): void
    {
        \FresnsCmdWord::plugin()->removeSubscribeItem($this->fresnsWordBody);
    }
};
