<?php

/**
 * Ronin API Class
 */

class API_Ronin{
    
    /**
     * DEFINITIONS
    Get All Clients
    GET /clients
    {
      "page_count": 7
      "page_size": 10
      "total_count": 61
      "clients": [
        {
          "number": "101"
          "name": "ACME Corp"
          "city": "New York"
          "address": "123 Main St."
          "zip": "12345"
          "country": "US"
          "id": 2123
          "invoice_extra_fields": null
          "website": "http://www.example.com"
          "address_2": "Suite 101"
          "description": "A sample API Client"
          "state": "NY"
        },
        ...
      ]
    }
     */
    
    /**
     * CONTACTS
        Get All Contacts
        GET /contacts
        {
          "page_count": 1,
          "page_size": 10,
          "total_count": 8,
          "contacts": [
            {
              "name": "John Doe"
              "ext": "1234"
              "title": "Product Manager"
              "id": 495
              "client_id": 117
              "mobile": "123-456-7891"
              "phone": "123-456-7890"
              "avatar": "default_avatars/avatars/missing_original.jpg"
              "email": "jdoe@example.com"
            },
            ...
          ]
        }
     */
    
    /**
     * ESTIMATES
        Get All Estimates
        GET /estimates
        {
          "page_count": 3
          "page_size": 30
          "total_count": 61
          "estimates": [
            {
              "tax": 5
              "number": "184"
              "total_cost": 129.15
              "subtotal": 123
              "title": ""
              "tax_label": "Sales Tax"
              "compound_tax": true
              "id": 110
              "date": "2011/07/26"
              "client_id": 4
              "note": ""
              "estimate_items": [
                {
                  "price": 123
                  "title": "Estimate Item"
                  "quantity": 1
                  "taxable": true
                  ""id"": 184
                },
                ...
              ]
              "tax2_label": "Secondary Tax"
              "summary": null
              "tax2": 0
              "status": 0
              "currency_code": "EUR"
            },
            ...
          ]
        }
     */
    
    /**
     * EXPENSES
        GET /expenses
        By default, expenses are retrieved for the current fiscal year, as defined by the fiscal year start in Account Settings.
        {
          "total_count": 6
          "from_date": "2011/01/01",
          "to_date": "2011/12/31",
          "expenses": [
            {
                "invoice_item_id": null
                "incurred_on": "2011/03/02"
                "project_id": null
                "invoice_id": null
                "amount": 500
                "id": 3
                "user_id": 1
                "client_id": 4
                "description": "Expense Description!"
            },
            ...
          ]
        }
     */
    
    /**
     * INVOICES
        Get All Invoices
        GET /invoices
        {
          "page_count": 3
          "page_size": 30
          "total_count": 61
          "invoices": [
            {
              "tax": 5
              "number": "184"
              "total_cost": 129.15
              "balance": 129.15
              "subtotal": 123
              "title": ""
              "tax_label": "Sales Tax"
              "compound_tax": true
              "id": 110
              "date": "2011/07/26"
              "client_id": 4
              "note": ""
              "due_date": "2011/08/25"
              "invoice_items": [
                {
                  "price": 123
                  "title": "Invoice Item"
                  "quantity": 1
                  "taxable": true
                  "id": 184
                },
                ...
              ]
              "tax2_label": "Secondary Tax"
              "summary": null
              "total_payments": 0
              "tax2": 0
              "status": 0
              "po": ""
              "currency_code": "EUR"
              "payments": [
                {
                  "received_on": "2011/07/29"
                  "amount": 10
                  "id": 2053932801
                  "note": "Paid by check"
                }
              ]
            },
            ...
          ]
        }
     */
    
    /**
     * HOURS
        Get Hours For Project
        GET /projects/:project_id/hours
        {
          "hours": [
            {
              "title": "Redesign Work"
              "date": "2011/07/29"
              "project_id": 1
              "id": 1
              "rate": 75
              "user_id": 2
              "cost": 600
              "hours": 8
              "description": "Logged Description of Work"
              "billed?": false
            },
            ...
          ]
        }
     */
    
