<?php

namespace KlinkDMS\Listeners;

use Avvertix\TusUpload\Events\TusUploadStarted;
use Klink\DmsDocuments\DocumentsService;
use Log;

class TusUploadStartedHandler
{
    /**
     *
     *
     * @var \Klink\DmsDocuments\DocumentsService
     */
    private $documentsService = null;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DocumentsService $documentsService)
    {
        $this->documentsService = $documentsService;
    }

    /**
     * Handle the event.
     *
     * @param  TusUploadStarted  $event
     * @return void
     */
    public function handle(TusUploadStarted $event)
    {
        Log::info("Upload {$event->upload->request_id} started.");
        
        try {
            $mime = $event->upload->mimetype ? $event->upload->mimetype : \KlinkDocumentUtils::get_mime($event->upload->filename);

            // creating the File entry

            $file = $this->documentsService->createFile(
                $event->upload->user_id,
                $event->upload->filename,
                $mime,
                '',
                hash('sha512', $event->upload->request_id.$event->upload->user_id.$event->upload->filename), // used as the hash value as the file is not yet here
                $event->upload->size
            );

            // links the newly created File to the upload job
            $file->request_id = $event->upload->request_id;
            $file->upload_started = true;

            $file->save();

            // Creating the correspondent DocumentDescriptor
            // TODO: handle addding document to collection

            $descriptor = $this->documentsService->createDocumentDescriptor($file->fresh());
        } catch (\Exception $ex) {
            Log::error('File creation error while handling the TusUploadStarted event.', ['upload' => $event->upload,'error' => $ex]);

            // not throwing the exception outside as this is executed asynchronously
        }
    }
}
