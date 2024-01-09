<?php
session_start();

// if (!isset($_COOKIE['dealer_id'])) {
//     header('Location: ../index.html');
//     exit();  
// }

include "../connection.php";
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;




class Dealer {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function register($formData) {
        $first_name = $formData['firstname'];
        $last_name = $formData['lastname'];
        $gender = $formData['gender'];
        $email = $formData['email'];
        $phone = $formData['phone'];
        $password = trim($formData['password']);
        $cpassword = $formData['cpassword'];
        $companyName = $formData['companyName'];
        $address = $formData['address'];
        $city = $formData['City'];
        $postalcode = $formData['postalcode'];
        $country = $formData['country'];
    
        if (
            !empty($first_name) &&
            !empty($last_name) &&
            !empty($email) &&
            !empty($phone) &&
            !empty($password) &&
            !empty($cpassword) &&
            !empty($companyName) &&
            !empty($address) &&
            !empty($city) &&
            !empty($postalcode) &&
            !empty($country)
        ) {
            // Check if the email already exists using a prepared statement
            $email = $this->con->real_escape_string($email);
            $query = "SELECT email FROM dealer_register WHERE email = ?";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
    
            if ($stmt->num_rows > 0) {
                http_response_code(400);
                echo json_encode("email-exist");
            } else {
                $hashedPassword = password_hash($password,PASSWORD_DEFAULT);
    
                // Use prepared statement to insert data safely
                $query = "INSERT INTO dealer_register (firstname, lastname, gender, email, phone, password, confirmpassword, company_name, address, city, postcode, country, status, created_at)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'for-approval', now())";
    
                $stmt = $this->con->prepare($query);
                $stmt->bind_param("ssssssssssss", $first_name, $last_name, $gender, $email, $phone, $hashedPassword, $cpassword, $companyName, $address, $city, $postalcode, $country);
    
                if ($stmt->execute()) {
                    http_response_code(200);
                    echo json_encode("Registration-successful");
                } else {
                    http_response_code(500);
                    echo json_encode("Registration failed");
                }
            }
        } else {
            http_response_code(400);
            echo "Please fill out all required fields";
        }
    }
    

