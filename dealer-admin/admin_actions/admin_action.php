<?php

session_start();
// if (!isset($_COOKIE['dealer_id'])) {
//     header('Location: ../../index.html');
//     exit();  
// }
include "../../connection.php";

class Admin {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }


    public function login($email, $password){
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid-email-format";
            return;
        }
    
        $admin_login_sql = "SELECT * FROM admin WHERE email = ?";
        $stmt = mysqli_prepare($this->con, $admin_login_sql); // Use $this->con here
    
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
    
            $result = mysqli_stmt_get_result($stmt);
    
            if ($result) {
                $user = mysqli_fetch_assoc($result);
    
                if ($user) {
                    
                    if (password_verify($password, $user['password'])) {
                        $uniqueValue = uniqid();
                        if (setcookie(
                            'auth_admin',           
                            $uniqueValue,            
                            time() + (3 * 24 * 60 * 60),  
                            '/',                    
                            '',                   
                            true,                  
                            'Lax'                   
                        )) {
                            echo "Login-successful";
                        } else {
                            echo "Failed to set cookie";
                        }
                    } else {
                        echo "Incorrect-password";
                    }
                } else {
                    echo "User-not-found";
                }
            } else {
                echo "Error Admin Login in query: " . mysqli_error($this->con); // Use $this->con here
            }
    
            // Close the statement
            mysqli_stmt_close($stmt);
        }
    }

    public function logout(){
        
    
        if (setcookie(
            'auth_admin',
            '',
            time() - 3600,
            '/',
            '',
            false,  
            'Lax'
        )) {
            http_response_code(200);
            echo json_encode("logout-successfully");
        } else {
            http_response_code(500);
            echo json_encode("logout-failed");
        }
    }
    
    public function fetchDataForApproval() {
        $status = 'for-approval';
        $query = "SELECT * FROM dealer_register WHERE status = ?";
        $stmt = $this->con->prepare($query);
        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if (count($data) > 0) {
            http_response_code(200);
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode("No_request_found");
        }
    }

    public function dealerApprove($email) {
        $approve_status = 'Approved'; 
        $approve_email = $email;
        $approve_query = "UPDATE dealer_register SET status=? WHERE email=?";
        
        $stmt = $this->con->prepare($approve_query);
        $stmt->bind_param('ss', $approve_status, $approve_email);
        
        if ($stmt->execute()) {
            echo "Dealer with email $email has been approved.";
        } else {
            echo "Failed to approve dealer with email $email.";
        }
    }

    public function dealerCancel($email) {
        $approve_status = 'Rejected'; 
        $approve_email = $email;
        $approve_query = "UPDATE dealer_register SET status=? WHERE email=?";
        
        $stmt = $this->con->prepare($approve_query);
        $stmt->bind_param('ss', $approve_status, $approve_email);
        
        if ($stmt->execute()) {
            echo "Dealer with email $email has been approved.";
        } else {
            echo "Failed to approve dealer with email $email.";
        }
    }


}


class Category{

    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function createCategory($category_type,$category_name){
        

        $create_category_sql = "INSERT INTO category (category_type,category_name,status) VALUES (?, ? ,?)";

        $stmt = $this->con->prepare($create_category_sql);

        if ($stmt) {
            $status = 'Created';

            $stmt->bind_param('sss', $category_type, $category_name, $status);

            if ($stmt->execute()) {
                echo "Category-created-successfully";
            } else {
                echo "Failed to create category: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error Create Category in query: " . $this->con->error;
        }
    }

    public function loadCategory(){
        $load_category_sql = "SELECT * FROM category";
        
        $result = $this->con->query($load_category_sql);
    
        if ($result) {
            $categories = array();
    
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
    
            if (count($categories) > 0) {
                $response = array(
                    'status' => 'success',
                    'message' => $categories
                );
            } else {
                $response = array(
                    'status' => 'empty',
                    'message' => 'No categories found'
                );
            }
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Error loading categories: ' . $this->con->error
            );
        }
    
        echo json_encode($response);
    }

    public function editCategory_Details($category_id){
        $load_category_sql = "SELECT * FROM category WHERE category_id=?";
        
        $stmt = $this->con->prepare($load_category_sql);
        $stmt->bind_param('i', $category_id);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $category = $result->fetch_assoc();
    
                $jsonResponse = json_encode($category);
    
                echo $jsonResponse;
            } else {
                echo json_encode(['error' => 'Category not found']);
            }
        } else {
            echo json_encode(['error' => 'Error in executing Edit Details query']);
        }
    
        $stmt->close();
    }

    public function editCategory($category_id,$category_type,$category_name){
        $edit_category_sql = "UPDATE category SET category_type='$category_type',category_name='$category_name'  WHERE category_id = ?";
        $stmt = $this->con->prepare($edit_category_sql);
    
        if ($stmt) {
            $stmt->bind_param('i', $category_id); 
    
            if ($stmt->execute()) {
                echo "Category Updated successfully";
            } else {
                echo "Failed to Update category: " . $stmt->error;
            }
    
            $stmt->close();
        } else {
            echo "Error in Category Update query: " . $this->con->error;
        }
    }


    public function deleteCategory($category_id) {
        $delete_category_sql = "DELETE FROM category WHERE category_id = ?";
        $stmt = $this->con->prepare($delete_category_sql);
    
        if ($stmt) {
            $stmt->bind_param('i', $category_id); 
    
            if ($stmt->execute()) {
                echo "Category deleted successfully";
            } else {
                echo "Failed to delete category: " . $stmt->error;
            }
    
            $stmt->close();
        } else {
            echo "Error in Category Delete query: " . $this->con->error;
        }
    }

    public function fetchCategoryNames($category_type) {
        $category_name_sql = "SELECT DISTINCT category_name FROM category WHERE category_type=?";
        
        $stmt = $this->con->prepare($category_name_sql);
        $stmt->bind_param('s', $category_type);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $category_names = array();
        
            while ($row = $result->fetch_assoc()) {
                $category_names[] = $row['category_name'];
            }
        
            $stmt->close();
        
            $response = array('category_name' => $category_names);
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            echo "Failed to Fetch Category Names: " . $stmt->error;
        }
    }

}

class subCategory extends Category{
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function createSubCategory($category_type,$category_name,$sub_category_name){
        

        $create_category_sql = "INSERT INTO sub_category (category_type,category_name,sub_category_name,status) VALUES (?, ?, ? ,?)";

        $stmt = $this->con->prepare($create_category_sql);

        if ($stmt) {
            $status = 'Created';

            $stmt->bind_param('ssss', $category_type, $category_name, $sub_category_name , $status);

            if ($stmt->execute()) {
                echo "Sub-Category-created-successfully";
            } else {
                echo "Failed to create category: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error Create Category in query: " . $this->con->error;
        }
    }

    public function loadSubCategory(){
        $load_subcategory_sql = "SELECT * FROM sub_category WHERE status='Created'";
        
        $result = $this->con->query($load_subcategory_sql);
    
        if ($result) {
            $sub_categories = array();
    
            while ($row = $result->fetch_assoc()) {
                $sub_categories[] = $row;
            }
    
            if (count($sub_categories) > 0) {
                $response = array(
                    'status' => 'success',
                    'message' => $sub_categories
                );
            } else {
                $response = array(
                    'status' => 'empty',
                    'message' => 'No sub-categories found'
                );
            }
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Error loading sub-categories: ' . $this->con->error
            );
        }
    
        echo json_encode($response);
    }


    public function loadSubCategoryById($sub_category_id) {
        $load_subcategory_byid_sql = "SELECT * FROM sub_category WHERE sub_category_id = ?";
        
        $stmt = $this->con->prepare($load_subcategory_byid_sql);
        $stmt->bind_param('i', $sub_category_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
    
        if ($result) {
            $sub_category_value = array();
    
            while ($row = $result->fetch_assoc()) {
                $sub_category_value[] = $row;
            }
    
            if (count($sub_category_value) > 0) {
                $response = array(
                    'status' => 'success',
                    'message' => $sub_category_value
                );
            } else {
                $response = array(
                    'status' => 'empty',
                    'message' => 'No sub-categories found'
                );
            }
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Error loading sub-categories: ' . $this->con->error
            );
        }
    
        $stmt->close();
    
        echo json_encode($response);
    }


    public function loadSubCategoryByCategory($category_type, $category_name) {
        $load_subcategory_bycategory_sql = "SELECT * FROM sub_category WHERE category_type = ? AND category_name = ?";
        
        $stmt = $this->con->prepare($load_subcategory_bycategory_sql);
        $stmt->bind_param('ss', $category_type, $category_name);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result) {
            $sub_category_value = array();
        
            while ($row = $result->fetch_assoc()) {
                $sub_category_value[] = $row;
            }
    
            $response = array('status' => 'success', 'message' => $sub_category_value);
            header('Content-Type: application/json');
            echo json_encode($response);
        } else {
            $response = array('status' => 'error', 'message' => 'Error loading sub-categories: ' . $this->con->error);
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    
        $stmt->close();
    }
    
    

    public function deleteSubCategory($sub_category_id) {
        // echo $sub_category_id;
        $delete_subcategory_sql = "DELETE FROM sub_category WHERE sub_category_id = ?";
        $stmt = $this->con->prepare($delete_subcategory_sql);
    
        if ($stmt) {
            $stmt->bind_param('i', $sub_category_id); 
    
            if ($stmt->execute()) {
                echo "Sub Category deleted successfully";
            } else {
                echo "Failed to delete Sub category: " . $stmt->error;
            }
    
            $stmt->close();
        } else {
            echo "Error in SubCategory Delete query: " . $this->con->error;
        }
    }

    public function editSubCategory($sub_category_id, $category_type, $category_name, $sub_category_name) {
        $edit_subcategory_sql = "UPDATE sub_category SET category_type=?, category_name=?, sub_category_name=? WHERE sub_category_id=?";
    
        $stmt = $this->con->prepare($edit_subcategory_sql);
        
        if ($stmt) {
            $stmt->bind_param('sssi', $category_type, $category_name, $sub_category_name, $sub_category_id);
    
            if ($stmt->execute()) {
                echo 'Sub_category_updated_successfully';
            } else {
                echo 'Error updating sub category: ' . $stmt->error;
            }
    
            $stmt->close();
        } else {
            echo 'Error in editSubCategory query: ' . $this->con->error;
            
        }
    
        // echo json_encode($response);
    }

    public function fetchAllCategory($main_category) {
        $category_type = $main_category;
        $category_name_sql = "SELECT DISTINCT category_name FROM category WHERE category_type=?";
        
        $stmt = $this->con->prepare($category_name_sql);
        $stmt->bind_param('s', $category_type);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $category_data = array();
        
            while ($row = $result->fetch_assoc()) {
                $category_name = $row['category_name'];
    
                // Fetch each sub_category_name by category name
                $load_subcategory_bycategory_sql = "SELECT sub_category_name FROM sub_category WHERE category_type = ? AND category_name = ?";
                $stmt = $this->con->prepare($load_subcategory_bycategory_sql);
                $stmt->bind_param('ss', $category_type, $category_name);
                $stmt->execute();
                
                $sub_category_result = $stmt->get_result();
                $sub_category_value = array();
                
                while ($sub_category_row = $sub_category_result->fetch_assoc()) {
                    $sub_category_value[] = $sub_category_row['sub_category_name'];
                }
    
                $category_data[$category_name] = $sub_category_value;
            }
    
            $stmt->close();
    
            $response = json_encode($category_data);
            header('Content-Type: application/json');
            http_response_code(200);
            echo $response;
        } else {
            echo json_encode("Failed to Fetch Category Names: " . $stmt->error);
        }
    }

}

