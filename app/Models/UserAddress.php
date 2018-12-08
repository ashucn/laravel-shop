<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 代码解析：
 * protected $dates = ['last_used_at']; 表示 last_used_at 字段是一个时间日期类型，在之后的代码中
 * $address->last_used_at 返回的就是一个时间日期对象（确切说是 Carbon 对象）。
 * public function user() 与 User 模型关联，关联关系是一对多
 * （一个 User 可以有多个 UserAddress，一个 UserAddress 只能属于一个 User）。
 * public function getFullAddressAttribute() 创建了一个访问器，
 * 在之后的代码里可以直接通过 $address->full_address 来获取完整的地址，而不用每次都去拼接。
 *
 * Class UserAddress
 * @package App\Models
 */
class UserAddress extends Model
{
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at',
    ];

    protected $dates = ['last_used_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 修改器文档：
     * https://laravel-china.org/docs/laravel/5.5/eloquent-mutators/1406#accessors-and-mutators
     * @return string
     */
    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
