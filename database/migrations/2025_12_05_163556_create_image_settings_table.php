<?php
// database/migrations/xxxx_xx_xx_create_image_settings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateImageSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('image_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // opcional: seed valores padrão
        DB::table('image_settings')->insert([
            ['key' => 'IMAGE_THUMB_WIDTH', 'value' => '300'],
            ['key' => 'IMAGE_MEDIA_WIDTH', 'value' => '1200'],
            ['key' => 'IMAGE_THUMB_QUALITY', 'value' => '60'],
            ['key' => 'IMAGE_MEDIA_QUALITY', 'value' => '80'],
            ['key' => 'IMAGE_WATERMARK_PATH', 'value' => 'public/uploads/watermark/wm.png'],
            ['key' => 'IMAGE_WATERMARK_OPACITY', 'value' => '40'],
            ['key' => 'IMAGE_WATERMARK_SCALE_PERCENT', 'value' => '15'],
            ['key' => 'IMAGE_WATERMARK_TILE_SPACING', 'value' => '0'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('image_settings');
    }
}
