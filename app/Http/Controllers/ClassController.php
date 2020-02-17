<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ClassController extends Controller
{
    //
    public function index()
    {
        $endpoint = "https://graph.microsoft.com/beta/education/classes";
        $client = new Client();

        $response = $client->request('GET', $endpoint);

        // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

        $statusCode = $response->getStatusCode();
        $content = json_decode($response->getBody()->getContents());
        //echo "<img src='". $content->icon_url ."'></img> says: ". $content->value;
    }
}
