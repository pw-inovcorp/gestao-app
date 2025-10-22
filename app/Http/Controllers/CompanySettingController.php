<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanySettingController extends Controller
{
    //
    public function edit()
    {
        $settings = CompanySetting::getSettings();

        return Inertia::render('CompanySetting/Edit', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $settings = CompanySetting::getSettings();

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:255', 'min:2'],
            'morada' => ['nullable', 'string', 'max:255'],
            'codigo_postal' => ['nullable', 'string', 'regex:/^\d{4}-\d{3}$/'],
            'localidade' => ['nullable', 'string', 'max:255'],
            'numero_contribuinte' => ['required', 'string', 'size:9'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp,svg', 'max:2048'],
            'remove_logo' => ['nullable', 'string']
        ], [
            'nome.required' => 'O nome da empresa é obrigatório.',
            'nome.min' => 'O nome deve ter no mínimo 2 caracteres.',
            'codigo_postal.regex' => 'O código postal deve ter o formato XXXX-XXX.',
            'numero_contribuinte.required' => 'O número de contribuinte é obrigatório.',
            'numero_contribuinte.size' => 'O número de contribuinte deve ter exatamente 9 dígitos.',
            'logo.image' => 'O ficheiro deve ser uma imagem.',
            'logo.max' => 'A imagem não pode ter mais de 2MB.',
        ]);


        if ($request->has('remove_logo')) {
            if ($settings->logo && \Storage::disk('public')->exists($settings->logo)) {
                \Storage::disk('public')->delete($settings->logo);
            }
            $validated['logo'] = null;

        } elseif ($request->hasFile('logo')) {

            if ($settings->logo && \Storage::disk('public')->exists($settings->logo)) {
                \Storage::disk('public')->delete($settings->logo);
            }

            $path = $request->file('logo')->store('company', 'public');
            $validated['logo'] = $path;
        } else {

            unset($validated['logo']);
        }

        unset($validated['remove_logo']);


        $settings->update($validated);

        return redirect()->route('home')
            ->with('success', 'Configurações da empresa atualizadas com sucesso.');
    }
}


