<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogObserver
{
    public function created($model)
    {
        $this->log($model, 'created');
    }

    public function updated($model)
    {
        $this->log($model, 'updated');
    }

    public function deleted($model)
    {
        $this->log($model, 'deleted');
    }

    private function log($model, string $action)
    {
        try {
            $user = Auth::user();
            $module = $this->getModule($model);
            
            if (!$module) return;

            ActivityLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? $user?->email ?? 'Sistem',
                'user_role' => $user?->roles->first()?->name ?? 'system',
                'action' => $action,
                'module' => $module,
                'target_type' => get_class($model),
                'target_id' => $model->id,
                'target_name' => $model->name ?? $model->ad ?? $model->title ?? $model->user?->name ?? ' #' . $model->id,
                'description' => $action === 'created' ? 'Oluşturuldu' : ($action === 'updated' ? 'Güncellendi' : 'Silindi'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            report($e);
        }
    }

    private function getModule($model): ?string
    {
        return match(true) {
            $model instanceof \App\Models\Sinif => 'sinif',
            $model instanceof \App\Models\Ogrenci => 'ogrenci',
            $model instanceof \App\Models\User => 'user',
            $model instanceof \App\Models\Veli => 'veli',
            default => null,
        };
    }
}