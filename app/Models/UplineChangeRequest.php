<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UplineChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'mitra_id', 'requested_by', 'new_upline_id', 'status', 'approved_by'
    ];

    protected static function booted()
    {
        static::updated(function ($request) {
            if ($request->isDirty('status') && $request->status === 'approved') {
                // Update the mitra's supervisor/upline and cabang to the newly approved upline
                $newUpline = User::find($request->new_upline_id);
                $newCabang = $newUpline ? $newUpline->cabang : null;

                $mitra = User::find($request->mitra_id);
                if ($mitra) {
                    $mitra->update([
                        'supervisor_id' => $request->new_upline_id,
                        'cabang' => $newCabang
                    ]);
                }

                if (!$request->approved_by) {
                    $request->updateQuietly(['approved_by' => auth()->id()]);
                }
            }
        });
    }

    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function newUpline()
    {
        return $this->belongsTo(User::class, 'new_upline_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