class Product{
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function addProductColor($color_name, $color_value) {
        $color_type = "code";
        $check_color_sql = "SELECT COUNT(*) FROM product_color WHERE color_name = ?";
        $check_stmt = mysqli_prepare($this->con, $check_color_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $color_name);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_bind_result($check_stmt, $count);
        mysqli_stmt_fetch($check_stmt);
        mysqli_stmt_close($check_stmt);

        if ($count > 0) {
            echo "color_already_exists";
        } else {
            $add_color_sql = "INSERT INTO product_color (color_name, color_value, color_type) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($this->con, $add_color_sql);
            mysqli_stmt_bind_param($stmt, "sss", $color_name, $color_value,$color_type);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                echo "color_added_successfully";
            } else {
                echo "Error adding color: " . mysqli_error($this->con);
            }
        }

    }

    public function loadColor() {
        $select_all_color = "SELECT * FROM product_color";
        $result = mysqli_query($this->con, $select_all_color);
    
        $colors = array();
    
        while ($row = mysqli_fetch_assoc($result)) {
            $colors[] = $row;
        }
    
        echo json_encode($colors);
    }

    public function deletecolor($colorId){
        $delete_color_sql = "DELETE FROM product_color WHERE color_id=$colorId";
        $result = mysqli_query($this->con,$delete_color_sql);

        if($result){
            echo "color_deleted_successfully";
        }
        else{
            echo "color_delete_query_failed";
        }
    }

    public function addProduct($postData, $fileData) {
        $product_name = $postData['product_name'];
        $category_type = $postData['category_type'];
        $category_name = $postData['category_name'];
        $sub_category_name = $postData['sub_category_name'];

        $product_description = $postData['product_description'];
        $product_specification = $postData['product_specification'];
        $product_price = $postData['product_price'][0];
        $product_msrp_price = $postData['msrp_price'][0];

        if (isset($postData['product_color'])) {
            $product_color = $postData['product_color'];
        } elseif (isset($fileData['product_color_file']['name'])) {
            $color_value = $fileData['product_color_file']['name'];
        
            if (empty($postData['product_color_name'])) {
                http_response_code(400);
                echo "Color Name Is Empty";
                return;
            } else {
                $product_color = $postData['product_color_name'];
        
                // Check if the color already exists
                $check_color_query = "SELECT COUNT(*) FROM product_color WHERE color_name = ?";
                $check_color_stmt = mysqli_prepare($this->con, $check_color_query);
                mysqli_stmt_bind_param($check_color_stmt, "s", $product_color);
                mysqli_stmt_execute($check_color_stmt);
                mysqli_stmt_bind_result($check_color_stmt, $color_count);
                mysqli_stmt_fetch($check_color_stmt);
                mysqli_stmt_close($check_color_stmt);
        
                if ($color_count > 0) {
                    http_response_code(400);
                    echo "Color Already Exists.Please Select Colour For This Product";
                    return;
                }
        
                $targetPath = '../color_images/' . $color_value;
        
                if (move_uploaded_file($fileData['product_color_file']['tmp_name'], $targetPath)) {
    
                    $color_type = "image";
                    $color_insert_query = "INSERT INTO product_color (color_name, color_value, color_type) VALUES (?, ?, ?)";
                    $color_insert_stmt = mysqli_prepare($this->con, $color_insert_query);
                    mysqli_stmt_bind_param($color_insert_stmt, "sss", $product_color, $color_value, $color_type);
        
                    if (mysqli_stmt_execute($color_insert_stmt)) {
                        http_response_code(200);
                        // echo "Color_added_successfully";
                    } else {
                        http_response_code(500); 
                        echo "Error adding color: " . mysqli_error($this->con);
                    }
        
                    mysqli_stmt_close($color_insert_stmt);
                } else {
                    http_response_code(500); 
                    echo "Error moving uploaded file.";
                }
            }
        }
    
        // Check if the product already exists
        $check_product_sql = "SELECT COUNT(*) FROM product WHERE 
                                product_name = ? AND 
                                product_category_type = ? AND 
                                product_category_name = ? AND 
                                product_sub_category_name = ? AND 
                                product_color = ?";
        $check_product_stmt = mysqli_prepare($this->con, $check_product_sql);
        mysqli_stmt_bind_param($check_product_stmt, "sssss", $product_name, $category_type, $category_name, $sub_category_name, $product_color);
        mysqli_stmt_execute($check_product_stmt);
        mysqli_stmt_bind_result($check_product_stmt, $count);
        mysqli_stmt_fetch($check_product_stmt);
        mysqli_stmt_close($check_product_stmt);
    
        if ($count > 0) {
            echo "Product_already_exists";
        } else {
            // Insert product details into the "product" table
            $add_product_sql = "INSERT INTO product (product_name, product_category_type, product_category_name, product_sub_category_name, product_color, product_description, product_specification,product_price,product_msrp_price)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $add_product_stmt = mysqli_prepare($this->con, $add_product_sql);
            mysqli_stmt_bind_param($add_product_stmt, "sssssssii", $product_name, $category_type, $category_name, $sub_category_name, $product_color, $product_description, $product_specification,$product_price,$product_msrp_price);

            
                
            if (mysqli_stmt_execute($add_product_stmt)) {
                $product_id = mysqli_insert_id($this->con);

                $add_color_product_sql = "INSERT INTO color_product (product_id, product_name, product_category_type, product_category_name, product_sub_category_name, product_color, product_description, product_specification)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $add_color_product_stmt = mysqli_prepare($this->con, $add_color_product_sql);
                mysqli_stmt_bind_param($add_color_product_stmt, "isssssss", $product_id, $product_name, $category_type, $category_name, $sub_category_name, $product_color, $product_description, $product_specification);

                if (mysqli_stmt_execute($add_color_product_stmt)) {
                    $color_product_id = mysqli_insert_id($this->con);

                    // Insert product details into the "product_details" table for each size, quantity, price, and msrp price
                    $size_array = $postData['product_size'];
                    $quantity_array = $postData['product_quantity'];
                    $price_array = $postData['product_price'];
                    $msrp_array = $postData['msrp_price'];
        
                    for ($i = 0; $i < count($size_array); $i++) {
                        $size = $size_array[$i];
                        $quantity = $quantity_array[$i];
                        $price = $price_array[$i];
                        $msrp = $msrp_array[$i];
        
                        // Insert product details into the "product_details" table
                        $add_product_details_sql = "INSERT INTO product_details (product_id, product_size, product_quantity, product_price, product_msrp_price)
                                VALUES (?, ?, ?, ?, ?)";
                        $add_product_details_stmt = mysqli_prepare($this->con, $add_product_details_sql);
                        mysqli_stmt_bind_param($add_product_details_stmt, "isiii", $product_id, $size, $quantity, $price, $msrp);
                        mysqli_stmt_execute($add_product_details_stmt);
                        mysqli_stmt_close($add_product_details_stmt);
                    }

                    $imageUploadPath = "../product_images/";

                    for ($i = 0; $i < count($fileData['product_image']['name']); $i++) {
                        $imageName = $fileData['product_image']['name'][$i];
                        $imageTmpName = $fileData['product_image']['tmp_name'][$i];
                        $imagePath = $imageUploadPath . $imageName;

                        $allowedExtensions = ['jpg', 'jpeg', 'png'];
                        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

                        if (in_array(strtolower($imageExtension), $allowedExtensions)) {
                            move_uploaded_file($imageTmpName, $imagePath);

                            $add_image_sql = "INSERT INTO product_images (product_id, product_image)
                                            VALUES (?, ?)";
                            $add_image_stmt = mysqli_prepare($this->con, $add_image_sql);
                            mysqli_stmt_bind_param($add_image_stmt, "is", $product_id, $imageName);
                            mysqli_stmt_execute($add_image_stmt);
                            mysqli_stmt_close($add_image_stmt);
                        } 
                        else 
                        {
                            http_response_code(400); 
                            echo "Invalid_file_format";
                            exit;
                        }
        
                    }

                    // Insert product details into the "product_details" table for each size, quantity, price, and msrp price
                    $size_array = $postData['product_size'];
                    $quantity_array = $postData['product_quantity'];
                    $price_array = $postData['product_price'];
                    $msrp_array = $postData['msrp_price'];
        
                    for ($i = 0; $i < count($size_array); $i++) {
                        $size = $size_array[$i];
                        $quantity = $quantity_array[$i];
                        $price = $price_array[$i];
                        $msrp = $msrp_array[$i];
        
                        // Insert product details into the "product_details" table
                        $add_product_details_sql = "INSERT INTO color_product_details (product_id,color_product_id, product_color, product_size, product_quantity, product_price, product_msrp_price)
                                VALUES (? ,?, ?, ?, ?, ?, ?)";
                        $add_product_details_stmt = mysqli_prepare($this->con, $add_product_details_sql);
                        mysqli_stmt_bind_param($add_product_details_stmt, "iissiii", $product_id, $color_product_id, $product_color, $size, $quantity, $price, $msrp);
                        mysqli_stmt_execute($add_product_details_stmt);
                        mysqli_stmt_close($add_product_details_stmt);
                    }

                    $imageUploadPath = "../product_images/";

                    for ($i = 0; $i < count($fileData['product_image']['name']); $i++) {
                        $imageName = $fileData['product_image']['name'][$i];
                        $imageTmpName = $fileData['product_image']['tmp_name'][$i];
                        $imagePath = $imageUploadPath . $imageName;

                        $allowedExtensions = ['jpg', 'jpeg', 'png'];
                        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

                        if (in_array(strtolower($imageExtension), $allowedExtensions)) {
                            move_uploaded_file($imageTmpName, $imagePath);

                            $add_image_sql = "INSERT INTO color_product_images (product_id, color_product_id, product_image)
                                            VALUES (?, ?, ?)";
                            $add_image_stmt = mysqli_prepare($this->con, $add_image_sql);
                            mysqli_stmt_bind_param($add_image_stmt, "iis", $product_id, $color_product_id, $imageName);
                            mysqli_stmt_execute($add_image_stmt);
                            mysqli_stmt_close($add_image_stmt);
                        } 
                        else 
                        {
                            http_response_code(400); 
                            echo "Invalid_file_format";
                            exit;
                        }
        
                    }
                
                }

                $add_another_table_sql = "INSERT INTO new_product_updates (product_id)
                                    VALUES (?)";
                $add_another_table_stmt = mysqli_prepare($this->con, $add_another_table_sql);
                mysqli_stmt_bind_param($add_another_table_stmt, "i", $product_id);

                mysqli_stmt_execute($add_another_table_stmt);

                echo "Product_added_successfully";
            } else {
                echo "Error adding product: " . mysqli_error($this->con);
            }
    
            mysqli_stmt_close($add_product_stmt);
        }
    }


    public function fetchProducts()
    {
        // Set the response code to 200 
        http_response_code(200);

        $response = array();

        $query = "SELECT * FROM product";
        $result = mysqli_query($this->con, $query);

        if ($result) {
            $products = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
            mysqli_free_result($result);

            $response['status'] = 'success';
            
            if (empty($products)) {
                $response['message'] = 'No_products_found';
            } else {
                $response['products'] = $products;
            }

            // Echo the JSON-encoded response
            echo json_encode($response);
        } else {
            http_response_code(400);

            echo json_encode(array('status' => 'error', 'message' => 'Error fetching products'));
        }

        return $response;
    }

    public function editProduct($product_details) {
        $product_id = $product_details['product_id'];
        $product_name = $product_details['product_name'];
        $category_type = $product_details['category_type'];
        $category_name = $product_details['category_name'];
        $sub_category_name = $product_details['sub_category_name'];
        $product_color = $product_details['product_color'];
        $product_description = $product_details['product_description'];
        $product_specification = $product_details['product_specification'];
    
        // Update product details with prepared statement
        $update_product_details_sql = "UPDATE product SET 
            product_name = ?,
            product_category_type = ?,
            product_category_name = ?,
            product_sub_category_name = ?,
            product_color = ?,
            product_description = ?,
            product_specification = ?
            WHERE product_id = ?";
    
        $stmt = mysqli_prepare($this->con, $update_product_details_sql);
        mysqli_stmt_bind_param($stmt, "sssssssi", $product_name, $category_type, $category_name, $sub_category_name, $product_color, $product_description, $product_specification, $product_id);
    
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'product_updated_successfully']);
        } else {
            mysqli_stmt_close($stmt);
            http_response_code(500); // Internal Server Error
            header('Content-Type: application/json');
            echo json_encode(['message' => 'product_update_failed']);
        }
    }

    public function deleteProduct($product_id){

        $get_images_sql = "SELECT product_image FROM product_images WHERE product_id = ?";
        $get_images_stmt = mysqli_prepare($this->con, $get_images_sql);
        mysqli_stmt_bind_param($get_images_stmt, "i", $product_id);
        mysqli_stmt_execute($get_images_stmt);
        mysqli_stmt_bind_result($get_images_stmt, $image_name);
    
        // Store the image names in an array
        $image_names = [];
        while (mysqli_stmt_fetch($get_images_stmt)) {
            $image_names[] = $image_name;
        }
    
        mysqli_stmt_close($get_images_stmt);
    
        // Delete from product_images table
        $delete_images_sql = "DELETE FROM product_images WHERE product_id = ?";
        $delete_images_stmt = mysqli_prepare($this->con, $delete_images_sql);
        mysqli_stmt_bind_param($delete_images_stmt, "i", $product_id);
        $delete_images_result = mysqli_stmt_execute($delete_images_stmt);
        mysqli_stmt_close($delete_images_stmt);
    
        // Delete images from the product_images folder
        $imageUploadPath = "../product_images/";
    
        foreach ($image_names as $image_name) {
            $imagePath = $imageUploadPath . $image_name;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    
        // Delete from product_details table
        $delete_details_sql = "DELETE FROM product_details WHERE product_id = ?";
        $delete_details_stmt = mysqli_prepare($this->con, $delete_details_sql);
        mysqli_stmt_bind_param($delete_details_stmt, "i", $product_id);
        $delete_details_result = mysqli_stmt_execute($delete_details_stmt);
        mysqli_stmt_close($delete_details_stmt);
    
        // Delete from product table
        $delete_product_sql = "DELETE FROM product WHERE product_id = ?";
        $delete_product_stmt = mysqli_prepare($this->con, $delete_product_sql);
        mysqli_stmt_bind_param($delete_product_stmt, "i", $product_id);
        $delete_product_result = mysqli_stmt_execute($delete_product_stmt);
        mysqli_stmt_close($delete_product_stmt);

        $row_count_sql = "SELECT COUNT(*) FROM product";
        $row_count_result = mysqli_query($this->con, $row_count_sql);
        $row_count = mysqli_fetch_array($row_count_result)[0];

        $delete_color_product_sql = "DELETE FROM color_product_details WHERE product_id = ?";
        $delete_color_product_stmt = mysqli_prepare($this->con, $delete_color_product_sql);
        mysqli_stmt_bind_param($delete_color_product_stmt, "i", $product_id);
        $delete_color_product_details_result = mysqli_stmt_execute($delete_color_product_stmt);
        mysqli_stmt_close($delete_color_product_stmt);

        $delete_color_product_sql = "DELETE FROM color_product WHERE product_id = ?";
        $delete_color_product_stmt = mysqli_prepare($this->con, $delete_color_product_sql);
        mysqli_stmt_bind_param($delete_color_product_stmt, "i", $product_id);
        $delete_color_product_result = mysqli_stmt_execute($delete_color_product_stmt);
        mysqli_stmt_close($delete_color_product_stmt);

        // $get_color_product_images_sql = "
        //     SELECT product_image
        //     FROM color_product_images
        //     WHERE color_product_id IN (
        //         SELECT color_product_id
        //         FROM color_product
        //         WHERE product_id = ?
        //     )
        // ";
        // $get_color_product_images_stmt = mysqli_prepare($this->con, $get_color_product_images_sql);

        // if ($get_color_product_images_stmt === false) {
        //     http_response_code(500);
        //     echo json_encode("Error preparing SQL statement: " . mysqli_error($this->con));
        //     exit();
        // }

        // mysqli_stmt_bind_param($get_color_product_images_stmt, "i", $product_id);

        // if (!mysqli_stmt_execute($get_color_product_images_stmt)) {
        //     http_response_code(500);
        //     echo json_encode("Error executing SQL statement: " . mysqli_error($this->con));
        //     exit();
        // }

        // mysqli_stmt_bind_result($get_color_product_images_stmt, $product_image);

        // $image_names = [];

        // while (mysqli_stmt_fetch($get_color_product_images_stmt)) {
        //     $image_names[] = $product_image;
        // }

        // mysqli_stmt_close($get_color_product_images_stmt);

        // $imageUploadPath = "../product_images/";

        // $unlinkSuccess = true; 

        // foreach ($image_names as $image_name) {
        //     $imagePath = $imageUploadPath . $image_name;

        //     if (file_exists($imagePath)) {
        //         if (!unlink($imagePath)) {
        //             $unlinkSuccess = false;
        //             break; 
        //         }
        //     }
        // }

        // if (!$unlinkSuccess) {
        //     http_response_code(500);
        //     echo json_encode("Error unlinking images");
        //     exit();
        // }

        $delete_color_product_images_sql = "DELETE FROM color_product_images WHERE color_product_id IN (SELECT color_product_id FROM color_product WHERE product_id = ?)";
        $delete_color_product_images_stmt = mysqli_prepare($this->con, $delete_color_product_images_sql);
        mysqli_stmt_bind_param($delete_color_product_images_stmt, "i", $product_id);
        $delete_color_product_images_result = mysqli_stmt_execute($delete_color_product_images_stmt);
        mysqli_stmt_close($delete_color_product_images_stmt);

        $delete_wishlist_sql = "DELETE FROM wishlist WHERE product_id = ?";
        $delete_wishlist_stmt = mysqli_prepare($this->con, $delete_wishlist_sql);
        mysqli_stmt_bind_param($delete_wishlist_stmt, "i", $product_id);
        $delete_wishlist_result = mysqli_stmt_execute($delete_wishlist_stmt);
        mysqli_stmt_close($delete_wishlist_stmt);

        $delete_cart_sql = "DELETE FROM dealer_cart WHERE product_id = ?";
        $delete_cart_stmt = mysqli_prepare($this->con, $delete_cart_sql);
        mysqli_stmt_bind_param($delete_cart_stmt, "i", $product_id);
        $delete_cart_result = mysqli_stmt_execute($delete_cart_stmt);
        mysqli_stmt_close($delete_cart_stmt);
        
        if ($delete_images_result && $delete_details_result && $delete_product_result && $delete_color_product_details_result && $delete_color_product_result && $delete_color_product_images_result && $delete_wishlist_result && $delete_cart_result) {
            if ($row_count == 0) {
                http_response_code(200);
                echo "Last_product_deleted_successfully";
            } else {
                http_response_code(200);
                echo "Product_deleted_successfully";
            }
        } else {
            http_response_code(400);
            echo "Error_deleting_product";
        }
    }

    public function fetchProductById($product_id){
        // Fetch product details
        $get_product_by_id_sql = "SELECT * FROM product WHERE product_id = ?";
        $get_product_by_id_stmt = mysqli_prepare($this->con, $get_product_by_id_sql);
        mysqli_stmt_bind_param($get_product_by_id_stmt, "i", $product_id);
    
        $product = array();
    
        if (mysqli_stmt_execute($get_product_by_id_stmt)) {
            $result = mysqli_stmt_get_result($get_product_by_id_stmt);
    
            if ($row = mysqli_fetch_assoc($result)) {
                $product = $row;
            }
    
            mysqli_free_result($result);
        }
    
        mysqli_stmt_close($get_product_by_id_stmt);
    
    
        // Send the response as JSON with both product details and images
        header('Content-Type: application/json');
        echo json_encode($product);

    }

    public function getProductImageById($product_id){
        $get_product_images_sql = "SELECT product_image FROM product_images WHERE product_id=$product_id";

        $result = $this->con->query($get_product_images_sql);
    
        if ($result->num_rows > 0) {
            $images = [];
            while ($row = $result->fetch_assoc()) {
                $images[] = $row['product_image'];
            }
    
            $this->con->close();
    
            http_response_code(200);
            echo json_encode(['images' => $images]);
        } else {
            $this->con->close();
    
            http_response_code(404);
            echo json_encode(['error' => 'no_images_found']);
        }

    }

    public function addProductImage($product_id, $image_names) {
        $successCount = 0;
        $upload_directory = '../product_images/';
    
        foreach ($image_names as $image_name) {
            $target_path = $upload_directory . $image_name;
            $query = "INSERT INTO product_images (product_id, product_image) VALUES (?, ?)";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("is", $product_id, $image_name);
    
            if ($stmt->execute()) {
                $successCount++;
            } else {
                // Cleanup: Delete uploaded image if query execution fails
                unlink($target_path);
    
                http_response_code(400);
                echo "Failed to insert image into the database.";
                break;
            }
    
            $stmt->close();
        }
    
        if ($successCount == count($image_names)) {
            http_response_code(200);
            echo "image_upload_successfully";
        } else {
            foreach ($image_names as $image_name) {
                unlink($upload_directory . $image_name);
            }
    
            http_response_code(400);
            echo "No images inserted into the database.";
        }
    }
    

    function deleteProductImage($imageName, $product_id) {
        $delete_image_sql = "DELETE FROM product_images WHERE product_id = ? AND product_image = ?";
        $delete_image_stmt = mysqli_prepare($this->con, $delete_image_sql);
    
        mysqli_stmt_bind_param($delete_image_stmt, "is", $product_id, $imageName);
    
        $success = mysqli_stmt_execute($delete_image_stmt);
    
        mysqli_stmt_close($delete_image_stmt);
    
        $image_path = "../product_images/" . $imageName;
        if ($success && file_exists($image_path)) {
            unlink($image_path);
            $response = ['code' => 200, 'message' => 'Image_deleted_successfully'];
        } else {
            $response = ['code' => 500, 'error' => 'Failed to delete image'];
        }
    
        // Send the JSON response
        header('Content-Type: application/json');
        http_response_code($response['code']);
        echo json_encode($response);
    }

    public function getProductDetailsById($product_id) {
        $response = array();
    
        $get_product_details_by_id_sql = "SELECT * FROM product_details WHERE product_id = ?";
        $stmt = $this->con->prepare($get_product_details_by_id_sql);
        $stmt->bind_param("i", $product_id);
    
        if ($stmt->execute()) {
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $productDetails = array();
    
                while ($data = $result->fetch_assoc()) {
                    $productDetails[] = $data;
                }
    
                $response['status'] = 'success';
                $response['productDetails'] = $productDetails;
                http_response_code(200);
            } else {
                http_response_code(404);
                $response['status'] = 'error';
                $response['message'] = 'No_product_details_found';
            }
        } else {
            http_response_code(400);
            $response['status'] = 'error';
            $response['message'] = 'Failed to fetch product details.';
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
    }


    public function editProductDetails($productDetails) {
    
        $product_details = $productDetails;
        $product_id = $product_details['productId'];
        $product_details_id = $product_details['productDetailsId'];
        $product_size = $product_details['productSize'];
        $product_quantity = $product_details['productQuantity'];
        $product_price = $product_details['productPrice'];
        $msrp_price = $product_details['msrpPrice'];
    
        $edit_produuct_details_sql = "UPDATE product_details SET product_size = ?, product_quantity = ?, product_price = ?, product_msrp_price = ? WHERE product_details_id = ? AND product_id = ?";
    
        $stmt = $this->con->prepare($edit_produuct_details_sql);
    
        $stmt->bind_param("ssssii", $product_size, $product_quantity, $product_price, $msrp_price, $product_details_id, $product_id);
    
        $stmt->execute();
    
        if ($stmt->error) {
            http_response_code(400); // Bad Request
            echo json_encode(["error" => "Error: " . $stmt->error]);
        } else {
            http_response_code(200); // OK
            echo "product_details_updated";
        }
    
        $stmt->close();
    }

    public function deleteProductDetails($product_id,$product_details_id){
        $delete_product_details_sql = "DELETE FROM product_details WHERE product_id = ? AND product_details_id = ?";

        $stmt = $this->con->prepare($delete_product_details_sql);
        $stmt->bind_param("ii", $product_id, $product_details_id);

        $stmt->execute();

        if ($stmt->error) {
            http_response_code(400);
            echo json_encode(["error" => "Error: " . $stmt->error]);
        } else {
            http_response_code(200); 
            echo "Product_details_deleted_successfully";
        }

        $stmt->close();
    }

    public function addMoreDetails($product_details){
        $product_id = $product_details['product_id'];
        $product_size = $product_details['product_size'];
        $product_quantity = $product_details['product_quantity'];
        $product_price = $product_details['product_price'];
        $msrp_price = $product_details['msrp_price'];
    
        $stmt = $this->con->prepare("INSERT INTO product_details (product_id, product_size, product_quantity, product_price, product_msrp_price) VALUES (?, ?, ?, ?, ?)");
    
        $stmt->bind_param("isddd", $product_id, $product_size, $product_quantity, $product_price, $msrp_price);
    
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode("Details_Added_Successfully");
        } else {
            http_response_code(500);
            echo json_encode('Failed to insert data into the database: ' . $stmt->error);
        }
    
        $stmt->close();
    }
    
    public function fetchAllProductDetails($product_id) {
        $response = array();
    
        // Fetch basic product information
        $get_product_by_id_sql = "SELECT * FROM product WHERE product_id = ?";
        $get_product_by_id_stmt = mysqli_prepare($this->con, $get_product_by_id_sql);
        mysqli_stmt_bind_param($get_product_by_id_stmt, "i", $product_id);
    
        $productInfo = array();
    
        if (mysqli_stmt_execute($get_product_by_id_stmt)) {
            $result = mysqli_stmt_get_result($get_product_by_id_stmt);
    
            if ($row = mysqli_fetch_assoc($result)) {
                $productInfo = $row;
            }
    
            mysqli_free_result($result);
        }
    
        mysqli_stmt_close($get_product_by_id_stmt);
    
        // Fetch product details
        $get_product_details_by_id_sql = "SELECT * FROM product_details WHERE product_id = ?";
        $stmt = $this->con->prepare($get_product_details_by_id_sql);
        $stmt->bind_param("i", $product_id);
    
        if ($stmt->execute()) {
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $productDetails = array();
    
                while ($data = $result->fetch_assoc()) {
                    $productDetails[] = $data;
                }
    
                $response['status'] = 'success';
                $response['productInfo'] = $productInfo;
                $response['productDetails'] = $productDetails;
                http_response_code(200);
            } else {
                http_response_code(404);
                $response['status'] = 'error';
                $response['message'] = 'No_product_details_found';
            }
        } else {
            http_response_code(400);
            $response['status'] = 'error';
            $response['message'] = 'Failed to fetch product details.';
        }
    
        // Send the final response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function fetchProductByCategoryType($category_type) {
        $fetch_product_by_category_type_sql = "SELECT product_id, product_category_type, product_name FROM product WHERE product_category_type = ? ORDER BY created_at DESC LIMIT 10";
    
        $stmt = $this->con->prepare($fetch_product_by_category_type_sql);
    
        $stmt->bind_param("s", $category_type);
    
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        if ($result === false) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to get product"]);
        } else {
            $products = [];
    
            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $product_category_type = $row['product_category_type'];
                $product_name = $row['product_name'];
    
                // Fetch product images for the current product_id
                $product_images = $this->fetchProductImages($product_id);
                $product_details = $this->fetchProductDetailsById($product_id);
                $product_price = $product_details['product_price'];
                $msrp_price = $product_details['msrp_price'];
    
                // Add the product and its images to the $products array
                $products[] = [
                    "product" => [
                        "product_id" => $product_id,
                        "product_category_type" => $product_category_type,
                        "product_name" => $product_name,
                        "product_image" => $product_images,
                        "product_price" => $product_price,
                        "msrp_price" => $msrp_price
                    ]
                ];
            }
    
            $stmt->close();
    
            if (!empty($products)) {
                http_response_code(200);
                echo json_encode($products);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "No_products_found_for_the_specified_category_type"]);
            }
        }
    }
    
    private function fetchProductImages($product_id) {
        $fetch_product_image_sql = "SELECT product_image FROM product_images WHERE product_id = ? LIMIT 1";
    
        try {
            $stmt_images = $this->con->prepare($fetch_product_image_sql);
    
            if (!$stmt_images) {
                throw new Exception("Failed to prepare statement");
            }
    
            $stmt_images->bind_param("i", $product_id);
            $stmt_images->execute();
            $result_images = $stmt_images->get_result();
    
            if ($image_row = $result_images->fetch_assoc()) {
                $product_image = $image_row['product_image'];
            } else {
                $product_image = $image_row['product_image']; 
            }
    
            $stmt_images->close();
    
            return $product_image;
        } catch (Exception $e) {
            return "Error fetching product image: " . $e->getMessage();
        }
    }

    private function fetchProductDetailsById($product_id){
        $get_product_details_by_id_sql = "SELECT product_price,product_msrp_price FROM product_details WHERE product_id = ?";
        $stmt = $this->con->prepare($get_product_details_by_id_sql);
        $stmt->bind_param("i", $product_id);
    
        try {
            if (!$stmt) {
                throw new Exception("Failed to prepare statement");
            }
    
            if ($stmt->execute()) {
                $result = $stmt->get_result();
    
                if ($result->num_rows > 0) {
                    $data = $result->fetch_assoc();
                    $product_price = $data['product_price'];
                    $msrp_price = $data['product_msrp_price'];
                    if ($product_price !== null) {
                        return ['product_price' => $product_price, 'msrp_price' => $msrp_price];
                    }
                }
            }
    
            return null;
    
        } catch (Exception $e) {
            return "Error fetching product details: " . $e->getMessage();
        }
    }

    private function fetchColorProductImagesById($color_product_id) {
        $color_product_images_sql = "SELECT * FROM color_product_images WHERE color_product_id = ? ORDER BY created_at DESC LIMIT 1";
    
        $stmt = $this->con->prepare($color_product_images_sql);
        $stmt->bind_param("i", $color_product_id);
    
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        if ($result === false) {
            return false;
        }
    
        $images = [];
    
        while ($row = $result->fetch_assoc()) {
            $images[] = $row['product_image']; 
        }
    
        $stmt->close();
    
        return $images;
    }

    private function fetchColorById($product_id) {
        $color_product_sql = "SELECT * FROM color_product WHERE product_id = ?";
        
        $stmt = $this->con->prepare($color_product_sql);
        $stmt->bind_param("i", $product_id);
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if ($result === false) {
            return false;
        }
        
        $colors = [];
        $product_image = null; // Initialize with a default value
        
        while ($row = $result->fetch_assoc()) {
            $color_product_id = $row['color_product_id'];
            $color_name = $row['product_color']; 
        
            // Fetch color information from product_color table
            $color_info_sql = "SELECT color_value, color_type FROM product_color WHERE color_name = ?";
            $color_info_stmt = $this->con->prepare($color_info_sql);
            $color_info_stmt->bind_param("s", $color_name);
            $color_info_stmt->execute();
        
            $color_info_result = $color_info_stmt->get_result();
        
            if ($color_info_result === false) {
                $color_info_stmt->close();
                return false;
            }
        
            $color_info = $color_info_result->fetch_assoc();
        
            $color_images = $this->fetchColorProductImagesById($color_product_id);
            
            if (!empty($color_images)) {
                $product_image = $color_images[0];
            }
    
            $colors[] = [
                'color_product_id' => $color_product_id,
                'color_name' => $color_name,
                'color_value' => $color_info['color_value'],
                'color_type' => $color_info['color_type'],
                'product_image' => $product_image,
            ];
        
            $color_info_stmt->close();
        }
        
        $stmt->close();
    
        return [
            'colors' => $colors,
            'product_image' => $product_image,
        ];
    }
    

    public function fetchNewArrivalProducts(){
        $fetch_new_arrival_product_sql = "SELECT product_id, product_category_type, product_name FROM product ORDER BY created_at DESC LIMIT 4";

        $stmt = $this->con->prepare($fetch_new_arrival_product_sql);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result === false) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to get product"]);
        } else {
            $products = [];

            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $product_category_type = $row['product_category_type'];
                $product_name = $row['product_name'];

                // Fetch product images for the current product_id
                $product_images = $this->fetchProductImages($product_id);
                $product_price = $this->fetchProductDetailsById($product_id);

                // Add the product and its images to the $products array
                $products[] = [
                    "new_arrival_product" => [
                        "product_id" => $product_id,
                        "product_category_type" => $product_category_type,
                        "product_name" => $product_name,
                        "product_image" => $product_images,
                        "product_price" => $product_price
                    ]
                ];
            }

            $stmt->close();

            if (!empty($products)) {
                http_response_code(200);
                echo json_encode($products);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "No_products_found"]);
            }
        }
    }
    
    public function fetchAllProductWithAllDetails(){
        
        // $shop_all_product_sql = "SELECT * FROM product";


        // $whereClause = [];

        // if (isset($_GET['category_name']) && !empty($_GET['category_name'])) {
        //     $category_names = explode(',', $_GET['category_name']);
        //     $category_names = array_map('trim', $category_names);

        //     $category_condition = implode("' OR product_category_name = '", $category_names);
        //     $whereClause[] = "product_category_name = '$category_condition'";   
        // }

        // if (isset($_GET['sort_by']) && !empty($_GET['sort_by'])) {
        //     $sort_by = $_GET['sort_by'];

        //     switch ($sort_by) {
        //         case 'asc_product':
        //             $whereClause[] = "1"; 
        //             $orderByClause = " ORDER BY product_name ASC";
        //             break;
        //         case 'desc_product':
        //             $whereClause[] = "1";
        //             $orderByClause = " ORDER BY product_name DESC";
        //             break;
        //         case 'low_to_high':
        //             $orderByClause = " ORDER BY product_price ASC";
        //             break;
        //         case 'high_to_low':
        //             $orderByClause = " ORDER BY product_price DESC";
        //             break;
        //     }
        // }


        $shop_all_product_sql = "SELECT DISTINCT p.* FROM product AS p";
        // echo json_encode($shop_all_product_sql);

        $whereClause = [];

        if (isset($_GET['category_name']) && !empty($_GET['category_name'])) {
            $category_names = explode(',', $_GET['category_name']);
            $category_names = array_map('trim', $category_names);

            $category_condition = implode("' OR p.product_category_name = '", $category_names);
            $whereClause[] = "p.product_category_name = '$category_condition'";
        }

        if (isset($_GET['size']) && !empty($_GET['size'])) {
            $sizes = explode(',', $_GET['size']);
            $sizes = array_map('trim', $sizes);

            $size_condition = implode("', '", $sizes);

            $whereClause[] = "EXISTS (SELECT 1 FROM product_details pd WHERE pd.product_id = p.product_id AND pd.product_size IN ('$size_condition'))";
        }

        if (isset($_GET['color_name']) && !empty($_GET['color_name'])) {
            $color = trim($_GET['color_name']);
            $whereClause[] = "p.product_id IN (SELECT DISTINCT product_id FROM color_product WHERE product_color = '$color')";
        }

        if (isset($_GET['min_price']) && isset($_GET['max_price']) && !empty($_GET['min_price']) && !empty($_GET['max_price'])) {
            $min_price = floatval($_GET['min_price']);
            $max_price = floatval($_GET['max_price']);
            
            $whereClause[] = "p.product_price BETWEEN $min_price AND $max_price";
        }
        
        if (isset($_GET['sort_by']) && !empty($_GET['sort_by'])) {
            $sort_by = $_GET['sort_by'];

            switch ($sort_by) {
                case 'asc_product':
                    $whereClause[] = "1";
                    $orderByClause = " ORDER BY p.product_name ASC";
                    break;
                case 'desc_product':
                    $whereClause[] = "1";
                    $orderByClause = " ORDER BY p.product_name DESC";
                    break;
                case 'low_to_high':
                    $orderByClause = " ORDER BY p.product_price ASC";
                    break;
                case 'high_to_low':
                    $orderByClause = " ORDER BY p.product_price DESC";
                    break;
            }
        }

        // Construct the final SQL query
        if (!empty($whereClause)) {
            $shop_all_product_sql .= " WHERE " . implode(' AND ', $whereClause);
        }

        if (isset($orderByClause)) {
            $shop_all_product_sql .= $orderByClause;
        }

        $stmt = $this->con->prepare($shop_all_product_sql);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result === false) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to get product"]);
        } else {
            $products = [];

            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $product_category_type = $row['product_category_type'];
                $product_name = $row['product_name'];
                $product_description = $row['product_description'];

                $product_images = $this->fetchProductImages($product_id);

                $product_details = $this->fetchProductDetailsById($product_id);
                $color_products_details = $this->fetchColorById($product_id);
                $product_image = $color_products_details['product_image'];
                $color_products = $color_products_details['colors'];
                $product_price = $product_details['product_price'];
                $msrp_price = $product_details['msrp_price'];

                $products[] = [
                    "shop_all_product" => [
                        "product_id" => $product_id,
                        "product_name" => $product_name,
                        "product_description" => $product_description,
                        "product_image" => $product_image,
                        "product_price" => $product_price,
                        "msrp_price" => $msrp_price,
                        "color" => $color_products
                    ]
                ];
            }

            $stmt->close();

            if (!empty($products)) {
                http_response_code(200);
                echo json_encode($products);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "No_products_found"]);
            }
        }
    }

    public function fetchAllProductFilters() {
        $response = array();
    
        // Load categories
        $load_category_sql = "SELECT DISTINCT category_name FROM category";
        $result = $this->con->query($load_category_sql);
    
        if ($result) {
            $categories = array();
    
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row['category_name'];
            }
    
            if (count($categories) > 0) {
                $response['category'] = $categories;
            } else {
                http_response_code(404);
                echo json_encode('No categories found');
            }
        } else {
            http_response_code(500);
            echo json_encode('Error loading categories: ' . $this->con->error);
        }
    
        // Load sizes
        $load_sizes_sql = "SELECT DISTINCT product_size FROM color_product_details";
        $result = $this->con->query($load_sizes_sql);
    
        if ($result) {
            $sizes = array();
    
            while ($row = $result->fetch_assoc()) {
                $sizes[] = $row['product_size'];
            }
    
            if (count($sizes) > 0) {
                $response['sizes'] = $sizes;
            } else {
                http_response_code(404);
                echo json_encode('No sizes found');
            }
        } else {
            http_response_code(500);
            echo json_encode('Error loading sizes: ' . $this->con->error);
        }
    
        // Load colors
        $select_all_color_sql = "SELECT * FROM product_color";
        $result = $this->con->query($select_all_color_sql);
    
        if ($result) {
            $colors = $result->fetch_all(MYSQLI_ASSOC);
            $response['colors'] = $colors;
        } else {
            http_response_code(500);
            echo json_encode('Error loading colors: ' . $this->con->error);
        }

        // Maximum Product Price

        $load_max_price_sql = "SELECT MAX(product_price) AS max_price FROM product";
        $result = $this->con->query($load_max_price_sql);

        if ($result) {
            $row = $result->fetch_assoc();
            $maxPrice = $row['max_price'];
            $response['max_price'] = $maxPrice;
        } else {
            http_response_code(500);
            echo json_encode('Error loading maximum price: ' . $this->con->error);
            return;
        }
    
        echo json_encode($response);
    }

    public function newArrivals(){
        // Assuming $mysqli is your database connection
        $get_new_arrival_products_sql = "SELECT * FROM product ORDER BY created_at DESC LIMIT 4";
        $result = $this->con->query($get_new_arrival_products_sql);
    
        $products = []; // Initialize an array to store product data
    
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $product_category_type = $row['product_category_type'];
            $product_name = $row['product_name'];
            $product_description = $row['product_description'];
    
            $product_images = $this->fetchProductImages($product_id);
    
            $product_details = $this->fetchProductDetailsById($product_id);
            $color_products_details = $this->fetchColorById($product_id);
            $product_image = $color_products_details['product_image'];
            $color_products = $color_products_details['colors'];
            $product_price = $product_details['product_price'];
            $msrp_price = $product_details['msrp_price'];
    
            $products[] = [
                "shop_all_product" => [
                    "product_id" => $product_id,
                    "product_name" => $product_name,
                    "product_description" => $product_description,
                    "product_image" => $product_image,
                    "product_price" => $product_price,
                    "msrp_price" => $msrp_price,
                    "color" => $color_products
                ]
            ];
        }
    
        echo json_encode($products); 
    }

    public function addWishlist($product_id){
        $dealer_id = $_COOKIE['dealer_id'];
    
        // Check if the product already exists in the wishlist
        $check_wishlist_exist_sql = "SELECT product_id FROM wishlist WHERE dealer_id=? AND product_id=?";
        $stmt_check = $this->con->prepare($check_wishlist_exist_sql);
        $stmt_check->bind_param("ii", $dealer_id, $product_id);
        $stmt_check->execute();
        $stmt_check->store_result();
    
        if ($stmt_check->num_rows > 0) {
            $stmt_check->close();
            http_response_code(400);
            echo json_encode("Product_already_exists");
        } else {
            // Fetch product details from the product table
            $get_product_details_sql = "SELECT product_name, product_description FROM product WHERE product_id=?";
            $stmt_product = $this->con->prepare($get_product_details_sql);
            $stmt_product->bind_param("i", $product_id);
            $stmt_product->execute();
            $stmt_product->bind_result($product_name, $product_description);
            $stmt_product->fetch();
            $stmt_product->close();
    
            // Insert values into the wishlist
            $add_to_wishlist_sql = "INSERT INTO wishlist (dealer_id, product_id, product_name, product_description) VALUES (?, ?, ?, ?)";
            $stmt_add = $this->con->prepare($add_to_wishlist_sql);
            $stmt_add->bind_param("iiss", $dealer_id, $product_id, $product_name, $product_description);
    
            if ($stmt_add->execute()) {
                $stmt_add->close();
                http_response_code(200);
                echo json_encode("Product_added_wishlist_successfully");
            } else {
                $stmt_add->close();
                http_response_code(500);
                echo json_encode("Error adding product to the wishlist: " . $this->con->error);
            }
        }
    }

    public function showWishlistItems(){
        $dealer_id = $_COOKIE['dealer_id'];
        $get_wishlist_products_sql = "SELECT * FROM wishlist WHERE dealer_id=?";
        
        $stmt = $this->con->prepare($get_wishlist_products_sql);
        $stmt->bind_param("i", $dealer_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $products = []; 
        
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $wishlist_id = $row['wishlist_id'];
            $product_name = $row['product_name'];
            $product_description = $row['product_description'];
    
            // Fetch product details
            $product_details = $this->fetchProductDetailsById($product_id);
            $color_products_details = $this->fetchColorById($product_id);
    
            $product_price = $product_details['product_price'];
            $msrp_price = $product_details['msrp_price'];
            $product_image = $color_products_details['product_image'];
            $color_products = $color_products_details['colors'];
    
            $products[] = [
                "wishlist_product" => [
                    "wishlist_id" => $wishlist_id,
                    "product_id" => $product_id,
                    "product_name" => $product_name,
                    "product_description" => $product_description,
                    "product_image" => $product_image,
                    "product_price" => $product_price,
                    "msrp_price" => $msrp_price,
                    "color" => $color_products
                ]
            ];
        }
    
        $stmt->close();
    
        echo json_encode($products);
    }    

    public function removeWishlist($wishlist_id) {
        $dealer_id = $_COOKIE['dealer_id'];
        $remove_wishlist_sql = "DELETE FROM wishlist WHERE wishlist_id=? AND dealer_id=?";
        $stmt = $this->con->prepare($remove_wishlist_sql);
    
        if (!$stmt) {
            http_response_code(500);
            echo json_encode("Error preparing statement: " . $this->con->error);
            exit;
        }
    
        $stmt->bind_param("ii", $wishlist_id,$dealer_id);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode("Wishlist_item_removed_successfully");
        } else {
            http_response_code(404);
            echo json_encode("Wishlist item not found");
        }
    
        $stmt->close();
    }

    public function wishlistCount() {
        $dealer_id = $_COOKIE['dealer_id'];
    
        // Count from wishlist table
        $wishlist_count_sql = "SELECT COUNT(*) AS wishlist_count FROM wishlist WHERE dealer_id=?";
        $stmt_wishlist = $this->con->prepare($wishlist_count_sql);
    
        if (!$stmt_wishlist) {
            http_response_code(500);
            echo json_encode("Error preparing statement for wishlist: " . $this->con->error);
            exit;
        }
    
        $stmt_wishlist->bind_param("i", $dealer_id);
        $stmt_wishlist->execute();
        $stmt_wishlist->bind_result($wishlist_count);
        $stmt_wishlist->fetch();
        $stmt_wishlist->close();
    
        // Count from dealer_cart table
        $dealer_cart_count_sql = "SELECT COUNT(*) AS dealer_cart_count FROM dealer_cart WHERE dealer_id=?";
        $stmt_cart = $this->con->prepare($dealer_cart_count_sql);
    
        if (!$stmt_cart) {
            http_response_code(500);
            echo json_encode("Error preparing statement for dealer_cart: " . $this->con->error);
            exit;
        }
    
        $stmt_cart->bind_param("i", $dealer_id);
        $stmt_cart->execute();
        $stmt_cart->bind_result($dealer_cart_count);
        $stmt_cart->fetch();
        $stmt_cart->close();
    
        http_response_code(200);
        echo json_encode(["wishlist_count" => $wishlist_count, "cart_count" => $dealer_cart_count]);
    }

    public function fetchProductByCategory() {
        if (isset($_GET['main_category']) && isset($_GET['category']) && isset($_GET['sub_category'])) {
            $main_category = $_GET['main_category'];
            $category = $_GET['category'];
            $sub_category = $_GET['sub_category'];
            $get_product_by_category_sql = "SELECT * FROM product WHERE product_category_type=? AND product_category_name=? AND product_sub_category_name=?";
            $stmt = $this->con->prepare($get_product_by_category_sql);
            $stmt->bind_param("sss", $main_category, $category, $sub_category);
        } elseif (isset($_GET['main_category']) && isset($_GET['category'])) {
            $main_category = $_GET['main_category'];
            $category = $_GET['category'];
            $get_product_by_category_sql = "SELECT * FROM product WHERE product_category_type=? AND product_category_name=?";
            $stmt = $this->con->prepare($get_product_by_category_sql);
            $stmt->bind_param("ss", $main_category, $category);
        } elseif (isset($_GET['main_category'])) {
            $category = $_GET['main_category'];
            $get_product_by_category_sql = "SELECT * FROM product WHERE product_category_type=?";
            $stmt = $this->con->prepare($get_product_by_category_sql);
            $stmt->bind_param("s", $category);
        }
    
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        $products = [];
    
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $product_name = $row['product_name'];
            $product_description = $row['product_description'];
    
            // Fetch product details
            $product_details = $this->fetchProductDetailsById($product_id);
            $color_products_details = $this->fetchColorById($product_id);
    
            $product_price = $product_details['product_price'];
            $msrp_price = $product_details['msrp_price'];
            $product_image = $color_products_details['product_image'];
            $color_products = $color_products_details['colors'];
    
            $products[] = [
                "category_product" => [
                    "product_id" => $product_id,
                    "product_name" => $product_name,
                    "product_description" => $product_description,
                    "product_image" => $product_image,
                    "product_price" => $product_price,
                    "msrp_price" => $msrp_price,
                    "color" => $color_products
                ]
            ];
        }
    
        $stmt->close();
    
        echo json_encode($products);
    }

    public function relatedProducts($product_id) {
        $query = "SELECT product_category_name FROM product WHERE product_id = ?";
        
        $stmt = $this->con->prepare($query);
        $stmt->bind_param("i", $product_id);
        
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode("Error fetching product category");
            exit();
        }
        
        $stmt->bind_result($product_category);
        
        if ($stmt->fetch()) {
            $category = $product_category;
        } else {
            http_response_code(500);
            echo json_encode("Error fetching product category");
            exit();
        }
    
        $stmt->close();
    
        $get_products_by_category_sql = "SELECT product_id, product_name, product_price FROM product WHERE product_category_name = ? LIMIT 8";
    
        $stmt = $this->con->prepare($get_products_by_category_sql);
        $stmt->bind_param("s", $category);
        
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode("Error fetching products by category");
            exit();
        }
    
        $result = $stmt->get_result();
    
        if (!$result) {
            http_response_code(500);
            echo json_encode("Error fetching products by category");
            exit();
        }
    
        $products = array();
        while ($row = $result->fetch_assoc()) {
            $product_id = $row['product_id'];
            $productImageQuery = "SELECT product_image FROM product_images WHERE product_id = ? LIMIT 1";
            $imageStmt = $this->con->prepare($productImageQuery);
            $imageStmt->bind_param("i", $product_id);
            
            if (!$imageStmt->execute()) {
                http_response_code(500);
                echo json_encode("Error fetching product image");
                exit();
            }
    
            $imageStmt->bind_result($product_image);
    
            if ($imageStmt->fetch()) {
                $row['product_image'] = $product_image;
            }
    
            $imageStmt->close();
    
            $products[] = $row;
        }
    
        http_response_code(200);
        echo json_encode($products);
        $stmt->close();
    }

    public function searchProduct($searchArray){
        
    
        $query = "SELECT product_id,product_name FROM product WHERE 1";
    
        $params = array();
    
        if(isset($searchArray['search_category'])){
            $query .= " AND product_category_type = ?";
            $params[] = $searchArray['search_category'];
        }
    
        if(isset($searchArray['search_product'])){
            $query .= " AND product_name LIKE ?";
            $params[] = '%' . $searchArray['search_product'] . '%';
        }
    
        $stmt = $this->con->prepare($query);
    
        if ($stmt) {
            $types = str_repeat('s', count($params));  
            $stmt->bind_param($types, ...$params);
    
            $stmt->execute();
    
            $result = $stmt->get_result();
    
            $results = $result->fetch_all(MYSQLI_ASSOC);
    
            echo json_encode($results);
            
            $stmt->close();
        }
    
    }

    public function getProductAlerts(){
        $get_product_alerts = "SELECT * FROM product_alert ORDER BY created_at DESC";
        
        $result = $this->con->query($get_product_alerts);
    
        if (!$result) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to fetch product alerts"]);
            exit();
        }
    
        $productAlerts = [];
        
        while ($row = $result->fetch_assoc()) {
            $colorProductId = $row['color_product_id'];
            $size = $row['product_size'];
    
            $colorProductInfo = $this->alertProductName($colorProductId, $size);
    
            $mergedData = array_merge($row, $colorProductInfo);
    
            $productAlerts[] = $mergedData;
        }
    
        $result->close();
    
        http_response_code(200);
        echo json_encode($productAlerts);
    }
    
    public function alertProductName($colorProductId, $size) {
        $product_name = '';
    
        $get_name_sql = "SELECT product_name FROM color_product WHERE color_product_id = ?";
        $stmt1 = $this->con->prepare($get_name_sql);
        $stmt1->bind_param("i", $colorProductId);
    
        if (!$stmt1->execute()) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to fetch product name"]);
            exit();
        }
    
        $stmt1->bind_result($product_name);
    
        if (!$stmt1->fetch()) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to fetch product name"]);
            exit();
        }
    
        $stmt1->close(); 
    
        $colorProductInfo = ['product_name' => $product_name];
        
        $get_quantity_sql = "SELECT product_quantity FROM color_product_details WHERE color_product_id = ? AND product_size = ?";
        $stmt2 = $this->con->prepare($get_quantity_sql);
        $stmt2->bind_param("is", $colorProductId, $size);
    
        if (!$stmt2->execute()) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to fetch product quantity"]);
            exit();
        }
    
        $stmt2->bind_result($product_quantity);
    
        if ($stmt2->fetch()) {
            $colorProductInfo['product_quantity'] = $product_quantity;
        } else {
            $colorProductInfo['product_quantity'] = 0;
        }
    
        $stmt2->close();
    
        return $colorProductInfo;
    }

    public function updateProductQuantity($product_alert_id, $color_product_id, $product_size, $updatedQuantity){
        $update_quantity_sql = "UPDATE color_product_details SET product_quantity = ? WHERE color_product_id = ? AND product_size = ?";
        
        $stmt = $this->con->prepare($update_quantity_sql);
        
        $stmt->bind_param("iss", $updatedQuantity, $color_product_id, $product_size);
        
        if ($stmt->execute()) {
            $stmt->close();
            
            $delete_alert_sql = "DELETE FROM product_alert WHERE product_alert_id = ?";
            $stmt_delete = $this->con->prepare($delete_alert_sql);
            $stmt_delete->bind_param("i", $product_alert_id);
            
            if ($stmt_delete->execute()) {
                $stmt_delete->close();
                $this->addToStockUpdateTable($color_product_id, $product_size, $updatedQuantity);
                http_response_code(200);
                echo json_encode("success");

            } else {
                $stmt_delete->close();
                http_response_code(500);
                echo json_encode("Failed to delete product alert after updating quantity");
            }
        } else {
            $stmt->close();
            http_response_code(500);
            echo json_encode("Failed to update product quantity");
        }
    }

    private function addToStockUpdateTable($color_product_id, $product_size, $updatedQuantity) {
        $insert_query = "INSERT INTO stock_update (color_product_id, product_size, updated_quantity, created_at) VALUES (?, ?, ?, NOW())";
    
        $stmt = $this->con->prepare($insert_query);
    
        if ($stmt === false) {
            http_response_code(500);
            echo json_encode("Error preparing SQL statement: " . $this->con->error);
            exit();
        }
    
        $stmt->bind_param("iss", $color_product_id, $product_size, $updatedQuantity);
    
        if ($stmt->execute()) {
            $stmt->close();
        } else {
            $stmt->close();
            http_response_code(500);
            echo json_encode("Error executing SQL statement: " . $this->con->error);
            exit();
        }
    }

    public function updateProductDate($product_alert_id, $color_product_id, $product_size, $updatedDate){
        if (strtotime($updatedDate) >= strtotime(date('Y-m-d'))) {
            $update_date_sql = "UPDATE product_alert SET arrive_date = ? WHERE product_alert_id = ?";
            
            $stmt = $this->con->prepare($update_date_sql);
            
            $stmt->bind_param("si", $updatedDate, $product_alert_id);
            
            if ($stmt->execute()) {
                $stmt->close();
                http_response_code(200);
                echo json_encode("success");
            } else {
                $stmt->close();
                http_response_code(500);
                echo json_encode("Failed to update arrival date");
            }
        } else {
            http_response_code(400);
            echo json_encode("Updated_date_cannot_be_earlier_than_the_current_date");
        }
    }
    
}

