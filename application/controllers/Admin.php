<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Admin extends Application {

    function __construct()
    {
        parent::__construct();
        $this->load->helper('formfields'); 
      
    }

    function index()
    {
        $this->data['title'] = 'Programs Maintenance';
        $this->data['programs'] = $this->Programs->getall();
        $this->data['pagebody'] = 'admin_main'; 
        $this->render();
    }
  
    // Add a new quotation
    function add()
    {
      $program = $this->Programs->create();
      $this->present($program);
    }
    
    // Present a quotation for adding/editing
    function present($program)
    {
        $message = '';
        if (count($this->errors) > 0)
        {
            foreach ($this->errors as $booboo)
            $message .= $booboo . BR;
        }
        $this->data['message'] = $message;
        
        $this->data['fid'] = makeTextField('ID#', 'id', $program->id,"Unique quote identifier, system-assigned",10,10,true);
        $this->data['fname'] = makeTextField('Name', 'name', $program->name);
        $this->data['fcaption'] = makeTextField('Caption', 'caption', $program->caption, 'Summary of Program');
        $this->data['fdescription'] = makeTextArea('Description', 'description', $program->description, 'Detailed description of the program', 500, 40, 5);
        $this->data['flocation'] = makeTextField('Location', 'location', $program->location, 'The address/location of the program');
        $this->data['fprice'] = makeTextField('Price', 'price', $program->price, 'Cost of the program monthly');
        $this->data['fimage1'] = makeImageUploader('Main Picture', 'picmain', 'Choose a file');
        $this->data['fimage2'] = makeImageUploader('Picture 1', 'pic1', 'Choose a file');
        $this->data['fimage3'] = makeImageUploader('Picture 2', 'pic2', 'Choose a file');
        $this->data['fimage4'] = makeImageUploader('Picture 3', 'pic3', 'Choose a file');
               
        $this->data['pagebody'] = 'add_program';
        
        $this->data['fsubmit'] = makeSubmitButton('Add Program', "Click here to validate the quotation data", 'btn-success');
        
        $this->render();
    }
    
    // process a quotation edit
    function confirm()
    {
        
        $record = $this->Programs->create();
        // Extract submitted fields
        $record->id = $this->input->post('id');
        $record->name = $this->input->post('name');
        $record->caption = $this->input->post('caption');
        $record->description = $this->input->post('description');
        $record->location = $this->input->post('location');
        $record->price = $this->input->post('price');
        
            $config['upload_path'] = 'data/images';  
            $config['allowed_types'] = 'gif|jpg|png'; 
            $config['max_size']	= '3000'; //in kilobytes
            $config['max_width']  = '1600';
            $config['max_height']  = '900';
            $config['remove_spaces'] = true;  //substitutes spaces with underscores
            $config['overwrite'] = true; //allows overwriting of previous files
            $this->load->library('upload', $config); //load upload library '/libraries/Upload.php'
            

            //these are the names of your upload buttons from your form
            $upload_controls = array('picmain', 'pic1', 'pic2', 'pic3');
            foreach ($upload_controls as $uploadpic)
            {
                //this is where i rename the files to 'attraction_id_ + formname + last 4 digits of the orignal name'
                // ie.   thisismypic.jpg  -->  'attraction_id_pic1.jpg' //attraction_id will be replaced with a variable.
                if($_FILES[$uploadpic]['name'] != ""){
                    $temp = $_FILES[$uploadpic]['name'];
                    $_FILES[$uploadpic]['name'] = $attraction_num . $uploadpic . substr($temp, -4);
                }

                //call the codeigniter upload $uploadpic is the form upload control
                if ($this->upload->do_upload($uploadpic))
                {   
                    $record = (array) $this->attractionsDB->get($attraction_num);
                    if($uploadpic == 'picmain'){
                        $record['image1'] = '/data/images/' . $_FILES[$uploadpic]['name'];
                    }
                    if($uploadpic == 'pic1'){
                        $record['image2'] = '/data/images/' . $_FILES[$uploadpic]['name'];
                    }
                    if($uploadpic == 'pic2'){
                        $record['image3'] = '/data/images/' . $_FILES[$uploadpic]['name'];
                    }
                    if($uploadpic == 'pic3'){
                        $record['image4'] = '/data/images/' . $_FILES[$uploadpic]['name'];
                    }
                }
            } // end of foreach upload
            
        
        //validate
        if (empty($record->name))
        $this->errors[] = 'You must specify a name.';
        if (empty($record->location))
        $this->errors[] = 'You must specify a location.';
        if (empty($record->price))
        $this->errors[] = 'You must specify a price.';
                  
        if (strlen($record->description) < 50)
        $this->errors[] = 'A description must be at least 50 characters long.';
        
        //reporompt form
        if (count($this->errors) > 0)
        {
            $this->present($record);
            return; // make sure we don't try to save anything
        }
        //save
        if (empty($record->id)) $this->Programs->add($record);
        else $this->Programs->update($record);
        redirect('/admin');
    }
    
    function delete($program)
    {
        //get attraction to delete
        $record = (array)$this->Programs->get($program); 
        //Column names for images
        $filesToDelete = array('image1', 'image2', 'image3', 'image4'); 
        foreach ($filesToDelete as $delete) {
            if($record[$delete] !== '/data/images/default.jpg'){
                unlink(FCPATH.$record[$delete]);
            }
        }
        //delete the record after images were deleted
        $this->Programs->delete($program);
        redirect("/admin");
    }

}