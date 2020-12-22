<!-- This page displays the footer when the timeline page is shown -->
<footer class="footer">
    <div class="row subnav">
        <?php
            // When a subject is choosen, the buttons changes colour
            $sub = $_GET['sub'];

            echo '<a href="?type=side-line&sub=pandemie" class = "link_subnavs">';
            if ($sub == 'pandemie'){
                echo '<div class="col-sm-2 selected_sub">pandemie  </div>';
            }
            else {
                echo '<div class="col-sm-2">pandemie  </div>';
            }
            echo '</a>';

            echo '<a href="?type=side-line&sub=uitbraak"  class = "link_subnavs">';
            if ($sub == 'uitbraak'){
                echo '<div class="col-sm-2 selected_sub">uitbraak  </div>';
            }
            else {
                echo '<div class="col-sm-2">uitbraak  </div>';
            }
            echo '</a>';

            echo '<a href="?type=side-line&sub=immuniteit"  class = "link_subnavs">';
            if ($sub == 'immuniteit'){
                echo '<div class="col-sm-2 selected_sub">immuniteit  </div>';
            }
            else {
                echo '<div class="col-sm-2">immuniteit  </div>';
            }
            echo '</a>';

            echo '<a href="?type=side-line&sub=spaansegriep" class = "link_subnavs">';
            if ($sub == 'spaansegriep'){
                echo '<div class="col-sm-2 selected_sub">Spaanse griep  </div>';
            }
            else {
                echo '<div class="col-sm-2">Spaanse griep  </div>';
            }
            echo '</a>';

            echo '</div>';
            echo '</footer>';
            echo '</body>';
            echo '</html>';
?>