class colorProduct{
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function addColorProduct($product_details,$product_images){
            $product_id = $product_details['product_id'];
            $product_name = $product_details['color_product_name'];
            $category_type = $product_details['color_category_type'];
            $category_name = $product_details['color_category_name'];
            $sub_category_name = $product_details['color_sub_category_name'];
            // $product_color = $product_details['color_product_color'];
            $product_description = $product_details['color_product_description'];
            $product_specification = $product_details['color_product_specification'];

            if (isset($product_details['color_product_color'])) {
                $product_color = $product_details['color_product_color'];
            } elseif (isset($product_images['product_color_file']['name'])) {
                $color_value = $product_images['product_color_file']['name'];
            
                if (empty($product_details['product_color_name'])) {
                    http_response_code(404);
                    echo json_encode("Color_Name_Is_Empty");
                    return;
                } else {
                    $product_color = $product_details['product_color_name'];
            
                    // Check if the color already exists
                    $check_color_query = "SELECT COUNT(*) FROM product_color WHERE color_name = ?";
                    $check_color_stmt = mysqli_prepare($this->con, $check_color_query);
                    mysqli_stmt_bind_param($check_color_stmt, "s", $product_color);
                    mysqli_stmt_execute($check_color_stmt);
                    mysqli_stmt_bind_result($check_color_stmt, $color_count);
                    mysqli_stmt_fetch($check_color_stmt);
                    mysqli_stmt_close($check_color_stmt);
            
                    if ($color_count > 0) {
                        http_response_code(400);
                        echo json_encode("Color_Already_Exists");
                        return;
                    }
            
                    $targetPath = '../color_images/' . $color_value;
            
                    if (move_uploaded_file($product_images['product_color_file']['tmp_name'], $targetPath)) {
                        $color_type = "image";
                        $color_insert_query = "INSERT INTO product_color (color_name, color_value, color_type) VALUES (?, ?, ?)";
                        $color_insert_stmt = mysqli_prepare($this->con, $color_insert_query);
                        mysqli_stmt_bind_param($color_insert_stmt, "sss", $product_color, $color_value, $color_type);
            
                        if (mysqli_stmt_execute($color_insert_stmt)) {
                            http_response_code(200);
                            // echo "Color_added_successfully";
                        } else {
                            http_response_code(500); 
                            echo "Error adding color: " . mysqli_error($this->con);
                        }
            
                        mysqli_stmt_close($color_insert_stmt);
                    } else {
                        http_response_code(500); 
                        echo "Error moving uploaded file.";
                    }
                }
            }
        
            // Check if the product already exists
            $check_product_sql = "SELECT COUNT(*) FROM color_product WHERE 
                                    product_name = ? AND 
                                    product_category_type = ? AND 
                                    product_category_name = ? AND 
                                    product_sub_category_name = ? AND 
                                    product_color = ?";
            $check_product_stmt = mysqli_prepare($this->con, $check_product_sql);
            mysqli_stmt_bind_param($check_product_stmt, "sssss", $product_name, $category_type, $category_name, $sub_category_name, $product_color);
            mysqli_stmt_execute($check_product_stmt);
            mysqli_stmt_bind_result($check_product_stmt, $count);
            mysqli_stmt_fetch($check_product_stmt);
            mysqli_stmt_close($check_product_stmt);
        
            if ($count > 0) {
                http_response_code(400);
                echo json_encode("Product_already_exists");
            } else {
                // Insert product details into the "product" table
                $add_product_sql = "INSERT INTO color_product (product_id, product_name, product_category_type, product_category_name, product_sub_category_name, product_color, product_description, product_specification)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $add_product_stmt = mysqli_prepare($this->con, $add_product_sql);
                mysqli_stmt_bind_param($add_product_stmt, "isssssss", $product_id, $product_name, $category_type, $category_name, $sub_category_name, $product_color, $product_description, $product_specification);
        
                if (mysqli_stmt_execute($add_product_stmt)) {
                    // Get the last inserted product ID
                    $color_product_id = mysqli_insert_id($this->con);
        
                    // Insert product details into the "product_details" table for each size, quantity, price, and msrp price
                    $size_array = $product_details['color_product_size'];
                    $quantity_array = $product_details['color_product_quantity'];
                    $price_array = $product_details['color_product_price'];
                    $msrp_array = $product_details['color_msrp_price'];
        
                    for ($i = 0; $i < count($size_array); $i++) {
                        $size = $size_array[$i];
                        $quantity = $quantity_array[$i];
                        $price = $price_array[$i];
                        $msrp = $msrp_array[$i];
        
                        // Insert product details into the "product_details" table
                        $add_product_details_sql = "INSERT INTO color_product_details (product_id, color_product_id, product_color, product_size, product_quantity, product_price, product_msrp_price)
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                        $add_product_details_stmt = mysqli_prepare($this->con, $add_product_details_sql);
                        mysqli_stmt_bind_param($add_product_details_stmt, "iissiii", $product_id, $color_product_id, $product_color, $size, $quantity, $price, $msrp);
                        mysqli_stmt_execute($add_product_details_stmt);
                        mysqli_stmt_close($add_product_details_stmt);
                    }
    
                    $imageUploadPath = "../product_images/";
    
                    for ($i = 0; $i < count($product_images['color_product_image']['name']); $i++) {
                        $imageName = $product_images['color_product_image']['name'][$i];
                        $imageTmpName = $product_images['color_product_image']['tmp_name'][$i];
                        $imagePath = $imageUploadPath . $imageName;
    
                        $allowedExtensions = ['jpg', 'jpeg', 'png'];
                        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);
    
                        if (in_array(strtolower($imageExtension), $allowedExtensions)) {
                            move_uploaded_file($imageTmpName, $imagePath);
    
                            $add_image_sql = "INSERT INTO color_product_images (product_id, color_product_id, product_image)
                                            VALUES (?, ?, ?)";
                            $add_image_stmt = mysqli_prepare($this->con, $add_image_sql);
                            mysqli_stmt_bind_param($add_image_stmt, "iis", $product_id, $color_product_id, $imageName);
                            mysqli_stmt_execute($add_image_stmt);
                            mysqli_stmt_close($add_image_stmt);
                        } else {
                            http_response_code(400); // Bad Request
                            echo "Invalid_file_format";
                            exit;
                        }
                    }
        
                    echo "Product_added_successfully";
                } else {
                    echo "Error adding product: " . mysqli_error($this->con);
                }
        
                // Close the prepared statements
                mysqli_stmt_close($add_product_stmt);
            }
    }

    public function fetchColorProduct($color_product_id){
        
        
    
        $response = array();
    
        $query = "SELECT * FROM color_product WHERE product_id = ?";
        $stmt = mysqli_prepare($this->con, $query);
        mysqli_stmt_bind_param($stmt, "i", $color_product_id);
    
        $result = mysqli_stmt_execute($stmt);
    
        if ($result) {
            $products = array();
            $result_set = mysqli_stmt_get_result($stmt);
            
            while ($row = mysqli_fetch_assoc($result_set)) {
                $products[] = $row;
            }
            mysqli_free_result($result_set);
    
            $response['status'] = 'success';
            
            if (empty($products)) {
                $response['message'] = 'No_products_found';
            } else {
                $response['products'] = $products;
            }

            http_response_code(200);
            echo json_encode($response);
        } else {
            http_response_code(400);
    
            echo json_encode(array('status' => 'error', 'message' => 'Error fetching products'));
        }
    
        mysqli_stmt_close($stmt);
    
        return $response;
    }
    

    public function deleteColorProduct($colorProductId){
            $get_images_sql = "SELECT product_image FROM color_product_images WHERE color_product_id = ?";
            $get_images_stmt = mysqli_prepare($this->con, $get_images_sql);
            mysqli_stmt_bind_param($get_images_stmt, "i", $colorProductId);
            mysqli_stmt_execute($get_images_stmt);
            mysqli_stmt_bind_result($get_images_stmt, $image_name);
        
            // Store the image names in an array
            $image_names = [];
            while (mysqli_stmt_fetch($get_images_stmt)) {
                $image_names[] = $image_name;
            }

            // echo json_encode($image_names);
        
            mysqli_stmt_close($get_images_stmt);
        
            // Delete from product_images table
            $delete_images_sql = "DELETE FROM color_product_images WHERE color_product_id = ?";
            $delete_images_stmt = mysqli_prepare($this->con, $delete_images_sql);
            mysqli_stmt_bind_param($delete_images_stmt, "i", $colorProductId);
            $delete_images_result = mysqli_stmt_execute($delete_images_stmt);
            mysqli_stmt_close($delete_images_stmt);
        
            // Delete images from the product_images folder
            $imageUploadPath = "../product_images/";
        
            foreach ($image_names as $image_name) {
                $imagePath = $imageUploadPath . $image_name;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                } else {
                    echo "File not found: " . $imagePath . "<br>";
                }
            }
        
            // Delete from product_details table
            $delete_details_sql = "DELETE FROM color_product_details WHERE color_product_id = ?";
            $delete_details_stmt = mysqli_prepare($this->con, $delete_details_sql);
            mysqli_stmt_bind_param($delete_details_stmt, "i", $colorProductId);
            $delete_details_result = mysqli_stmt_execute($delete_details_stmt);
            mysqli_stmt_close($delete_details_stmt);
        
            // Delete from product table
            $delete_product_sql = "DELETE FROM color_product WHERE color_product_id = ?";
            $delete_product_stmt = mysqli_prepare($this->con, $delete_product_sql);
            mysqli_stmt_bind_param($delete_product_stmt, "i", $colorProductId);
            $delete_product_result = mysqli_stmt_execute($delete_product_stmt);
            mysqli_stmt_close($delete_product_stmt);
    
            $row_count_sql = "SELECT COUNT(*) FROM color_product";
            $row_count_result = mysqli_query($this->con, $row_count_sql);
            $row_count = mysqli_fetch_array($row_count_result)[0];
            
            if ($delete_images_result && $delete_details_result && $delete_product_result) {
                if ($row_count == 0) {
                    http_response_code(200);
                    echo "Last_product_deleted_successfully";
                } else {
                    http_response_code(200);
                    echo "Product_deleted_successfully";
                }
            } else {
                http_response_code(400);
                echo "Error_deleting_product";
            }
    }

    public function fetchColorProductById($color_product_id){
        // Fetch product details
        $get_color_product_by_id_sql = "SELECT * FROM color_product WHERE color_product_id = ?";
        $get_color_product_by_id_stmt = mysqli_prepare($this->con, $get_color_product_by_id_sql);
        mysqli_stmt_bind_param($get_color_product_by_id_stmt, "i", $color_product_id);
    
        $color_product = array();
    
        if (mysqli_stmt_execute($get_color_product_by_id_stmt)) {
            $result = mysqli_stmt_get_result($get_color_product_by_id_stmt);
    
            if ($row = mysqli_fetch_assoc($result)) {
                $color_product = $row;
            }
    
            mysqli_free_result($result);
        }
    
        mysqli_stmt_close($get_color_product_by_id_stmt);
    
    
        // Send the response as JSON with both product details and images
        header('Content-Type: application/json');
        echo json_encode($color_product);

    }

    public function editColorProduct($product_details) {
        $product_id = $product_details['color_product_id'];
        $product_name = $product_details['product_name'];
        $category_type = $product_details['category_type'];
        $category_name = $product_details['category_name'];
        $sub_category_name = $product_details['sub_category_name'];
        $product_color = $product_details['product_color'];
        $product_description = $product_details['product_description'];
        $product_specification = $product_details['product_specification'];
    
        // Update product details with prepared statement
        $update_product_details_sql = "UPDATE color_product SET 
            product_name = ?,
            product_category_type = ?,
            product_category_name = ?,
            product_sub_category_name = ?,
            product_color = ?,
            product_description = ?,
            product_specification = ?
            WHERE color_product_id = ?";
    
        $stmt = mysqli_prepare($this->con, $update_product_details_sql);
        mysqli_stmt_bind_param($stmt, "sssssssi", $product_name, $category_type, $category_name, $sub_category_name, $product_color, $product_description, $product_specification, $product_id);
    
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header('Content-Type: application/json');
            echo json_encode(['message' => 'product_updated_successfully']);
        } else {
            mysqli_stmt_close($stmt);
            http_response_code(500); // Internal Server Error
            header('Content-Type: application/json');
            echo json_encode(['message' => 'product_update_failed']);
        }
    }

    public function getColorProductImageById($color_product_id){
        $get_color_product_images_sql = "SELECT product_image FROM color_product_images WHERE color_product_id=$color_product_id";

        $result = $this->con->query($get_color_product_images_sql);
    
        if ($result->num_rows > 0) {
            $images = [];
            while ($row = $result->fetch_assoc()) {
                $images[] = $row['product_image'];
            }
    
            $this->con->close();
    
            http_response_code(200);
            echo json_encode(['images' => $images]);
        } else {
            $this->con->close();
    
            http_response_code(404);
            echo json_encode(['error' => 'no_images_found']);
        }

    }

    public function deleteProductImage($imageName, $color_product_id) {
        $delete_image_sql = "DELETE FROM color_product_images WHERE color_product_id = ? AND product_image = ?";
        $delete_image_stmt = mysqli_prepare($this->con, $delete_image_sql);
    
        mysqli_stmt_bind_param($delete_image_stmt, "is", $color_product_id, $imageName);
    
        $success = mysqli_stmt_execute($delete_image_stmt);
    
        mysqli_stmt_close($delete_image_stmt);
    
        $image_path = "../product_images/" . $imageName;
        if ($success && file_exists($image_path)) {
            unlink($image_path);
            $response = ['code' => 200, 'message' => 'Image_deleted_successfully'];
        } else {
            $response = ['code' => 500, 'error' => 'Failed to delete image'];
        }
    
        // Send the JSON response
        header('Content-Type: application/json');
        http_response_code($response['code']);
        echo json_encode($response);
    }

    public function addColorProductImage($product_id,$color_product_id, $image_names) {
        $successCount = 0;
        $upload_directory = '../product_images/';
    
        foreach ($image_names as $image_name) {
            $target_path = $upload_directory . $image_name;
            $query = "INSERT INTO color_product_images (product_id,color_product_id, product_image) VALUES (?,  ?, ?)";
            $stmt = $this->con->prepare($query);
            $stmt->bind_param("iis", $product_id, $color_product_id, $image_name);
    
            if ($stmt->execute()) {
                $successCount++;
            } else {
                // Cleanup: Delete uploaded image if query execution fails
                unlink($target_path);
    
                http_response_code(400);
                echo "Failed to insert image into the database.";
                break;
            }
    
            $stmt->close();
        }
    
        if ($successCount == count($image_names)) {
            http_response_code(200);
            echo "image_upload_successfully";
        } else {
            foreach ($image_names as $image_name) {
                unlink($upload_directory . $image_name);
            }
    
            http_response_code(400);
            echo "No images inserted into the database.";
        }
    }

    public function getProductDetailsById($color_product_id) {
        $response = array();
    
        $get_color_product_details_by_id_sql = "SELECT * FROM color_product_details WHERE color_product_id = ?";
        $stmt = $this->con->prepare($get_color_product_details_by_id_sql);
        $stmt->bind_param("i", $color_product_id);
    
        if ($stmt->execute()) {
            $result = $stmt->get_result();
    
            if ($result->num_rows > 0) {
                $productDetails = array();
    
                while ($data = $result->fetch_assoc()) {
                    $productDetails[] = $data;
                }
    
                $response['status'] = 'success';
                $response['productDetails'] = $productDetails;
                http_response_code(200);
            } else {
                http_response_code(404);
                $response['status'] = 'error';
                $response['message'] = 'No_product_details_found';
            }
        } else {
            http_response_code(400);
            $response['status'] = 'error';
            $response['message'] = 'Failed to fetch product details.';
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function deleteColorProductDetails($color_product_id,$color_product_details_id){
        $delete_color_product_details_sql = "DELETE FROM color_product_details WHERE color_product_id = ? AND color_product_detail_id = ?";

        $stmt = $this->con->prepare($delete_color_product_details_sql);
        $stmt->bind_param("ii", $color_product_id, $color_product_details_id);

        $stmt->execute();

        if ($stmt->error) {
            http_response_code(400);
            echo json_encode(["error" => "Error: " . $stmt->error]);
        } else {
            http_response_code(200); 
            echo "Product_details_deleted_successfully";
        }

        $stmt->close();
    }

    public function addMoreColorProductDetails($product_details){
        $product_id = $product_details['product_id'];
        $color_product_id = $product_details['color_product_id'];
        $product_size = $product_details['product_size'];
        $product_quantity = $product_details['product_quantity'];
        $product_price = $product_details['product_price'];
        $msrp_price = $product_details['msrp_price'];
    
        // Fetch product_color from color_product_table
        $color_stmt = $this->con->prepare("SELECT product_color FROM color_product WHERE product_id = ? AND color_product_id = ?");
        $color_stmt->bind_param("ii", $product_id, $color_product_id);
        $color_stmt->execute();
        $color_result = $color_stmt->get_result();
    
        if ($color_result->num_rows > 0) {
            $color_row = $color_result->fetch_assoc();
            $product_color = $color_row['product_color'];
        } else {
            http_response_code(404);
            echo json_encode('Product color not found for the given product_id and color_product_id');
            return;
        }
    
        $stmt = $this->con->prepare("INSERT INTO color_product_details (product_id, color_product_id, product_color, product_size, product_quantity, product_price, product_msrp_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
        $stmt->bind_param("iissiii", $product_id, $color_product_id, $product_color, $product_size, $product_quantity, $product_price, $msrp_price);
    
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode("Details_Added_Successfully");
        } else {
            http_response_code(500);
            echo json_encode('Failed to insert data into the database: ' . $stmt->error);
        }
    
        $stmt->close();
        $color_stmt->close();
    }

    public function editColorProductDetails($productDetails) {
    
        $product_details = $productDetails;
        $color_product_id = $product_details['color_productId'];
        $color_product_details_id = $product_details['color_productDetailsId'];
        $product_size = $product_details['productSize'];
        $product_quantity = $product_details['productQuantity'];
        $product_price = $product_details['productPrice'];
        $msrp_price = $product_details['msrpPrice'];
    
        $edit_color_product_details_sql = "UPDATE color_product_details SET product_size = ?, product_quantity = ?, product_price = ?, product_msrp_price = ? WHERE color_product_detail_id = ? AND color_product_id = ?";
    
        $stmt = $this->con->prepare($edit_color_product_details_sql);
    
        $stmt->bind_param("ssssii", $product_size, $product_quantity, $product_price, $msrp_price, $color_product_details_id, $color_product_id);
    
        $stmt->execute();
    
        if ($stmt->error) {
            http_response_code(400); // Bad Request
            echo json_encode(["error" => "Error: " . $stmt->error]);
        } else {
            http_response_code(200); // OK
            echo "product_details_updated";
        }
    
        $stmt->close();
    }

    public function getAllColorProductById($product_id) {

        // First query to get product_color values
        $select_product_colors_sql = "SELECT product_color FROM color_product WHERE product_id = ?";
        
        $stmt_colors = $this->con->prepare($select_product_colors_sql);
        $stmt_colors->bind_param("i", $product_id);
        $stmt_colors->execute();
        
        $result_colors = $stmt_colors->get_result();
    
        if ($result_colors === false) {
            $error_message = "Error executing the first product color query: " . $this->con->error;
            http_response_code(500); 
            echo json_encode(array('error' => $error_message));
        } else {
            $colorProducts = array();
    
            while ($row_colors = $result_colors->fetch_assoc()) {
                $product_color = $row_colors['product_color'];
    
                // Second query to get color_type and color_value
                $select_color_type_value_sql = "SELECT color_type, color_value FROM product_color WHERE color_name = ?";
                $stmt_type_value = $this->con->prepare($select_color_type_value_sql);
                $stmt_type_value->bind_param("s", $product_color);
                $stmt_type_value->execute();
                $result_type_value = $stmt_type_value->get_result();
    
                if ($result_type_value === false) {
                    $error_message = "Error executing the second product color query: " . $this->con->error;
                    http_response_code(500); 
                    echo json_encode(array('error' => $error_message));
                } else {
                    $row_type_value = $result_type_value->fetch_assoc();
                    $colorProduct = array(
                        'color_product' => $product_color,
                        'color_type' => $row_type_value['color_type'],
                        'color_value' => $row_type_value['color_value']
                    );
    
                    $colorProducts[] = $colorProduct;
    
                    $stmt_type_value->close();
                    $result_type_value->close();
                }
            }
    
            $stmt_colors->close();
            $result_colors->close();
    
            http_response_code(200);
            echo json_encode($colorProducts);
        }
    }

    public function getColorProductByColor($product_id, $color_name) {
    
        $select_product_colors_sql = "SELECT * FROM color_product WHERE product_id = ? AND product_color = ?";
        
        $stmt_colors = $this->con->prepare($select_product_colors_sql);
        $stmt_colors->bind_param("is", $product_id, $color_name);
        $stmt_colors->execute();
        
        $result_colors = $stmt_colors->get_result();
        
        if ($result_colors === false) {
            http_response_code(500);
            echo json_encode(array('error' => "Error executing the first product color query: " . $this->con->error));
        } else {
            $colorProducts = array();
        
            while ($row_colors = $result_colors->fetch_assoc()) {
                $color_product_id = $row_colors['color_product_id'];
        
                // Second query to get all values from color_product_details
                $select_color_product_details_sql = "SELECT product_size,product_price FROM color_product_details WHERE color_product_id = ?";
                $stmt_type_value = $this->con->prepare($select_color_product_details_sql);
                $stmt_type_value->bind_param("i", $color_product_id);
                $stmt_type_value->execute();
                $result_type_value = $stmt_type_value->get_result();
        
                if ($result_type_value === false) {
                    http_response_code(500);
                    echo json_encode(array('error' => "Error executing the second product color query: " . $this->con->error));
                } else {
                    $sizes = array();
                    
                    // Loop through all rows in color_product_details
                    while ($row_type_value = $result_type_value->fetch_assoc()) {
                        $sizes[] = $row_type_value;
                    }
        
                    // Third query to get color_product_images
                    $select_color_product_images_sql = "SELECT product_image FROM color_product_images WHERE color_product_id = ?";
                    $stmt_images = $this->con->prepare($select_color_product_images_sql);
                    $stmt_images->bind_param("i", $color_product_id);
                    $stmt_images->execute();
                    $result_images = $stmt_images->get_result();
        
                    $images = array();
                    
                    // Loop through all rows in color_product_images
                    while ($row_images = $result_images->fetch_assoc()) {
                        $images[] = $row_images['product_image'];
                    }
        
                    // Combine the color_product, sizes, and images into a single array
                    $colorProduct = array_merge($row_colors, array('sizes' => $sizes, 'images' => $images));
        
                    $colorProducts[] = $colorProduct;
        
                    $stmt_type_value->close();
                    $result_type_value->close();
                    $stmt_images->close();
                    $result_images->close();
                }
            }
        
            $stmt_colors->close();
            $result_colors->close();
        
            http_response_code(200);
            echo json_encode($colorProducts);
        }
    }

    public function orderSheet($product_id){

        // Fetch colors
        $select_all_colors_sql = "SELECT color_product_id, product_color FROM color_product WHERE product_id = ?";
        $stmt = $this->con->prepare($select_all_colors_sql);
        $stmt->bind_param("i", $product_id);
    
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to fetch colors"]);
            exit();
        }
    
        $stmt->bind_result($color_product_id, $product_color);
    
        $colors = [];
        while ($stmt->fetch()) {
            $colors[] = [
                'color_product_id' => $color_product_id,
                'product_color' => $product_color,
            ];
        }
        $stmt->close();
    
        // Fetch sizes
        $select_all_sizes_sql = "SELECT DISTINCT product_size FROM color_product_details WHERE product_id = ?";
        $stmt = $this->con->prepare($select_all_sizes_sql);
        $stmt->bind_param("i", $product_id);
    
        if (!$stmt->execute()) {
            http_response_code(500);
            echo json_encode(["error" => "Failed to fetch sizes"]);
            exit();
        }
    
        $stmt->bind_result($product_size);
    
        $sizes = [];
        while ($stmt->fetch()) {
            $sizes[] = $product_size;
        }
        $stmt->close();
    
        // $stockData = [];
        $quantityData = [];
        $zeroQuantityData = [];

        foreach ($sizes as $size) {
            $sizeData = [];

            $index = 0;
            foreach ($colors as $color) {
                $index++; 

                $select_quantity_sql = "SELECT product_quantity FROM color_product_details WHERE product_id = ? AND product_color = ? AND product_size = ?";
                $stmt = $this->con->prepare($select_quantity_sql);
                $stmt->bind_param("iss", $product_id, $color['product_color'], $size);

                if (!$stmt->execute()) {
                    http_response_code(500);
                    echo json_encode(["error" => "Failed to fetch product quantity"]);
                    exit();
                }

                $stmt->bind_result($product_quantity);

                if ($stmt->fetch()) {
                    if ($product_quantity == 0) {
                        $zeroQuantityData[] = [
                            'color_product_id' => $color['color_product_id'],
                            'size' => $size,
                            'quantity' => $product_quantity,
                            'index' => $index, 
                        ];
                    } else {
                        $quantityData[] = [
                            'color_product_id' => $color['color_product_id'],
                            'quantity' => $product_quantity,
                            'index' => $index, 
                        ];
                    }
                } else {
                    $quantityData[] = [
                        'color_product_id' => $color['color_product_id'],
                        'quantity' => 'empty',
                        'index' => $index,
                    ];
                }

                $stmt->close();
            }

            if (!empty($zeroQuantityData)) {
                foreach ($zeroQuantityData as &$zeroData) {
                    $zeroData['Arrival Date'] = $this->arrivalDate($zeroData['color_product_id'], $zeroData['size']);
                }
                unset($zeroData);
            }

            $stockData[$size] = array_merge($quantityData, $zeroQuantityData, $sizeData);

            usort($stockData[$size], function($a, $b) {
                return $a['index'] - $b['index'];
            });
            
            $quantityData = [];
            $zeroQuantityData = [];
        }

            // foreach ($sizes as $size) {
            //     $sizeData = [];
            
            //     foreach ($colors as $color) {
            //         $select_quantity_sql = "SELECT product_quantity FROM color_product_details WHERE product_id = ? AND product_color = ? AND product_size = ?";
            //         $stmt = $this->con->prepare($select_quantity_sql);
            //         $stmt->bind_param("iss", $product_id, $color['product_color'], $size);
            
            //         if (!$stmt->execute()) {
            //             http_response_code(500);
            //             echo json_encode(["error" => "Failed to fetch product quantity"]);
            //             exit();
            //         }
            
            //         $stmt->bind_result($product_quantity);
            
            //         if ($stmt->fetch()) {
            //             if ($product_quantity == 0) {
            //                 // $arrival_date = $this->arrivalDate($color['color_product_id'], $size);
            //                 $sizeData[] = [
            //                     'color_product_id' => $color['color_product_id'],
            //                     'quantity' => $product_quantity,
            //                     'Arrival Date' => '2024-03-12'
            //                 ];
            //             } else {
            //                 $sizeData[] = [
            //                     'color_product_id' => $color['color_product_id'],
            //                     'quantity' => $product_quantity
            //                 ];
            //             }
            //         } else {
            //             $sizeData[] = [
            //                 'color_product_id' => $color['color_product_id'],
            //                 'quantity' => 'empty'
            //             ];
            //         }
            
            //         $stmt->close(); 
            //     }
            
            //     $stockData[$size] = $sizeData;
            // }
            
        http_response_code(200);
        echo json_encode(["colors" => $colors, "sizes" => $sizes, "stockData" => $stockData]);
    } 

        private function arrivalDate($color_product_id, $size) {
            // Validate and sanitize user input
            $color_product_id = (int)$color_product_id;
            $size = htmlspecialchars($size, ENT_QUOTES, 'UTF-8');
        
            $get_arrival_date = "SELECT arrive_date FROM product_alert WHERE color_product_id=? AND product_size=?";
            $stmt = $this->con->prepare($get_arrival_date);
            $stmt->bind_param("is", $color_product_id, $size);
        
            if (!$stmt->execute()) {
                return null;
            }
        
            $stmt->bind_result($arrive_date);
        
            if ($stmt->fetch()) {
                $stmt->close();
                return $arrive_date;
            } else {
                $stmt->close();
                return date('Y-m-d', strtotime('+7 days'));
            }
        }
        
        
}

