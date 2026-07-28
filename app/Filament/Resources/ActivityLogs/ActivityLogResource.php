<?php

namespace App\Filament\Resources\ActivityLogs;

use App\Models\ActivityLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;
    protected static ?string $navigationLabel = 'Aktivite Logları';
    protected static ?string $label = 'Aktivite Logları';
    protected static bool $shouldRegisterNavigation = true;
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('admin');
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                TextColumn::make('user_name')
                    ->label('Yapan Kişi')
                    ->searchable(),
                
                TextColumn::make('user_role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'admin' => 'warning',
                        'ogretmen' => 'primary',
                        'veli' => 'gray',
                        default => 'gray',
                    }),
                
                TextColumn::make('action')
                    ->label('İşlem')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'approved' => 'info',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                
                TextColumn::make('module')
                    ->label('Modül')
                    ->badge()
                    ->color('primary'),
                
                TextColumn::make('description')
                    ->label('Açıklama')
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('module')
                    ->label('Modül')
                    ->options([
                        'sinif' => 'Sınıf',
                        'ogrenci' => 'Öğrenci',
                        'user' => 'Kullanıcı',
                    ]),
                SelectFilter::make('action')
                    ->label('İşlem')
                    ->options([
                        'created' => 'Oluşturuldu',
                        'updated' => 'Güncellendi',
                        'deleted' => 'Silindi',
                        'approved' => 'Onaylandı',
                        'rejected' => 'Reddedildi',
                        'login' => 'Giriş yaptı',
                        'logout' => 'Çıkış yaptı',
                    ]),
                SelectFilter::make('user_role')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Admin',
                        'ogretmen' => 'Öğretmen',
                    ]),
                SelectFilter::make('tarih')
                    ->label('Tarih')
                    ->options([
                        'today' => 'Bugün',
                        'this_week' => 'Bu Hafta',
                        'this_month' => 'Bu Ay',
                    ])
                    ->query(function ($query, $value) {
                        if (!$value) return $query;
                        switch ($value) {
                            case 'today':
                                return $query->whereDate('created_at', today());
                            case 'this_week':
                                return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                            case 'this_month':
                                return $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                        }
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([20, 50, 100]);
    }
}