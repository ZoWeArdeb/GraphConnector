<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Beta\Model;
use App\TokenStore\TokenCache;

class ClassController extends Controller
{
    //
    public function index()
    {
        // Get the access token from the cache
        $tokenCache = new TokenCache();
        $accessToken = $tokenCache->getAccessToken();
        
        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
    
        echo "<h1>Berichten</h1>";
        $getMailsUrl = "/me/messages";
        try{
            $messages = $graph->createRequest('GET', $getMailsUrl)
            ->setReturnType(Model\Message::class)
            ->execute();
            foreach($messages as $message)
            {
                if(null !== $message->getSender() && $message->getSender() !== "")
                {
                    if($message->getSubject() !== "")
                    {
                        echo "<h3>".$message->getSubject()."</h3>";
                    }
                    else
                    {
                        echo "<h3>Geen onderwerp!</h3>";
                    }

                    echo "<p><i>".$message->getSender()->getProperties()['emailAddress']['name']."</i></p>";
                    print_r("<div>".$message->getBodyPreview()."</div>");
                }
            }
        }
        catch (\GuzzleHttp\Exception\ClientException $e) { dd($e->getResponse()->getBody()->getContents()); }
        echo "<h1>Klassen</h1>";
        // Append query parameters to the '/me/events' url
        $getEventsUrl = '/education/classes';
    
        $classes = $graph->createRequest('GET', $getEventsUrl)
            ->setReturnType(Model\EducationClass::class)
            ->execute();
        foreach($classes as $class)
        {
            // Append query parameters to the '/me/events' url
            $getEventsUrl = '/education/classes/'.$class->getId().'/members';
            $getEventsUrlTeachers = '/education/classes/'.$class->getId().'/teachers';
            $getEventsUrlAssignments = '/education/classes/'.$class->getId().'/assignments';
            try{
                $members = $graph->createRequest('GET', $getEventsUrl)
                ->setReturnType(Model\EducationUser::class)
                ->execute();
                $teachers = $graph->createRequest('GET', $getEventsUrlTeachers)
                ->setReturnType(Model\EducationUser::class)
                ->execute();
                /* $assignments = $graph->createRequest('GET', $getEventsUrlAssignments)
                ->setReturnType(Model\EducationAssignment::class)
                ->execute(); */
            }
            catch (\GuzzleHttp\Exception\ClientException $e) { dd($e->getResponse()->getBody()->getContents()); }
            echo "<b>". $class->getDisplayName(). " </b>";
            foreach($teachers as $teacher)
            {
                echo $teacher->getDisplayName()." ";
            }
            echo "<ul>";
            foreach($members as $member)
            {
                echo '<li>';
                echo $member->getDisplayName();
                echo '</li>';
            }
            echo "</ul>";
            print_r("<hr/>");
        }
       
    }

    public function assignments()
    {
                // Get the access token from the cache
                $tokenCache = new TokenCache();
                $accessToken = $tokenCache->getAccessToken();
                
                // Create a Graph client
                $graph = new Graph();
                $graph->setBaseUrl("https://graph.microsoft.com/")
                ->setApiVersion("beta")->setAccessToken($accessToken);
            
                echo "<h1>Klassen</h1>";
                // Append query parameters to the '/me/events' url
                $getEventsUrl = '/education/classes';
            
                $classes = $graph->createRequest('GET', $getEventsUrl)
                    ->setReturnType(Model\EducationClass::class)
                    ->execute();
                foreach($classes as $class)
                {
                    // Append query parameters to the '/me/events' url
                    $getAssigmentsUrl = '/education/classes/'.$class->getId().'/assignments';
                    try{
                        $assignments = $graph->createRequest('GET', $getAssigmentsUrl)
                        ->setReturnType(Model\EducationAssignment::class)
                        ->execute();
                    }
                    catch (\GuzzleHttp\Exception\ClientException $e) { dd($e->getResponse()->getBody()->getContents()); }
                    echo "<b>". $class->getDisplayName(). " </b>";
                    dd($assignments);
                    print_r("<hr/>");
        }
    }
}