class oderHistory{
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    // public function getOrderDetails() {
    //     $get_all_order_details = "SELECT * FROM product_orders ORDER BY created_at DESC";
        
    //     $result = $this->con->query($get_all_order_details);
        
    //     if ($result) {
    //         $orderDetails = $result->fetch_all(MYSQLI_ASSOC);
        
    //         $uniqueOrders = [];
    //         foreach ($orderDetails as $order) {
    //             $uniqueOrderId = $order['unique_orderid'];
        
    //             if (!array_key_exists($uniqueOrderId, $uniqueOrders)) {
    //                 $uniqueOrders[$uniqueOrderId] = $order;
    //             }
    //         }
        
    //         $uniqueOrderDetails = array_values($uniqueOrders);
        
    //         http_response_code(200);
    //         echo json_encode($uniqueOrderDetails);
    //     } else {
    //         http_response_code(500);
    //         echo json_encode('Order Details Query Failed');
    //     } 
    // }

    public function getOrderDetails() {
        $get_all_order_details = "SELECT * FROM product_orders ORDER BY created_at DESC";
        
        $result = $this->con->query($get_all_order_details);
        
        if ($result) {
            $orderDetails = $result->fetch_all(MYSQLI_ASSOC);
    
            $uniqueOrders = [];
    
            foreach ($orderDetails as $order) {
                $uniqueOrderId = $order['unique_orderid'];
    
                if (!array_key_exists($uniqueOrderId, $uniqueOrders)) {
                    $dealer_id = $order['dealer_id'];
                    $get_user_details_sql = "SELECT firstname, email, phone, country FROM dealer_register WHERE dealer_id = $dealer_id";
                    $user_result = $this->con->query($get_user_details_sql);
    
                    if ($user_result) {
                        $user_info = $user_result->fetch_assoc();
                        $order['firstname'] = $user_info['firstname'];
                        $order['email'] = $user_info['email'];
                        $order['phone'] = $user_info['phone'];
                        $order['country'] = $user_info['country'];
                    } else {
                        $order['firstname'] = 'N/A';
                        $order['email'] = 'N/A';
                        $order['phone'] = 'N/A';
                        $order['country'] = 'N/A';
                    }
    
                    $uniqueOrders[$uniqueOrderId] = $order;
                }
            }
    
            $uniqueOrderDetails = array_values($uniqueOrders);
    
            http_response_code(200);
            echo json_encode($uniqueOrderDetails);
        } else {
            http_response_code(500);
            echo json_encode('Order Details Query Failed');
        }
    }

