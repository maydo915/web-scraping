<?php
/*
 * Date: 22 Feb 2016
 * Sources:
 * http://www.syntaxxx.com/using-php-and-curl-to-scrape-web-pages/
 * http://php.net/manual/en/function.curl-init.php
 */


// Create and load the HTML file
//require_once 'simple_html_dom.php';

function get_html($page){
    
    // Initialize a cURL session, create a new cURL resource
    $ch = curl_init();

    // Set URL and other appropriate options
    // Set an URL will be scraping
    curl_setopt($ch, CURLOPT_URL, $page);
    //curl_setopt($ch, CURLOPT_HEADER, 0);

    // Tells cURL to store the scraped page in a variable, rather than its default, which is to simply display the entire page as it is
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the session
    $output = curl_exec($ch);

    // Basic error checking
    if ($output === FALSE) {
        echo 'cURL error: ' .curl_error($ch); //echo last session errors
    }

    // Close the session, frees up all session resources; the cURL handle is also deleted.
    curl_close($ch);

    return $output;
		
} // end function curl_download

function setTimeZone(){
    
    // Manually set the timezone
    date_default_timezone_set('Australia/Adelaide');
}

/**
 * 
 * @param type $url
 * @return type
 */
function getDetails($url){
    //echo $url;
    
    // Define class selectors of HTML DOM
    $index = 0;
    $class_selectors = array(
        "price" => "ul[class=info] li",
        "image" => "div[class=innerCont] a",
        "features" => "div[class=featureList] ul"
    );

    // Create a property DOM object
    $property_dom = new Property();
    $property_dom->createDOM($url);

    // ID and address
    $property_dom->getHeader('listing_header');

    // Get price
    $property_dom->retrievePrice($class_selectors['price'], $index);

    // Get features
    $property_dom->retrieveFeatures($class_selectors['features'], $index);

    // Get title and description
    $property_dom->getDescription('description');

    // Get image
    $property_dom->retrieveImages($class_selectors['image']);

    // Get details
    $prop_details = $property_dom->getPropertyDetails();
    
    return $prop_details;
    
}

/**
 * Insert property details into database
 * @param array $property_list
 */
function insertToDB($property_list){
    // Get current date, stored in MySQL date format Y/m/d
    $today = date('Y/m/d h:i:s a', time());
    
    // Insert to DB (not insert STATE)
    $insert_to_properties = "INSERT IGNORE INTO Properties(id, address, suburb, postcode, "
        . "price_text, no_bedroom, no_bathroom, build_size, "
        . "land_size, title, description, property_type, date_inserted) "
        . " VALUES(:id, :address , :suburb , :postcode , :price_text, :no_bedroom , :no_bathroom , "
        . ":build_size, :land_size, :title, :description, :type, :date_inserted)";
            
    /* Insert a new record */
    $database = new Database();

    // Begin a traction
    $database->beginTransaction();

    //Prepare the query
    //$database->query($insert_to_properties);
    $database->prepareQuery($insert_to_properties);
            
    // INSERT TO DATABASE
    // BIND multiple records of properties

    foreach ( $property_list as $rows ){
        $database->bind(':id', $rows['id']);
        $database->bind(':address', $rows['address']);
        $database->bind(':suburb', $rows['suburb']);
        $database->bind(':postcode', $rows['postcode']);
        $database->bind(':price_text', $rows['price_text']);
        $database->bind(':no_bedroom', $rows['no_bedroom']);
        $database->bind(':no_bathroom', $rows['no_bathroom']);
        $database->bind(':build_size', $rows['build_size']);
        $database->bind(':land_size', $rows['land_size']);
        $database->bind(':title', $rows['title']);
        $database->bind(':description', $rows['description']);
        $database->bind(':type', $rows['type']);
        $database->bind(':date_inserted', $today);

        // Execute
        $database->execute();
        $database->rowCount();

    }

    // End the transaction
    $database->endTransaction();
    
}

/**
 * Get a list of suburbs
 * @param string $sql
 * @return array
 */
function getSuburbs($sql){
    $db = new Database();  

    // Prepare the query
    $db->prepareQuery($sql);

    // Get result set
    $rows = $db->resultSet();
    
    // Get all suburbs
    foreach( $rows as $area) {
        $index = $area['suburb'];
        $suburb[$index] = $area['suburb']. ', ' .$area['state']. ', ' .$area['postcode'] ;
    }
    
    return $suburb;
}

/**
 * 
 * @return array User inputs from search form
 */
function getSearchInputs(){
    if(filter_input(INPUT_POST, "keyword") ){
        $inputs['keyword'] = filter_input(INPUT_POST, "keyword");
    }
    if(filter_input(INPUT_POST, "suburbOption") ){
        $inputs['suburbOption'] = filter_input(INPUT_POST, "suburbOption");
    }
    
    return $inputs;
}

function getPropertySet($inputs){
    
    $sql = "SELECT * "
            . "FROM Properties "
            . "WHERE description LIKE :keyword AND suburb = :suburb";
    
    $db = new Database();  

    // Prepare the query
    $db->prepareQuery($sql);
    
    $keyword_str = '%' .$inputs['keyword']. '%';
    
    $db->bind(":keyword", $keyword_str);
    $db->bind(":suburb", $inputs['suburbOption']);
    
    // Execute the query   
    $properties = $db->resultSet();
    
    return $properties;
}