    public function login($email, $password) {
        $login_email = trim($email);
        $login_password = trim($password);
    
        $query = "SELECT * FROM dealer_register WHERE email = ? AND status='Approved'";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("s", $login_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    
        if ($user) {
            $hashedPasswordFromDatabase = $user['password'];
    
            $verify = password_verify($login_password, $hashedPasswordFromDatabase);
    
            if ($verify) {

                $_SESSION['dealer_id'] = $user['dealer_id'];
                $dealer_id = $user['dealer_id'];
                $expiration_time = time() + (3 * 24 * 60 * 60);

                setcookie('dealer_id', $dealer_id, [
                    'expires' => $expiration_time,
                    'path' => '/',
                    'domain' => '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict', 
                ]);

                $response = array(
                    "status" => "login-success",
                    "dealer_id" => $dealer_id
                );
                echo json_encode($response);
            } else {
                http_response_code(400);
                echo "incorrect-password";
            }
        } else {
            http_response_code(404);
            echo "user-not-found";
        }
    }

    public function editProfileWithImage($dealer_details, $profileImage)
    {
        $dealerId = $_COOKIE['dealer_id'];
        $successMessage = "";
        $currentDateTime = new DateTime();
        $formattedDateTime = $currentDateTime->format('Ymd_His');

        $originalFileName = $profileImage['name'];
        $originalFileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $newFileName = $formattedDateTime . '.' . $originalFileExtension;

        $profileImagePath = 'profile_images/' . $newFileName;
        $tmpImagePath = $profileImage['tmp_name'];

        if (move_uploaded_file($tmpImagePath, $profileImagePath)) {
            $successMessage .= "Image_ok";
        } else {
            http_response_code(500);
            echo json_encode("Error moving image!");
            return;
        }

        $existingProfileStmt = $this->con->prepare("SELECT * FROM dealer_profile_image WHERE dealer_id = ?");
        $existingProfileStmt->bind_param('i', $dealerId);
        $existingProfileStmt->execute();
        $existingProfileResult = $existingProfileStmt->get_result();

        $dealer_profile = $newFileName;

        if ($existingProfileResult->num_rows > 0) {
            $updateStmt = $this->con->prepare("UPDATE dealer_profile_image SET profile_image = ? WHERE dealer_id = ?");
            $updateStmt->bind_param('si', $dealer_profile, $dealerId);
    
            if ($updateStmt->execute()) {
                $successMessage .= "Profile_ok";
            } else {
                http_response_code(500);
                echo json_encode("Error updating profile image: " . $updateStmt->error);
            }
    
            $updateStmt->close();
        } else {
            $insertStmt = $this->con->prepare("INSERT INTO dealer_profile_image (dealer_id, profile_image) VALUES (?, ?)");
            $insertStmt->bind_param('is', $dealerId, $dealer_profile);
    
            if ($insertStmt->execute()) {
                $successMessage .= "Profile_image_inserted_ok";
            } else {
                http_response_code(500);
                echo json_encode("Error inserting profile image: " . $insertStmt->error);
            }
    
            $insertStmt->close();
        }
    
        $updateDealerStmt = $this->con->prepare("UPDATE dealer_register SET firstname = ?, lastname = ?, phone = ?, address = ?, email = ? WHERE dealer_id = ?");
        $updateDealerStmt->bind_param('sssssi', $dealer_details['first_name'], $dealer_details['last_name'], $dealer_details['contact'], $dealer_details['address'], $dealer_details['email'], $dealerId);
    
        if ($updateDealerStmt->execute()) {
            $successMessage .= "Dealer_details_updated_successfully";
        } else {
            http_response_code(500);
            echo json_encode("Error updating dealer details: " . $updateDealerStmt->error);
        }
    
        $updateDealerStmt->close();
        $existingProfileStmt->close();
        $this->con->close();
    
        http_response_code(200);
        echo json_encode("success");
    }

    public function editProfileWithoutImage($dealer_details)
    {
        $dealerId = $_COOKIE['dealer_id'];
        $successMessage = "";

        $existingProfileStmt = $this->con->prepare("SELECT * FROM dealer_profile_image WHERE dealer_id = ?");
        $existingProfileStmt->bind_param('i', $dealerId);
        $existingProfileStmt->execute();
        $existingProfileResult = $existingProfileStmt->get_result();

        if ($existingProfileResult->num_rows > 0) {
            $updateDealerStmt = $this->con->prepare("UPDATE dealer_register SET firstname = ?, lastname = ?, phone = ?, address = ?, email = ? WHERE dealer_id = ?");
            $updateDealerStmt->bind_param('sssssi', $dealer_details['first_name'], $dealer_details['last_name'], $dealer_details['contact'], $dealer_details['address'], $dealer_details['email'], $dealerId);
        
            if ($updateDealerStmt->execute()) {
                $successMessage .= "Dealer_details_updated_successfully";
            } else {
                http_response_code(500);
                echo json_encode("Error updating dealer details: " . $updateDealerStmt->error);
            }
        } else {
            $default_image = "default-profile.svg";
            $insertStmt = $this->con->prepare("INSERT INTO dealer_profile_image (dealer_id, profile_image) VALUES (?, ?)");
            $insertStmt->bind_param('is', $dealerId, $default_image);
    
            if ($insertStmt->execute()) {
                $successMessage .= "Profile_image_inserted_ok";
            } else {
                http_response_code(500);
                echo json_encode("Error inserting profile image: " . $insertStmt->error);
            }
    
            $insertStmt->close();
            $updateDealerStmt = $this->con->prepare("UPDATE dealer_register SET firstname = ?, lastname = ?, phone = ?, address = ?, email = ? WHERE dealer_id = ?");
            $updateDealerStmt->bind_param('sssssi', $dealer_details['first_name'], $dealer_details['last_name'], $dealer_details['contact'], $dealer_details['address'], $dealer_details['email'], $dealerId);
        
            if ($updateDealerStmt->execute()) {
                $successMessage .= "Dealer_details_updated_successfully";
            } else {
                http_response_code(500);
                echo json_encode("Error updating dealer details: " . $updateDealerStmt->error);
            }
        }
    
        $updateDealerStmt->close();
        $existingProfileStmt->close();
        $this->con->close();
    
        http_response_code(200);
        echo json_encode("success");
    }

    public function getDealerProfile()
    {
        $dealer_id = $_COOKIE['dealer_id'];

        $stmt = $this->con->prepare("SELECT profile_image FROM dealer_profile_image WHERE dealer_id = ?");
        $stmt->bind_param('i', $dealer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $product_image = $row['profile_image'];

            $stmt->close();
            http_response_code(200);
            echo json_encode($product_image);
        } else {

            $stmt->close();
            http_response_code(404);
            echo json_encode('profile_image_not_found');
        }
    }

    public function fetchAllDealers() {
        $fetch_all_dealers = "SELECT * FROM dealer_register WHERE status='Approved'";
            
        $result = mysqli_query($this->con, $fetch_all_dealers);
    
        if ($result) {
            $dealers = mysqli_fetch_all($result, MYSQLI_ASSOC);
            
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode($dealers);
        } else {
            http_response_code(500); 
            echo "Error fetching dealers: " . mysqli_error($this->con);
        }
    }

    public function fetchDealerById() {
        $get_dealer_details_sql = "SELECT dealer_id,firstname,lastname,gender,email,phone,company_name,address,city,postcode,country FROM dealer_register WHERE dealer_id = ?";
        
        $stmt = $this->con->prepare($get_dealer_details_sql);
    
        if (!$stmt) {
            http_response_code(500);
            echo json_encode("Prepare statement error: " . $this->con->error);
            return;
        }
    
        $stmt->bind_param('i', $_COOKIE['dealer_id']);
        
        $stmt->execute();
    
        if ($stmt->error) {
            http_response_code(500);
            echo json_encode("Execute statement error: " . $stmt->error);
            return;
        }
    
        $result = $stmt->get_result();
    
        $dealerDetails = $result->fetch_all(MYSQLI_ASSOC);
    
        $stmt->close();
    
        http_response_code(200);
        echo json_encode($dealerDetails);
    }

    public function deleteDealer($dealer_id) {
        $delete_dealer_sql = "DELETE FROM dealer_register WHERE dealer_id = ?";
        
        $stmt = $this->con->prepare($delete_dealer_sql);
        
        if ($stmt) {
            $stmt->bind_param('i', $dealer_id);
            
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                http_response_code(200);
                echo json_encode('Dealer_deleted_successfully');

            } else {
                http_response_code(404);
                echo json_encode('Dealer_not_found');
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode('Remove_Dealer_Failed');
        }
    }

    public function addtoCart(){
        if (isset($_POST['dealer_id']) && $_POST['dealer_id']) {
            if (isset($_POST['productId'])) {
                $product_id = $_POST['productId'];
        
                if (!empty($_POST['cartValues'])) {
                    $cartValues = $_POST['cartValues'];
        
                    foreach ($cartValues as $cartItem) {
                        $stock = $cartItem['dataQuantity'];
                        $color_product_id = $cartItem['dataColorProductId'];
                        $product_size = $cartItem['dataSize'];
                        $cart_quantity = $cartItem['inputValue'];

        
                        // Query to retrieve product_name and product_color from color_product
                        $get_product_details = "SELECT product_name, product_color FROM color_product WHERE product_id=? AND color_product_id=?";
                        $stmt_product_details = $this->con->prepare($get_product_details);
                        $stmt_product_details->bind_param('ii', $product_id, $color_product_id);
                        $stmt_product_details->execute();
                        $stmt_product_details->bind_result($product_name, $product_color);
                        $stmt_product_details->fetch();
                        $stmt_product_details->close();
        
                        // Query to retrieve product_price and product_msrp_price from color_product_details
                        $get_details_for_this_product_sql = "SELECT product_price, product_msrp_price FROM color_product_details WHERE product_id=? AND color_product_id=? AND product_color=? AND product_size=?";
                        $stmt_details_for_this_product = $this->con->prepare($get_details_for_this_product_sql);
                        $stmt_details_for_this_product->bind_param('iiss', $product_id, $color_product_id, $product_color, $product_size);
                        $stmt_details_for_this_product->execute();
                        $stmt_details_for_this_product->bind_result($product_price, $product_msrp_price);
                        $stmt_details_for_this_product->fetch();
                        $stmt_details_for_this_product->close();

                        // Get Product Image For Cart
                        $get_image_for_this_product_sql = "SELECT product_image FROM color_product_images WHERE product_id=? AND color_product_id=? LIMIT 1";
                        $stmt_details_for_this_product = $this->con->prepare($get_image_for_this_product_sql);
                        $stmt_details_for_this_product->bind_param('ii', $product_id, $color_product_id);
                        $stmt_details_for_this_product->execute();
                        $stmt_details_for_this_product->bind_result($product_image);
                        $stmt_details_for_this_product->fetch();
                        $stmt_details_for_this_product->close();

                        $check_item_already_exist_in_cart_sql = "SELECT cart_id, cart_quantity FROM dealer_cart WHERE dealer_id=? AND product_id=? AND color_product_id=? AND product_name=? AND product_color=? AND product_size=?";
                        $stmt_check_existing = $this->con->prepare($check_item_already_exist_in_cart_sql);
                        $stmt_check_existing->bind_param('iiisss', $_SESSION['dealer_id'], $product_id, $color_product_id, $product_name, $product_color, $product_size);
                        $stmt_check_existing->execute();
                        $stmt_check_existing->store_result();

                        if ($stmt_check_existing->num_rows > 0) {
                            $stmt_check_existing->bind_result($cart_id, $existing_cart_quantity);
                            $stmt_check_existing->fetch();
                            $stmt_check_existing->close();

                            $updated_cart_quantity = $existing_cart_quantity + $cart_quantity;

                            // Perform the update
                            $update_cart_sql = "UPDATE dealer_cart SET cart_quantity=?, total_amount=? WHERE cart_id=?";
                            $stmt_update_cart = $this->con->prepare($update_cart_sql);
                            $total_amount = $updated_cart_quantity * $product_price;
                            $stmt_update_cart->bind_param('idi', $updated_cart_quantity, $total_amount, $cart_id);
                            $stmt_update_cart->execute();
                            $stmt_update_cart->close();
                        } else {
                            $stmt_check_existing->close();

                            $total_amount = $cart_quantity * $product_price;

                            $cart_insert_sql = "INSERT INTO dealer_cart (dealer_id, product_id, color_product_id, product_image, product_name, product_color, product_size, product_price, product_msrp_price, product_quantity, cart_quantity,total_amount) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                            $stmt_cart_insert = $this->con->prepare($cart_insert_sql);
                            $stmt_cart_insert->bind_param('iiissssiiiii', $_COOKIE['dealer_id'], $product_id, $color_product_id, $product_image, $product_name, $product_color, $product_size, $product_price, $product_msrp_price, $stock, $cart_quantity, $total_amount);
                            $stmt_cart_insert->execute();
                            $stmt_cart_insert->close();
                        }
                    }
                    http_response_code(200);
                    echo json_encode("Cart_updated_successfully");
                }  
                else {
                    http_response_code(404);
                    echo json_encode("Cart_values_are_empty");
                }
            } else {
                http_response_code(404);
                echo json_encode("Product Id Not Found");
            }
        } else {
            http_response_code(404);
            echo json_encode("Dealer Id Not Found or does not match the session");
        }
    }

    public function getCartProducts() {
        if (isset($_GET['dealer_id']) && isset($_COOKIE['dealer_id'])) {
            $dealer_id = $_COOKIE['dealer_id'];
            $get_all_cart_items_sql = "SELECT cart_id,dealer_id,product_id,color_product_id,product_color,product_image,product_name,product_size, product_price, product_quantity, cart_quantity, total_amount FROM dealer_cart WHERE dealer_id=?";
            $stmt = $this->con->prepare($get_all_cart_items_sql);
            $stmt->bind_param('i', $dealer_id);
            $stmt->execute();
            $result = $stmt->get_result();
        
            $cartItems = array();
        
            while ($row = $result->fetch_assoc()) {
                $cartItems[] = array(
                    'cart_id' => $row['cart_id'],
                    'dealer_id' => $row['dealer_id'],
                    'product_id' => $row['product_id'],
                    'color_product_id' => $row['color_product_id'],
                    'product_color' => $row['product_color'],
                    'product_image' => $row['product_image'],
                    'product_name' => $row['product_name'],
                    'product_size' => $row['product_size'],
                    'product_price' => $row['product_price'],
                    'product_quantity' => $row['product_quantity'],
                    'cart_quantity' => $row['cart_quantity'],
                    'total_amount' => $row['total_amount']
                );
            }
        
            $stmt->close();
            $result->free_result();
        
            header('Content-Type: application/json');
            echo json_encode($cartItems);
        }
        else{
            http_response_code(404);
            echo json_encode("Dealer Id Not Found");
        }
    }

    public function removeCartItem($cart_id, $dealer_id){
        $remove_cart_item_sql = "DELETE FROM dealer_cart WHERE cart_id=? AND dealer_id=?";
        
        $stmt = $this->con->prepare($remove_cart_item_sql);
        $stmt->bind_param('ii', $cart_id, $dealer_id);
        
        if ($stmt->execute()) {
            // Item removed successfully
            $stmt->close();
            http_response_code(200);
            echo json_encode("Item-Removed");
        } else {
            // Error in removing item
            $stmt->close();
            http_response_code(500);
            echo json_encode("Cart Remove Query Failed");
        }
    }

    public function updateCart($cart_id, $total_amount, $quantity) {

        $update_cart_sql = "UPDATE dealer_cart SET cart_quantity=?, total_amount=? WHERE cart_id=?";
        $stmt = $this->con->prepare($update_cart_sql);
    
        $stmt->bind_param('idi', $quantity, $total_amount, $cart_id);
    
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode("cart_updated");
        } else {
            http_response_code(200);
            echo json_encode("No changes to update");
        }
    
        $stmt->close();
    }

