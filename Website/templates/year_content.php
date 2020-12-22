<div class="lds-spinner hidden overlay" id = "loader">
           <div></div>
           <div></div>
           <div></div>
           <div></div>
           <div></div>
           <div></div>
           <div></div>
           <div></div>
           <div></div>
           <div></div>
           <div></div>
           <div></div>
           </div>
<?php
    include './db_config.php';
    $sql_total = "SELECT count(*) as totaal from Articles where category = '$sub'";
    $result_total = $dbh->query($sql_total);
    $sub = $_GET['sub'];
    if ($sub == 'spaansegriep') {
        $subname = 'Spaanse griep';
        $sublink = '("spaansche+griep")+or+("spaanse+griep")';
	$subtitle = $subname;

    }
    else {
    $subname = $sub;
    $sublink = $sub.'*';
    $subtitle = ucfirst($sub);
    }

    // De code for when there is a year selected
    if (!empty($_GET['year'])){
        $year = $_GET['year'];


    $sql_years = "SELECT year, count(*) as artikelen FROM Articles WHERE year = '$year' AND category = '$sub' GROUP BY year";
    $sql_keywords = "SELECT keyword, count(*) as keyword_count FROM Keywords INNER JOIN Articles ON Keywords.id_nr =artikelen.id_nr
                    WHERE Articles.year = '$year' AND Articles.category = '$sub' GROUP BY Keywords.keyword";
    $sql_bow = "SELECT year, bag_of_words FROM Articles WHERE year = '$year' AND category = '$sub' GROUP BY year";

    $result_years = $dbh->query($sql_years);
    $result_keywords =  $dbh->query($sql_keywords);
    $result_bow = $dbh->query($sql_bow);
    $dbh = null; 

    while($row = $result_years->fetch(PDO::FETCH_ASSOC)){
        echo '<h4 class = "pink">'.$subtitle.' - '.$year.'</h4>';
        if($row['artikelen'] < 2){ 
            $sentence = '<p>Er is '.$row['artikelen'].' artikel gevonden waarin  <b>'.$subname.'</b> voorkomt.</p>';
        }
        else {
            $sentence = '<p>Er zijn '.$row['artikelen'].' artikelen gevonden waarin <b>'.$subname.'</b> voorkomt.</p>';
        }
        echo $sentence;
        $article_count = $row['artikelen'];
    }       

    if (!empty($sub)){
        $keywords = array();
        while($row2 = $result_keywords->fetch(PDO::FETCH_ASSOC)){
            if ($row2['keywords'] != 'overig'){
                $keywords[] = $row2['keywords']; 
            }
        }
 
        echo '<p>';
        echo '<div class = "buttons">';
        echo '<button value="alle" type="button" class="btn btn-info choose startbtn">geen selectie</button>    ';
        foreach($keywords as $keyword){
            echo '<button value="'.$keyword.'" type="button" class="btn btn-info choose">'.$keyword.'</button>    ';
            }
        }
        echo "</div>";
        echo '<button class="infobutton" id = "buttoninfo_button"><img src= "images/info_logo.jpg" alt="Informatie over de keuzeknoppen" class = "info_img" /></button>';
        echo "</p>";
	echo "<div class = 'details' id='ajax_responses'>";
	include "details.php";
	echo "</div>";


    }

    else {
        include './db_config.php';
	$sql_years = "SELECT year, count(*) as artikelen FROM Articles WHERE category = '$sub' GROUP BY year";
        $sql_bow = "SELECT bag_of_words FROM Articles WHERE category = '$sub'";
        $sql_keywords = "SELECT keyword, count(*) as keyword_count FROM Keywords INNER JOIN Articles ON Keywords.id_nr =Articles.id_nr
                    WHERE Articles.category = '$sub' GROUP BY Keywords.keyword";
        $result_bow = $dbh->query($sql_bow);
        $lines = file('./csv/stopwords.csv', FILE_IGNORE_NEW_LINES);
        
        $result_keywords =  $dbh->query($sql_keywords);
	$result_year_count =  $dbh->query($sql_years);

        $dbh = null; //This is how you close a PDO connection

        $stopwords = array();
        
        $total = 0;
        while($row = $result_total->fetch(PDO::FETCH_ASSOC)){
            $total = $row;
            }

        foreach ($total as $total => $value){
            $total_count = $value;
            }
        $data = array();

        //////////////////////////////////////////  Wordcloud calculation /////////////////////////////////////////////////////////////
        $stopwords = array();
        foreach ($lines as $key => $value){
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
        // Sort only on value due to memory error otherwise
        arsort($data_bow);
        $data_bow = array_slice($data_bow, 0, 15);

    // Adding the divs and the content to the webpage
    echo '<h4 class = "pink">'.$subtitle.' - Details</h4>
            <p>Er zijn '.$total_count.' artikelen gevonden waar het woord <b>'.$subname.'</b> in voor komt. 
            <div class = "wordcloud">
                Deze wordcloud toont de 20 meest voorkomende woorden in artikelen die ook het woord <b>'.$subname.'</b> bevatten.</br>
                Hoe groter het woord, hoe vaker dit woord voorkomt.
             </div>
            <div class = "info grid-parent">
                <div class = "container_wordcloud"> 
                    <div class = "wordcloud_2" id = "wordcloud_js1">
                    </div>
                    <div class = "infobutton_wc">
                        <button class="infobutton" id = "wordcloud_button"><img src= "images/info_logo.jpg" alt="Informatie over de wordcloud" class = "info_img" /></button>
                    </div>
                    <div class = "infotext">';

                        echo "<a class = 'infotext_links' href='https://www.delpher.nl/nl/kranten/results?query=".$sublink."&facets[type][]=artikel&page=1&coll=ddd' target='blank'>
                        Zoeken op Delpher";
                        echo '<img class = "down_img" src="images/external.jpg"></img></a></br>
                        Zoek de bijbehorende krantenartikelen op Delpher.nl. Het aantal artikelen op Delpher.nl kan afwijken wegens regelmatige updates. 
			</br></br>
			
			</p>
                    </div>
                </div>   
           <div class = "container_graphs"> 
                    <div id = "linechart_div" style="display:display;">
                        Aantal artikelen per jaar waarin het woord <b>'.$subname.'</b> voorkomt. 
                        <button class="infobutton" id = "linechartinfo_button"><img src= "images/info_logo.jpg" alt="Informatie over de lijngrafiek" class = "info_img" /></button>
                        <div class = "linechart" id = "linechart"></div>
                     </div>
                     <div id = "staaf_tot_div" style="display:none;">
                        Aantal artikelen per subonderwerp voor de artikelen met het woord <b>'.$subname.'</b>
                        <button class="infobutton" id = "staafinfo_button"><img src= "images/info_logo.jpg" alt="Informatie over het staafdiagram" class = "info_img" /></button>
                        <div class = "columnchart_values_total_tot" id = "columnchart_values_total_tot"></div>
                    </div>
                    <div class = "buttons-radio">
                        <input value="linechart" type="radio" onclick="linechartFunc()" id = "wordtree" name = "graphs" checked> Lijngrafiek&nbsp&nbsp</input>
                        <input value="column" type="radio" onclick="chart_totFunc()" id = "chart"  name = "graphs"> Staafdiagram</input>
                </div>
            </div>
    ';
    ?>

<!----------------------------------------------- Javascript code for the wordcloud  --------------------------------------------------------------------------->   
<script type="text/javascript">

    anychart.onDocumentReady(function () {
        var subject = '<?php echo $sublink; ?>';
        var data = [
                    <?php
                        foreach ($data_bow as $word => $value){
                            echo '{"x": "'.$word.'", "value": '.$value.'},';
                        }
                    ?>
                    ];

        // create a tag cloud chart
        var chart = anychart.tagCloud(data);
        chart.background().fill("#D8F1F0");
        chart.fontFamily('Arial');
        // set array of angles, by which words will be placed
        chart.angles([0, 0, 0])
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
            var url = "https://www.delpher.nl/nl/kranten/results?query="+ subject + "+and+" + e.point.get("x") + "&page=1&coll=ddd";
            var redirectWindow  = window.open(url, "_blank");
            redirectWindow.location;
        });

        // display chart
        chart.container("wordcloud_js1");
        chart.draw();
    });
