<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\AdminApproval;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => substr($state, 0, 5) . '*****'),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('info'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('admin_approval_status')
                    ->label('Admin Onay')
                    ->badge()
                    ->color(fn ($record) => !$record->hasRole('admin') ? 'gray' : ($record->isFullyApproved() ? 'success' : 'warning'))
                    ->formatStateUsing(function ($record) {
                        if (!$record->hasRole('admin')) return '—';
                        if ($record->isFullyApproved()) return 'Onaylandı';
                        $total = $record->adminApprovalsRequested()->count();
                        $done = $record->adminApprovalsRequested()->whereNotNull('approved_at')->count();
                        return "Onay Bekliyor ({$done}/{$total})";
                    })
                    ->visible(fn () => auth()->user()?->hasRole('admin')),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Roller')
                    ->relationship('roles', 'name', fn ($q) => $q->where('name', '!=', 'yonetici'))
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record) => auth()->user()?->hasRole('admin') && (!$record->hasRole('admin') || $record->id === auth()->id())),
                Action::make('approve_admin')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(function ($record) {
                        $user = auth()->user();
                        if (!$user || !$user->hasRole('admin')) return false;
                        if (!$record->hasRole('admin') || $record->isFullyApproved()) return false;
                        return !$record->adminApprovalsRequested()
                            ->where('approver_user_id', $user->id)
                            ->whereNotNull('approved_at')
                            ->exists();
                    })
                    ->action(function ($record) {
                        $approval = $record->adminApprovalsRequested()
                            ->where('approver_user_id', auth()->id())
                            ->first();

                        if (!$approval) return;

                        $approval->update(['approved_at' => now()]);

                        if ($record->isFullyApproved()) {
                            $record->update(['is_active' => true]);

                            Notification::make()
                                ->title('Admin tamamen onaylandı')
                                ->body("{$record->name} artık admin paneline giriş yapabilir.")
                                ->success()
                                ->send();
                        } else {
                            $total = $record->adminApprovalsRequested()->count();
                            $done = $record->adminApprovalsRequested()->whereNotNull('approved_at')->count();

                            Notification::make()
                                ->title('Onaylandı')
                                ->body("{$done}/{$total} admin onayladı.")
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('admin'))
                        ->action(function ($records) {
                            $adminIds = $records->filter(fn ($r) => $r->hasRole('admin'))->pluck('id');
                            $records = $records->reject(fn ($r) => $r->hasRole('admin'));
                            if ($adminIds->isNotEmpty()) {
                                Notification::make()
                                    ->title('Admin kullanıcıları silinemez')
                                    ->danger()
                                    ->send();
                            }
                            if ($records->isNotEmpty()) {
                                $records->each->delete();
                            }
                        }),
                ]),
            ]);
    }
}
