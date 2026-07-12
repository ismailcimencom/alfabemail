<?php

namespace App\Filament\Portal\Resources\Odevler\Pages;

use App\Filament\Portal\Resources\Odevler\OdevResource;
use App\Models\BekleyenTakvimEtkinligi;
use App\Models\Ogrenci;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOdev extends CreateRecord
{
    protected static string $resource = OdevResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getSubmitFormAction(),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $selectedStudents = $data['ogrenciler'] ?? [];
        $sinifId = $data['sinif_id'];
        unset($data['ogrenciler']);

        $odev = static::getModel()::create($data);

        if (!empty($selectedStudents)) {
            $ogrenciler = Ogrenci::whereIn('id', $selectedStudents)->get();
        } else {
            $ogrenciler = Ogrenci::where('sinif_id', $sinifId)->get();
        }

        $attachData = [];
        foreach ($ogrenciler as $ogrenci) {
            $attachData[$ogrenci->id] = ['tamamlandi' => false];
        }
        $odev->ogrenciler()->attach($attachData);

        foreach ($ogrenciler as $ogrenci) {
            BekleyenTakvimEtkinligi::create([
                'ogrenci_id' => $ogrenci->id,
                'odev_id' => $odev->id,
                'baslik' => $data['baslik'],
                'aciklama' => $data['aciklama'] ?? null,
                'teslim_tarihi' => $data['teslim_tarihi'],
            ]);
        }

        return $odev;
    }
}