</script>
<!----------------------------------------------- Javascript code for the bar chart  --------------------------------------------------------------------------->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
                    ["keyword", "aantal artikelen", { role: "style" } ],
                    <?php
                        $c = 1;
                        while($row2 = $result_keywords->fetch(PDO::FETCH_ASSOC)){
                            if ($row2['keywords'] == 'overig'){
                                echo '["'.$row2['keywords'].'", '.$row2['keyword_count'].', "#3476A3"],';
                             }
                            else if ($row2['keywords'] == $sub){
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
            width: 650,
            height: 230,
            bar: {groupWidth: "95%"},
            legend: { position: "none" },
            chartArea:{left:0,top:30,width:"100%",height:"70%"}
        };
        var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values_total_tot"));
        chart.draw(view, options);
    }
</script>
<script>
anychart.onDocumentReady(function () {

// create data
var data = [ 	
    <?php
        while($row = $result_year_count->fetch(PDO::FETCH_ASSOC)){
        $year = $row['year'];
        $count = $row['artikelen'];
  
            echo '["'.$year.'",'.$count.'],';
        }
    ?>
    
];

// create a chart
var chart = anychart.line();


// create a line series and set the data
var series = chart.line(data);
series.name("Aantal artikelen");
series.stroke("#5AC3BF");
// set scale mode
chart.xScale().mode('continuous');

// set the chart title
// set the titles of the axes
var xAxis = chart.xAxis();
xAxis.title("Jaren");
var yAxis = chart.yAxis();
yAxis.title("Aantal artikelen");

series.labels().fontColor("#242626");
series.labels().fontFamily("Arial");

chart.labels().fontFamily("Arial");
chart.labels().fontColor("#242626");

// set the container id
chart.container("linechart");
chart.height(275);
chart.width(550);


// initiate drawing the chart
chart.draw();
});

