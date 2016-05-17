<?php
require 'sections/get_sections.php';
require_once 'class/simple_html_dom.php';
require_once 'class/class.Property.inc.php';
require_once 'class/utility.php';
require_once 'class/class.Database.inc';

// Get URL from the form
$url = getURL(); 

// Create a property DOM object
$property_dom = new Property();
$property_dom->createDOM($url);

// Get a webpage's pagination
$pagination = $property_dom->getPagination();
if( count($pagination)){
    $no_pagination = count($pagination);
}  else {
    $no_pagination = 0;
}

// Get a list of address in the first page
$property_dom->getAddresses();

// Get all property addresses by going thru the pagination of webpage
foreach ($pagination as $page) {  
    $property_dom->createDOM($page);
    $property_dom->getAddresses();
}
$addresses = (Property::$addresses);


//###########################################################################

// HTML Header
get_header($title);


?>
<div class="container-fluid">
    <div class="row text-center">
        <div class="col-md-6 site_title">
            <a href="index.php" title="Scraping to Real Estate">
                <img src="img/digging.png" alt=""/><span>Scraping to Real Estate</span>
            </a>            
        </div>
        <div class="col-md-6"></div>        
    </div>
    
    <hr>
    
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <?php

            echo '<h3>Scraping Results</h3>';
            echo 'Search URL: <p><strong>' .$url. '</strong></p>';        

            // Checking property types
            $invalid_types = array(
                "residential+land",
                "project-",
                "project-lightsview",
                "project-park",
                "project-living"
            );
            
            // Number of valid property type
            $no_valid_prop = 0;

            // Number of invalid property type
            $no_invalid_prop = 0;

            // Flag to keep track where finding invalid properties
            $is_valid = TRUE;
            
            echo '<div class="panel panel-primary">';
                // Panel heading
                echo '<div class="panel-heading">';
                    echo 'Found number of property: <span class="label label-default">' .  count($addresses). '</span>';
                echo '</div>';
                
                echo '<div class="panel-body">';
                
                    echo '<div class="row">';
                        echo '<div class="col-md-8 col-sm-8">';
                        
                            // Wrap all links of property URL into scrollable div
                            echo '<div class="scrollable">';
                            // Check each property whether it is valid for scraping
                            foreach ($addresses as $index=>$name){

                                // Convert all URLs to lowercase
                                $name = strtolower($name);

                                // Check whether the property falls into these types
                                foreach( $invalid_types as $type) {

                                    if(strpos($name, $type)){
                                        $is_valid = FALSE;
                                        echo '<br><span class="label label-danger">' .$name. '<br>';
                                        echo 'Property is invalid: ' .$type. '</span>'; 
                                    }
                                }

                                // Get number of valid/invalid properties for scraping
                                if($is_valid){
                                    // Increase number of valid property
                                    $no_valid_prop++;
                                    echo '<br>' .$no_valid_prop. '. ' .$name;
                                    $property_list[] = getDetails($name);

                                }else{
                                    // Increase number of invalid property
                                    $no_invalid_prop++;
                                }

                                // Reset invalid value
                                $is_valid = TRUE;
                            }
                            echo '</div>'; // Close srollable div
                        
                        echo '</div>';
                        echo '<div class="col-md-4 col-sm-4">';
                            // Display number of valid/invalid properties for scraping
                            echo '<p>Total of invalid: <span class="label label-danger">' .$no_invalid_prop. '</span></p>';
                            echo '<p>Number of valid: <span class="label label-success">' .$no_valid_prop. '</span></p>';
                            echo '<p><span class="label label-success">Inserted number of properties: ' .Database::$no_insert_rows. '</span></p>';
                            
                            echo '<p><strong>Legend</strong></p>';
                            echo '<p><span class="label label-danger">Types: residential-land/project-lightview/project-park</span></p>';
                            echo '<p><span class="label label-success">Standard types for scraping</span></p>';

                        echo '</div>';
                    echo '</div>'; //end inner row within panel-body
                    
                echo '</div>'; //end panel-body
            echo '</div>'; //end panel wrapper       
            
            // INSERT INTO DATABASE  
            insertToDB($property_list);
            
            echo '<br>';
            
        ?>
            
            <p class='text-right'><a href="index.php" title="Perform another search?">Perform another search?</a></p>
            
        </div>
    </div>

</div>

<?php
// Get page footer
include 'sections/footer.html';