    public function cartTotal() {
        $get_cart_total_sql = "SELECT total_amount FROM dealer_cart WHERE dealer_id = ?";
        
        $stmt = $this->con->prepare($get_cart_total_sql);
    
        if (!$stmt) {
            http_response_code(500);
            echo json_encode("Prepare statement error: " . $this->con->error);
            return;
        }
    
        $stmt->bind_param('i', $_SESSION['dealer_id']);
        
        $stmt->execute();
    
        if ($stmt->error) {
            http_response_code(500);
            echo json_encode("Execute statement error: " . $stmt->error);
            return;
        }
    
        $cartTotal = 0;
    
        $stmt->bind_result($currentTotal);
    
        while ($stmt->fetch()) {
            $cartTotal += $currentTotal;
        }
    
        $stmt->close();
    
        http_response_code(200);
        echo json_encode($cartTotal);
    }

    public function viewOrderDetails() {
        
        $dealer_id = $_COOKIE['dealer_id'];
    
        $orderDetails = $this->getOrderDetailsByDealerId($dealer_id);
    
        echo json_encode($orderDetails);
        // print_r($orderDetails);
    }
    
    private function getOrderDetailsByDealerId($dealer_id) {
        $query = "
            SELECT po1.*
            FROM product_orders po1
            JOIN (
                SELECT unique_orderid, MAX(created_at) as max_created_at
                FROM product_orders
                WHERE dealer_id = ?
                GROUP BY unique_orderid
            ) po2 ON po1.unique_orderid = po2.unique_orderid AND po1.created_at = po2.max_created_at
            WHERE po1.dealer_id = ?
            ORDER BY po1.created_at DESC
        ";
    
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("ii", $dealer_id, $dealer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orderDetails = [];
        
        while ($row = $result->fetch_assoc()) {
            $existingKey = array_search($row['unique_orderid'], array_column($orderDetails, 'unique_orderid'));
    
            if ($existingKey === false) {
                $orderDetails[] = $row;
            }
        }
    
        $stmt->close();
    
        return $orderDetails;
    }

    public function viewOrderDetailsByOrderid($unique_orderid) {
        $query = "SELECT * FROM product_orders WHERE unique_orderid = ?";
        $stmt = mysqli_prepare($this->con, $query);
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $unique_orderid);
    
            $result = mysqli_stmt_execute($stmt);
    
            if ($result) {
                $result_set = mysqli_stmt_get_result($stmt);
    
                $data = [];
                while ($row = mysqli_fetch_assoc($result_set)) {
                    $data[] = $row;
                }
    
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode($data);
            } else {
                http_response_code(500);
                echo json_encode('Execution failed');
            }
    
            mysqli_stmt_close($stmt);
        } else {
            http_response_code(500);
            echo json_encode('Statement preparation failed');
        }
    
