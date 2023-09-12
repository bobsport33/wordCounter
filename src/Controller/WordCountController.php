<?php

namespace App\Controller;

use Html2Text\Html2Text;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use GuzzleHttp\Client;


function countWords($text) {
    // Remove URLs
    $text = preg_replace('/https?:\/\/\S+/', '', $text);

    // Remove punctuation and convert to lowercase
    $text = strtolower(preg_replace('/[^A-Za-z0-9\s]/', '', $text));

    $words = explode(' ', $text);

    $wordCounts = [];
    foreach ($words as $word) {
        // Trim whitespace
        $word = trim($word);

        if (!empty($word)) { // Skip empty words
            // If the word exists in the associative array, increment its count
            if (array_key_exists($word, $wordCounts)) {
                $wordCounts[$word]++;
            } else { // If the word doesn't exist, initialize its count to 1
                $wordCounts[$word] = 1;
            }
        }
    }

    arsort($wordCounts); // Sort the word counts in descending order

    return $wordCounts;
}




class WordCountController extends AbstractController
{

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
   
    return $this->render(
      'word-count/index.html.twig'
    );
    }

  #[Route("/api/post", methods: ['POST'], name: "form_submit")]
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