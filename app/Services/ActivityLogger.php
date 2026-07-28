<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log(string $action, string $module, array $data = []): void
    {
        try {
            $user = Auth::user();
            
            ActivityLog::create([
                'user_id' => $user?->id,
                'user_name' => $user?->name ?? $user?->email ?? 'Sistem',
                'user_role' => $user?->roles->first()?->name ?? 'system',
                'action' => $action,
                'module' => $module,
                'target_type' => $data['target_type'] ?? null,
                'target_id' => $data['target_id'] ?? null,
                'target_name' => $data['target_name'] ?? null,
                'parent_type' => $data['parent_type'] ?? null,
                'parent_id' => $data['parent_id'] ?? null,
                'parent_name' => $data['parent_name'] ?? null,
                'description' => $data['description'] ?? null,
                'extra_data' => $data['extra_data'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the main flow
            report($e);
        }
    }

    // Convenience methods
    public static function created($model, string $description = null): void
    {
        self::log('created', self::getModule($model), [
            'target_type' => get_class($model),
            'target_id' => $model->id,
            'target_name' => $model->name ?? $model->ad ?? $model->title ?? ' #' . $model->id,
            'description' => $description ?? get_class($model) . ' oluşturuldu',
        ]);
    }

    public static function updated($model, string $description = null): void
    {
        self::log('updated', self::getModule($model), [
            'target_type' => get_class($model),
            'target_id' => $model->id,
            'target_name' => $model->name ?? $model->ad ?? $model->title ?? ' #' . $model->id,
            'description' => $description ?? get_class($model) . ' güncellendi',
        ]);
    }

    public static function deleted($model, string $description = null): void
    {
        self::log('deleted', self::getModule($model), [
            'target_type' => get_class($model),
            'target_id' => $model->id,
            'target_name' => $model->name ?? $model->ad ?? $model->title ?? ' #' . $model->id,
            'description' => $description ?? get_class($model) . ' silindi',
        ]);
    }

    public static function approved($model, string $description = null): void
    {
        self::log('approved', self::getModule($model), [
            'target_type' => get_class($model),
            'target_id' => $model->id,
            'target_name' => $model->name ?? $model->ad ?? $model->title ?? ' #' . $model->id,
            'description' => $description ?? get_class($model) . ' onaylandı',
        ]);
    }

    public static function rejected($model, string $reason = null): void
    {
        self::log('rejected', self::getModule($model), [
            'target_type' => get_class($model),
            'target_id' => $model->id,
            'target_name' => $model->name ?? $model->ad ?? $model->title ?? ' #' . $model->id,
            'description' => $reason ? 'Reddedildi: ' . $reason : get_class($model) . ' reddedildi',
        ]);
    }

    private static function getModule($model): string
    {
        $class = get_class($model);
        
        return match(true) {
            str_contains($class, 'Sinif') => 'sinif',
            str_contains($class, 'Ogrenci') => 'ogrenci',
            str_contains($class, 'User') => 'user',
            str_contains($class, 'Veli') => 'veli',
            default => 'other',
        };
    }
}