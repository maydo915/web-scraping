<?php
require 'sections/get_sections.php';
require_once 'class/class.Database.inc';
require 'class/utility.php';

// Page's title
$title = 'Searching Form';

// HTML Header
get_header($title);

?>

<div class="container-fluid">
    <!-- Site Title -->
    <div class="row text-center">
        <div class="col-md-12 site_title">
            <img src="img/digging.png" alt=""/><span>Scraping to Real Estate</span>
        </div>     
    </div>
    <hr>
    
    <h3>Property Search</h3>
    <?php    include 'forms/form_search.php'; ?>
</div>

<?php
// Get page footer
include 'sections/footer.html';