<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;

class MctoolsDownload extends Model
{
    protected $fillable = [
        'item_id',
        'item_name',
        'version_id',
        'version_name',
        'provider',
        'category',
        'file_size',
        'server_id',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'server_id' => 'integer',
    ];
}
