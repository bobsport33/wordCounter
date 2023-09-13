<?php

namespace App\Controller;

use App\Repository\UrlEntryRepository;
use Html2Text\Html2Text;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use GuzzleHttp\Client;


function countWords($text) {
    // clean text
    $text = preg_replace('/https?:\/\/\S+/', '', $text);
    $text = strtolower(preg_replace('/[^A-Za-z0-9\s]/', '', $text));

    $words = explode(' ', $text);
    $totalWords = count($words);

    $wordCounts = [];
    foreach ($words as $word) {
        $word = trim($word);

        if (!empty($word)) { 
            if (array_key_exists($word, $wordCounts)) {
                $wordCounts[$word]++;
            } else { 
                $wordCounts[$word] = 1;
            }
        }
    }

    $wordPercentages = [];
    foreach ($wordCounts as $word => $count) {
        $percentage = ($count / $totalWords) * 100;
        $wordPercentages[$word] = number_format((float)$percentage, 2, '.', '');
    }

    arsort($wordPercentages); 

    $topWordPercentages = array_slice($wordPercentages, 0, 20);

    return $topWordPercentages;
}




class WordCountController extends AbstractController
{

    #[Route('/', name: 'app_index')]
    public function index(UrlEntryRepository $posts): Response
    {
        dd($posts->findAll());
    return $this->render(
      'word-count/index.html.twig'
    );
    }

  #[Route("/add", methods: ['POST'], name: "form_submit")]
  public function processForm(Request $request): Response
    {
        // Parse form submission
        $url = $request->query->get('website_url');
        $id=0;

        // Connect to db

        // Add url to db

        // get id from db to pass to redirect

        // Redirect to webpage about db
        return $this->redirectToRoute('page_details', ['id' => $id]);
    }


    #[Route('/page/{id<\d+>}', name: "page_details")]
    public function showOne(int $id): Response
    {
    // Connect to the database and retrieve the URL
    // $entityManager = $this->getDoctrine()->getManager();
    // $page = $entityManager->getRepository(Page::class)->find($id);

    // if (!$page) {
    //     throw $this->createNotFoundException('Page not found');
    // }

    // Get the URL from the database
    $url = "https://www.chicagotribune.com/";

    // Use Guzzle HTTP Client to fetch the HTML content
    $client = new Client(['verify'=>false]);
    $response = $client->get($url);
    $htmlContent = $response->getBody()->getContents();

    
    // Get html from webpage
    $html2Text = new Html2Text($htmlContent);

    // Convert HTML to plain text
    $text = $html2Text->getText();

    // Now you have the HTML content, you can perform your calculations
    $wordCounts = countWords(strip_tags($text));

    return $this->render(
        'word-count/page_details.html.twig',
        [
            'wordCounts' => $wordCounts,
        ]
    );
}
}