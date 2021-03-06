<?php
/**
 * Created by PhpStorm.
 * User: gseidel
 * Date: 02.10.18
 * Time: 14:22
 */

namespace Enhavo\Bundle\MediaBundle\Controller;

use Enhavo\Bundle\AppBundle\Controller\AbstractViewController;
use Enhavo\Bundle\MediaBundle\Exception\StorageException;
use Enhavo\Bundle\MediaBundle\Factory\FileFactory;
use Enhavo\Bundle\MediaBundle\Media\MediaManager;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LibraryController extends AbstractViewController
{
    use FileControllerTrait;

    public function indexAction(Request $request): Response
    {
        $view = $this->viewFactory->create('media_library', [
//            'request' => $request
        ]);
        return $this->viewHandler->handle($view);
    }

    public function uploadAction(Request $request)
    {
        if(!$this->isGranted('ROLE_ENHAVO_MEDIA_LIBRARY_CREATE')) {
            throw $this->createAccessDeniedException();
        }

        $storedFiles = [];
        foreach($request->files as $uploadedFile) {
            try {
                /** @var $uploadedFile UploadedFile */
                if ($uploadedFile->getError() != UPLOAD_ERR_OK) {
                    throw new UploadException('Error in file upload');
                }
                $file = $this->getFileFactory()->createFromUploadedFile($uploadedFile);
                $file->setLibrary(true);
                $this->getMediaManager()->saveFile($file);
                $storedFiles[] = $file;
            } catch(StorageException $exception) {
                foreach($storedFiles as $file) {
                    $this->getMediaManager()->deleteFile($file);
                }
            }
        }

        return $this->getFileResponse($storedFiles);
    }

    /**
     * @return MediaManager
     */
    private function getMediaManager()
    {
        return $this->container->get('enhavo_media.media.media_manager');
    }

    /**
     * @return FileFactory
     */
    private function getFileFactory()
    {
        return $this->container->get('enhavo_media.factory.file');
    }
}
