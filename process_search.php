<?php
require 'sections/get_sections.php';
require_once 'class/class.Database.inc';
require 'class/utility.php';

// Page's title
$title = 'Scraping Form to Real Estate';

// HTML Header
get_header($title);

?>

<div class="container-fluid">
    <div class="row text-center">
        <div class="col-md-12 site_title">
            <a href="index.php">
                <img src="img/digging.png" alt=""/>
            </a>
            <a href="index.php"><span>Scraping to Real Estate</span></a>
        </div>
     
    </div>
    
    <hr>
    
    <?php
        
        // Get user's input from the search form
        $inputs = getSearchInputs();
        
        // Get properties based on the inputs
        $properties = getPropertySet($inputs);
        
        
        // Display results if found
        if( count($properties) ){
            echo '<div class="row">';
                echo '<div class="col-md-6 col-sm-6">';
                    echo 'Keyword: <strong>' .$inputs['keyword']. '</strong><br>';
                    echo 'Suburb: <strong>' .$inputs['suburbOption']. '</strong><br>';
                    echo "<h4>Found: " .count($properties). " properties</h4>";
                echo '</div>';
                
                echo '<div class="col-md-6 col-sm-6 text-right">';
                echo '<a href="index.php" title="Another search">Another search</a>';
                echo '</div>';
                
            echo '</div>'; //end row

            // Set directory for images
            $img_dir = 'images_property/';

            
            foreach( $properties as $row ){
                // Display each property details
                echo '<div class="row property">';
                
                // Property image
                echo '<div class="col-sm-4 col-md-4">'; 
                    echo '<img src="' .$img_dir.$row['id'].'.jpg" class="img-rounded" width="400" height="300">';
                echo '</div>'; //end property image

                // Property description
                echo '<div class="col-sm-8 col-md-8">';
                
                    echo '<div class="row">';
                        echo '<div class="col-md-6">';
                            $addresses = $row['address']. ', ' .$row['suburb']. ', ' .$row['postcode'];
                            echo '<p><b>' .$row['price_text']. '</b></p>';
                            echo '<p><b>'.$addresses.'</b></p>';
                        echo '</div>';
                        echo '<div class="col-md-6">';
                            echo '<p>ID: ' .$row['id']. '</p>';
                            echo '<p>Bedroom: <strong>' .$row['no_bedroom']. '</strong> | Bathroom: <strong>'.$row['no_bathroom'].'</strong> | Type: <strong>' .$row['type']. '</strong></p>';
                        echo '</div>';
                    echo '</div>'; //end inner row
 
                    echo '<p><b>' .$row['title']. '</b></p>';
                    echo '<p>' .$row['description']. '</p>';
                echo '</div>'; //end property description
                
                echo '</div>'; //end property row
            }
        }else{ // No properties found
            echo '<div class="row">';
                echo '<div class="col-sm-12 col-md-12">';
                    echo "<h3>Search Results</h3>";
                    
                    // Create/open a panel
                    echo '<div class="panel panel-danger">';
                    
                    // Panel heading
                    echo '<div class="panel-heading">Sorry, no properties found.</div>';
                    // Panel body
                    echo '<div class="panel-body">';
                    echo 'Searched: <br>Keyword: <strong>' .$inputs['keyword']. '</strong><br>Suburb: <strong>' .$inputs['suburbOption']. '</strong>';
                    echo '<p><a href="index.php">Make another search</a></p>';
                    echo '</div>';
                    
                    // Closing panel
                    echo '</div>'; //end panel
                    
                echo '</div>';
            echo '</div>';                  
        }

    ?>
</div>
        
<?php
// Get page footer
include 'sections/footer.html';