<?php

/**
 * Import Class
 */

class Import{
    
    /**
     * Function to convert Ronin status code to Freeagent status
     */
    
    public function convert_status($code, $type) {
        
        switch ($type) {
            
            case 'estimate':
                
                $translate = array(
                    
                    '0' => 'Draft',
                    '1' => 'Sent',
                    '2' => null, // Viewed in Ronin
                    '3' => null, // Cancelled in Ronin
                    '4' => 'Rejected', // Declined in Ronin
                    '5' => null, // Revised in Ronin
                    '6' => 'Approved', // Accepted in Ronin
                    '7' => null // Archived in Ronin
                );
                break;
            
            case 'invoice':
                
                $translate = array(
                    
                    '0' => 'Draft',
                    '1' => 'Sent',
                    '2' => null, // Viewed in Ronin
                    '3' => 'Cancelled',
                    '4' => null, // Paid in Ronin
                );
                break;
            
            case 'project':
                
                $translate = array(
                    
                    '1' => 'Active',
                    '2' => 'Completed',
                    '4' => null, //Billable in Ronin
                );
                break;
        }
        
        return $translate[$code];
    }
    
    /**
     * Function to import contacts
     */
    
    public function contacts($test = true) {
        
        $result = $this->ronin->get_contacts();
        $contacts = $result->contacts;
        
        $count = 0;
        
        foreach($contacts as $contact) {
            
            $client = $this->ronin->get_client(
                    $contact->client_id
                );
            
            $contact_expl = explode(' ', $contact->name);
            
            $translation = array(
                'contact' => array(
                    'organisation_name' => $client->name, //(Required if either first_name or last_name are empty)
                    'first_name' =>  $contact_expl[0], //(Required if organisation_name is empty)
                    'last_name' => $contact_expl[1], //(Required if organisation_name is empty)
                    'email' => $contact->email,
                    'phone_number' => $contact->phone,
                    'address1' => $client->address,
                    'town' => $client->city,
                    'region' => $client->state,
                    'postcode' => $client->zip,
                    'address2' => $client->address_2,
                    'address3' => null,
                    //'contact_name_on_invoices' => $client->name,
                    //'country' => 'United Kingdom',//$client->country,
                    //'sales_tax_registration_number' => null,
                    //'uses_contact_invoice_sequence' => null,
                )
            );

            echo '<p>';
            echo $count;
            echo '</p>';
            echo '<pre>';
            //print_r($translation);
            echo json_encode($translation);
            echo '</pre>';
            
            if(!$test) {
                
                $response = $this->freeagent->create_contact(
                     $translation
                );
                
                echo '<p>Response: </p><pre>';
                print_r($response);
                echo '</pre>';
            
                /*
                if(
                        $response = $this->freeagent->create_contact(
                            $translation
                        )
                    ){
                    
                    echo $response;
                }else{
                    
                    echo '<p>failed</p>';
                }
                 */
                
            }
            
            ++$count;
        }
        
        echo '<p>count: ';
        echo $count;
        echo '</p>';
    }
    
    /**
     * Function to import estimates
     */
    
