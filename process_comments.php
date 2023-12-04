<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicatia pentru Evaluarea Tendintei unui Video</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .txt-form {

            border-radius: 5px;
            padding:3xp;
            align-items: center;
            box-shadow: 0 0 50px rgb(245, 244, 244);
        }
        .container{
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            
        }
        .bt-con{
            display: flex;
            justify-content: center;
            align-items: center;
            height: 5vh;
            
        }

    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
<div class="container">
<?php

require_once __DIR__ . '/vendor/autoload.php';
$config = require_once __DIR__ . '/config.php';

$client = new Google_Client();
$client->setDeveloperKey($config['apiKey']);

$service = new Google_Service_YouTube($client);

// Verifică dacă un URL a fost trimis prin formular.
if (isset($_POST['youtubeUrl'])) {
    // Extrage ID-ul videoclipului din link.
    $videoId = getVideoIdFromUrl($_POST['youtubeUrl']);

    // Verifică dacă ID-ul videoclipului este valid.
    if ($videoId) {
        try {
            // Obține numărul total de comentarii.
            $video = $service->videos->listVideos('snippet,statistics', ['id' => $videoId])->getItems()[0];
            $totalComments = $video->getStatistics()->getCommentCount();

            // Setează numărul maxim de comentarii de extras pe pagină.
            $maxResults = min($totalComments, 200);

            $commentsData = [];

            // Cerere inițială pentru a obține prima pagină de comentarii.
            $comments = $service->commentThreads->listCommentThreads('snippet', [
                'videoId' => $videoId,
                'maxResults' => $maxResults,
            ]);

            // Parcurge fiecare pagină de comentarii.
            while ($comments) {
                foreach ($comments as $comment) {
                    $snippet = $comment['snippet']['topLevelComment']['snippet'];
                    $author = $snippet['authorDisplayName'];
                    $text = $snippet['textDisplay'];
                    // Conversie caractere speciale
                    //$text = htmlspecialchars_decode($text, ENT_QUOTES);
                    //$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
                    //$text = strip_tags($text);
        
                    // Rezolvare probleme diacritice
                    //$text = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);

                    // Alte înlocuiri necesare
                    //$text = str_replace(
                    //array('&Acirc;', '&acirc;', '&lsquo;', '&rsquo;', '&ldquo;', '&rdquo;'),
                    //array('Â', 'â', '‘', '’', '“', '”'),
                    //$text
                    //);
                    // Adaugă fiecare comentariu la array.
                    $commentsData[] = [
                        'Author' => $author,
                        'Comment' => $text,
                    ];
                }

                // Verifică dacă există încă o pagină de comentarii.
                $pageToken = $comments['nextPageToken'] ?? null;
                if ($pageToken) {
                    // Realizează următoarea cerere cu pageToken-ul.
                    $comments = $service->commentThreads->listCommentThreads('snippet', [
                        'videoId' => $videoId,
                        'maxResults' => $maxResults,
                        'pageToken' => $pageToken,
                    ]);
                } else {
                    // Nu mai există pagini, ieși din buclă.
                    break;
                }
            }

            // Salvează comentariile într-un fișier CSV.
            $videoId = getVideoIdFromUrl($_POST['youtubeUrl']);
            if ($videoId) {
                 $filename = "comments_$videoId.csv";
                 $fp = fopen($filename,"w");

            // Scrie antetul în fișierul CSV.
            fputcsv($fp, array('Author', 'Comment'));

            // Scrie datele comentariilor în fișierul CSV.
            foreach ($commentsData as $row) {
                fputcsv($fp, $row);
            }
            fclose($fp);
            
            $cuvcheie = $_POST['cuvcheie'];
            exec("python analiza1.py $filename $cuvcheie", $output);
            session_start();
            $_SESSION['output']=$output;/*
            for ($i=3; $i < count($output); $i++)
            {
                echo "$output[$i]<br/>";
            }   */
            header("Location: show_results.php");
            exit();
            } 

        } catch (Google\Service\Exception $e) {
            // Gestionează eroarea API-ului.
            $error = json_decode($e->getMessage(), true);
            if (isset($error['error']['message'])) {
                echo "<p>Error: " . $error['error']['message'] . "</p>";
            } else {
                echo "<p>An error occurred while retrieving comments.</p>";
            }
        }
    } else {
        echo "<p>The entered YouTube link does not contain a valid video ID.</p>";
    }
} else {
    echo "<p>You did not provide a valid YouTube link.</p>";
}

// Funcție pentru extragerea ID-ului videoclipului dintr-un link YouTube.
function getVideoIdFromUrl($url) {
    $parsedUrl = parse_url($url);
    if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $query);
        if (isset($query['v'])) {
            return $query['v'];
        }
    } elseif (isset($parsedUrl['path'])) {
        $pathParts = explode('/', trim($parsedUrl['path'], '/'));
        return end($pathParts);
    }
    return null;
}
?>
</div>
</body>
</html>