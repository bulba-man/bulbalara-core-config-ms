<?php

namespace Bulbalara\CoreConfigMs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConfigModel extends Model
{
    public $timestamps = false;

    protected $casts = [
        'resettable' => 'boolean',
        'methods' => 'array',
    ];

    protected $primaryKey = 'config_id';

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
        $connection = config('bl.config.database.connection') ?: config('database.default');
        $table = config('bl.config.db.table', 'core_config_admin');

        $this->setConnection($connection);
        $this->setTable($table);
    }

    public function coreConfig(): BelongsTo
    {
        return $this->belongsTo(\Bulbalara\CoreConfig\Models\Config::class, 'config_id');
    }
}
