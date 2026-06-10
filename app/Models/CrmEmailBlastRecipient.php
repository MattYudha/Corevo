<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmEmailBlastRecipient extends Model
{
    protected $fillable = ['email_blast_id', 'contact_id', 'email', 'status', 'error_message'];

    public function contact()
    {
        return $this->belongsTo(CrmContact::class, 'contact_id');
    }
}
