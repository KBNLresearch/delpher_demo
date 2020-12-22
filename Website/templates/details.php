<?php
// Include database config file, for some reason the path needs to be different, dependent on whether a keywords is choosen or not
if (!empty($_POST['nm'])){
    include '../db_config.php';
}
else {
    include './db_config.php';
}

// Declare the variables based on whether a user has already used a keyword or not 
if (empty($_POST['year'])){
    if(!empty($_GET['year'])){
    $year = $_GET['year']; 
    $sub = $_GET['sub'];  
    }  
}
else {
    $year = $_POST['year'];
    $sub = $_POST['sub']; 
}

// Deal with the subject 'Spaanse griep' for display on website and searchmethod in Delpher.nl
if ($sub == 'spaansegriep') {
    $subname = 'Spaanse griep';
    $sublink = '("spaansche+griep")+or+("spaanse+griep")';
}
else {
  $subname = $sub;
  $sublink = $sub.'*';
}

// Create links with historical variants on words, so the correct searchlink will be send to Delpher.nl when a keyword is choosen. 
if (!empty($_POST['nm'])){
    $nm = $_POST['nm'];
    include '../db_config.php';
    if ($nm == 'ziekte'){
      $link = '(ziekt*+or+sieck*+or+siek*)';
    }
    else if ($nm == 'verspreiding'){
      $link = '(verspreiding*+or+verspreyding*)';
    }
    else if ($nm == 'virus'){
      $link = '(virus*+or+vira*+or+viren*)';
    }
    else if ($nm == 'alle'){
        $link = '';
    }
    else {
    $link = $nm."*";
  }
}

/* Check if a year is selected or not. If a year is selected, the following items will be displayed:
    * buttons with keywords found for this years articles
    * a wordcloud 
    * a link to Delpher.nl
    * a downloadlink for a csv file, containing the bag-of-words for the selected articles
    * a bar chart or a wordtree when a keyword is selected. 
   When there is not yet a year chosen, there will be a wordcloud and a bar chart displayed
*/

