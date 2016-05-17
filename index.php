<?php

require 'sections/get_sections.php';
require_once 'class/class.Database.inc';
require 'class/utility.php';

// Page's title
$title = 'Scraping Form to Real Estate';

// HTML Header
get_header($title);
?>

<script type="text/javascript">
    /*
    $(function(){
        // Hide initially
        $(".loader").hide();

        $("#scraping").click(function(){
            $(".loader").show();
        });

     });
*/
</script>


<div class="container-fluid">
    <!-- Site Header/Title -->
    <div class="row text-center">
        <div class="col-md-12 site_title">
            <img src="img/digging.png" alt=""/><span>Scraping to Real Estate</span>
        </div>
     
    </div>
    
    <!-- Centered Tabs -->
    <ul class="nav nav-pills nav-justified red">
        <li class="active"><a data-toggle="pill" href="#scraping">Scraping to Real Estate Site</a></li>
        <li><a data-toggle="pill" href="#search">Search Properties</a></li>
    </ul>

    <!-- Tab contents -->
    <div class="tab-content">
        
        <!-- Scraping Form -->
        <div id="scraping" class="tab-pane fade in active">
            <!-- Instruction --> 
            <p></p>
            <p>Please select either <strong>Scraping to Real Estate</strong> OR <strong>Search Properties</strong> tabs above</p>
            
            <h3>Scraping Form</h3>
            <p>Please copy and paste an URL of realestate website</p>

            <div class="loader"></div>

            <?php include 'forms/form_scraping.html'; ?>

            <hr>

            <div class="alert alert-info">
                <strong>Notes:</strong> <mark>residential-land/project-types</mark> are excluded for scraping
            </div>
        </div>
        
        <!-- Searching Form -->
        <div id="search" class="tab-pane">
            <h3>Search Properties</h3>
            <?php include 'forms/form_search.php'; ?>
        </div>
        
    </div>

</div><!-- Main contents -->

<?php
// Get page footer
include 'sections/footer.html';