        mysqli_close($this->con);
    }

    public function getUpdatedQuantityAlert(){
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
    
        $query = "SELECT su.*, cp.product_name, cp.product_id 
                  FROM stock_update su
                  JOIN color_product cp ON su.color_product_id = cp.color_product_id
                  WHERE su.created_at >= ?";
    
        $stmt = $this->con->prepare($query);
    
        if ($stmt === false) {
            http_response_code(500);
            echo json_encode("Error preparing SQL statement: " . $this->con->error);
            exit();
        }
    
        $stmt->bind_param("s", $sevenDaysAgo);
    
        if ($stmt->execute()) {
            $result = $stmt->get_result();
    
            $organizedData = array();
    
            while ($row = $result->fetch_assoc()) {
                $date = $row['created_at'];
                unset($row['created_at']); 
    
                if (!isset($organizedData[$date])) {
                    $organizedData[$date] = array();
                }
    
                $organizedData[$date][] = $row;
            }
    
            $stmt->close();
    
            http_response_code(200);
            echo json_encode($organizedData);
        } else {
            $stmt->close();
            http_response_code(500);
            echo json_encode("Error executing SQL statement: " . $this->con->error);
            exit();
        }
    }

    public function getUpdatedProduct(){
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));

        $query = "SELECT su.*, p.product_name
          FROM new_product_updates su
          JOIN product p ON su.product_id = p.product_id
          WHERE su.created_at >= ?";

        $stmt = $this->con->prepare($query);

        if ($stmt === false) {
            http_response_code(500);
            echo json_encode("Error preparing SQL statement: " . $this->con->error);
            exit();
        }

        $stmt->bind_param("s", $sevenDaysAgo);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            $organizedData = array();

            while ($row = $result->fetch_assoc()) {
                $date = $row['created_at'];
                unset($row['created_at']);

                if (!isset($organizedData[$date])) {
                    $organizedData[$date] = array();
                }

                $organizedData[$date][] = $row;
            }

            $stmt->close();

            http_response_code(200);
            echo json_encode($organizedData);
        } else {
            $stmt->close();
            http_response_code(500);
            echo json_encode("Error executing SQL statement: " . $this->con->error);
            exit();
        }

    }
    
}

