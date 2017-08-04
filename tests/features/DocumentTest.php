<?php

use App\Owner;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use LaravelEnso\DocumentsManager\app\Models\Document;
use LaravelEnso\TestHelper\app\Classes\TestHelper;

class DocumentTest extends TestHelper
{
    use DatabaseMigrations;

    private $owner;

    protected function setUp()
    {
        parent::setUp();

        // $this->disableExceptionHandling();
        config()->set('laravel-enso.paths.files', 'testFolder');
        $this->owner = Owner::first();
        $this->signIn(User::first());
    }

    /** @test */
    public function index()
    {
        $this->get('/core/documents/owner/'.$this->owner->id)
            ->assertStatus(200);
    }

    /** @test */
    public function upload()
    {
        $this->json('POST', '/core/documents/upload/owner/'.$this->owner->id, [
            'file' => UploadedFile::fake()->create('document.doc'),
        ]);

        $document = $this->owner->documents->first();
        $this->assertNotNull($document);
        Storage::assertExists('testFolder/'.$document->saved_name);

        $this->cleanUp();
    }

    /** @test */
    public function show()
    {
        $this->uploadDocument();
        $document = $this->owner->documents->first();

        $this->get('/core/documents/show/'.$document->id)
            ->assertStatus(200);

        $this->cleanUp();
    }

    /** @test */
    public function download()
    {
        $this->uploadDocument();
        $document = $this->owner->documents->first();

        $this->get('/core/documents/download/'.$document->id)
            ->assertStatus(200);

        $this->cleanUp();
    }

    /** @test */
    public function destroy()
    {
        $this->uploadDocument();
        $document = $this->owner->documents->first();

        $this->delete('/core/documents/destroy/'.$document->id);

        $this->assertNull($document->fresh());
        Storage::assertMissing('testFolder/'.$document->saved_name);

        $this->cleanUp();
    }

    private function uploadDocument()
    {
        $this->json('POST', '/core/documents/upload/owner/'.$this->owner->id, [
            'file' => UploadedFile::fake()->create('document.doc'),
        ]);
    }

    private function cleanUp()
    {
        Storage::deleteDirectory('testFolder');
    }
}
