<?php


namespace App\Controller;


use App\Import\Importer;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/webhooks", name="webhooks_")
 */
class WebHookController extends Controller
{

    /**
     * @Route("/import-events", name="import_events")
     */
    public function importEvents(Importer $importer){
        $day = Carbon::today();
        $message = $importer->import($day);

        $client = new  Client();
        $client->request('POST', getenv('WEBHOOK_SEND_MESSAGE'), ['json' => ['text' => $message]]);

        return new JsonResponse($message);
    }
}