class Order{
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    
    private function sendOrderConfirmationEmail($order_id, $dealer_id, $dealer_email, $cart_products) {
        // echo json_encode($dealer_id);
        // echo json_encode($order_id);
        $mail = new PHPMailer(true);

        try {
            // Configure SMTP settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jtestpurpose@gmail.com';
            $mail->Password   = 'dixm snph blsl iqpc';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            // $mail->SMTPDebug = 2;

            // Recipients
            $mail->setFrom('jtestpurpose@gmail.com', 'Equipride Dealer');
            $mail->addAddress($dealer_email, 'Recipient Name');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Order Confirmation';
            $emailBody = '
            <html>
                <head>
                    <style>
                        /* Add your styles here */
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f4f4f4;
                        }
                        .order-details {
                            border-collapse: collapse;
                            width: 100%;
                            margin-top: 20px;
                        }
                        .order-details th, .order-details td {
                            border: 1px solid #ddd;
                            padding: 8px;
                            text-align: left;
                        }
                        .order-details th {
                            background-color: #f2f2f2;
                        }
                        .thank-you-icon {
                            font-size: 24px;
                            color: #008000;
                        }
                    </style>
                </head>
                <body>
                    <p class="thank-you-icon">&#128077; Thank you for your order!</p>
                    <p>Here are the order details:</p>
                    <table class="order-details">
                        <tr>
                            <th>Product Name</th>
                            <th>Colour</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                        </tr>';

                        $totalAmount = 0;
                        foreach ($cart_products as $product) {
                            
                            $emailBody .= "
                                <tr>
                                    <td>{$product['product_name']}</td>
                                    <td>{$product['product_color']}</td>
                                    <td>{$product['product_size']}</td>
                                    <td>£ {$product['product_price']}</td>
                                    <td>{$product['cart_quantity']}</td>
                                </tr>";

                            $totalAmount += $product['total_amount'];
                        }

                        $emailBody .= "
                                    </table>
                                    <p>Total Amount: £ $totalAmount</p>
                                </body>
                            </html>";

                        $mail->Body = $emailBody;

                $mail->send();

                // Update Product Quantity

                foreach ($cart_products as $product) {
                    $color_product_id = $product['color_product_id'];
                    $cart_quantity = $product['cart_quantity'];
                    $product_size = $product['product_size'];

                    $this->updateProductQuantity($color_product_id, $cart_quantity, $product_size);
                }

                // Delete Items From Cart

                $this->deleteDealerCart($dealer_id);

            http_response_code(200);
            echo json_encode('Order_confirmation_email_has_been_sent');
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    private function sendReOrderConfirmationEmail($order_id, $dealer_id, $dealer_email, $orderDetails) {
        // echo json_encode($dealer_id);
        // echo json_encode($orderDetails);
        $mail = new PHPMailer(true);

        try {
            // Configure SMTP settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jtestpurpose@gmail.com';
            $mail->Password   = 'dixm snph blsl iqpc';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;
            // $mail->SMTPDebug = 2;

            // Recipients
            $mail->setFrom('jtestpurpose@gmail.com', 'Equipride Dealer');
            $mail->addAddress($dealer_email, 'Recipient Name');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Order Confirmation';
            $emailBody = '
            <html>
                <head>
                    <style>
                        /* Add your styles here */
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f4f4f4;
                        }
                        .order-details {
                            border-collapse: collapse;
                            width: 100%;
                            margin-top: 20px;
                        }
                        .order-details th, .order-details td {
                            border: 1px solid #ddd;
                            padding: 8px;
                            text-align: left;
                        }
                        .order-details th {
                            background-color: #f2f2f2;
                        }
                        .thank-you-icon {
                            font-size: 24px;
                            color: #008000;
                        }
                    </style>
                </head>
                <body>
                    <p class="thank-you-icon">&#128077; Thank you for your order!</p>
                    <p>Here are the order details:</p>
                    <table class="order-details">
                        <tr>
                            <th>Product Name</th>
                            <th>Colour</th>
                            <th>Size</th>
                            <th>Price</th>
                            <th>Quantity</th>
                        </tr>';

                        $totalAmount = 0;
                        foreach ($orderDetails as $product) {
                            
                            $emailBody .= "
                                <tr>
                                    <td>{$product['product_name']}</td>
                                    <td>{$product['product_color']}</td>
                                    <td>{$product['product_size']}</td>
                                    <td>£ {$product['product_price']}</td>
                                    <td>{$product['order_quantity']}</td>
                                </tr>";

                            $totalAmount += $product['total_amount'];
                        }

                        $emailBody .= "
                                    </table>
                                    <p>Total Amount: £ $totalAmount</p>
                                </body>
                            </html>";

                        $mail->Body = $emailBody;

                $mail->send();

                // Update Product Quantity

                foreach ($orderDetails as $product) {
                    $color_product_id = $product['color_product_id'];
                    $cart_quantity = $product['order_quantity'];
                    $product_size = $product['product_size'];

                    $this->updateProductQuantity($color_product_id, $cart_quantity, $product_size);
                }

            http_response_code(200);
            echo json_encode('Order_confirmation_email_has_been_sent');
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    // private function updateProductQuantity($color_product_id, $cart_quantity, $product_size) {
    //     $update_sql = "UPDATE color_product_details SET product_quantity = product_quantity - ? WHERE color_product_id = ? AND product_size = ?";
    //     $stmt = $this->con->prepare($update_sql);
    //     $stmt->bind_param("iis", $cart_quantity, $color_product_id, $product_size);
    //     $stmt->execute();
    //     $stmt->close();
    // }

    private function updateProductQuantity($color_product_id, $cart_quantity, $product_size) {
        $currentQuantity = $this->getUpdatedProductQuantity($color_product_id, $product_size);
    
        if ($currentQuantity < $cart_quantity) {
            return;
        }
    
        $update_sql = "UPDATE color_product_details SET product_quantity = GREATEST(product_quantity - ?, 0) WHERE color_product_id = ? AND product_size = ?";
        $stmt = $this->con->prepare($update_sql);
        $stmt->bind_param("iis", $cart_quantity, $color_product_id, $product_size);
        $stmt->execute();
        $stmt->close();
    
        $product_quantity = $this->getUpdatedProductQuantity($color_product_id, $product_size);
    
        if ($product_quantity === 0) {
            $this->insertProductAlert($color_product_id, $cart_quantity, $product_size);
        }
    }
    
    private function getUpdatedProductQuantity($color_product_id, $product_size) {
        $select_sql = "SELECT product_quantity FROM color_product_details WHERE color_product_id = ? AND product_size = ?";
        $stmt = $this->con->prepare($select_sql);
        $stmt->bind_param("is", $color_product_id, $product_size);
        $stmt->execute();
        $stmt->bind_result($product_quantity);
        $stmt->fetch();
        $stmt->close();
    
        return $product_quantity;
    }
    
    private function insertProductAlert($color_product_id, $cart_quantity, $product_size) {
        $currentDateTime = new DateTime();
        $arriveDate = $currentDateTime->modify('+7 days')->format('Y-m-d');
    
        $insert_sql = "INSERT INTO product_alert (color_product_id, cart_quantity, product_size, arrive_date) VALUES (?, ?, ?, ?)";
        $stmt = $this->con->prepare($insert_sql);
        $stmt->bind_param("iiss", $color_product_id, $cart_quantity, $product_size, $arriveDate);
        $stmt->execute();
        $stmt->close();
    }

    private function deleteDealerCart($dealer_id) {
        $delete_sql = "DELETE FROM dealer_cart WHERE dealer_id = ?";
        $stmt = $this->con->prepare($delete_sql);
        $stmt->bind_param("i", $dealer_id);
        $stmt->execute();
        $stmt->close();
    }

    public function placeOrder(){
        if(isset($_POST['firstName']) && !empty($_POST['firstName'])){
            if(isset($_POST['lastName']) && !empty($_POST['lastName'])){
                if(isset($_POST['address']) && !empty($_POST['address'])){
                    if(isset($_POST['city']) && !empty($_POST['city'])){
                        if(isset($_POST['postalCode']) && !empty($_POST['postalCode'])){
                            if (isset($_POST['country']) && !empty($_POST['country'])) {
                                if (isset($_POST['email']) && !empty($_POST['email'])) {

                                    $dealer_id = $_COOKIE['dealer_id'];
                                    $dealer_email = $_POST['email'];
                                    
                                    // Get the latest order number from the product_orders table
                                    $get_latest_order_number_sql = "SELECT MAX(unique_orderid) AS max_order_number FROM product_orders";
                                    $latest_order_number_result = $this->con->query($get_latest_order_number_sql);
                                
                                    if ($latest_order_number_result && $latest_order_number_row = $latest_order_number_result->fetch_assoc()) {
                                        $order_number_string = $latest_order_number_row['max_order_number'];
                                    
                                        if (preg_match('/^([a-zA-Z_]+)(\d*)$/', $order_number_string, $matches)) {
                                            $prefix = $matches[1];
                                            $numeric_part = $matches[2] !== '' ? $matches[2] : '0';
                                        
                                            $new_numeric_part = str_pad($numeric_part + 1, strlen($numeric_part), '0', STR_PAD_LEFT);
                                        
                                            $new_order_id = $prefix . $new_numeric_part;
                                        } else {
                                            $new_order_id = 'OrderEqp_000001';
                                        }
                                    } else {
                                        $new_order_id = 'OrderEqp_000001';
                                    }
                                
                                    $order_status = "Completed";
                                    $insert_new_order_sql = "INSERT INTO product_orders (unique_orderid, dealer_id, product_id, color_product_id, product_name, product_color, product_size, product_price, order_quantity, total_amount,order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                    $stmt = $this->con->prepare($insert_new_order_sql);
                                
                                    // Get cart products
                                    $get_all_cart_products_sql = "SELECT * FROM dealer_cart WHERE dealer_id = $dealer_id";
                                    $result = $this->con->query($get_all_cart_products_sql);
                                
                                    $cart_products = array();
                                
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $cart_products[] = $row;
                                
                                            $stmt->bind_param("siiisssiiis", $new_order_id, $dealer_id, $row['product_id'], $row['color_product_id'], $row['product_name'], $row['product_color'], $row['product_size'], $row['product_price'], $row['cart_quantity'], $row['total_amount'],$order_status);
                                            $stmt->execute();
                                        }
                                
                                        http_response_code(200);
                                        $this->sendOrderConfirmationEmail($new_order_id, $dealer_id, $dealer_email, $cart_products);
                                        // echo json_encode("Ordered Successfully");
                                    } else {
                                        http_response_code(400);
                                        echo json_encode("No_products_found_in_the_cart");
                                    }
                                
                                    $stmt->close();
                                    $this->con->close();
                                } 
                            }
                            else{
                                http_response_code(404);
                                echo json_encode("Country Is Empty");  
                            }
                        }
                        else{
                            http_response_code(404);
                            echo json_encode("Postal Code Is Empty");
                        }
                    }
                    else{
                        http_response_code(404);
                        echo json_encode("City Is Empty");
                    }
                }
                else{
                    http_response_code(404);
                    echo json_encode("Last Name Is Empty");
                }
            }
            else{
                http_response_code(404);
                echo json_encode("Last Name Is Empty");
            }
        }
        else{
            http_response_code(404);
            echo json_encode("First Name Is Empty");
        }
    }