</script>
<!----------- Javascript to change which visualisation is visible ------>
<script>
    function chart_totFunc() {
        $('#staaf_tot_div').css('display', 'block');
        $('#linechart_div').css('display', 'none');
    }   
</script>
<script>
    function linechartFunc() {
        $('#linechart_div').css('display', 'block');
        $('#staaf_tot_div').css('display', 'none');
    }
</script>



<?php
}

?>
<!---------------------------------------------------------------------- The content for the information pop-ups --------------------------------->
<div id="tijdlijn_info" class="popup_info">
    <div class="infopopup_content">
        <span class="close">&times;</span>
            <div class = 'infopopup_title'>
                <b>De tijdlijn</b>
            </div>
            <div class="infopopup_text">
            <p>
                In de tijdlijn vindt u een overzicht van de jaren waarin artikelen zijn gevonden van het gekozen onderwerp. 
                Per jaar ziet u hoeveel artikelen er gevonden zijn. 
            </p>
            <p>
            U kunt op een jaar klikken om in het rechtervak meer details over de gevonden artikelen van dat jaar te zien. 
            </p>
            </div>
    </div>
</div>

<div id="staaf_info" class="popup_info">
    <div class="infopopup_content">
        <span class="close">&times;</span>
        <div class = 'infopopup_title'>
            <b>Het staafdiagram</b>
        </div>
        <div class="infopopup_text">
        <p>
            In het staafdiagram wordt van elk sleutelwoord weergegeven in hoeveel artikelen dit woord is gevonden. Hoe hoger een staaf, hoe vaker dit woord voorkomt.
            Het precieze aantal wordt weergegeven zodra met de muis over het diagram wordt bewogen. </br>
            Het woord 'overig' geeft het aantal artikelen weer waarin geen één van de sleutelwoorden is gevonden. 
        </p>
        <p>
            Op het moment dat er een selectie wordt gemaakt door op een sleutelwoord te klikken, past het staafdiagram zich aan aan deze selectie. 
        </p>
        </div>
    </div>