// First, we handle the case where a year is selected
if (!empty($year)){
// There are three possibilities: 1. a year is chosen but no keyword, 2. a year and keyword is chosen, 3. a year and the button 'no selection' are choosen. 
// For the second option, the databasequery is different then from the other two. 

// First, we declare the SQL queries
// When there is a keyword chosen:
if (!empty($_POST['nm'])){
    // If 'no selection' is on
    if ($nm == 'alle') {
        $sql_csv = "SELECT identifier, papertitle, date, bag_of_words FROM artikelen WHERE artikelen.year = '$year' AND category = '$sub'";
        $sql_bow = "SELECT bag_of_words FROM artikelen WHERE category = '$sub' and year = '$year'";
        $sql_keywords = "SELECT keywords, count(*) as keyword_count FROM keywords INNER JOIN artikelen ON keywords.id_nr =artikelen.id_nr
                    WHERE artikelen.year = '$year' AND artikelen.category = '$sub' GROUP BY keywords.keywords";
        $sql_subtotal = "SELECT count(DISTINCT keywords.identifier) as total FROM keywords INNER JOIN artikelen ON keywords.id_nr=artikelen.id_nr
                    WHERE artikelen.year = $year AND artikelen.category = '$sub'";
        $lines = file('../csv/stopwords.csv', FILE_IGNORE_NEW_LINES);
    }
    // if a keyword is selected
    else{
        $sql_csv = "SELECT artikelen.identifier, artikelen.papertitle, artikelen.date, artikelen.bag_of_words 
                    FROM artikelen INNER JOIN keywords ON keywords.id_nr = artikelen.id_nr
                    WHERE artikelen.year = '$year' AND artikelen.category = '$sub' AND keywords.keywords = '$nm'"; 
        $sql_bow = "SELECT artikelen.bag_of_words FROM artikelen INNER JOIN keywords ON keywords.id_nr=artikelen.id_nr
                    WHERE artikelen.year = '$year' AND artikelen.category = '$sub' AND keywords.keywords = '$nm'";
        $sql_keywords = "SELECT keywords.keywords, count(*) as keyword_count FROM keywords INNER JOIN artikelen ON keywords.id_nr =artikelen.id_nr
                    WHERE artikelen.year = $year AND artikelen.category = '$sub' AND keywords.id_nr in (select keywords.id_nr from keywords where keywords.keywords = '$nm') 
                    GROUP BY keywords.keywords";
        $sql_wordtree = "SELECT artikelen.identifier, keywords.keywords FROM keywords INNER JOIN artikelen ON keywords.id_nr=artikelen.id_nr
                    WHERE artikelen.year = $year AND artikelen.category = '$sub' AND keywords.id_nr in (select keywords.id_nr from keywords where keywords.keywords = '$nm') 
                    order by keywords";
        $sql_subtotal = "SELECT count(DISTINCT keywords.identifier) as total FROM keywords INNER JOIN artikelen ON keywords.id_nr=artikelen.id_nr
                    WHERE artikelen.year = '$year' AND artikelen.category = '$sub' AND keywords.keywords = '$nm' ";
        $lines = file('../csv/stopwords.csv', FILE_IGNORE_NEW_LINES);
    }
}
// If there is not yet a button clicked
else {
    $sql_csv = "SELECT identifier, papertitle, date, bag_of_words FROM artikelen WHERE artikelen.year = '$year' AND category = '$sub'";
    $sql_bow = "SELECT bag_of_words FROM artikelen WHERE category = '$sub' and year = '$year'";
    $sql_keywords = "SELECT keywords, count(*) as keyword_count FROM keywords INNER JOIN artikelen ON keywords.id_nr =artikelen.id_nr
            WHERE artikelen.year = '$year' AND artikelen.category = '$sub' GROUP BY keywords.keywords";
    $sql_subtotal = "SELECT count(DISTINCT keywords.identifier) as total FROM keywords INNER JOIN artikelen ON keywords.id_nr=artikelen.id_nr
            WHERE artikelen.year = $year AND artikelen.category = '$sub'";
    $lines = file('./csv/stopwords.csv', FILE_IGNORE_NEW_LINES);
}

$result_subtotal = $dbh->query($sql_subtotal);
$result_keywords = $dbh->query($sql_keywords);
$result_csv = $dbh->query($sql_csv);
$result_bow = $dbh->query($sql_bow);

if (!empty($_POST['nm'])){
    if ($nm != 'alle') {
    $result_wordtree = $dbh->query($sql_wordtree);
    }
}

// Close the connection to the database
$dbh = null; 

////////////////////////////////////////// Creating of the downloadable csv file /////////////////////////////////////////////////////////////
// Declare the headers of the file
$headers = ["identifier", "papertitle", "date", "bag of words"];

// The location where the file can be found
if (!empty($nm)){
$fp = fopen('../downloads/download.csv', 'w');
}
else {
  $fp = fopen('downloads/download.csv', 'w');
}

// Open the csv file and add the headers
fputcsv($fp, $headers);

// Loop through the query output and store the result in the csv file
while ($row = $result_csv->fetch(PDO::FETCH_ASSOC)){
    fputcsv($fp, $row);
    //var_dump($row);
}

// Close the file
fclose($fp);

//////////////////////////////////////////  Wordcloud calculation /////////////////////////////////////////////////////////////

$stopwords = array();
foreach ($lines as $key => $value)
{
    $stopwords[] = $value;
}

$data_bow = array();
while($row = $result_bow->fetch(PDO::FETCH_ASSOC)){
  $words = explode(" ", $row['bag_of_words']);
  $words = array_diff($words, $stopwords);
  foreach ($words as $id => $word){
    if (array_key_exists($word, $data_bow)){
      $data_bow[$word]++;
    }
    else {
      $data_bow[$word] = 1;
    }
  }
  
}
$word = array(); 
$freq = array();

foreach($data_bow as $key => $value){ 
$word[] = $key; 
$freq[] = $value; 
}

array_multisort($freq, SORT_DESC, $word, SORT_ASC, $data_bow);
$data_bow = array_slice($data_bow, 0, 15);

////////////////////////////////////////// Wordtree calculation /////////////////////////////////////////////////////////////
$wordlist = array();
if (!empty($nm)){
    if ($nm != 'alle'){ 
        $identifiers = array();
        $keywords_list = array();
        $wordtree = array();
 
        // put all the keywords that are going to be used in a array to calculate the most frequent keyword
        // and create an array with all identifiers
        while($row = $result_wordtree->fetch(PDO::FETCH_ASSOC)){
            $identifiers[] = $row;
            if ($row['keywords'] != $nm){
                if ($row['keywords'] != $sub){
                    $keywords_list[] = $row['keywords'];
                }
            }
        }

        $wordlist = array();
        foreach ($identifiers as $key => $identifier){
            $keyword = $identifier['keywords'];
            $id = $identifier['identifier'];
            if (!array_key_exists($id, $wordlist)){
                $wordlist[$id] = $nm;
            }
            else if (array_key_exists($id, $wordlist)){
                if ($keyword != $nm){
                    $value = $wordlist[$id];
                    $newvalue = $value." -".$keyword;
                    $wordlist[$id] = $newvalue;
                }
            } 
        }
    }
}
?>
<!-- ---------------------------------------------------------- Javascript to create bar chart  ------------------------------------------------------------>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ["keyword", "aantal artikelen", { role: "style" } ],
            <?php
            $count = 0;
            while($row2 = $result_keywords->fetch(PDO::FETCH_ASSOC)){
                if ($row2['keywords'] == 'overig'){
                    echo '["overig", '.$row2['keyword_count'].', "#3476A3"],';
                }
                else {
                    echo '["'.$row2['keywords'].'", '.$row2['keyword_count'].', "#5AC3BF"],';
                }
            }
            ?>
            ]);

        var view = new google.visualization.DataView(data);
        view.setColumns([0, 1,
            { calc: "stringify",
              sourceColumn: 1,
              type: "string",
              role: "annotation" },
            2]);

        var options = {
            width: 500,
            bar: {groupWidth: "95%"},
            legend: { position: "none" },
            chartArea:{left:0,top:30,width:"100%",height:"70%"}
        };
        var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
        chart.draw(view, options);
    }
