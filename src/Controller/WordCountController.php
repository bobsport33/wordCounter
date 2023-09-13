<?php

namespace App\Controller;

use DateTime;
use GuzzleHttp\Client;
use App\Entity\UrlEntry;
use Html2Text\Html2Text;
use App\Repository\UrlEntryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


function countWords($text) {
    // clean text
    $text = strip_tags($text);
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
   
       
    return $this->render(
      'word-count/index.html.twig', [
        'posts' => $posts->findALl()
      ]
    );
    }

    #[Route("/add",  name: "app_form")]
    public function processForm(Request $request, UrlEntryRepository $posts): Response
    {
        $UrlEntry = new UrlEntry();
        $form = $this->createFormBuilder($UrlEntry)
            ->add('url')
            ->add('notes')
            // ->add('submit', SubmitType::class, ['label' => 'Save'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();
            $post->setCreatedDate(new DateTime());
            $posts->save($post, true);
            $id = $post->getId();
          
            // redirect
            return $this->redirectToRoute('app_page_details', array('id' => $id));
        }
        
        return $this->render('word-count/add.html.twig', [
            'form' => $form
        ]);

    }


    #[Route('/page/{id<\d+>}', name: "app_page_details")]
    public function showOne(int $id, UrlEntryRepository $posts): Response
    {
    // Connect to the database and retrieve post
    $post = $posts->find($id);

    // Get the URL from the database
    $url = $post->getUrl();

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
            'post' => $post
        ]
    );
}
}