</div>

<div id="wordtree_info" class="popup_info">
    <div class="infopopup_content">
        <span class="close">&times;</span>
        <div class = 'infopopup_title'>
        <b>De Wordtree</b>
        </div>
        <div class="infopopup_text">
        <p>
            De Wordtree laat zien welke sleutelwoord in combinatie met elkaar in de artikelen voorkomen. 
            De boom begint met het gekozen sleutelwoord en vanaf daar kunt u de lijnen volgen om te zien welke sleutelwoordcombinaties in de artikelen gevonden zijn. 
            Hoe groter de woorden, hoe vaker deze voorkomen in de tekst.
        </p>
        <p>
            Bij sommige sleutelwoorden ziet u alleen één enkel woord en geen Wordtree. Dit betekent dat er geen andere sleutelwoorden in de gevonden artikelen voorkomen. </br>
            Het kan ook voorkomen dat alle sleutelwoorden op één rij worden afgedrukt, dit betekent dat in elke artikel dezelfde combinatie gevonden is. 
        </p>
        <p>
            Soms zijn de woordcombinatie zo divers, dat deze niet in één oogopslag getoond kunnen worden. U ziet dan '+ meer' in de Wordtree staan. Door op het woord vóór deze plus te klikken, 
            kunt u zien hoe de rest van de combinaties met dit sleutelwoord eruit ziet. Door nogmaals op dit woord te klikken, komt u terug bij het eerste overzicht. </br>
            Ook kunt u de wordtree in zijn geheel weergeven door op 'Bekijk de Wordtree op volledig scherm' te klikken. Er wordt dan een pop-up venster geopend waarin u de gehele wordtree kunt 
            bekijken. U kunt dit venster sluiten door op 'esc' te drukken. Deze functionaliteit werkt niet op een iPhone. 
        </p>
        </div>
    </div>
</div>

<div id="linechart_info" class="popup_info">
    <div class="infopopup_content">
        <span class="close">&times;</span>
        <div class = 'infopopup_title'>
        <b>De lijngrafiek</b>
        </div>
        <div class="infopopup_text">
        <p>
            De lijngrafiek toont het aantal artikelen per jaar dat over het gekozen onderwerp is gevonden. Wanneer u met de muis over een jaar beweegt, 
	    ziet u het precieze aantal artikelen van dat jaar.
        </p>
        <p>
            Vanwege regelmatige updates van Delpher.nl, kunnen die hier genoemde artikelen afwijken van de aantallen die u op Delpher.nl vindt. 
        </p>
        </div>
    </div>
</div>


<div id="wordcloud_info" class="popup_info">
    <div class="infopopup_content">
        <span class="close">&times;</span>
        <div class = 'infopopup_title'>
        <b>De wordcloud</b>
        </div>
        <div class="infopopup_text">
        <p>
            De wordcloud toont de 20 meest voorkomende sleutelwoorden. Hoe groter het woord, hoe vaker dit woord voorkomt in de artikelen. 
            Wanneer u met de muis over een woord gaat, ziet u hoe vaak dit woord voorkomt in deze artikelen. Aangezien één woord meerdere keren kan voorkomen in één artikel, 
            kan dit aantal afwijken van het aantel gevonden artikelen.  
        </p>
        <p>
            De woorden in de wordcloud zijn aanklikbaar. Zodra op een woord geklikt wordt, zal een link naar Delpher openen, met daarin als zoekterm de 
            huidige selectie én het geselecteerde woord uit de wordcloud. 
        </p>
        <p>
            De woorden uit de wordcloud zijn automatisch uit de artikelen geselecteerd. 
            De betreffende artikelen zijn  geautomatiseerd gemaakt met OCR (Optical Character Recognition).
            Deze techniek levert geen 100% correct resultaat op. Dit komt mede doordat oude drukken moeilijker te lezen zijn met software dan moderne.
            Dat betekent dat er onjuiste tekens in de tekst, en dus ook in de wordcloud, kunnen voorkomen. 
        </p>
        </div>
    </div>