    public function reOrder($order_id) {
        $query = "SELECT * FROM product_orders WHERE unique_orderid = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("s", $order_id); 
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orderDetails = array();
        while ($row = $result->fetch_assoc()) {
            if ($row['order_quantity'] != 0) {
                $productQuantity = $this->getProductQuantity($row['product_id'], $row['color_product_id'], $row['product_color'], $row['product_size']);
        
                if ($productQuantity != 0 && $row['order_quantity'] <= $productQuantity) {
                    $row['product_quantity'] = $productQuantity;
                    $orderDetails[] = $row;
                }
                else{
                    http_response_code(400);
                    echo json_encode("quantity_exceeds");
                }
                // echo json_encode($productQuantity);
            }
        }
    
        // echo json_encode($orderDetails);
    
        $stmt->close();

        if (!empty($orderDetails)) {
            $this->placeOrderFromDetails($orderDetails);
        }
    }
    
    private function getProductQuantity($product_id, $color_product_id, $product_color, $product_size) {
        $query = "SELECT product_quantity FROM color_product_details WHERE product_id = ? AND color_product_id = ? AND product_color = ? AND product_size = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("isss", $product_id, $color_product_id, $product_color, $product_size);
        $stmt->execute();
        $stmt->bind_result($productQuantity);
        $stmt->fetch();
        $stmt->close();
    
        return $productQuantity;
    }

