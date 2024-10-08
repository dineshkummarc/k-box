<?php

namespace Tests\Unit\Documents\PreviewDriver;

use Tests\TestCase;
use KBox\File;
use KBox\DocumentDescriptor;
use KBox\Documents\FileHelper;

use KBox\Documents\Preview\PdfPreviewDriver;
use Illuminate\Contracts\Support\Renderable;

class PdfPreviewDriverTest extends TestCase
{
    protected function createFileForPath($path)
    {
        list($mimeType) = FileHelper::type($path);

        return File::factory()->create([
            'path' => $path,
            'mime_type' => $mimeType
        ]);
    }
    
    public function test_pdf_can_be_previewed()
    {
        $path = base_path('tests/data/example.pdf');

        $file = $this->createFileForPath($path);

        $document = DocumentDescriptor::factory()->create([
            'file_id' => $file->id,
            'mime_type' => $file->mime_type
        ]);

        $preview = (new PdfPreviewDriver())->preview($file);
        $preview->with(['document' => $document]);
        $html = $preview->render();

        $this->assertInstanceOf(PdfPreviewDriver::class, $preview);
        $this->assertInstanceOf(Renderable::class, $preview);
        $this->assertNotNull($html);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('iframe', $html);
        $this->assertStringContainsString($document->uuid, $html);
        $this->assertStringContainsString($file->uuid, $html);
    }
}
