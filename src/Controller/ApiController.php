<?php


namespace App\Controller;


use App\Entity\Event;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends Controller
{
    /**
     * @Route("/hide/{id}", name="hide")
     */
    public function hide(Event $event){
        $event->setHidden(true);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse('ok');
    }

    /**
     * @Route("/star/{id}", name="star")
     */
    public function star(Event $event){
        $event->setStarred(true);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse('ok');
    }

    /**
     * @Route("/unstar/{id}", name="unstar")
     */
    public function unstar(Event $event){
        $event->setStarred(false);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse('ok');

    }
}