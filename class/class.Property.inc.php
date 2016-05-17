<?php

/* 
 * Retrieve property list based on URLs
 * Date: 28 March 2016
 */

class Property {
    // Webpage's url to be scraped for addresses
    private $url;
    
    // DOM object for finding HTML elements
    private $dom;
    
    // Number of page in webpage's pagination
    private $pagination = array();
    
    // Property addresses
    public static $addresses = array();

    // Store property details
    private $property = array(); 
    
    // Real Estate default server URL
    const DEFAULT_URL = 'http://www.realestate.com.au';
    
    /**
     * Constructor
     */
    public function __construct() {}

    /**
     * Create a DOM object based on a given URL
     * @param string $url Address URL of webpage
     * @return void Assign DOM object using a given URL
     */
    public function createDOM($url){
        if(!is_null($this->dom)) { 
            $this->dom->clear();
            unset($this->dom);  
        }
        
        $this->url = $url;
        $this->dom = new simple_html_dom();
        
        // Using cURL
        $html_source = get_html($url);
        $this->dom->load($html_source);
    }
    
    /**
     * Retrieve the page's URL
     * @return string 
     */
     public function getURL(){
        return $this->url;
    }
    
    /**
     * Clear memory for DOM object
     */
    
    public function __destruct(){
        $this->dom->clear();
        unset($this->dom);  
    }

    /**
     * Get pagination list of a webpage
     * @return array $pagination Return an array with number of pages
     */
    public function getPagination(){       
        // PAGINATION -  get the bottom navigation
        $pagination = $this->dom->find('ul[class=pagination]' , 1);

        if( count($pagination) ){
            foreach( $pagination->find('a') as $link) {
                // Ignore Next link
                if( $link->plaintext !== 'Next'){
                    // Get all anchor attributes of links
                    //$link_url = $this->DEFAULT_URL .$link->href;
                    $link_url = self::DEFAULT_URL .$link->href;
                    $this->pagination[] = $link_url;
                }    
            }
        }
        
        return $this->pagination;
    }
    
    /**
     * Get a list of all addressess
     */
    public function getAddresses(){
        $class_selector = 'a[class=name]';   
        
        // Get all elements of properties
        $list = $this->dom->find($class_selector);
        
        foreach ($list as $name){
            $link = self::DEFAULT_URL. $name->href;
            self::$addresses[] = $link;
        }
    }
    
    public function getHeader($id_selector){
        $pro_header = $this->dom->getElementById($id_selector);

        // Get property id
        $id_str = $pro_header->children(0)->plaintext;
        $id = substr($id_str, 13);


        // Remove white spaces both left side and right side of id string
        $this->property['id'] = trim($id);

        // Get property address
        $address = $pro_header->children(1)->children(0)->plaintext;
        // Remove white spaces both left side and right side of address string
        $this->property['address'] = trim($address);

        // Suburb/City
        $suburb = $pro_header->children(1)->children(1)->plaintext;
        $this->property['suburb'] = trim($suburb);
        
        // Postcode
        $postcode = $pro_header->children(1)->children(3)->plaintext;
        $this->property['postcode'] = trim($postcode);
    }

    public function getDescription($id_selector){
        // Get property title  
        $pro_des = $this->dom->getElementById($id_selector) ;

        // Get title
        $title = $pro_des->children(2)->plaintext ;
        $this->property['title'] = trim($title);

        // Get property description
        $description = $pro_des->children(3)->innertext;
        $this->property['description'] = trim($description);
    }