    public function getQuickOrderDetails() {
        $get_quick_order_details_sql = "SELECT * FROM quick_orders ORDER BY created_at DESC";
    
        $result = $this->con->query($get_quick_order_details_sql);
    
        if ($result) {
            $data = [];
    
            while ($order = $result->fetch_assoc()) {
                $dealer_id = $order['dealer_id'];
    
                $get_dealer_details_sql = "SELECT email, phone, firstname,country FROM dealer_register WHERE dealer_id = $dealer_id";
                $dealer_result = $this->con->query($get_dealer_details_sql);
    
                if ($dealer_result) {
                    $dealer_info = $dealer_result->fetch_assoc();
                    $order['email'] = $dealer_info['email'];
                    $order['phone'] = $dealer_info['phone'];
                    $order['firstname'] = $dealer_info['firstname'];
                    $order['country'] = $dealer_info['country'];
                } else {
                    $order['email'] = 'N/A';
                    $order['phone'] = 'N/A';
                    $order['firstname'] = 'N/A';
                    $order['country'] = 'N/A';
                }
    
                $data[] = $order;
            }
    
            $result->close();
            http_response_code(200);
            echo json_encode($data);
        } else {
            http_response_code(500);
            echo json_encode('Failed to retrieve quick order details');
        }
    }
    
}

