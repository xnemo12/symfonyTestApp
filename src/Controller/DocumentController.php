<?php

namespace App\Controller;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class DocumentController extends AbstractController
{

    /**
     * @Rest\Get("/document")
     * @param Request $request
     * @param DocumentRepository $repository
     * @return JsonResponse
     */
    public function getDocuments(Request $request, DocumentRepository $repository)
    {
        $q = $request->get('q', null);
        $perPage = $request->get('perPage', 10);
        $page = $request->get('page', 1);
        $sort = $request->get('sort', 'ASC');

        $documents = $repository->getAll($q, $perPage, $page, $sort);
        $count = $repository->getCount($q);

        return $this->json(['data'=>$documents, 'count'=>$count],JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Get("/document/{id}")
     * @param Document $document
     * @return JsonResponse
     */
    public function getDocument(Document $document)
    {
        if($document)
            return $this->json($document, JsonResponse::HTTP_OK);

        return $this->json(['msg'=>'Not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    /**
     * @Rest\Post("/document")
     * @param Request $request
     * @return JsonResponse
     */
    public function postDocument(Request $request)
    {
        $document = new Document();
        $document->setName($request->get('name'));
        $document->setDescription($request->get('description'));

        $em = $this->getDoctrine()->getManager();
        $em->persist($document);
        $em->flush();

        return $this->json([
            'document' => $document
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @Rest\Put("/document/{id}")
     * @param Document $document
     * @param Request $request
     * @return JsonResponse
     */
    public function updateDocument(Document $document, Request $request)
    {
        if ($document){
            $document->setName($request->get('name'));
            $document->setDescription($request->get('description'));

            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();
        }

        return $this->json([], JsonResponse::HTTP_ACCEPTED);
    }

    /**
     * @Rest\Delete("/document/{id}")
     * @param Document $document
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function deleteDocument(Document $document)
    {
        if ($document){
            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();
        }

        return $this->json([], JsonResponse::HTTP_NO_CONTENT);
    }
}
