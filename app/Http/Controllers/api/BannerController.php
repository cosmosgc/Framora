<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Banner::where('ativo', true)
                ->orderBy('ordem')
                ->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'link' => 'nullable|string|max:255',
            'ordem' => 'nullable|integer',
            'imagem' => 'required|image|max:4096'
        ]);

        // Ensure directory exists
        $destinationPath = public_path('uploads/banner');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Generate unique filename
        $file = $request->file('imagem');
        $filename = uniqid('banner_') . '.' . $file->getClientOriginalExtension();

        // Move file
        $file->move($destinationPath, $filename);

        // Save relative path
        $banner = Banner::create([
            'titulo' => $data['titulo'],
            'descricao' => $data['descricao'] ?? null,
            'link' => $data['link'] ?? null,
            'ordem' => $data['ordem'] ?? 0,
            'imagem' => 'uploads/banner/' . $filename,
            'ativo' => true
        ]);

        return response()->json([
            'success' => true,
            'data' => $banner
        ]);
    }


    public function show(Banner $banner)
    {
        return response()->json([
            'success' => true,
            'data' => $banner
        ]);
    }

    public function destroy(Banner $banner)
    {
        if ($banner->imagem) {
            $filePath = public_path($banner->imagem);

            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $banner->delete();

        return response()->json([
            'success' => true
        ]);
    }

}