</div>


<div id="button_info" class="popup_info">
    <div class="infopopup_content">
        <span class="close">&times;</span>
        <div class = 'infopopup_title'>
            <b>De keuzeknoppen</b>
        </div>
        <div class="infopopup_text">
        <p>
            Om de relatie tussen verschillende woorden nader te bekijken, kan er een subselectie gemaakt worden op basis van sleutelwoorden. 
            Een sleutelwoord kan gekozen worden door op één van de blauwe knoppen te drukken.</br>
            Als alleen de knop 'geen selectie' zichtbaar is, betekent dit dat er geen sleutelwoorden in de artikelen zijn gevonden. U kunt dan geen selecte maken.   

        </p>
        <p>
            Op het moment dat er een selectie gemaakt wordt, zullen de wordcloud, de links en het visualisaties onder de wordcloud, zich aanpassen aan de huidige selectie. 
            U kunt de selectie veranderen door op een ander sleutelwoord te klikken. Ook kunt u kiezen voor 'geen selectie' om de gegevens van het gehele gekozen jaar te bekijken.
        </p>
        <p>
            Via 'Zoek op Delpher' komt u op de website van Delpher terecht, waarbij u de artikelen van de huidige selectie kunt bekijken. </br>
            De content van Delpher wordt regelmatig bijgewerkt. Het kan dus voorkomen dat u op deze website een andere hoeveelheid artikelen ziet dan via de zoekmachine van Delpher.
        </p>
        <p>
            Het is ook mogelijk om een csv file te downloaden, waarin van de gekozen selectie de volgende onderdelen per artikel zijn opgenomen: </br>
            De identifier van het artikel, de naam van de krant, de datum van verschijnen en de 'bag of words' (alle woorden uit het artikel op alfabetische volgorde).
            Deze csv kunt u gebruiken om zelf verder onderzoek te doen. Voorbeeldcode voor de visualisaties kunt u vinden op onze <a href="https://github.com/KBNLresearch/delpher_demo" target='_blank'>Githubpagina</a> 
        </p>
        </div>
    </div>
</div>
<!----------- Javascript to handle the information buttons ------>
<script>
    function initializeModal(modalID, buttonID) {
        // Get the modal element
        var modal = document.getElementById(modalID);

        // Get the button that opens the modal
        var btn = document.getElementById(buttonID);

        // Get the <span> element that closes the modal
        var span = modal.querySelector('.close');

        // When the user clicks on the button, open the modal
        btn.addEventListener('click', function() {
            modal.style.display = "block";
        });

        // When the user clicks on <span> (x), close the modal
        span.addEventListener('click', function() {
            modal.style.display = "none";
        });

        // When the user clicks anywhere outside of the modal, close it
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    }

initializeModal('tijdlijn_info', 'tijdlijn_button');

var chartinfo = document.getElementById("wordtreeinfo_button");
    if(chartinfo){
        initializeModal('wordtree_info', 'wordtreeinfo_button');
    }
var chartinfo = document.getElementById("staafinfo_button");
    if(chartinfo){
        initializeModal('staaf_info', 'staafinfo_button');
    }
var wordcloudinfo = document.getElementById("wordcloud_button");
    if(wordcloudinfo){
        initializeModal('wordcloud_info', 'wordcloud_button');
    }
var wordcloudinfo = document.getElementById("buttoninfo_button");
    if(wordcloudinfo){
        initializeModal('button_info', 'buttoninfo_button');
    }
var wordcloudinfo = document.getElementById("linechartinfo_button");
    if(wordcloudinfo){
        initializeModal('linechart_info', 'linechartinfo_button');
    }

</script>

	

