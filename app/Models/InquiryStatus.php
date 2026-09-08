<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryStatus extends Model
{
    protected $table = 'inquiries_status';

    protected $fillable = [
        'inq_id',
        'state_id',
        'name',
    ];


}

