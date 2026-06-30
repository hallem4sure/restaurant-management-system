<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\SettingServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct(
        protected SettingServiceInterface $settingService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Setting::class);
        
        $settingsGrouped = $this->settingService->allGrouped();
        
        return view('admin.settings.index', compact('settingsGrouped'));
    }

    public function store(UpdateSettingsRequest $request)
    {
        $this->authorize('create', Setting::class);

        $settings = $request->input('settings', []);

        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            // Store file in storage/app/public/logos
            $path = $file->store('logos', 'public');
            $settings['general.restaurant_logo'] = 'storage/' . $path;
        }

        $this->settingService->updateMultiple($settings);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
}