$admin = new Admin($con);
$category = new Category($con);
$subCategory = new subCategory($con);
$product = new Product($con);
$colorProduct = new colorProduct($con);
$order = new oderHistory($con);

// Handle all post request

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // View Request

    if (isset($_POST['dealerRequst'])) 
    {
        $action_value = $_POST['dealerRequst'];

        if ($action_value === 'dealer') {
            $data = $admin->fetchDataForApproval();
        } else {
            echo "Invalid action value.";
        }
    } 

    // Approve Request
    
    else if (isset($_POST['dealer_approve'])) 
    {
        $dealer_approve_email = $_POST['dealer_approve'];
        $admin->dealerApprove($dealer_approve_email);
    } 

    // Cancel Request

    else if (isset($_POST['dealer_cancel'])) 
    {
        $dealer_cancel_email = $_POST['dealer_cancel'];
        $admin->dealerCancel($dealer_cancel_email);
    } 

    // Login Request

    elseif (isset($_POST['login_request'])) {
        $login_request = $_POST['login_request'];
        
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
    
        if (empty($email) && empty($password)) {
            echo "Email-and-password-are-empty";
        } elseif (empty($email)) {
            echo "Email-is-empty";
        } elseif (empty($password)) {
            echo "Password-is-empty";
        } else {
            $admin->login($email, $password);
        }
    }

    // Logout Request

    elseif(isset($_POST['logout_request'])){
        $admin->logout();
    }

    // Create Category 

    elseif(isset($_POST['createCategory_request'])){
        parse_str($_POST['formData'], $formData);
        $category_type = $formData['category_type'];
        $category_name = $formData['category_name'];
    
        if (empty($category_type) && empty($category_name)) {
            echo "Please-fill-all-fields";
        } elseif (empty($category_name)) {
            echo "Please-fill-category-name";
        } else {
            $category->createCategory($category_type, $category_name);
        }
    }

    // Load All Category 

    elseif(isset($_POST['loadCategory_request'])){
        $category->loadCategory();
    }

    // Delete Category

    elseif(isset($_POST['deleteCategory_request'])){
        if(isset($_POST['category_id'])){
            $category_id = $_POST['category_id'];
            $category->deleteCategory($category_id);
        }
        else{
            echo "Category Id Not Found";
        }
    }


    // Fetch Values For Category Edit Operation

    elseif(isset($_POST['loadCategory_For_Edit_request'])){
        if(isset($_POST['category_id'])){
            $category_id = $_POST['category_id'];
            $category->editCategory_Details($category_id);
        }
    }

    // Edit Category

    elseif(isset($_POST['editCategory_request'])){
        if(isset($_POST['category_id'])){
            parse_str($_POST['formData'], $formData);

            $category_id = $_POST['category_id'];
            $category_name = $formData['edit_category_name'];
            $category_type = $formData['edit_category_type'];

            $category->editCategory($category_id,$category_type,$category_name);
        }
        else{
            echo "Category Id not set";
        }
    }


    // Fetch Category Name For Create Sub Category

    elseif(isset($_POST['category_name_request'])){
        if(isset($_POST['categoryType'])){
            $category_type = $_POST['categoryType'];
            $category->fetchCategoryNames($category_type);
        }
        else{
            echo "Category Type is empty";
        }
    }

    // Create Sub Category

    elseif (isset($_POST['createSubCategory_request'])) {
    
        if (empty($_POST['category_type']) && empty($_POST['category_name']) && empty($_POST['sub_category_name'])) {
            echo 'Please_fill_all_fields';
        }
        elseif (empty($_POST['category_type'])) {
            echo 'Please_fill_category_type';
        } 
        elseif (empty($_POST['category_name'])) {
            echo 'Please_fill_category_name';
        } 
        elseif (empty($_POST['sub_category_name'])) {
                echo 'Please_fill_sub_category_name';
        } 
        else {
            
            $category_type = $_POST['category_type'];
            $category_name = $_POST['category_name'];
            $sub_category_name = $_POST['sub_category_name'];
            $subCategory->createSubCategory($category_type, $category_name, $sub_category_name);
        }
    } 

    // Load All sub Categories

    elseif(isset($_POST['loadSubCategory_request'])){
        $subCategory->loadSubCategory();
    }

    // Delete Sub Category

    elseif(isset($_POST['deleteSubCategory_request'])){
        if(isset($_POST['category_id'])){
            $sub_category_id = $_POST['category_id'];
            $subCategory->deleteSubCategory($sub_category_id);
        }
        else{
            echo "SubCategory Id is Empty";
        }
    }

    // Load Sub Category By Id for Edit

    elseif(isset($_POST['loadSubCategory_For_Edit_request'])){
        if(isset($_POST['sub_category_id'])){
            $sub_category_id = $_POST['sub_category_id'];
            // echo $sub_category_id;
            $subCategory->loadSubCategoryById($sub_category_id);
        }
        else{
            echo "Sub Category Id is Empty";
        }
    }

    // Edit Sub Category

    elseif(isset($_POST['editSubCategory_request'])){

        if (empty($_POST['sub_category_id']) && empty($_POST['edit_sub_category_type']) && empty($_POST['edit_category_name']) && empty($_POST['edit_sub_category_name'])) {
            echo 'Please_fill_all_fields';
        }
        elseif (empty($_POST['edit_sub_category_name'])) {
            echo 'Please_fill_sub_category_name';
        }
        else{
            $sub_category_id = $_POST['sub_category_id'];
            $category_type = $_POST['edit_sub_category_type'];
            $category_name = $_POST['edit_category_name'];
            $sub_category_name = $_POST['edit_sub_category_name'];

            $subCategory->editSubCategory($sub_category_id,$category_type,$category_name,$sub_category_name);
        }
    }

    // Fetch Sub Category name for Add New Product By category_type and category_name

    elseif(isset($_POST['loadSubCategoryByCategory'])){
        if(isset($_POST['category_type']) && isset($_POST['category_name'])){
            $category_type = filter_var($_POST['category_type'], FILTER_SANITIZE_STRING);
            $category_name = filter_var($_POST['category_name'], FILTER_SANITIZE_STRING);

            $subCategory->loadSubCategoryByCategory($category_type,$category_name);
        }
        else{
            echo "Category Name And Category Type Not Found for Fetch Sub Category Name";
        }
    }

    // Add Product Colour

    elseif(isset($_POST['add_color_request'])){
        if(empty($_POST['colorName']) && empty($_POST['colorValue'])){
            echo "please_select_fill_color_details";
        }
        elseif(empty($_POST['colorName'])){
            echo "please_fill_color_name";
        }
        elseif(empty($_POST['colorValue'])){
            echo "please_fill_color_value";
        }
        else{
            
            // echo "success";
            $color_name = $_POST['colorName'];
            $color_value = $_POST['colorValue'];

            $product->addProductColor($color_name,$color_value);
        }

        
    }

    // Load All colour 

    elseif(isset($_POST['load_color_request'])){
        $product->loadColor();
    }

    // Delete Product Colour

    elseif(isset($_POST['delete_color_request'])){
        if(isset($_POST['color_id'])){
            $color_id = $_POST['color_id'];
            $product->deletecolor($color_id);
        }
        else{
            echo "Color Id Not Founf For delete Query";
        }
    }

    // Create New Product

    elseif(isset($_POST['createNewProduct_request'])){
        
        if (empty($_POST['product_name'])) {
            echo "Please_select_product_name";
        } 

        elseif ($_POST['category_type'] == 'null' || empty($_POST['category_type'])) {
            echo "Please_select_category_type";
        }

        elseif ($_POST['category_name'] == 'null' || empty($_POST['category_name'])) {
            echo "Please_select_category_name";
        } 

        elseif ($_POST['sub_category_name'] == 'null' || empty($_POST['sub_category_name'])) {
            echo "Please_select_sub_category_name";
        }
        
        elseif (empty($_POST['product_color']) && $_FILES['product_color_file']['error'] == UPLOAD_ERR_NO_FILE) {
            echo "Please_select_color";
        }

        elseif (!empty($_POST['product_color']) && is_uploaded_file($_FILES['product_color_file']['tmp_name'])) {
            echo "Please_select_any_one_color";
        }

        // elseif ($_FILES['product_color_file']['error'] == UPLOAD_ERR_NO_FILE) {
        //     echo "Color image is empty.";
        // }        
        
        elseif (empty($_POST['product_size'][0])) {
            echo "Please_select_size";
        } 
        elseif (empty($_POST['product_quantity'][0])) {
            echo "Please_enter_quantity";
        }
        elseif (empty($_POST['product_price'][0])) {
            echo "Please_enter_price";
        } 
        elseif (empty($_POST['msrp_price'][0])) {
            echo "Please_enter_msrp_price";
        } 
        elseif (empty($_POST['product_description'])) {
            echo "Please_enter_description";
        } 
        elseif (empty($_POST['product_specification'])) {
            echo "Please_enter_specification";
        }  
         elseif ($_FILES["product_image"]["error"][0] !== UPLOAD_ERR_OK) {
            echo "Please_select_product_image";
        } 
        else {
            for ($i = 1; isset($_POST['product_quantity'][$i]); $i++) {
                if (
                    empty($_POST['product_quantity'][$i])
                    || empty($_POST['product_price'][$i])
                    || empty($_POST['msrp_price'][$i])
                    || empty($_POST['product_size'][$i])
                ) {
                    if (empty($_POST['product_size'][$i])) {
                        echo "Please_select_size";
                    }
                    if (empty($_POST['product_quantity'][$i])) {
                        echo "Please_enter_quantity";
                    }
                    if (empty($_POST['product_price'][$i])) {
                        echo "Please_enter_price";
                    }
                    if (empty($_POST['msrp_price'][$i])) {
                        echo "Please_enter_msrp_price";
                    }
        
                    exit;
                }
            }
        
            // echo "success";
            $product->addProduct($_POST, $_FILES);
        }
        
    }

    // Add More Product Images 

    elseif(isset($_POST['add_product_image_request'])){
        if (!empty($_POST['product_id'])) {
            $product_id = $_POST['product_id'];
            
            if (!empty($_FILES['images']['name'])) {
                $uploadedImages = $_FILES['images'];
                $imageNames = [];
    
                foreach ($uploadedImages['name'] as $key => $imageName) {
                    $imageTmpName = $uploadedImages['tmp_name'][$key];
                    $imageType = $uploadedImages['type'][$key];
    
                    $allowedExtensions = ['JPEG','JPG','PNG','jpeg', 'jpg', 'png'];
                    $fileExtension = pathinfo($imageName, PATHINFO_EXTENSION);
    
                    if (in_array($fileExtension, $allowedExtensions)) {
                        $uploadDirectory = '../product_images/';
                        $targetPath = $uploadDirectory . $imageName;
    
                        if (move_uploaded_file($imageTmpName, $targetPath)) {
                            $imageNames[] = $imageName;
                        } else {
                            http_response_code(400);
                            echo "image_upload_failed";
                            break;
                        }
                    } else {
                        http_response_code(400);
                        echo "Invalid_file_type";
                        break;
                    }
                }
    
                if (!empty($imageNames)) {
                    $product->addProductImage($product_id, $imageNames);
                }
            } else {
                http_response_code(400);
                echo "No_images_uploaded";
            }
        } else {
            http_response_code(400);
            echo "Product ID is missing for Add More Images";
        }
    }


    // Add More Product Details 

    elseif(isset($_POST['add_more_details_request'])){
        if(isset($_POST['productDetails'])){
            $product_details = $_POST['productDetails'];
            if(empty($product_details['product_id'])){
                http_response_code(404);
                echo json_encode("product_id_is_empty");

            }
            elseif(empty($product_details['product_size'])){
                http_response_code(404);
                echo json_encode("product_size_is_empty");
            }
            elseif(empty($product_details['product_quantity'])){
                http_response_code(404);
                echo json_encode("product_quantity_is_empty");

            }
            elseif(empty($product_details['product_price'])){
                http_response_code(404);
                echo json_encode("product_price_is_empty");

            }
            elseif(empty($product_details['msrp_price'])){
                http_response_code(404);
                echo json_encode("msrp_price_is_empty");
            }
            else{
                $product->addMoreDetails($product_details);
            }     
        }
        else{
            http_response_code(404);
            echo "Product Id not Found For Add More Product Details";
        }
    }

    // Add New Colour Product

    elseif(isset($_POST['createNewColorProduct_request'])){
        if(isset($_POST['product_id'])){
            if (empty($_POST['color_product_name'])) {
                http_response_code(404);
                echo json_encode("Please_select_product_name");
            } 
    
            elseif ($_POST['color_category_type'] == 'null') {
                http_response_code(404);
                echo json_encode("Please_select_category_type");
            }
    
            elseif ($_POST['color_category_name'] == 'null') {
                http_response_code(404);
                echo json_encode("Please_select_category_name");
            } 
    
            elseif ($_POST['color_sub_category_name'] == 'null') {
                http_response_code(404);
                echo json_encode("Please_select_sub_category_name");
            }
            
            // elseif (empty($_POST['color_product_color'])) {
            //     http_response_code(404);
            //     echo json_encode("Please_select_color");
            // } 

            elseif (empty($_POST['color_product_color']) && $_FILES['product_color_file']['error'] == UPLOAD_ERR_NO_FILE) {
                // echo "Please_select_color";
                http_response_code(404);
                echo json_encode("Please_select_color");
            }
    
            elseif (!empty($_POST['color_product_color']) && is_uploaded_file($_FILES['product_color_file']['tmp_name'])) {
                http_response_code(404);
                echo json_encode("Please_select_any_one_color");
            }
            
            elseif (empty($_POST['color_product_size'][0])) {
                http_response_code(404);
                echo json_encode("Please_select_size");
            } 
            elseif (empty($_POST['color_product_quantity'][0])) {
                http_response_code(404);
                echo json_encode("Please_enter_quantity");
            }
            elseif (empty($_POST['color_product_price'][0])) {
                http_response_code(404);
                echo json_encode("Please_enter_price");
            } 
            elseif (empty($_POST['color_msrp_price'][0])) {
                http_response_code(404);
                echo json_encode("Please_enter_msrp_price");
            } 
            elseif (empty($_POST['color_product_description'])) {
                http_response_code(404);
                echo json_encode("Please_enter_description");
            } 
            elseif (empty($_POST['color_product_specification'])) {
                http_response_code(404);
                echo json_encode("Please_enter_specification");
            }  
            elseif ($_FILES["color_product_image"]["error"][0] !== UPLOAD_ERR_OK) {
                http_response_code(404);
                echo json_encode("Please_select_product_image");
            } 
            // elseif ($_FILES["color_product_image"]["error"][0] !== UPLOAD_ERR_OK) {
            //     http_response_code(404);
            //     echo json_encode("Please_select_product_image");
            // }
            else {
                for ($i = 1; isset($_POST['color_product_quantity'][$i]); $i++) {
                    if (
                        empty($_POST['color_product_quantity'][$i])
                        || empty($_POST['color_product_price'][$i])
                        || empty($_POST['color_msrp_price'][$i])
                        || empty($_POST['color_product_size'][$i])
                    ) {
                        if (empty($_POST['color_product_size'][$i])) {
                            http_response_code(404);
                            echo json_encode("Please_select_size");
                        }
                        elseif (empty($_POST['color_product_quantity'][$i])) {
                            http_response_code(404);
                            echo json_encode("Please_enter_quantity");
                        }
                        elseif (empty($_POST['color_product_price'][$i])) {
                            http_response_code(404);
                            echo json_encode("Please_enter_price");
                        }
                        elseif (empty($_POST['color_msrp_price'][$i])) {
                            http_response_code(404);
                            echo json_encode("Please_enter_msrp_price");
                        }
            
                        exit;
                    }
                }
                http_response_code(200);
                $colorProduct->addColorProduct($_POST, $_FILES);
            }
        }
        else{
            http_response_code(200);
            echo json_encode("Product Id Not Found For Create New Colour Product");
        }     
    }

    // Add More Colour Product Images

    elseif(isset($_POST['add_color_product_image_request'])){
        if(isset($_POST['product_id'])){
            $product_id = $_POST['product_id'];
            if (!empty($_POST['color_product_id'])) {
                $color_product_id = $_POST['color_product_id'];
                
                if (!empty($_FILES['images']['name'])) {
                    $uploadedImages = $_FILES['images'];
                    $imageNames = [];
        
                    foreach ($uploadedImages['name'] as $key => $imageName) {
                        $imageTmpName = $uploadedImages['tmp_name'][$key];
                        $imageType = $uploadedImages['type'][$key];
        
                        $allowedExtensions = ['JPEG','JPG','PNG','jpeg', 'jpg', 'png'];
                        $fileExtension = pathinfo($imageName, PATHINFO_EXTENSION);
        
                        if (in_array($fileExtension, $allowedExtensions)) {
                            $uploadDirectory = '../product_images/';
                            $targetPath = $uploadDirectory . $imageName;
        
                            if (move_uploaded_file($imageTmpName, $targetPath)) {
                                $imageNames[] = $imageName;
                            } else {
                                http_response_code(400);
                                echo "image_upload_failed";
                                break;
                            }
                        } else {
                            http_response_code(400);
                            echo "Invalid_file_type";
                            break;
                        }
                    }
        
                    if (!empty($imageNames)) {
                        $colorProduct->addColorProductImage($product_id, $color_product_id, $imageNames);
                    }
                } else {
                    http_response_code(400);
                    echo "No_images_uploaded";
                }
            } else {
                http_response_code(400);
                echo "Product ID is missing for Add More Images";
            }
        }
        else{
            http_response_code(404);
            echo "Product Id Not Found";
        }
        
    }

    // Add More Color Product Details

    elseif(isset($_POST['add_more_color_details_request'])){
        if(isset($_POST['productDetails'])){
            $product_details = $_POST['productDetails'];
            if(empty($product_details['product_id'])){
                http_response_code(404);
                echo json_encode("product_id_is_empty");
            }
            elseif(empty($product_details['color_product_id'])){
                http_response_code(404);
                echo json_encode("color_product_id_is_empty");
            }
            elseif(empty($product_details['product_size'])){
                http_response_code(404);
                echo json_encode("product_size_is_empty");
            }
            elseif(empty($product_details['product_quantity'])){
                http_response_code(404);
                echo json_encode("product_quantity_is_empty");

            }
            elseif(empty($product_details['product_price'])){
                http_response_code(404);
                echo json_encode("product_price_is_empty");

            }
            elseif(empty($product_details['msrp_price'])){
                http_response_code(404);
                echo json_encode("msrp_price_is_empty");
            }
            else{
                $colorProduct->addMoreColorProductDetails($product_details);
            }     
        }
        else{
            http_response_code(404);
            echo "Product Id not Found For Add More Product Details";
        }
    }

    // Add Wishlist

    elseif(isset($_POST['add_wishlist_request'])){
        if(!empty($_POST['product_id'])){
            $product_id = $_POST['product_id'];
            $product->addWishlist($product_id);
        }
        else{
            http_response_code(404);
            echo json_encode("Product Id Not Found");
        }
    }

    
}