    /**
     * PROJECTS
        Get All Projects
        GET /projects
        {
          "page_count": 4
          "page_size": 20
          "total_count": 61
          "projects": [
            {
                "number": "PROJ101"
                "name": "Project Name"
                "budget_type": 0
                "client": {
                  "number": "101"
                  "name": "ACME Corp"
                  "city": "New York"
                  "address": "123 Main St."
                  "zip": "12345"
                  "country": "US"
                  "id": 2123
                  "invoice_extra_fields": null
                  "website": "http://www.example.com"
                  "address_2": "Suite 101"
                  "description": "A sample API Client"
                  "state": "NY"
                }
                "project_type": 0
                "worked_hours": 87
                "rate": 125
                "unbilled_hours": 8.5
                "id": 24
                "cost": 11675
                "description": "Project description"
                "status": 1
                "end_date": "2011/04/06"
                "currency_code": "USD"
                "budget": 15000
            },
            ...
          ]
        }
     */
    
    /**
     * API METHODS
     */
    
    /**
     * Function to get file contents using cURL
     */
    
    protected function curl_get_file_contents($url) {

        $ch = curl_init($url);
        
        curl_setopt_array(
            $ch,
            array(
                //CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                CURLOPT_USERPWD => $this->token.':'.$this->token
            )
        );
        
        //WARNING: this would prevent curl from detecting a 'man in the middle' attack
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        
        $contents = curl_exec($ch);
        
        $err = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if($err != 200){
            
            //die(var_dump($err));
            return false;
            
        }        
        
        curl_close($ch);
        
        if ($contents)
            return $contents;
        else
            return false;
    }
    
    /**
     * CLIENTS
     */
    
    #Function to get clients
    
    public function get_clients($page = 1) {
        
        $url = $this->domain.'clients.json?page='.$page;
        
        return json_decode($this->curl_get_file_contents($url));
    }
    
    #Function to get client from client_id
    
    public function get_client($client_id) {
        
        $url = $this->domain.'clients/'.$client_id.'.json';
        
        return json_decode($this->curl_get_file_contents($url));
    }
    
    #Function to get contact associated with client from client_id
    
    public function get_client_contact($client_id) {
        
        $url = $this->domain.'clients/'.$client_id.'/contacts.json';
        
        $decoded = json_decode($this->curl_get_file_contents($url));
        
        $contacts = $decoded->contacts;
        
        return $contacts[0];
    }
    
    #Function to create a new client
    //Not yet implemented
    
    #Function to update a client
    //Not yet implemented
    
    #Function to delete a client
    //Not yet implemented
    
    /**
     * CONTACTS
     */
    
    #Function to get contacts
    
    public function get_contacts($page = 1, $page_size = 100) {
        
        $url = $this->domain.'contacts.json?page='.$page.'&page_size='.$page_size;
        
        return json_decode($this->curl_get_file_contents($url));
    }
    
    public function get_contact($client_id) {
        
        $url = $this->domain.'clients.json';
        
        return json_decode($this->curl_get_file_contents($url));
    }
    
    #Function to create a new contact
    //Not yet implemented
    
    #Function to update a contact
    //Not yet implemented
    
    #Function to delete a contact
    //Not yet implemented
    
    /**
     * ESTIMATES
     */
    
    #Function to get all estimates
    
    public function get_estimates($page = 1) {
        
        $url = $this->domain.'estimates.json?page='.$page;
        
        return json_decode($this->curl_get_file_contents($url));
    }
    
    #Function to get an estimate
    //Not yet implemented
    
    /**
     * EXPENSES
     */
    
    #Function to get expenses
    
    public function get_expenses() {
        
        $url = $this->domain.'expenses.json';
        
        return json_decode($this->curl_get_file_contents($url));
    }
    
    /**
     * INVOICES
     */
    
    #Function to get invoices
    
    public function get_invoices($page = 1) {
        
        $url = $this->domain.'invoices.json?page='.$page;
        
        return json_decode($this->curl_get_file_contents($url));
    }
    
    /**
     * HOURS
     */
    
    #Function to get hours logged
    
    public function get_hours() {
        
        $url = $this->domain.'hours.json';
        
        return json_decode($this->curl_get_file_contents($url));
    }
    
    /**
     * PROJECTS
     */
    
    #Function to get projects
    #Projects in Ronin have many sub-resources,
    #but only the time-tracking component is currently available through the API.
    
    public function get_projects() {
        
        $url = $this->domain.'projects.json';
        
        return json_decode($this->curl_get_file_contents($url));
    }
}