    private function placeOrderFromDetails($orderDetails) {
        $dealer_id = $_COOKIE['dealer_id'];
    
        $userDetails = $this->getUserDetails($dealer_id);
    
        $firstName = $userDetails['firstname'];
        $lastName = $userDetails['lastname'];
        $email = $userDetails['email'];
        $phone = $userDetails['phone'];
        $address = $userDetails['address'];
        $city = $userDetails['city'];
        $postcode = $userDetails['postcode'];
        $country = $userDetails['country'];
    
        $get_latest_order_number_sql = "SELECT MAX(unique_orderid) AS max_order_number FROM product_orders";
        $latest_order_number_result = $this->con->query($get_latest_order_number_sql);
    
        if ($latest_order_number_result && $latest_order_number_row = $latest_order_number_result->fetch_assoc()) {
            $order_number_string = $latest_order_number_row['max_order_number'];
    
            if (preg_match('/^([a-zA-Z_]+)(\d*)$/', $order_number_string, $matches)) {
                $prefix = $matches[1];
                $numeric_part = $matches[2] !== '' ? $matches[2] : '0';
    
                $new_numeric_part = str_pad($numeric_part + 1, strlen($numeric_part), '0', STR_PAD_LEFT);
    
                $new_order_id = $prefix . $new_numeric_part;
            } else {
                $new_order_id = 'OrderEqp_000001';
            }
        } else {
            $new_order_id = 'OrderEqp_000001';
        }
    
        $order_status = "Completed";
        $insert_new_order_sql = "INSERT INTO product_orders (unique_orderid, dealer_id, product_id, color_product_id, product_name, product_color, product_size, product_price, order_quantity, total_amount, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->con->prepare($insert_new_order_sql);
    
        foreach ($orderDetails as $product) {
            if ($product['order_quantity'] != 0) {
                $stmt->bind_param("siiisssiiis", $new_order_id, $dealer_id, $product['product_id'], $product['color_product_id'], $product['product_name'], $product['product_color'], $product['product_size'], $product['product_price'], $product['order_quantity'], $product['total_amount'], $order_status);
                $stmt->execute();
            }
        }
    
        $stmt->close();
    
        $emailSent = $this->sendReOrderConfirmationEmail($new_order_id, $dealer_id, $email, $orderDetails);
        if ($emailSent === "Order_confirmation_email_has_been_sent") {
            http_response_code(200);

            echo json_encode("Ordered Successfully");
        } elseif ($emailSent === "Error sending confirmation email") {
            http_response_code(500);

            echo json_encode("Error sending confirmation email");
        } 
        $this->con->close();
    }
    

    private function getUserDetails($dealer_id) {
        $query = "SELECT firstname,lastname,email,phone,address,city,postcode,country FROM dealer_register WHERE dealer_id = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("i", $dealer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $userDetails = $result->fetch_assoc();
    
        $stmt->close();
    
        return $userDetails;
    }

    public function quickOrder($file) {
        $targetDirectory = '../dealer-admin/quick_orders/';
    
        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }
    
        $currentDateTime = date('Y-m-d');
    
        $filenameWithoutExtension = pathinfo($file['name'], PATHINFO_FILENAME);
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
    
        $newFilename = $filenameWithoutExtension . '_' . $currentDateTime . '.' . $fileExtension;
    
        $targetFile = $targetDirectory . $newFilename;
    
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            
            $dealerId = $_COOKIE['dealer_id']; 
            $status = 'Completed';
    
            $sql = "INSERT INTO quick_orders (dealer_id, order_file, status) VALUES (?, ?, ?)";
            $stmt = $this->con->prepare($sql);
            $stmt->bind_param('iss', $dealerId, $newFilename, $status);
    
            if ($stmt->execute()) {
                http_response_code(200);
                echo json_encode('Order_Placed');
            } else {
                http_response_code(500);
                echo json_encode('Error inserting data into the database.');
            }
    
            $stmt->close();
            $this->con->close();
        } else {
            http_response_code(500);
            echo json_encode('Error moving file to the target directory.');
        }
    }
    
}

$dealer = new Dealer($con);
$product_order = new Order($con);

// Handle All POST Request

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Register New Dealer

    if (isset($_POST['register_id'])) {
        if (isset($_POST['formData'])) {
            parse_str($_POST['formData'], $formData);

            $registrationResult = $dealer->register($formData);
   
        }
    }

    // Login as Dealer

    elseif(isset($_POST['login_request'])){
        if(isset($_POST['login_email']) && isset($_POST['login_password'])){
            $login_email = $_POST['login_email'];
            $login_password = $_POST['login_password'];
            $login_user = $dealer->login($login_email,$login_password);
        }
        else{
            echo "Email or Password is empty";
        }
    }

    // Add To Cart

    elseif(isset($_POST['add_to_cart_request']) && $_POST['add_to_cart_request'] == 'add_to_cart'){
        $dealer->addtoCart();
    }

    // Place Order

    elseif(isset($_POST['place_order_request'])){
        // echo "success";
        $product_order->placeOrder();
    }

    // Reorder Request

    elseif(isset($_POST['reorder_request'])){
        if(isset($_POST['unique_orderid']) && !empty(isset($_POST['unique_orderid']))){
            $unique_orderid = $_POST['unique_orderid'];
            $product_order->reOrder($unique_orderid);
        }
        else{
            http_response_code(404);
            json_encode("Order_id Not Found");
        }
    }

    // Quick Order

    elseif (isset($_POST['quick_order_request'])) {
        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['file'];

            $product_order->quickOrder($uploadedFile);
        } else {
            echo 'Please select a file before submitting or check the file upload error.';
        }
    }

    // Update Profile

    elseif(isset($_POST['update_dealer_details_request'])){
        
        if(isset($_POST['first_name']) && !empty($_POST['first_name'])){
            if(isset($_POST['last_name']) && !empty($_POST['last_name'])){
                if(isset($_POST['contact']) && !empty($_POST['contact'])){
                    if (preg_match('/^\d+$/', $_POST['contact'])) {
                        if(isset($_POST['address']) && !empty($_POST['address'])){
                            if(isset($_POST['email']) && !empty($_POST['email'])){
                                if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                                    if (isset($_FILES['profile_image'])) {
                                        $dataArray = [];
                                        $dataArray['first_name'] = $_POST['first_name'];
                                        $dataArray['last_name'] = $_POST['last_name'];
                                        $dataArray['contact'] = $_POST['contact'];
                                        $dataArray['address'] = $_POST['address'];
                                        $dataArray['email'] = $_POST['email'];
                                        $profileImage = $_FILES['profile_image'];

                                        $dealer->editProfileWithImage($dataArray, $profileImage);
                                        
                                    }
                                    else {
                                        $dataArray = [];
                                        $dataArray['first_name'] = $_POST['first_name'];
                                        $dataArray['last_name'] = $_POST['last_name'];
                                        $dataArray['contact'] = $_POST['contact'];
                                        $dataArray['address'] = $_POST['address'];
                                        $dataArray['email'] = $_POST['email'];
                                    
                                        $dealer->editProfileWithoutImage($dataArray);
                                    }
                                } else {
                                    http_response_code(400); 
                                    echo json_encode("Invalid_email_format");
                                }
                            }
                            else{
                                http_response_code(404);
                                echo json_encode("Email_is_empty");
                            }  
                        }
                        else{
                            http_response_code(404);
                            echo json_encode("Address_is_empty");
                        }
                    } else {
                        http_response_code(400);  
                        echo json_encode("Invalid_contact_format");
                    } 
                }
                else{
                    http_response_code(404);
                    echo json_encode("Contact_is_empty");
                }
            }
            else{
                http_response_code(404);
                echo json_encode("Last_Name_is_empty");
            }
        }
        else{
            http_response_code(404);
            echo json_encode("First_Name_is_empty");
        }
    }


}

