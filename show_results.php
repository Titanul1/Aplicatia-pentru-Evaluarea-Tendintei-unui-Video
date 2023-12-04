<!doctype html>
<html>
<header>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezultatele Analizei</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .addpad {
           padding: 30px;
        }
        .shiny {
            border-radius: 5px;
            padding: 3px;
            align-items: center;
            box-shadow: 0 0 50px rgb(245, 244, 244);
        }

    .termometru {
        position: relative;
        width: 80%; 
        height: 200px;
    }

    .cerc {
        position: absolute;
        z-index: 2;
        left: 20%;
        width: 100px; 
        height: 100px; 
        border-radius: 50%;
        background-color: red; 
    }

    .dreptunghi {
        position: absolute;
        z-index: 1;
        top: 13%;
        left: 30%;
        width: 70%;
        height: 50px; 
        background-color: blue; /*culoare goala*/
    }
    .dreptunghi2 {
        position: absolute;
        z-index: 3;
        top: 13%;
        left: 30%;
        width: 7%; /*7% este -1 (0%), 70% este 1 (100%) termometru... fiecare 0.1 este 3.5%*/
        height: 50px; 
        background-color: red;       
    }
    .semicerc {
        position: absolute;
        top: 13%;
        left: 100%;
        transform: translateX(-50%);
        width: 50px; 
        height: 50px;
        border-radius: 50%;
        background-color: blue; 
    }
    .bordered {
      border: 2px dotted white;
      padding: 10px;
    }
        
    </style>

</header>

<body class="text-center" style="min-height: 100vh;">
<div class="addpad">
    <h1 style='color: white;'>Distributia sentimentele comentariilor din videoclip</h1><br/>
    <h2 style='color: white;'>De la -1 (Negativ) la 1 (Pozitiv)</h2>
</div>

<?php
    session_start();
    $output = $_SESSION['output'];
    echo "<img class='shiny' src='http://localhost/php_scraper/$output[0]'><br/>";
