<?php

namespace Tests\Feature\Catalog;

use App\Models\ActivityImage;
use App\Models\BusinessActivity;
use App\Services\ImageUploader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\TestCase;

class ImageUploaderTest extends TestCase
{
    use RefreshDatabase;

    private ImageUploader $uploader;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->uploader = new ImageUploader();
    }

    private function info(string $path): array
    {
        return getimagesizefromstring(Storage::disk('public')->get($path));
    }

    public function test_jpeg_is_converted_to_webp_and_resized_when_too_wide(): void
    {
        $path = $this->uploader->store(UploadedFile::fake()->image('foto.jpg', 3200, 1600), 'activities');

        $this->assertMatchesRegularExpression('#^activities/[0-9a-f-]{36}\.webp$#', $path);
        Storage::disk('public')->assertExists($path);

        $info = $this->info($path);
        $this->assertSame(IMAGETYPE_WEBP, $info[2]);
        $this->assertSame(2000, $info[0]);
        $this->assertSame(1000, $info[1]);
    }

    public function test_small_image_is_never_upscaled(): void
    {
        $path = $this->uploader->store(UploadedFile::fake()->image('kecil.png', 640, 480), 'activities');

        $info = $this->info($path);
        $this->assertSame(IMAGETYPE_WEBP, $info[2]);
        $this->assertSame([640, 480], [$info[0], $info[1]]);
    }

    public function test_png_transparency_is_preserved(): void
    {
        $im = imagecreatetruecolor(100, 100);
        imagealphablending($im, false);
        imagesavealpha($im, true);
        imagefill($im, 0, 0, imagecolorallocatealpha($im, 255, 0, 0, 127));
        ob_start();
        imagepng($im);
        $png = ob_get_clean();

        $path = $this->uploader->store(UploadedFile::fake()->createWithContent('logo.png', $png), 'settings');

        $out = imagecreatefromstring(Storage::disk('public')->get($path));
        $alpha = (imagecolorat($out, 50, 50) >> 24) & 0x7F;
        $this->assertGreaterThan(100, $alpha);
    }

    public function test_output_is_smaller_than_a_lossless_source(): void
    {
        $file = UploadedFile::fake()->image('besar.png', 1600, 1000);
        $original = filesize($file->getRealPath());

        $path = $this->uploader->store($file, 'activities');

        $this->assertLessThan($original, Storage::disk('public')->size($path));
    }

    public function test_non_image_content_is_rejected_and_nothing_is_written(): void
    {
        $this->expectException(InvalidArgumentException::class);

        try {
            $this->uploader->store(UploadedFile::fake()->createWithContent('palsu.jpg', '<?php echo 1;'), 'activities');
        } finally {
            $this->assertSame([], Storage::disk('public')->allFiles());
        }
    }

    public function test_directory_is_validated(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->uploader->store(UploadedFile::fake()->image('a.jpg'), '../etc');
    }

    public function test_validation_rules_reject_svg_and_oversized_files(): void
    {
        $rules = ['photo' => ImageUploader::rules()];

        $svg = UploadedFile::fake()->createWithContent('x.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>');
        $big = UploadedFile::fake()->image('big.jpg', 100, 100)->size(6000);
        $ok = UploadedFile::fake()->image('ok.jpg', 100, 100);

        $this->assertTrue(Validator::make(['photo' => $svg], $rules)->fails());
        $this->assertTrue(Validator::make(['photo' => $big], $rules)->fails());
        $this->assertTrue(Validator::make(['photo' => $ok], $rules)->passes());
    }

    public function test_release_skips_external_urls_and_blank_values(): void
    {
        $this->assertFalse($this->uploader->release('https://images.unsplash.com/a.jpg'));
        $this->assertFalse($this->uploader->release(null));
        $this->assertFalse($this->uploader->release('../.env'));
    }

    public function test_release_deletes_unreferenced_file_but_keeps_referenced_one(): void
    {
        $kept = $this->uploader->store(UploadedFile::fake()->image('a.jpg'), 'activities');
        $orphan = $this->uploader->store(UploadedFile::fake()->image('b.jpg'), 'activities');

        ActivityImage::factory()->create(['image_path' => $kept]);
        BusinessActivity::factory()->create(['image_path' => $kept]);

        $this->assertFalse($this->uploader->release($kept));
        Storage::disk('public')->assertExists($kept);

        $this->assertTrue($this->uploader->release($orphan));
        Storage::disk('public')->assertMissing($orphan);
    }

    public function test_store_from_request_returns_null_without_a_file_and_stores_with_one(): void
    {
        $this->assertNull($this->uploader->storeFromRequest(Request::create('/x', 'POST'), 'activities'));

        $request = Request::create('/x', 'POST', [], [], ['cover' => UploadedFile::fake()->image('a.jpg', 300, 200)]);
        $path = $this->uploader->storeFromRequest($request, 'activities', 'cover');

        $this->assertMatchesRegularExpression('#^activities/[0-9a-f-]{36}\.webp$#', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_unreadable_files_become_a_validation_error_on_the_field(): void
    {
        $bad = UploadedFile::fake()->createWithContent('palsu.jpg', 'bukan gambar');

        try {
            $this->uploader->storeFromRequest(Request::create('/x', 'POST', [], [], ['image' => $bad]), 'activities');
            $this->fail('ValidationException seharusnya dilempar.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('Gambar tidak dapat diproses', $e->errors()['image'][0]);
            $this->assertSame('default', $e->errorBag);
        }
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_store_or_invalid_accepts_a_custom_message_and_error_bag(): void
    {
        $bad = UploadedFile::fake()->createWithContent('palsu.jpg', 'bukan gambar');

        try {
            $this->uploader->storeOrInvalid($bad, 'activities', 'images', 'Pesan khusus.', errorBag: 'image7');
            $this->fail('ValidationException seharusnya dilempar.');
        } catch (ValidationException $e) {
            $this->assertSame(['images' => ['Pesan khusus.']], $e->errors());
            $this->assertSame('image7', $e->errorBag);
        }
    }
}
