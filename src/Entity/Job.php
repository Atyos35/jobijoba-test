<?php namespace App\Entity; 

class Job { 
    public function __construct( 
        private string $title = '', 
        private string $description = '', 
        private string $location = '', 
        private string $contractType = '', 
        private string $company = '' ) {} 
        
    public function getTitle(): string { 
        return $this->title; 
    } 
    
    public function getDescription(): string { 
        return $this->description; 
    } 
    
    public function getLocation(): string { 
        return $this->location; 
    } 
    
    public function getContractType(): string { 
        return $this->contractType; 
    } 
    
    public function getCompany(): string { 
        return $this->company; 
    } 
}