// Hadle All get request

elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // Fetch All Products

    if (isset($_GET['loadProduct_request'])) {
        $product->fetchProducts();
    }

    // Fetch Product Details By Id For Edit

    elseif (isset($_GET['editProductDetails_request'])) {

        if (isset($_GET['product_id'])) {
            $product_id = $_GET['product_id'];
            http_response_code(200);
            // echo $product_id;

            $product->fetchProductById($product_id);
        } else {
            http_response_code(404);
            echo "Product Id Not Found For Edit Product";
        }
    }

    // Get Product Images By Id

    elseif(isset($_GET['getProductImageById_request'])){
        if(isset($_GET['product_id'])){
            $product_id = $_GET['product_id'];

            $product->getProductImageById($product_id);
        }
        else{
            http_response_code(404);
            echo "Product Id Not Found For Get Image";
        }
    }

    // Get Product Details By Id

    elseif(isset($_GET['get_product_details_request'])){
        if(isset($_GET['product_id'])){
            $product_id = $_GET['product_id'];
            http_response_code(200);
            // echo "success";
            $product->getProductDetailsById($product_id);
        }
        else{
            http_response_code(404);
            echo json_encode(['error' => 'ID not found for fetching product details.']);
        }
    }


    // Fetch All Colour Produtcs 

    elseif(isset($_GET['loadColorProduct_request'])){
        if(isset($_GET['color_product_id'])){
            $color_product_id = $_GET['color_product_id'];
            $colorProduct->fetchColorProduct($color_product_id);
        }
        else{
            http_response_code(404);
            echo json_encode("Colour Product Id Not Found For Fetch Colour Products By Id");
        }
    }

    // Load All Colour Products By Id

    elseif(isset($_GET['editColorProductDetails_request'])){
        // echo "success";
        if(!empty($_GET['color_product_id'])){
            $color_product_id = $_GET['color_product_id'];
            $colorProduct->fetchColorProductById($color_product_id);
        }
        else{
            http_response_code(404);
            echo json_encode("Colour Product Id Not Found For Fetch Colour Product Details");
        }
    }

    // Get ALl Color Product Images By Id

    elseif(isset($_GET['getColorProductImageById_request'])){
        if(isset($_GET['color_product_id'])){
            $color_product_id = $_GET['color_product_id'];

            // echo $color_product_id;
            $colorProduct->getColorProductImageById($color_product_id);
        }
        else{
            http_response_code(404);
            echo "Colour Product Id Not Found For Get Image";
        }
    }

    // Get colour Product Details

    elseif(isset($_GET['get_color_product_details_request'])){
        if(isset($_GET['color_product_id'])){
            $color_product_id = $_GET['color_product_id'];
            http_response_code(200);
            // echo "success";
            $colorProduct->getProductDetailsById($color_product_id);
        }
        else{
            http_response_code(404);
            echo json_encode(['error' => 'ID not found for fetching product details.']);
        }
    }

    // Fetch All Product Details By Id Except Image

    elseif(isset($_GET['fetchProductDetails_request'])){
        if(isset($_GET['product_id'])){
            $product_id = $_GET['product_id'];
            $productDetails = $product->fetchAllProductDetails($product_id);
        } else {
            http_response_code(400);
            echo json_encode(array('error' => 'Product ID not Found For Fetch Details'));
        }
    }

    // Fetch All Category And Sub Category By Main Category

    elseif(isset($_GET['fetch_all_category_request'])){
        // echo "success";
        if(isset($_GET['main_category'])){
            $category_type = $_GET['main_category'];
            $subCategory->fetchAllCategory($category_type);
        }
        else{
            http_response_code(404);
            echo "Category Type Not Found";
        }
    }

    // Fetch Products By Category

    elseif(isset($_GET['fetchProductByCategory_request'])){
        if(isset($_GET['category_type'])){
            $category_type = $_GET['category_type'];
            $product->fetchProductByCategoryType($category_type);
        }
        else{
            http_response_code(404);
            echo json_encode("Product Category Type Not Found for Get Products");
        }
    }

    elseif(isset($_GET['fetchNewArrivalProducts_request'])){
        // echo "success";
        $product->fetchNewArrivalProducts();
    }

    // Fetch All Products 

    elseif(isset($_GET['shopAllproducts_request'])){
        $product->fetchAllProductWithAllDetails();
    }

    // Get All Categories

    elseif(isset($_GET['loadCategory_request'])){
        $product->fetchAllProductFilters();
    }

    // Get All Color Product By Product Id

    elseif(isset($_GET['get_all_color_product_request'])){
        if(!empty($_GET['product_id'])){
            $product_id = $_GET['product_id'];
            $colorProduct->getAllColorProductById($product_id);
        }
        else{
            http_response_code(404);
            echo json_encode("Product Id Not Found For Get Product!");
        }
    }

    // Get All Color Product By Product Id && Color Name

    elseif(isset($_GET['get_color_product_by_color_request'])){
        if(!empty($_GET['productId']) && !empty($_GET['clickedColorName'])){
            $product_id = $_GET['productId'];
            $color_name = $_GET['clickedColorName'];
            $colorProduct->getColorProductByColor($product_id,$color_name);
        }
        else{
            http_response_code(404);
            echo json_encode("Product Id Not Found For Get Product!");
        }
    }

    elseif(isset($_GET['get_product_ordersheet_request'])){
        if(isset($_GET['productId'])){
            $product_id = $_GET['productId'];
            // echo $product_id;
            $colorProduct->orderSheet($product_id);
        }
        else{
            http_response_code(404);
            echo json_encode("Product Id Not Found For OrderSheet");
        }
    }

    // Get New Arrived Products

    elseif(isset($_GET['fetch_new_arrivals_request'])){
        $product->newArrivals();
    }

    // Get Wishlist Items

    elseif(isset($_GET['fetch_wishlist_items_request'])){
        $product->showWishlistItems();
    }

    // Wishlist Count

    elseif(isset($_GET['wishlist_count_request'])){
        $product->wishlistCount();
    }

    // Get Products By Category

    elseif(isset($_GET['get_product_by_category_request'])){
        $product->fetchProductByCategory();
    }

    // Related Products Request

    elseif(isset($_GET['related_product_request'])){
        if(isset($_GET['product_id']) && !empty($_GET['product_id'])){
            $product_id = $_GET['product_id'];
            $product->relatedProducts($product_id);
        }
        else{
            http_response_code(404);
            echo json_encode("Product Id Not Found");
        }
    }

    // Search Products

    elseif(isset($_GET['search_product_request'])){
        $searchArray = array();
    
        if(isset($_GET['search_category']) && !empty($_GET['search_category'])){
            $searchArray['search_category'] = $_GET['search_category'];
        }
    
        if(isset($_GET['search_product']) && !empty($_GET['search_product'])){
            $searchArray['search_product'] = $_GET['search_product'];
        }
    
        if(empty($searchArray)){
            http_response_code(404);
            echo json_encode("Cannot_able_to_search_any_product_because_search_values_are_empty");
        } else {
            $product->searchProduct($searchArray);
            // echo json_encode($searchArray);
        }
    }

    // Order History

    elseif(isset($_GET['order_details_request'])){
        $order->getOrderDetails();
    }

    // Quick Order History

    elseif(isset($_GET['quick_order_details_request'])){
        $order->getQuickOrderDetails();
    }

    elseif(isset($_GET['get_product_alert_request'])){
        // echo json_encode("success");
        $product->getProductAlerts();
    }

}