// Handle All GET Request

elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if(isset($_GET['fetchAlldelaers_request'])){
        http_response_code(200);
        // echo "success";
        $dealer->fetchAllDealers();
    }

    // Cart Product Request

    elseif(isset($_GET['get_cart_product_request'])){
        $dealer->getCartProducts();
    }

    // Get Total Amount

    elseif(isset($_GET['get_total_amount_request'])) {
        if(isset($_GET['dealer_id'])) {
            $requestedDealerId = $_GET['dealer_id'];
    
            $cookieDealerId = $_COOKIE['dealer_id'] ?? null;

            if ($requestedDealerId == $cookieDealerId) {
                $dealer->cartTotal();
            } else {
                http_response_code(403);  
                echo json_encode("Access Denied: Dealer Id does not match the session.");
            }
        } else {
            http_response_code(400);  
            echo json_encode("Dealer Id parameter not provided.");
        }
    }

    // Get Dealer Details

    elseif(isset($_GET['get_dealer_details'])){
        if(isset($_GET['dealerId'])){
            $requestedDealerId = $_GET['dealerId'];

            if($requestedDealerId == $_COOKIE['dealer_id']) {
                $dealer->fetchDealerById();
            }
            else {
                http_response_code(403);  
                echo json_encode("Access Denied: Dealer Id does not match the session.");
            }
        }
        else {
            http_response_code(400);  
            echo json_encode("Dealer Id parameter not provided.");
        }
    }

    // Get Order Details

    elseif(isset($_GET['order_details_request'])){
        $dealer->viewOrderDetails();
    }

    // Get Order Details By Id

    elseif(isset($_GET['viewOrderDeatils_by_unique_orderid_request'])){
        if(isset($_GET['unique_orderid']) && !empty($_GET['unique_orderid'])){
            // echo "success";
            $unique_orderid = $_GET['unique_orderid'];
            $dealer->viewOrderDetailsByOrderid($unique_orderid);
        }
        else{
            http_response_code(404);
            echo json_encode("Order Id Empty!");
        }
    }

    elseif(isset($_GET['dealer_profile_request'])){
        $dealer->getDealerProfile();
    }

    // Get Updated Quantity

    elseif(isset($_GET['updated_quantity_alert_request'])){
        $dealer->getUpdatedQuantityAlert();
    }

    // Get New Products Notification

    elseif(isset($_GET['get_updated_product_request'])){
        $dealer->getUpdatedProduct();
    }
}

// Handle All Delete Request

elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    
    $json_data = file_get_contents('php://input');
    
    $data = json_decode($json_data, true);
    
    // Remove Dealer Request

    if(isset($data['deleteDealer_request'])){
        http_response_code(200);

        if(empty($data['dealer_id'])){
            http_response_code(404);
            echo json_encode("Dealer Id Not Found For Delete Dealer");
        }
        else{
            $dealer_id = $data['dealer_id'];
            http_response_code(200);
            $dealer->deleteDealer($dealer_id);
        }
    }

    // Remove Cart Items Request

    elseif(isset($data['remove_cart_item_request'])){
        if(isset($data['dealer_id']) && $data['dealer_id'] == $_SESSION['dealer_id']){
            if(isset($data['cart_id'])){
                $cart_id = $data['cart_id'];
                $dealer_id = $data['dealer_id'];
                $dealer->removeCartItem($cart_id,$dealer_id);
            }
            else{
                http_response_code(404);
                echo json_encode("Cart Id Not Found Or Empty");
            }
        }
        else{
            http_response_code(404);
            echo json_encode("Dealer Id Not Found!");
        }
    }
    
}

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {

    // Update Cart

    $rawData = file_get_contents('php://input');
    $requestData = json_decode($rawData, true);

    if (isset($requestData['update_cart_request']) && $requestData['update_cart_request'] === 'update_cart') {
        if(isset($requestData['cart_id'])){
            $cartId = $requestData['cart_id'];
            $totalAmount = $requestData['total_amount'];
            $quantity = $requestData['quantity'];
            $dealer->updateCart($cartId,$totalAmount,$quantity);
        }
        else{
            http_response_code(404);
            echo json_encode("Cart Id Not Found For Update!");
        }
    }

}

?>

