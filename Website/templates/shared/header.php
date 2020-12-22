<!-- Include all the relevant stylesheets and javascript --> 
<!DOCTYPE html>
<html lang="en">
    <head>
	    <meta charset="UTF-8">
	    <title>Met Delpher de diepte in: pandemieën</title>
	    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous" />
        <link rel="stylesheet" href="<?=CSS.'/default'; ?>.css" type="text/css" />
        <?php 
            if (!empty($_GET['type'])){
                if ($_GET['type'] == 'side-line'){
                    echo  '<link rel="stylesheet" href="public/css/side-line.css" type="text/css" />';
                }
            }
        ?>
        
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
        <script src="https://cdn.anychart.com/releases/v8/js/anychart-tag-cloud.min.js"></script>
        <script src="https://cdn.anychart.com/releases/8.9.0/js/anychart-core.min.js"></script>
        <script src="https://cdn.anychart.com/releases/8.9.0/js/anychart-bundle.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  </head>

<!--  JQuery code which handles the reloading of the 'details' div when a button is clicked and displays the loader  -->
<script>
    function param(name) {
	    return (location.search.split(name + '=')[1] || '').split('&')[0];
    }

    $(document).ready(function(){
        $('#loader').addClass('hidden');
	    var year = param('year');
	    var sub = param('sub');

	    $('.choose').click(function(event) {
		    $(this).siblings().removeClass('startbtn');
		    $(this).addClass('startbtn');
		    event.preventDefault();
		    var nm = $(this).val();
		    $('#loader').removeClass('hidden');
		    $.ajax({
			    type: 'POST',
			    url: './templates/details.php',
			    data: ({ nm: nm, year: year, sub: sub }),
			    success: function(response) {
				    $('.details').html(response);
				    $('#loader').addClass('hidden');
				    anychart.selectAll(chart).remove();
				    $(".details").find("script").each(function(){
					    eval($(this).text());
				    });
				}
		    });
      });
    });
</script>
</head>
<body>
<?php include "site_header.php" ?>
<div class="main_content">
<div class="container">