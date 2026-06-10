<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmEmailBlast extends Model
{
    protected $fillable = ['subject', 'body', 'status', 'target_count', 'sent_count', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients()
    {
        return $this->hasMany(CrmEmailBlastRecipient::class, 'email_blast_id');
    }
}
