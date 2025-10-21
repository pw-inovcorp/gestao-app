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

}
