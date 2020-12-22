<!-- The page to add the loader and the divs for the timeline and details -->
<?php
    define('STYLE', 'side-line');
    include "shared/header.php"
?>
<div class="lds-spinner overlay" id = "loader">
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
    if (!empty($_GET['sub'])) {
        if (is_file(TEMPLATES.'/timeline_content.php')) {
            include (TEMPLATES.'/timeline_content.php');
        } 
        else {
            define('STYLE', 'error');
            include ('templates/404.php');
        }
    } 
?>
<div class = "year-container">
    <?php
    if (!empty($_GET['sub'])) {
        include (TEMPLATES.'/year_content.php');
    } 
    else {
        define('STYLE', 'error');
        include ('templates/404.php');
    }
?>
</div>
</div>
</div>
<?php include "shared/footer.php" ?>


