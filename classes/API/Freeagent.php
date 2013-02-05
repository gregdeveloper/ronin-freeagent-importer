<?php

/**
 * Freeagent Class
 */

class API_Freeagent{
    
    /**
     * DEFINITIONS
     */
    
    /**
     * CONTACTS
        Create a contact
        POST https://api.freeagent.com/v2/contacts
        Input
        organisation_name (Required if either first_name or last_name are empty)
        first_name (Required if organisation_name is empty)
        last_name (Required if organisation_name is empty)
        email
        phone_number
        address1
        town
        region
        postcode
        address2
        address3
        contact_name_on_invoices
        country
        sales_tax_registration_number
        uses_contact_invoice_sequence
     */
    
    /**
     * ESTIMATES
        Create an estimate
        POST https://api.freeagent.com/v2/estimates
        reference (Required)
        contact (Required)
        project
        invoice
        dated_on (Required)
        status (Required) can be one of the following:
        Draft
        Sent
        Approved
        Rejected
        notes
        estimate_type can be one of the following:
        Estimate
        Quote
        Proposal
        currency (Required)
        sales_tax_rate
        second_sales_tax_rate
        estimate_items (Array)
        estimate_item (Hash)
        position
        item_type (Required, if estimate_item is given) can be one of the following:
        Hours
        Days
        Weeks
        Months
        Years
        quantity
        price (Required, if estimate_item is given)
        description (Required, if estimate_item is given)
        sales_tax_rate
        second_sales_tax_rate
        category (optional)
     */
    
    /**
     * EXPENSES
        Create an expense
        POST https://api.freeagent.com/v2/expenses
        Input
        user (Required)
        project
        gross_value
        sales_tax_rate
        description (Required)
        dated_on (Required)
        category (Required)
        mileage (Required if mileage category selected)
        reclaim_mileage_rate (Required if mileage category selected)
        rebill_mileage_rate (Required if mileage category selected)
        recurring
        manual_sales_tax_amount
        rebill_factor
        attachment (Hash)
        data (Required only if attachment is given) must contain the binary data of the file being attached, encoded as Base64.
        file_name
        description
        content_type can be one of the following:
        image/png
        image/x-png
        image/jpeg
        image/jpg
        image/gif
        application/x-pdf
     */
    
    /**
     * INVOICES
        Create an invoice
        POST https://api.freeagent.com/v2/invoices
        Input
        reference (Optional, If omitted next invoice reference will be used)
        contact (Required)
        project
        status
        Draft
        Scheduled
        Sent
        Cancelled
        comments
        discount_percent
        dated_on (Required)
        due_on
        exchange_rate
        payment_terms_in_days (Required)
        currency
        ec_status
        Non-EC
        EC Goods
        EC Services
        written_off_date (Required if invoice status is Cancelled)
        invoice_items (Array)
        invoice_item (Hash)
        position
        item_type (Required, if invoice_item is given) can be one of the following:
        Hours
        Days
        Weeks
        Months
        Years
        Products
        Services
        Training
        Expenses
        Comment
        Bills
        Discount
        Credit
        VAT
        quantity
        price (Required, if invoice_item is given and item_type is non time based)
        description (Required, if invoice_item is given)
        sales_tax_rate
        second_sales_tax_rate
        category (optional)
     */
    
    /**
     * PROJECTS
        Create a project
        POST https://api.freeagent.com/v2/projects
        Input
        contact (Required)
        name (Required)
        starts_on
        ends_on
        budget
        is_ir35
        contract_po_reference
        status (Required) Can be one of the following:
        Active
        Completed
        Cancelled
        Hidden
        budget_units (Required) Can be one of the following:
        Hours
        Days
        Monetary
        normal_billing_rate
        hours_per_day
        uses_project_invoice_sequence
        currency (Required)
        billing_period
     */
    
    /**
     * API METHODS
     */
    
    /**
     * BANKING
     */
    
    /**
     * Function to get bank accounts
     */
    
    public function get_bank_accounts($page = 1) {
        
        $url = $this->base_url.'/bank_accounts';
        
        $response = $this->client->fetch(
            $url, //API path
            array(
                'view' => 'standard_bank_accounts',
                'page' => $page
            ), //request parameters
            OAuth2\Client::HTTP_METHOD_GET, //GET, PUT, POST, DELETE
            array('User-Agent' => 'Example app') //API requires UA header
        );
        
        return $response;
    }
    
    /**
     * Function to get contacts 
     */
    
    public function get_contacts() {
        
        $url = $this->base_url.'/contacts';
        
        $response = $this->client->fetch(
            $url, //API path
            array(
                'view' => 'all',
                'per_page' => '100'
            ), //request parameters
            OAuth2\Client::HTTP_METHOD_GET, //GET, PUT, POST, DELETE
            array('User-Agent' => 'Example app') //API requires UA header
        );
        
        return $response;
    }
    
    /**
     * Function to get a contact by name
     */
    
    public function get_contact_by_email($email) {
        
        $contacts = $this->get_contacts();
        
        foreach($contacts['result']['contacts'] as $contact) {
            
            if($contact['email'] == $email) {
                
                return $contact;
            }
        }
        
        return false;
    }
    
    /**
     * Function to create contact
     */
    
    public function create_contact($props) {
        
        $url = $this->base_url.'/contacts';
        
        $response = $this->client->fetch(
            $url, //API path
            json_encode($props),
            OAuth2\Client::HTTP_METHOD_POST, //GET, PUT, POST, DELETE
            array(
                'User-Agent' => $_SERVER['HTTP_USER_AGENT'], //API requires UA header
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            )
        );
        
        return $response;
    }
    
    /**
     * Function to get bank accounts
     */
    
    public function create_estimate($props) {
        
        $url = $this->base_url.'/estimates';
        
        $response = $this->client->fetch(
            $url, //API path
            json_encode($props),
            OAuth2\Client::HTTP_METHOD_POST, //GET, PUT, POST, DELETE
            array(
                'User-Agent' => $_SERVER['HTTP_USER_AGENT'], //API requires UA header
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            )
        );
        
        return $response;
    }
    
    /**
     * Function to get bank accounts
     */
    
    public function create_invoice($props) {
        
        $url = $this->base_url.'/invoices';
        
        $response = $this->client->fetch(
            $url, //API path
            json_encode($props),
            OAuth2\Client::HTTP_METHOD_POST, //GET, PUT, POST, DELETE
            array(
                'User-Agent' => $_SERVER['HTTP_USER_AGENT'], //API requires UA header
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            )
        );
        
        return $response;
    }
    
    /**
     * Function to get bank accounts
     */
    
    public function create_project($props) {
        
        $url = $this->base_url.'/projects';
        
        $response = $this->client->fetch(
            $url, //API path
            json_encode($props),
            OAuth2\Client::HTTP_METHOD_POST, //GET, PUT, POST, DELETE
            array(
                'User-Agent' => $_SERVER['HTTP_USER_AGENT'], //API requires UA header
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            )
        );
        
        return $response;
    }
    
}