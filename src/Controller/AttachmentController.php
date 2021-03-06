<?php

namespace App\Controller;

use App\Entity\Attachment;
use App\Entity\Document;;
use App\Util\FileUpload;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


class AttachmentController extends AbstractController
{
    /**
     * @Rest\Get("/attachments/{id}")
     * @param Document $document
     * @return JsonResponse
     */
    public function getAttachments(Document $document)
    {
        if(!$document)
            return $this->json(['msg'=>'Not found'], JsonResponse::HTTP_NOT_FOUND);

        $em = $this->getDoctrine()->getManager();

        $attachments = $em->getRepository(Attachment::class)->findByDocument($document->getId());
        return $this->json($attachments, JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Post("/attachment/{id}")
     * @param Document $document
     * @param Request $request
     * @param FileUpload $uploader
     * @return JsonResponse
     */
    public function postAttachment(Document $document, Request $request, FileUpload $uploader)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $file = $request->files->get('attach');

            if (!$document)
                return $this->json(["msg"=>"Document not found"],JsonResponse::HTTP_NOT_FOUND);

            $attachment = new Attachment();
            $attachment->setOriginalName($file->getClientOriginalName());
            $attachment->setExtension(!is_null($file->guessExtension()) ? : "temp" );
            $attachment->setSize($file->getSize());
            $attachment->setDocument($document);

            $fileName = $uploader->upload($file, $document->getId());

            $attachment->setFileName($fileName);
            $em->persist($attachment);
            $em->flush();

            return $this->json(["msg"=>"success"],JsonResponse::HTTP_CREATED);
        }
        catch(\Exception $ex)
        {
            return $this->json(['msg'=>$ex->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * @Rest\Delete("/attachment/{id}")
     * @param Attachment $attachment
     * @param FileUpload $uploader
     * @return JsonResponse
     */
    public function deleteAttachment(Attachment $attachment, FileUpload $uploader)
    {
        try
        {
            if ($attachment)
            {
                $em = $this->getDoctrine()->getManager();
                $em->remove($attachment);
                $em->flush();
                $uploader->remove($attachment);
            }
        }
        catch(\Exception $ex)
        {}
        return $this->json([], JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Post("/attachment/order/{srcAttach}/{destAttach}")
     * @param Attachment $srcAttach
     * @param Attachment $destAttach
     * @return JsonResponse
     */
    public function orderAttachment(Attachment $srcAttach, Attachment $destAttach)
    {
        $destPosition = $destAttach->getPosition();
        $destAttach->setPosition($srcAttach->getPosition());
        $srcAttach->setPosition($destPosition);

        $em = $this->getDoctrine()->getManager();

        $em->persist($srcAttach);
        $em->persist($destAttach);
        $em->flush();

        return $this->json(['msg'=>'Positions changed'], JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Get("/download/{id}", name="download_file")
     * @param Attachment $attachment
     * @param FileUpload $uploader
     * @return BinaryFileResponse
     */
    public function downloadFileAction(Attachment $attachment, FileUpload $uploader)
    {
        $filePath = $uploader->getFilePath($attachment->getPath());
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,$attachment->getOriginalName());
        return $response;
    }
}