    public function estimates($test = true) {
        
        $result_test = $this->ronin->get_estimates();
        
        for ($i = 1; $i <= $result_test->page_count; $i++) {
            
            echo 'PAGE: '.$i.'<br />';
            
            $result = $this->ronin->get_estimates($i);

            $estimates = $result->estimates;

            $count = 0;

            foreach($estimates as $estimate) {

                $client = $this->ronin->get_client(
                        $estimate->client_id
                    );

                $ra_contact = $this->ronin->get_client_contact(
                        $estimate->client_id
                    );

                if(
                        !$fa_contact = $this->freeagent->get_contact_by_email(
                            $ra_contact->email
                        )
                ){
                    
                    echo '<p>&nbsp;</p>';
                    echo '<p>Estimate number - '.$estimate->number.' not imported because contact not in freeagent: '.$ra_contact->email.'</p>';
                    echo '<p>&nbsp;</p>';
                    
                }else{
                
                    $fa_contact_id = end(explode('/',$fa_contact['url']));

                    $translation = array(

                        'estimate' => array(

                            'reference' => $estimate->number, //(Required)
                            'contact' => $fa_contact['url'], //(Required)
                            'project' => null,
                            'invoice' => null,
                            'dated_on' => $estimate->date, //(Required)
                            'status' => $this->convert_status($estimate->status, 'estimate'), //(Required)
                                /* can be one of the following:
                                'Draft'
                                'Sent'
                                'Approved'
                                'Rejected'
                                 */
                            'notes' => $estimate->note,
                            'estimate_type' => 'Estimate', 
                                /*can be one of the following:
                                'Estimate'
                                'Quote'
                                'Proposal'
                                 */
                            'currency' => $estimate->currency_code, //(Required)
                            //'sales_tax_rate' => $estimate->tax_label,
                            //'second_sales_tax_rate' => $estimate->tax2_label,
                            'estimate_items' => array(), //(Array)
                        )
                    );

                    //Add Estimate Items

                    foreach($estimate->estimate_items as $estimate_item){

                        $translation['estimate']['estimate_items'][] = array(

                            'item_type' => '-no unit-', //(Required, if estimate_item is given)
                                /* can be one of the following:
                                 'Hours'
                                 'Days'
                                 'Weeks'
                                 'Months'
                                 'Years'
                                */
                            'quantity' => $estimate_item->quantity,
                            'price' => $estimate_item->price, //(Required, if estimate_item is given)
                            'description' => $estimate_item->title, //(Required, if estimate_item is given)
                            //'sales_tax_rate' => '',
                            //'second_sales_tax_rate' => '',
                            //'category' => '', //(optional)
                        );
                    }

                    echo '<p>';
                    echo $count;
                    echo '</p>';
                    echo '<pre>';
                    print_r($translation);
                    echo json_encode($translation);
                    echo '</pre>';

                    if(!$test) {

                        /*
                        if(
                                $this->freeagent->create_estimate(
                                    $translation
                                )
                            ){

                            echo '<p>success</p>';
                        }else{

                            echo '<p>failed</p>';
                        }
                         */

                        $response = $this->freeagent->create_estimate(
                            $translation
                        );

                        echo '<p>Response: </p><pre>';
                        print_r($response);
                        echo '</pre>';
                    }

                    ++$count;
                    
                }
            }

            echo '<p>count: ';
            echo $count;
            echo '</p>';
            
        }
    }
    
    /**
     * Function to import invoices
     */
    
    public function invoices($test = true) {
        
        $result_test = $this->ronin->get_invoices();
        
        for ($i = 1; $i <= $result_test->page_count; $i++) {
            
            echo 'PAGE: '.$i.'<br />';
    
            $result = $this->ronin->get_invoices($i);
            $invoices = $result->invoices;

            $count = 0;

            foreach($invoices as $invoice) {
                
                $client = $this->ronin->get_client(
                        $invoice->client_id
                    );

                $ra_contact = $this->ronin->get_client_contact(
                        $invoice->client_id
                    );                

                if(
                        !$fa_contact = $this->freeagent->get_contact_by_email(
                            $ra_contact->email
                        )
                ){
                    
                    echo '<p>&nbsp;</p>';
                    echo '<p>Invoice number - '.$invoice->number.' not imported because contact not in freeagent: '.$ra_contact->email.'</p>';
                    echo '<p>&nbsp;</p>';
                    
                }else{

                    $translation = array(

                        'invoice' => array(

                            'reference' => $invoice->number, //(Optional, If omitted next invoice reference will be used)
                            'contact' => $fa_contact['url'], //(Required)
                            //'project' => '',
                            'status' => $this->convert_status($invoice->status, 'invoice'),
                                /* Options
                                'Draft'
                                'Scheduled'
                                'Sent'
                                'Cancelled'
                                 */
                            //'comments' => '',
                            'discount_percent' => '',
                            'dated_on' => $invoice->date, //(Required)
                            'due_on' => $invoice->due_date,
                            //'exchange_rate' => '',
                            'payment_terms_in_days' => 30, //(Required)
                            'currency' => $invoice->currency_code,
                            //'ec_status' => '',
                                /* Options?
                                'Non-EC'
                                'EC Goods'
                                'EC Services'
                                 */
                            //'written_off_date' => '', //(Required if invoice status is Cancelled)
                            'invoice_items' => array(), //(Array)

                        )
                    );

                    //Add Invoice Items

                    foreach($invoice->invoice_items as $invoice_item){

                        $translation['invoice']['invoice_items'][] = array(

                            //'position' => '',
                            'item_type' => 'Products',  //(Required, if invoice_item is given)
                            /* can be one of the following:
                            'Hours'
                            'Days'
                            'Weeks'
                            'Months'
                            'Years'
                            'Products'
                            'Services'
                            'Training'
                            'Expenses'
                            'Comment'
                            'Bills'
                            'Discount'
                            'Credit'
                            'VAT'
                             */
                            'quantity' => $invoice_item->quantity,
                            'price' => $invoice_item->price, //(Required, if invoice_item is given and item_type is non time based)
                            'description' => $invoice_item->title, //(Required, if invoice_item is given)
                            //'sales_tax_rate' => '',
                            //'second_sales_tax_rate' => '',
                            //'category' => '', //(optional)
                        );
                    }

                    echo '<p>';
                    echo $count;
                    echo '</p>';
                    echo '<pre>';
                    print_r($translation);
                    echo '</pre>';

                    if(!$test) {

                         $response = $this->freeagent->create_invoice(
                            $translation
                        );

                        echo '<p>Response: </p><pre>';
                        print_r($response);
                        echo '</pre>';
                    }

                    ++$count;
                    
                }
            }

            echo '<p>count: ';
            echo $count;
            echo '</p>';
        }
       
    }
    