// Handle All Delete request

elseif($_SERVER['REQUEST_METHOD'] === 'DELETE'){
    $data = json_decode(file_get_contents("php://input"), true);

    // Delete Product

    if (isset($data['deleteProduct_request']) && $data['deleteProduct_request'] === 'delete_product') {
        $productId = $data['productId'];

        $product->deleteProduct($productId);
    }

    // Delete Product Images By Id

    elseif (isset($data['product_image_delete_request']) && $data['product_image_delete_request'] === 'delete_product_image') {
        

        if (empty($data['imageName']) || empty($data['product_id'])) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ImageName and Product ID cannot be empty']);
        } else {
            $imageName = $data['imageName'];
            $product_id = $data['product_id'];

            $product->deleteProductImage($imageName,$product_id);
            
        }
    }

    // Delete Prodct Details

    elseif(isset($data['delete_product_details_request'])){
        if(isset($data['product_id'])){
            if(isset($data['product_details_id'])){
                $product_id = $data['product_id'];
                $product_details_id = $data['product_details_id'];
                $product->deleteColorProductDetails($product_id,$product_details_id);
            }
            else{
                http_response_code(404);
                echo json_encode(['error' => 'Product Id Not Found For Delete Product Detail']);
            }
        }
        else{
            http_response_code(404);
            echo json_encode(['error' => 'Product Id Not Found For Delete Product Detail']);
        }
    }

    // Delete Colour Product By Id 

    elseif(isset($data['deleteColorProduct_request'])){
        if (isset($data['deleteColorProduct_request']) && $data['deleteColorProduct_request'] === 'deletecolor_product') {
            $colorProductId = $data['colorProductId'];
    
            $colorProduct->deleteColorProduct($colorProductId);
        }
        else{
            http_response_code(404);
            echo json_encode("Colour Product Id Not Found For Delete Coloue Product");
        }
    }

    // Delete Colour Product Image By Id

    elseif (isset($data['color_product_image_delete_request']) && $data['color_product_image_delete_request'] === 'delete_color_product_image') {
        

        if (empty($data['imageName']) || empty($data['color_product_id'])) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'ImageName and Product ID cannot be empty']);
        } else {
            $imageName = $data['imageName'];
            $color_product_id = $data['color_product_id'];

            $colorProduct->deleteProductImage($imageName,$color_product_id);
            
        }
    }

    // Delete Colour Product Details By Id

    elseif(isset($data['delete_color_product_details_request'])){
        if(isset($data['color_product_id'])){
            if(isset($data['color_product_details_id'])){
                $color_product_id = $data['color_product_id'];
                $color_product_details_id = $data['color_product_details_id'];
                $colorProduct->deleteColorProductDetails($color_product_id,$color_product_details_id);
            }
            else{
                http_response_code(404);
                echo json_encode(['error' => 'Product Id Not Found For Delete Product Detail']);
            }
        }
        else{
            http_response_code(404);
            echo json_encode(['error' => 'Product Id Not Found For Delete Product Detail']);
        }
    }

    elseif(isset($data['remove_wishlist_request'])){
        if(!empty($data['wishlist_id'])){
            $wishlist_id = $data['wishlist_id'];
            $product->removeWishlist($wishlist_id);
        }
        else{
            http_response_code(404);
            echo json_encode("Whislist Id Not Found!");
        }
    }

}

// Handle All Update request

elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    // echo '<pre>';
    // var_dump($data); // or print_r($data);
    // echo '</pre>';

    if (isset($data['updateProduct_request']) && $data['updateProduct_request'] === 'update_product') {

        if (isset($data['product_details'])) {
            $product_details = $data['product_details'];

            if (empty($product_details['product_id'])) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(["error" => "product_id_not_found_for_edit_product"]);
            } else {
                if (empty($product_details['product_name'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "product_name_is_empty"]);
                }
                elseif (empty($product_details['product_category_type'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "category_type_is_empty"]);
                }
                elseif (empty($product_details['product_category_name'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "category_name_is_empty"]);
                }
                elseif (empty($product_details['product_sub_category_name'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "sub_category_name_is_empty"]);
                }
                elseif (empty($product_details['product_color'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "product_color_is_empty"]);
                }
                elseif (empty($product_details['product_description'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "product_description_is_empty"]);
                }
                elseif (empty($product_details['product_specification'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "product_specification_is_empty"]);
                }
                else {
                    $product_id = $product_details['product_id'];
                    $product_name = $product_details['product_name'];
                    $category_type = $product_details['product_category_type'];
                    $category_name = $product_details['product_category_name'];
                    $sub_category_name = $product_details['product_sub_category_name'];
                    $product_color = $product_details['product_color'];
                    $product_description = $product_details['product_description'];
                    $product_specification = $product_details['product_specification'];
                
                    // Create an array with the product details
                    $product_array = [
                        'product_id' => $product_id,
                        'product_name' => $product_name,
                        'category_type' => $category_type,
                        'category_name' => $category_name,
                        'sub_category_name' => $sub_category_name,
                        'product_color' => $product_color,
                        'product_description' => $product_description,
                        'product_specification' => $product_specification,
                    ];
                
                    $result = $product->editProduct($product_array);

                }
            }
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(["error" => "product_details_not_found"]);
        }

    }

    // Update Product Details

    elseif (isset($data['update_product_details_request']) && $data['update_product_details_request'] === 'update_product_details') {
    
        if (isset($data['edit_product_details'])) {
            $editProductDetails = $data['edit_product_details'];
    
            if (isset($editProductDetails['productId'])) {
                $product_id = $editProductDetails['productId'];
    
                if (empty($editProductDetails['productSize'])) {
                    http_response_code(404);
                    echo json_encode(['error' => 'product_size_empty']);
                }
    
                elseif (empty($editProductDetails['productQuantity'])) {
                    http_response_code(404);
                    echo json_encode(['error' => 'product_quantity_empty']);
                } 
                elseif (empty($editProductDetails['productPrice'])) {
                    http_response_code(404);
                    echo json_encode(['error' => 'product_price_empty']);
                } 
                elseif (empty($editProductDetails['msrpPrice'])) {
                    http_response_code(404);
                    echo json_encode(['error' => 'product_msrp_price_empty']);
                } 
                else{
                    $product->editProductDetails($editProductDetails);
                }
    
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Product ID not Found For Update Product Details']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'edit_product_details_not_found']);
        }
    }

    // Update Colour Product

    elseif (isset($data['updateColorProduct_request']) && $data['updateColorProduct_request'] === 'update_color_product') {

        if (isset($data['product_details'])) {
            $product_details = $data['product_details'];

            if (empty($product_details['colorProductId'])) {
                http_response_code(404);
                header('Content-Type: application/json');
                echo json_encode(["error" => "product_id_not_found_for_edit_product"]);
            } else {
                if (empty($product_details['product_name'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "product_name_is_empty"]);
                }
                elseif (empty($product_details['product_category_type'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "category_type_is_empty"]);
                }
                elseif (empty($product_details['product_category_name'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "category_name_is_empty"]);
                }
                elseif (empty($product_details['product_sub_category_name'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "sub_category_name_is_empty"]);
                }
                elseif (empty($product_details['product_color'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "product_color_is_empty"]);
                }
                elseif (empty($product_details['product_description'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "product_description_is_empty"]);
                }
                elseif (empty($product_details['product_specification'])) {
                    http_response_code(404);
                    header('Content-Type: application/json');
                    echo json_encode(["error" => "product_specification_is_empty"]);
                }
                else {
                    $color_product_id = $product_details['colorProductId'];
                    $product_name = $product_details['product_name'];
                    $category_type = $product_details['product_category_type'];
                    $category_name = $product_details['product_category_name'];
                    $sub_category_name = $product_details['product_sub_category_name'];
                    $product_color = $product_details['product_color'];
                    $product_description = $product_details['product_description'];
                    $product_specification = $product_details['product_specification'];
                
                    // Create an array with the product details
                    $product_array = [
                        'color_product_id' => $color_product_id,
                        'product_name' => $product_name,
                        'category_type' => $category_type,
                        'category_name' => $category_name,
                        'sub_category_name' => $sub_category_name,
                        'product_color' => $product_color,
                        'product_description' => $product_description,
                        'product_specification' => $product_specification,
                    ];
                
                    $result = $colorProduct->editColorProduct($product_array);

                }
            }
        } else {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(["error" => "product_details_not_found"]);
        }

    }

    // Update colour Product Details By Id

    elseif (isset($data['update_color_product_details_request']) && $data['update_color_product_details_request'] === 'update_color_product_details') {
    
        if (isset($data['edit_color_product_details'])) {
            $editColorProductDetails = $data['edit_color_product_details'];
    
            if (isset($editColorProductDetails['color_productId'])) {
                $color_product_id = $editColorProductDetails['color_productId'];
    
                if (empty($editColorProductDetails['productSize'])) {
                    http_response_code(404);
                    echo json_encode(['error' => 'product_size_empty']);
                }
    
                elseif (empty($editColorProductDetails['productQuantity'])) {
                    http_response_code(404);
                    echo json_encode(['error' => 'product_quantity_empty']);
                } 
                elseif (empty($editColorProductDetails['productPrice'])) {
                    http_response_code(404);
                    echo json_encode(['error' => 'product_price_empty']);
                } 
                elseif (empty($editColorProductDetails['msrpPrice'])) {
                    http_response_code(404);
                    echo json_encode(['error' => 'product_msrp_price_empty']);
                } 
                else{
                    $colorProduct->editColorProductDetails($editColorProductDetails);
                }
    
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Product ID not Found For Update Product Details']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'edit_product_details_not_found']);
        }
    }

    // Update Quantity

    elseif(isset($data['update_stock_request'])){
        // echo "success";
        if (isset($data['product_alert_id'], $data['color_product_id'], $data['product_size']) && isset($data['updatedDate'])) {
            $product_alert_id = $data['product_alert_id'];
            $color_product_id = $data['color_product_id'];
            $product_size = $data['product_size'];
            $updatedDate = $data['updatedDate'];
    
            if (isset($data['updatedQuantity'])) {
                $updatedQuantity = $data['updatedQuantity'];
    
                if ($updatedQuantity != 0) {
                    // echo "quanity not zero";
                    $product->updateProductQuantity($product_alert_id, $color_product_id, $product_size, $updatedQuantity);
                } else {
                    http_response_code(400); 
                    echo json_encode("Quantity should be greater than zero");
                    exit();
                }
            } else {
                // echo "quanitity is zero";
                $product->updateProductDate($product_alert_id, $color_product_id, $product_size, $updatedDate);
            }
        } else {
            http_response_code(400);
            echo json_encode("Missing or invalid parameters in the update request");
            exit();
        }
    }
    
    
}

// Handle All Put request

// if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
//     $json_data = file_get_contents("php://input");
//     $data = json_decode($json_data, true);

//     if ($data['updateProduct_request'] === 'update_product') {
//         if (isset($data['product_details'])) {
//             $product_details = $data['product_details'];

//             if (empty($product_details['product_id'])) {
//                 http_response_code(404);
//                 header('Content-Type: application/json');
//                 echo json_encode(["error" => "product_id_not_found_for_edit_product"]);
//             } else {
//                 if (empty($product_details['product_name'])) {
//                     http_response_code(404);
//                     header('Content-Type: application/json');
//                     echo json_encode(["error" => "product_name_is_empty"]);
//                 }
//                 elseif (empty($product_details['product_category_type'])) {
//                     http_response_code(404);
//                     header('Content-Type: application/json');
//                     echo json_encode(["error" => "category_type_is_empty"]);
//                 }
//                 elseif (empty($product_details['product_category_name'])) {
//                     http_response_code(404);
//                     header('Content-Type: application/json');
//                     echo json_encode(["error" => "category_name_is_empty"]);
//                 }
//                 elseif (empty($product_details['product_sub_category_name'])) {
//                     http_response_code(404);
//                     header('Content-Type: application/json');
//                     echo json_encode(["error" => "sub_category_name_is_empty"]);
//                 }
//                 elseif (empty($product_details['product_color'])) {
//                     http_response_code(404);
//                     header('Content-Type: application/json');
//                     echo json_encode(["error" => "product_color_is_empty"]);
//                 }
//                 elseif (empty($product_details['product_description'])) {
//                     http_response_code(404);
//                     header('Content-Type: application/json');
//                     echo json_encode(["error" => "product_description_is_empty"]);
//                 }
//                 elseif (empty($product_details['product_specification'])) {
//                     http_response_code(404);
//                     header('Content-Type: application/json');
//                     echo json_encode(["error" => "product_specification_is_empty"]);
//                 }
//                 else {
//                     $product_id = $product_details['product_id'];
//                     $product_name = $product_details['product_name'];
//                     $category_type = $product_details['product_category_type'];
//                     $category_name = $product_details['product_category_name'];
//                     $sub_category_name = $product_details['product_sub_category_name'];
//                     $product_color = $product_details['product_color'];
//                     $product_description = $product_details['product_description'];
//                     $product_specification = $product_details['product_specification'];
                
//                     // Create an array with the product details
//                     $product_array = [
//                         'product_id' => $product_id,
//                         'product_name' => $product_name,
//                         'category_type' => $category_type,
//                         'category_name' => $category_name,
//                         'sub_category_name' => $sub_category_name,
//                         'product_color' => $product_color,
//                         'product_description' => $product_description,
//                         'product_specification' => $product_specification,
//                     ];
                
//                     $result = $product->editProduct($product_array);

//                 }
//             }
//         } else {
//             http_response_code(404);
//             header('Content-Type: application/json');
//             echo json_encode(["error" => "product_details_not_found"]);
//         }
//     }
// }
?>