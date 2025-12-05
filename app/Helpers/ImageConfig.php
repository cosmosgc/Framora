<?php
// app/Helpers/ImageConfig.php
namespace App\Helpers;

use App\Models\ImageSetting;

class ImageConfig
{
    public static function get(string $key, $default = null)
    {
        // primeiro tenta DB (se existir tabela)
        if (class_exists(ImageSetting::class)) {
            try {
                $row = ImageSetting::where('key', $key)->first();
                if ($row && $row->value !== null) {
                    return $row->value;
                }
            } catch (\Exception $e) {
                // ignora se tabela não existir ou erro
            }
        }

        // fallback para env
        return env($key, $default);
    }
}