    /**
     * Function to import projects
     */
    
    public function projects($test = true) {
        
        $result_test = $this->ronin->get_projects();
        
        for ($i = 1; $i <= $result_test->page_count; $i++) {
            
            echo 'PAGE: '.$i.'<br />';
        
            $result = $this->ronin->get_projects($i);
            $projects = $result->projects;

            $count = 0;

            foreach($projects as $project) {
                
                if(
                    $client = $this->ronin->get_client(
                        $project->client->id
                    )
                ){
                    
                    $ra_contact = $this->ronin->get_client_contact(
                            $project->client->id
                        );

                    if(
                            !$fa_contact = $this->freeagent->get_contact_by_email(
                                $ra_contact->email
                            )
                    ){

                        echo '<p>&nbsp;</p>';
                        echo '<p>Project number - '.$project->number.' not imported because contact not in freeagent: '.$ra_contact->email.'</p>';
                        echo '<p>&nbsp;</p>';

                    }else{

                        $translation = array(

                            'project' => array(

                                'contact' => $fa_contact['url'], //'/v2/contacts/'.end(explode('/',$fa_contact['url'])), //(Required)
                                'name' => $project->name, //(Required)
                                //'starts_on' => '',
                                'ends_on' => $project->end_date,
                                'budget' => ($project->budget > 0)? $project->budget: 0,
                                //'is_ir35' => '',
                                //'contract_po_reference' => '',
                                'status' => $this->convert_status($project->status, 'project'), //(Required)
                                    /* Can be one of the following:
                                    'Active'
                                    'Completed'
                                    'Cancelled'
                                    'Hidden'
                                     */
                                'budget_units' => ($project->budget_type == 1)? 'Monetary':'Hours', //(Required)
                                    /* Can be one of the following:
                                    'Hours'
                                    'Days'
                                    'Monetary'
                                     */
                                'normal_billing_rate' => '',
                                //'hours_per_day' => '',
                                //'uses_project_invoice_sequence' => '',
                                'currency' => $project->currency_code, //(Required)
                                //'billing_period' => '',

                            )
                        );

                        echo '<p>';
                        echo $count;
                        echo '</p>';
                        echo '<pre>';
                        print_r($translation);
                        echo json_encode($translation);
                        echo '</pre>';

                        if(!$test) {

                            /*
                            if(
                                    $this->freeagent->create_project(
                                        $translation
                                    )
                                ){

                                echo '<p>success</p>';
                            }else{

                                echo '<p>failed</p>';
                            }
                             */

                            $response = $this->freeagent->create_project(
                               $translation
                            );

                            echo '<p>Response: </p><pre>';
                            print_r($response);
                            echo '</pre>';
                        }

                        ++$count;

                    }
                    
                }else{
                    
                    echo 'project '.$project->id.' is internal so not imported';

                }


            }
        }
        
        echo '<p>count: ';
        echo $count;
        echo '</p>';
    }
}