<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div class="lg:col-span-3">
            {{ $this->content }}
        </div>
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">E-posta ile İletişim</h2>
                </div>
                <div class="p-5 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mb-3">
                        <x-filament::icon name="heroicon-o-envelope" class="w-6 h-6" />
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Öğretmeninize doğrudan e-posta gönderebilirsiniz.</p>
                    <p class="text-xs text-gray-400 mb-4">alfabe.co mail adresiniz üzerinden</p>
                    <x-filament::button color="primary" onclick="contactTeacher()">
                        E-posta Oluştur
                    </x-filament::button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function contactTeacher() {
            var name = @json(count($students) > 0 ? $students[0]['user']['name'] : 'Öğrenci');
            window.location.href = 'mailto:ogretmen@alfabe.co?subject=' + encodeURIComponent(name + ' hakkında bilgi talebi') + '&body=' + encodeURIComponent('Merhaba Öğretmenim,\n\nGrafikte bir değişiklik fark ettim ve değerlendirme rica ediyorum.\n\nTeşekkürler.');
        }
    </script>
    @endpush
</x-filament-panels::page>
