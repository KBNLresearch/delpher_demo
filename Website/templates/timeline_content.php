<!-- This page fills the timeline with content from the database    -->
<div class="timeline">
    <div class = "timeline_title">
        <h4 class = "pink timeline_h">Tijdlijn </h4>
        <button class="infobutton" id = "tijdlijn_button"><img src= "images/info_logo.jpg" alt="Informatie over de tijdlijn" class = "info_img" /></button>
    </div>
<div class ="timeline-scroll">

<?php
    // Get the subject from the url
    $sub = $_GET['sub'];
    // Include the database connection
    include './db_config.php';
    
    // Define the database query
    $query =  "SELECT  year, count(*) as artikelen FROM artikelen WHERE category = '$sub' GROUP BY year ORDER BY year DESC";
    $result_years = $dbh->query($query);
    // Close the connection
    $dbh = null; 

    // The timeline is filled with the classes 'left' and 'right'. These classes determine the place on the time line. 
    // We base the class type on the calculations of odd and even numbers. 
    $class = 'left';
    $i = 0;
    
    while($row = $result_years->fetch(PDO::FETCH_ASSOC)){
    if($i%2 == 0)
        {
            $class = 'left';
        }
        else
        {
            $class = 'right';
        } 
        $i++;
   
    // Determine the right word based on the amount of articles in the database
    if($row['artikelen'] < 2){ 
        $sentence = $row['artikelen'].' artikel';
    }
    else {
        $sentence = $row['artikelen'].' artikelen';
    }

    // Fill the timeline with the years 
    echo '
        <div class="container-timeline '.$class.'-timeline">
            <a class="timeline_link" href="?type=side-line&sub='.$_GET['sub'].'&year='.$row['year'].'#'.$row['year'].'" id="'.$row['year'].'">
            <div class="content-timeline" >
                 <div class = "timeline-button">
                    <h4>'.$row['year'].'</h4>
                        <div class = "textbox" id = "sub='.$_GET['sub'].'&year='.$row['year'].'">
                        <p>'.$sentence.'
                        </div>
                </div>
          </div>
        </a>
    </div>
   ';
  }
?>
</div>
</div>