</script>
<!-- ---------------------------------------------------------- Javascript to create wordtree  ------------------------------------------------------------>
<script type="text/javascript">
    anychart.onDocumentReady(function () {

        var data = [
            <?php
                foreach ($wordlist as $word){
                    echo '["'.$word.'"],';
                }
            ?>
        ];
        
        var chart = anychart.wordtree(data);
        chart.tooltip(false);
        chart.postfix("meer");
        chart.minFontSize(8)
        chart.maxFontSize(20);
        chart.container("wordtree");
        chart.draw();

        $('.enable').click(function() {
            chart.fullScreen(true);
        });
    });
</script>
<!----------------------------------------------- Javascript code for the wordcloud  --------------------------------------------------------------------------->
<script type="text/javascript">
    anychart.onDocumentReady(function () {
        var subject = '<?php echo $sublink; ?>';
        var year = '<?php echo $year; ?>';
        var link = '<?php
                        if (!empty($nm)){
                            echo $link;
                        }
                        else {
                            echo '';
                        }
                        ?>';
        var data = [
                    <?php
                        foreach ($data_bow as $word => $value){
                            echo '{"x": "'.$word.'", "value": '.$value.'},';
                        }
                    ?>
                    ];

        var chart = anychart.tagCloud(data);
        chart.background().fill("#D8F1F0");
        chart.fontFamily('Arial');
        // set array of angles, by which words will be placed
        chart.angles([0, 0, 0]);
        // enable color range
        chart.colorRange(false);
        // set color range length
        chart.colorRange().length('80%');
        // format tooltips
        var formatter = "{%value}{scale:(1)(1000)(1000)(1000)|()( duizend)( miljoen)( biljoen)}";
        var tooltip = chart.tooltip();
        tooltip.format(formatter);
        var margin = chart.margin();
        margin.set(0, 0, 0, 0);
        var padding = chart.padding();
        padding.set(2, 2, 2, 2);

        // add an event listener
        chart.listen("pointClick", function(e){
            if (link == ''){
                var url = "https://www.delpher.nl/nl/kranten/results?query="+ subject + "+and+" + e.point.get("x") + "&page=1&cql%5B%5D=(date+_gte_+%2201-01-" + year + "%22)&cql%5B%5D=(date+_lte_+%2231-12-" + year + "%22)&coll=ddd";
            }
            else {
                var url = "https://www.delpher.nl/nl/kranten/results?query="+ subject + "+and+" + link + "+and+" + e.point.get("x") + "&page=1&cql%5B%5D=(date+_gte_+%2201-01-" + year + "%22)&cql%5B%5D=(date+_lte_+%2231-12-" + year + "%22)&coll=ddd";
            }
        var redirectWindow  = window.open(url, "_blank");
        redirectWindow.location;
        });

        // display chart
        chart.container("wordcloud_js");
        chart.draw();
    });
