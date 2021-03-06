<?php

    if (!defined('APPPATH'))
        exit('No direct script access allowed');

/**
 * Controller page for the "Program" page view
 *
 */
class Program extends Application{
    
    function __construct() {
        parent::__construct();
    }
    
    function index() 
    {
        
        $this->data['pagebody'] = 'programs';
        
        //Retrieve all programs from relevant database
        $program = $this->Programs->getall();
        $this->data['program'] = $program;
        
        $this->render();
    }
    
    //Returns a specified page regarding a program
    function getOne($id)
    {

        //Required page
        $this->data['pagebody'] = 'programs_sub';

        //Retrieve info for specified page by id
        $program = $this->Programs->get($id);

        //Populating page data
        $this->data['id'] = $program->id;
        $this->data['name'] = $program->name;
        $this->data['caption'] = $program->caption;
        $this->data['description'] = $program->description;
        $this->data['location'] = $program->location;
        $this->data['price'] = $program->price;
        $this->data['image1'] = $program->image1;
        $this->data['image2'] = $program->image2;
        $this->data['image3'] = $program->image3;
        $this->data['image4'] = $program->image4;

        $this->render();
    }

}
/* End of file Programs.php */
/* Location: application/controllers/Program.php */