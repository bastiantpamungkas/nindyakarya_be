<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Progres;
use App\Models\LogHistory;

class ProgresObserver
{
    /**
     * Handle the Progres "created" event.
     */
    public function created(Progres $progres): void
    {
        LogHistory::create([
            'user_id' => Auth::user()->id,
            'action' => 'created',
            'model_type' => Progres::class,
            'model_id' => $progres->id,
            'description' => 'Progres created with ID: ' . $progres->id,
        ]);
    }

    /**
     * Handle the Progres "updated" event.
     */
    public function updated(Progres $progres): void
    {
        $description = 'Progres updated with ID: ' . $progres->id;
        if ($progres->isDirty('status')) {
            $description .= ', Status changed from ' . $progres->getOriginal('status') . ' to ' . $progres->status;
        }

        LogHistory::create([
            'user_id' => Auth::user()->id,
            'action' => 'updated',
            'model_type' => Progres::class,
            'model_id' => $progres->id,
            'description' => $description,
        ]);
    }

    /**
     * Handle the Progres "deleted" event.
     */
    public function deleted(Progres $progres): void
    {
        LogHistory::create([
            'user_id' => Auth::user()->id,
            'action' => 'deleted',
            'model_type' => Progres::class,
            'model_id' => $progres->id,
            'description' => 'Progres deleted with ID: ' . $progres->id,
        ]);
    }
}