</script>

<!-------------------------------------------------------------------- Article count, Delpherlink and csv download ------------------------------------------>
<?php
    while($row = $result_subtotal->fetch(PDO::FETCH_ASSOC)){
        $total_sub = $row;
    }
    foreach ($total_sub as $total => $value){
        $total_subcount = $value;   
    }
    
    if (!empty($nm)){
        if ($nm != 'alle'){
            echo "Deze wordcloud toont de 20 woorden die het vaakst voorkomen in artikelen die ook de woorden <b>".$subname."</b> en <b>".$nm."</b> bevatten.";
            echo "Hoe groter het woord, hoe vaker dit woord voorkomt."; 
        }
        else {
            echo "Deze wordcloud toont de 20 woorden die het vaakst voorkomen in artikelen die ook het woord <b>".$subname."</b> bevatten.";
            echo "Hoe groter het woord, hoe vaker dit woord voorkomt.";
        }   
    }
    else{
        echo "Deze wordcloud toont de 20 woorden die het vaakst voorkomen in artikelen die ook het woord <b>".$subname."</b> bevatten.";
        echo "Hoe groter het woord, hoe vaker dit woord voorkomt.";
    }

    echo "<div class = 'info grid-parent'>";
        echo "<div class = 'container_wordcloud'>";
            echo "<div class = 'wordcloud_2' id = 'wordcloud_js'>";
            echo "</div>";
        echo "<div class = 'infobutton_wc'>";
            echo '<button class="infobutton" id = "wordcloud_button"><img src= "images/info_logo.jpg" alt="Informatie over de wordcloud" class = "info_img" /></button>';
        echo '</div>';
        echo "<div class = 'infotext'>";
        if (!empty($nm)){
            if ($nm != 'alle'){
                if ($total_subcount == 1){
                    echo "<p>Er is ".$total_subcount." artikel gevonden voor de selectie <b>".$subname."</b> en <b>".$nm."</b></p>";
                }
                else{
                    echo "<p>Er zijn ".$total_subcount." artikelen gevonden voor de selectie <b>".$subname."</b> en <b>".$nm."</b></p>";
                }
            }
            else{
                if ($total_subcount == 1){
                    echo "<p>Er is ".$total_subcount." artikel gevonden voor de selectie <b>".$subname."</b></p>";
                }
                else{
                    echo "<p>Er zijn ".$total_subcount." artikelen gevonden voor de selectie <b>".$subname."</b></p>";
                }
            }
        }
    else {
        if ($total_subcount == 1){
            echo "<p>Er is ".$total_subcount." artikel gevonden voor de selectie <b>".$subname."</b></p>";
        }
        else{
            echo "<p>Er zijn ".$total_subcount." artikelen gevonden voor de selectie <b>".$subname."</b></p>";
        }   
    }

    echo "<p>";
    if (!empty($nm)){
        if ($link == ''){
            echo "<a class = 'infotext_links' href='https://www.delpher.nl/nl/kranten/results?query=".$sublink."&facets[type][]=artikel&page=1&cql%5B%5D=(date+_gte_+%2201-01-".$year."%22)&cql%5B%5D=(date+_lte_+%2231-12-".$year."%22)&coll=ddd' target='_blank'>";
        }
        else {
            echo "<a class = 'infotext_links' href='https://www.delpher.nl/nl/kranten/results?query=".$sublink."+and+".$link."&facets[type][]=artikel&page=1&cql%5B%5D=(date+_gte_+%2201-01-".$year."%22)&cql%5B%5D=(date+_lte_+%2231-12-".$year."%22)&coll=ddd' target='_blank'>";
        }
    }
    else {
        echo "<a class = 'infotext_links'  href='https://www.delpher.nl/nl/kranten/results?query=".$sublink."&facets[type][]=artikel&page=1&cql%5B%5D=(date+_gte_+%2201-01-".$year."%22)&cql%5B%5D=(date+_lte_+%2231-12-".$year."%22)&coll=ddd' target='blank'>";
    }
    echo "Zoeken op Delpher<img class = 'down_img' src='images/external.jpg'></img></a></br>
            Zoek de bijbehorende krantenartikelen op Delpher.nl. Het aantal artikelen op Delpher.nl kan afwijken wegens regelmatige updates. 
            </p>
            <p>";
    if (!empty($nm)){
        echo "<a <a class = 'infotext_links' href='../downloads/download.csv' download>Download de csv<img class = 'down_img' src='images/download.jpg'></img></a></br>";
    }
    else {
        echo "<a <a class = 'infotext_links' href='downloads/download.csv' download>Download de csv<img class = 'down_img' src='images/download.jpg'></img></a></br>";
    }   
    echo  "Zelf aan de slag met de data? Download de csv file en bekijk de voorbeeldcode op de <a href='https://github.com/KBNLresearch/delpher_demo'  target='_blank'>Githubpagina</a>.
    </p>";
    echo "</div>";    
    echo "</div>";

    // The divs for the bar chart and word tree
    echo "<div class = 'container_graphs'>";
    if (!empty($nm)){
        if ($nm == 'alle'){
            echo '<div id = "staaf_div" style="display:display;">';
            echo 'Aantal artikelen per subonderwerp voor de artikelen met het woord <b>'.$subname.'</b>';
            echo '<button class="infobutton" id = "staafinfo_button"><img src= "images/info_logo.jpg" alt="Informatie over het staafdiagram" class = "info_img" /></button>';
            echo '<div id="columnchart_values"></div>';
            echo '</div>';
        }
        else {
            echo '<div id = "wordtree_div" style="display:display;">';
            echo 'Wordtree voor de artikelen met de woorden <b>'.$subname.'</b> en <b>'.$nm.'</b>';
            echo '<button class="infobutton" id = "wordtreeinfo_button"><img src= "images/info_logo.jpg" alt="Informatie over de Wordtree" class = "info_img" /></button>';
            echo '<div id="wordtree"></div>'; 
            echo '<button class="enable">Bekijk de Wordtree op volledig scherm</button>';
            echo '</div>';
            echo '<div id = "staaf_div" style="display:none;">';
            echo 'Aantal artikelen per subonderwerp voor de artikelen met de woorden <b>'.$subname.'</b> en <b>'.$nm.'</b>';
            echo '<button class="infobutton" id = "staafinfo_button"><img src= "images/info_logo.jpg" alt="Informatie over het staafdiagram" class = "info_img" /></button>';
            echo '<div id="columnchart_values"></div>';
            echo '</div>';
            echo '<div class = "buttons-radio">';
            echo '<input value="wordtree" type="radio" onclick="wordtreeFunc()" id = "wordtree" name = "graphs" checked> Wordtree&nbsp&nbsp</input>';
            echo '<input value="column" type="radio" onclick="chartFunc()" id = "chart"  name = "graphs"> Staafdiagram</input>';
            echo '</div>';
       }
    }
    else {
        echo '<div id = "staaf_div" style="display:display;">';
        echo 'Aantal artikelen per subonderwerp voor de artikelen met het woord <b>'.$subname.'</b>';
        echo '<button class="infobutton" id = "staafinfo_button"><img src= "images/info_logo.jpg" alt="Informatie over het staafdiagram" class = "info_img" /></button>';
        echo '<div id="columnchart_values"></div>';
        echo '</div>';
    }
    echo "</div>";
    echo "</div>";
}
?>

<!----------- Javascript to change which visualisation is visible ------>
<script>
    function chartFunc() {
        $('#staaf_div').css('display', 'block');
        $('#wordtree_div').css('display', 'none');
    }   
</script>
<script>
    function wordtreeFunc() {
        $('#wordtree_div').css('display', 'block');
        $('#staaf_div').css('display', 'none');
    }
</script>