    /**
     * Get property ID, address, title and description
     * @param string $id_selector Search based on element id
     * @return void Get property details and assign to property array
     */
    public function retrievePropertyHeader($id_selector){
        
        if( $id_selector == 'listing_header'){
            $pro_header = $this->dom->getElementById($id_selector);      
            // Get property id
            $id_str = $pro_header->children(0)->plaintext;
            $id = substr($id_str, 13);
            
            
            // Remove white spaces both left side and right side of id string
            $this->property['id'] = trim($id);
            
            // Get property address
            $address = $pro_header->children(1)->children(0)->plaintext;
            // Remove white spaces both left side and right side of address string
            $this->property['address'] = trim($address);

            // Suburb/City
            $suburb = $pro_header->children(1)->children(1)->plaintext;
            $this->property['suburb'] = trim($suburb);

            // Postcode
            $postcode = $pro_header->children(1)->children(3)->plaintext;
            $this->property['postcode'] = trim($postcode);
            
        }
        
        
        if( $id_selector === 'description'){
            
            // Get property title  
            $pro_des = $this->dom->getElementById($id_selector) ;
            
            // Get title
            $title = $pro_des->children(2)->plaintext ;
            $this->property['title'] = trim($title);
            
            // Get property description
            $description = $pro_des->children(3)->innertext;
            $this->property['description'] = trim($description);   
       
        }
        
    }
    
    /**
     * Get property price
     * @param string $class_selector Selector string to search price string
     * @param number $index The target of found elements based on search term.
     * @return void Retrieve property price
     */
    public function retrievePrice($class_selector, $index){

        $price_str = $this->dom->find($class_selector, $index);
        $price = $price_str->children(0)->plaintext;
        
        // Remove white spaces both left side and right side of price string
        $this->property['price_text'] = trim($price);
    }
    
    /**
     * Retrieve property images
     * @param string $class_selector 
     * @return void Retrieve images from web pages and store on the local system
     */
    public function retrieveImages($class_selector){
        
        $no_img = 0;
        $images = $this->dom->find($class_selector);
        
        foreach ($images as $img){

            $server_img = $img->children(0)->getAttribute('src');
            $img_dir = 'images_property/' .$this->property['id']. '.jpg' ;        
            
            // Check whether image file exist before copying
            if( !file_exists($img_dir) ){
                copy($server_img, $img_dir);
                $no_img ++;
            }
            
        }
    }
    
    /**
     * Retrieve property general features
     * @param string $class_selector Selector of class to find general feature strings
     * @param number $index The target of found elements based on seach term.
     * @return void Retrieve property general features
     */
    public function retrieveFeatures($class_selector, $index){
        
        // Point to featureList class
        $general_features = $this->dom->find($class_selector, $index);
        
        // Property type
        $type = $general_features->children(1)->children(0)->plaintext;
        // Get number of bedroom
        $no_bedroom = $general_features->children(2)->children(0)->plaintext;
        // Get number of bathroom
        $no_bathroom = $general_features->children(3)->children(0)->plaintext;
        
        // Assign to property, and remove white spaces both left side and right side strings
        $this->property['type'] =  trim($type);
        $this->property['no_bedroom'] = trim($no_bedroom);
        $this->property['no_bathroom'] = trim($no_bathroom);
        
        //echo '<br>type:' .$type;
        //echo '<br>type:' .$no_bedroom;
        //echo '<br>type:' .$no_bathroom;
        
        // Get property building/land size
        $build_land_size = $general_features->children(4);
        // Check whether building/land size is exist
        if ( $build_land_size !== NULL) {

            // Check whether building size exist
            if ( strstr($build_land_size, "Building Size:")) {
                $building_str = $build_land_size->children(0)->plaintext;
                $this->property['build_size'] = floatval($building_str);

            }elseif (strstr($build_land_size, "Land Size:")) {
                $land_str = $build_land_size->children(0)->plaintext;
                $this->property['land_size'] = floatval($land_str);
            }
        }
        
        $land_size = $general_features->children(5);

        // Check whether building/land size is exist
        if ( $land_size !== NULL) {
            if (strstr($land_size, "Land Size:")) {
                $land_str = $land_size->children(0)->plaintext;
                $this->property['land_size'] = floatval($land_str);
            }
        }
        
    }
    
    /**
     * @return array Property details stored in an array
     */
    public function getPropertyDetails(){
        return $this->property;
    }

    
}

