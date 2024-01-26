<?php
//تابع پستهای فروشگهاه صفحه اول و ادامه مطلب
session_start();
// Generate a random token and store it in the session variable
$_SESSION['token'] = bin2hex(random_bytes(32));

function shop_posts() {
  require('admin/admin/conn.php');
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}
if (isset($_GET["id"])) {
  // Use prepared statements instead of string concatenation to prevent SQL injection
  $post_id = $_GET["id"];
$sql = "SELECT products.*, category.category_name FROM products  JOIN category ON products.id = ?  and products.dastebandi = category.id";
  $stmt = $con->prepare($sql);
  $stmt->bind_param('i', $post_id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Use htmlspecialchars() to prevent XSS attacks
		echo "<img src='" .htmlspecialchars(filter_var($row["image"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "' alt='" . htmlspecialchars($row['name']) . "'>";
    echo "<title>" .htmlspecialchars(filter_var($row["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."-$namewebsite</title>";

    echo "<h1>" .htmlspecialchars(filter_var($row["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."</h1>";
	
	  echo "<h3>قیمت" .htmlspecialchars(filter_var($row["price"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). " تومان</h3>";

	  echo "<form action='shop2.php' method='post'>";
echo "<input type='hidden' name='id' value='" .htmlspecialchars(filter_var($row["id"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "'>";
echo "<h1><button type='submit' style='color:yellow;background-color:green;'>خرید محصول</button></h1>";
echo "</form>";
    echo "<p>" .filter_var($row["description"], FILTER_SANITIZE_STRING, ENT_QUOTES). "</p>";
	echo "<a href='shop_index.php?dastebandi=".htmlspecialchars(filter_var($row["dastebandi"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."'>" .htmlspecialchars(filter_var($row["category_name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."</a> | <span>" .htmlspecialchars(filter_var($row["data"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "</span>"; 
  } else {
    echo "No post found";
  }
} else {
  // Use constants instead of variables for fixed values
  define('POSTS_PER_PAGE', $total);
  // Use prepared statements instead of string concatenation to prevent SQL injection
  $sql = "SELECT COUNT(*) AS total FROM products";
  $stmt = $con->prepare($sql);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_posts = $row["total"];
  } else {
    echo "No posts found";
    return;
  }
  // Use ceil() function to round up the total pages
  $total_pages = ceil($total_posts / POSTS_PER_PAGE);
  if (isset($_GET["page"])) {
    // Use filter_var() function to validate and sanitize the page number
    $current_page = filter_var($_GET["page"], FILTER_VALIDATE_INT, array("options" => array("min_range" => 1, "max_range" => $total_pages)));
    // If the page number is invalid, redirect to the first page
    if ($current_page === false) {
      header("Location: shop.php?page=1");
      exit;
    }
  } else {
    // If the page number is not set, default to the first page
    $current_page = 1;
  }
  
  // Calculate the offset and limit for the SQL query
  $offset = ($current_page - 1) * POSTS_PER_PAGE;
  $limit = POSTS_PER_PAGE;
  
  // Use prepared statements instead of string concatenation to prevent SQL injection
$sql = "SELECT products.*, category.category_name FROM products JOIN category ON products.dastebandi = category.id   ORDER BY data desc  LIMIT ?, ?";
  
  // Use s for string parameters and i for integer parameters
  $stmt = $con->prepare($sql);
  
  // Bind the offset and limit parameters
  $stmt->bind_param('ii', $offset, $limit);
  
  // Execute the query and get the result
  $stmt->execute();
  
  // Get an associative array of the fetched row
  $result = $stmt->get_result();
  
if ($result->num_rows > 0) {
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
      // Use htmlspecialchars() to prevent XSS attacks
      echo "<li><h2><a href='shop.php?id=" .htmlspecialchars(filter_var($row["id"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."'>" .htmlspecialchars(filter_var($row["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "</a></h2></li><p>";
	  	echo "<img src='" .htmlspecialchars(filter_var($row["image"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."' alt='" .htmlspecialchars(filter_var($row["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "'><p>";

	  echo "<form action='shop2.php' method='post' >";
echo "<input type='hidden' name='id' value='" .htmlspecialchars(filter_var($row["id"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "'>";
echo "<h1><button type='submit' style='color:yellow;background-color:green;'>خرید محصول</button></h1>";
echo "</form>";
  echo "<h3>قیمت" .htmlspecialchars(filter_var($row["price"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). " تومان</h3>";
      echo "<p>" .filter_var(substr($row["description"],0, 200), FILTER_SANITIZE_STRING). "</p>";
	        echo "<h4><a href='shop.php?id=" .htmlspecialchars(filter_var($row["id"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "'>" . htmlspecialchars('ادامه مطلب...') . "</a></h4><p>";
echo "<a href='shop_index.php?dastebandi=".htmlspecialchars(filter_var($row["dastebandi"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."'>" .htmlspecialchars(filter_var($row["category_name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."</a> | <span>" .htmlspecialchars(filter_var($row["data"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "</span>";

    }
    echo "</ul>";
    
    // Generate the pagination links
    
    echo "<div class='pagination'>";
    
    if ($current_page > 1) {
      // Show the previous page link
      $prev_page = $current_page - 1;
            echo "<a href='shop.php?page=$prev_page'>Previous</a> ";
    }
    
    // Show the numbered pages
    
    for ($i = 1; $i <= $total_pages; $i++) {
      if ($i == $current_page) {
        // Highlight the current page number
        echo "<span class='current'>$i</span> ";
      } else {
        // Show the other page numbers as links
        echo "<a href='shop.php?page=$i'>$i</a> ";
      }
    }
    
    if ($current_page < $total_pages) {
      // Show the next page link
      $next_page = $current_page + 1;
            echo "<a href='shop.php?page=$next_page'>Next</a><p> <p>";
    }
    
    echo "</div>";
    
} else {
    echo "No posts found";
    return;
}
}


$con->close();
echo "<hr />";
if (isset($_GET['id'])) { // check if the id is set
echo "<form action='function/comment2.php' method='post'>";
echo "<input type='hidden' name='post_id' value='" .htmlspecialchars(filter_var($_GET["id"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."'>";
echo "<input id='shop' type='hidden' name='shop' value='1'>";

echo "<label for='email'>Email:</label>";
echo "<input type='email' id='email' name='email' required><p>";
echo "<label for='comment'>Comment:</label>";
// define a javascript code as a string
$js_code = "<script>
// define a function to sanitize the input
function sanitizeInput(input) {
  // create a temporary element
  var temp = document.createElement(\"div\");
  // set the input as the innerHTML of the element
  temp.innerHTML = input;
  // return the text content of the element
  return temp.textContent;
}
</script>

<textarea id='comment' name='comment' rows='4' cols='50' required oninput=\"this.value = sanitizeInput(this.value)\"></textarea><p>";
// echo the javascript code in php
echo $js_code;
echo "<label for='field1'>chaptcha:</label>";
echo "<input name='field1' type='text' disabled required id='field1' value='" . rand(999999,100000000) . "'><p>";
echo "<label for='field2'>ok prees chaptcha:</label><br>";
echo "<input type='text' id='field2' name='field2' required><p><p>";
echo "<button type='submit' onclick='return checkFields()'>submit</button><p>";
echo "</form>";
require('admin/admin/conn.php');
	echo '<a href="shop.php?id='.htmlspecialchars(filter_var($_GET["id"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES).'"><h2>next>>></h2></a>';
$sql = "SELECT * FROM comment WHERE post_id = ? order by rand() limit ?";
$stmt = $con->prepare($sql);
// Define a new variable and assign the value of rand() to it
$limit = rand(1, 10);
// Pass the variable as the second argument of bind_param()
$stmt->bind_param('ii', $_GET['id'], $limit);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
echo "<h3>Comments:</h3>";

echo "<ul>";

 while($row = $result->fetch_assoc()) {

// Use htmlspecialchars() to prevent XSS attacks

// Check if taid is not empty and is equal to ok
if (!empty($row["taid"]) && $row["taid"] == "ok" and  ($row["shop"]) && $row["shop"] == "1") {
echo "<li>" .htmlspecialchars(filter_var($row["email"], FILTER_SANITIZE_EMAIL), ENT_QUOTES)." <p> said: ". filter_var($row["text"], FILTER_SANITIZE_STRING, ENT_QUOTES). "<p> pasogh: " .htmlspecialchars(filter_var($row["pas"], FILTER_SANITIZE_STRING), ENT_QUOTES)."</li><hr />
";
}
}
echo "</ul>";



} else {

echo "No comments found";

}

$con->close();

}

}

echo "<script>

function checkFields() {

var field1 = document.getElementById('field1').value;

var field2 = document.getElementById('field2').value;

if (field1 != field2) {

alert('Fields are not equal!');

return false;

}

return true;

}

</script>";

?>
