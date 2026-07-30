<x-filament-panels::page>
    {{ $this->content }}

    @if(count($allActivities) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Son Aktiviteler</h2>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($allActivities as $activity)
            <div class="px-5 py-3 flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <x-filament::icon name="heroicon-o-user" class="w-4 h-4" />
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-900 truncate">{{ $activity['student_name'] ?? '' }}</p>
                    <p class="text-xs text-gray-400">{{ $activity['description'] ?? $activity['activity'] ?? '-' }}</p>
                </div>
                <span class="text-xs text-gray-400 shrink-0">{{ \Carbon\Carbon::parse($activity['created_at'])->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-filament-panels::page>
