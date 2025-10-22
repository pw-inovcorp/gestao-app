<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    //
    protected $fillable = [
        'logo',
        'nome',
        'morada',
        'codigo_postal',
        'localidade',
        'numero_contribuinte',
    ];

    protected $guarded = [];

    public static function getSettings()
    {
        return self::firstOrCreate([
            'id' => 1
        ], [
            'nome' => 'Nome da Empresa',
            'morada' => null,
            'codigo_postal' => null,
            'localidade' => null,
            'numero_contribuinte' => null,
            'logo' => null,
        ]);
    }

    public function hasLogo(): bool
    {
        return !empty($this->logo) && \Storage::disk('public')->exists($this->logo);
    }

    public function getLogoPath(): ?string
    {
        if ($this->hasLogo()) {
            return '/storage/' . $this->logo;
        }
        return null;
    }
}