?>
    <div class="row addpad">
        <div class="col-md-6">
            <?php
            echo "<h2 style='color: white;'>Polarizare de sentiment (dispersia standard): $output[2]</h2><br/>";
            if ($output[2] > 0.5)
            {
                echo "<h3 style='color: white;'>Acest videoclip provoaca sentimente foarte polarizate. </h3>";
            }
            else if ($output[2] < 0.2)
            {
                echo "<h3 style='color: white;'>Majoritatea oamenilor a reactionat in mod asemanator la acest videoclip. </h3>";
            }
            else
            {
                echo "<h3 style='color: white;'>Oamenii au avut reactii variate despre acest videoclip. </h3>";
            }
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo "<h2 style='color: white;'>Scorul medie de sentiment: $output[1]</h2><br/>";
            if ($output[1] > 0.3)
            {
                echo "<h3 style='color: white;'>Acest videoclip a inspirat in general raspunsuri pozitive. </h3>";
            }
            else if ($output[2] < -0.3)
            {
                echo "<h3 style='color: white;'>Acest videoclip are un efect negativ si raspunsuri toxice.</h3>";
            }
            else
            {
                echo "<h3 style='color: white;'>Audienta nu a avut o reactie puternica la acest videoclip (cam neutru). </h3>";
            }
            echo "<div class='termometru'>";
            if ($output[1] < 0)
            {
                $rvalue = 225;//intval($output[1]*225*-1);
                $gvalue = intval((1+$output[1])*225);
                $latime = (7+29*(1+$output[1]))."%";
            }
            else
            {
                $rvalue = intval((1-$output[1])*225);
                $gvalue = 225;
                $latime = (42+29*($output[1]))."%";
            }
            echo "<div class='cerc' style='background-color: rgb($rvalue, $gvalue, 0)'></div>";
            echo "<div class='dreptunghi'></div>";  
            echo "<div class='semicerc'></div>";
            echo "<div class='dreptunghi2' style='background-color: rgb($rvalue, $gvalue, 0); width: $latime;'></div>";
            ?>
        </div>      
    </div>
    <div class="text-center">
    <?php
        for ($i=3; $i < count($output); $i=$i+8)
        {
            $j = $i+1;
            if (strcmp($output[$j], "empty")==0)
            {
                echo "<div class='text-center'>";
                echo "<h1 style='color: white;'>Cuvantul '$output[$i]' nu apare in comentarii.</h1><br>";
                echo "</div>";
                $i=$i-6;
            }
            else
            {                
                echo "<div class='text-center'>";
                echo "<h1 style='color: white;'>Distributia sentimentele comentariilor cu mentiune de '$output[$i]'</h1><br>";
                echo "<h2 style='color: white;'>De la -1 (Negativ) la 1 (Pozitiv)</h2>";
                echo "<img class='shiny' src='http://localhost/php_scraper/$output[$j]'><br/>";
                echo "<div class='row addpad'>";
                echo "<div class='col-md-6'>";
                $j++;
                if (strcmp($output[$j], "n")==0)
                    echo "<h2 style='color: white;'>Cuvantul '$output[$i]' nu a aparut destul de multe ori sa calculez polarizare de sentiment.";
            else
            {
                    echo "<h2 style='color: white;'>Polarizare de sentiment (dispersia standard) pentru cuvantul '$output[$i]': $output[$j]</h2><br/>";
                    if ($output[$j] > 0.5)
                    {
                        echo "<h3 style='color: white;'>Acest cuvant provoaca sentimente foarte polarizate. </h3>";
                    }
                    else if ($output[$j] < 0.2)
                    {
                        echo "<h3 style='color: white;'>Majoritatea oamenilor a reactionat in mod asemanator la acest cuvant. </h3>";
                    }
                    else
                    {
                        echo "<h3 style='color: white;'>Oamenii au avut reactii variate despre acest cuvant. </h3>";
                    }
            }
            
                echo "</div>";
                echo "<div class='col-md-6'>";
                $j++;
                echo "<h2 style='color: white;'>Scorul medie de sentiment pentru '$output[$i]': $output[$j]</h2><br/>";
                    if ($output[$j] > 0.3)
                    {
                        echo "<h3 style='color: white;'>Acest cuvant a inspirat in general raspunsuri pozitive. </h3>";
                    }
                    else if ($output[$j] < -0.3)
                    {
                        echo "<h3 style='color: white;'>Acest cuvant a provocat raspunsuri toxice.</h3>";
                    }
                    else
                    {
                        echo "<h3 style='color: white;'>Audienta nu a avut o reactie puternica la acest cuvant (cam neutru). </h3>";
                    }
                    echo "<div class='termometru'>";
                    if ($output[$j] < 0)
                    {
                        $rvalue = 225;
                        $gvalue = intval((1+$output[$j])*225);
                        $latime = (7+29*(1+$output[$j]))."%";
                    }
                    else
                    {
                        $rvalue = intval((1-$output[$j])*225);
                        $gvalue = 225;
                        $latime = (42+29*($output[$j]))."%";
                    }
                    echo "<div class='cerc' style='background-color: rgb($rvalue, $gvalue, 0)'></div>";
                    echo "<div class='dreptunghi'></div>";  
                    echo "<div class='semicerc'></div>";
                    echo "<div class='dreptunghi2' style='background-color: rgb($rvalue, $gvalue, 0); width: $latime;'></div>";
                echo "</div>";      
                echo "</div>";
                //cele mai negative si cele mai pozitive comentarii
                echo "<div class='row addpad'>";
                echo "<div class='col-md-6 bordered'>";
                $j++;
                echo "<h2 style='color: white;'>Comentariul cel mai negativ cu cuvantul '$output[$i]' a avut scorul $output[$j]:</h2><br/>";
                $j++;
                echo "<h4 style='color: white;'>$output[$j]</h4>";
                echo "</div>";     
                echo "<div class='col-md-6  bordered'>";
                $j++;
                echo "<h2 style='color: white;'>Comentariul cel mai pozitiv cu cuvantul '$output[$i]' a avut scorul $output[$j]:</h2><br/>";
                $j++;
                echo "<h4 style='color: white;'>$output[$j]</h4>";
                echo "</div>";      
                echo "</div>";
                echo "</div>";
            }
        }
    ?>
    </div>
</body>

</html>
