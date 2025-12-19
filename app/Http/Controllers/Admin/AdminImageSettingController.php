<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImageSetting;
use Illuminate\Http\Request;

class AdminImageSettingController extends Controller
{
    public function index()
    {
        $settings = ImageSetting::asArray();

        return view('admin.image-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'IMAGE_THUMB_WIDTH'             => 'required|integer|min:50|max:2000',
            'IMAGE_MEDIA_WIDTH'             => 'required|integer|min:200|max:5000',
            'IMAGE_THUMB_QUALITY'           => 'required|integer|min:1|max:100',
            'IMAGE_MEDIA_QUALITY'           => 'required|integer|min:1|max:100',
            'IMAGE_WATERMARK_OPACITY'       => 'required|integer|min:0|max:100',
            'IMAGE_WATERMARK_SCALE_PERCENT' => 'required|integer|min:1|max:100',
            'IMAGE_WATERMARK_TILE_SPACING'  => 'required|integer|min:0|max:1000',

            // optional upload
            'watermark_file' => 'nullable|image|mimes:png|max:2048',
        ]);

        /*
         |------------------------------------------------------------
         | Handle watermark upload (NO Storage::)
         |------------------------------------------------------------
         */
        if ($request->hasFile('watermark_file')) {
            $file = $request->file('watermark_file');

            $targetDir = public_path('uploads/watermark');

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $filename = 'wm.png'; // fixed name
            $file->move($targetDir, $filename);

            ImageSetting::set(
                'IMAGE_WATERMARK_PATH',
                'public/uploads/watermark/' . $filename
            );
        }

        // Save numeric/text settings
        foreach ($validated as $key => $value) {
            if ($key !== 'watermark_file') {
                ImageSetting::set($key, $value);
            }
        }

        return redirect()
            ->route('admin.image-settings.index')
            ->with('success', 'Configurações de imagem atualizadas com sucesso!');
    }
}
