<?php
 class Movies {
    private $ticketPrice;
    private $fullTicketPrice = 50; // Base ticket price
    
    public function setTicketPrice($age) {        
        if ($age <= 5) {
            $this->ticketPrice = 0; 
        } elseif ($age <= 17) {
            $this->ticketPrice = $this->fullTicketPrice / 2; 
        } elseif ($age <= 55) {
            $this->ticketPrice = $this->fullTicketPrice;
        } else {
            $this->ticketPrice = $this->fullTicketPrice - 10;
        }
    }
    public function getTicketPrice() {
        return $this->ticketPrice;
    }
}
// This checks to see if the Movies class exists
if (!class_exists('Movies')) {
    echo "Movies class is not defined!";
    exit();
}
//This takes the value from the html text box and assigns it to $age
$movies = new Movies();
if (isset($_GET['age'])) {
    $age = intval($_GET['age']);
    $movies->setTicketPrice($age);
    $ticketPrice = $movies->getTicketPrice();
    echo "Your ticket price is: R" . number_format($ticketPrice, 2);
}
?>
