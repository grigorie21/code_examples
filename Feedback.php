<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{

    const STATUS_NEW = 'new';
    const STATUS_DENY = 'deny';
    const STATUS_ALLOW = 'allow';

    const STATUSES = [
        self::STATUS_NEW => 'новый',
        self::STATUS_DENY => 'отказано',
        self::STATUS_ALLOW => 'принято'
    ];

    protected $fillable = [
        'user_id',
        'order_id',
        'service_id',
        'status',
        'text',
        'created_at',
        'updated_at',
    ];
    public $table = 'feedbacks';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
