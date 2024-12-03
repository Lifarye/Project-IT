<?php

if (isset($_POST['searchQuery'])) {

    $query = strtolower(trim($_POST['searchQuery']));

    $validCategories = ['laptops', 'computers', 'smartphones', 'accessories'];

    if (in_array($query, $validCategories)) {

        switch ($query) {
            case 'laptops':
                header("Location: laptops.php");
                break;
            case 'computers':
                header("Location: computers.php");
                break;
            case 'smartphones':
                header("Location: smartphones.php");
                break;
            case 'accessories':
                header("Location: accessories.php");
                break;
            default:
                echo "<script>alert('Invalid category.');</script>";
                break;
        }
        exit(); 
    } else {
        
        echo "<script>alert('Product category not found. Please enter one of the following: Laptops, Computers, Smartphones, or Accessories.');</script>";
    }
} else {
    
    echo "<script>alert('No search query provided.');</script>";
}

?>
