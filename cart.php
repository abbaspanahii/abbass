
<?php
//سبد خرید فروشگاه
session_start();

require('admin/admin/conn.php');

// Check the connection
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}

// Define a class for the cart item
class CartItem {
  // Properties
  public $id;
  public $name;
  public $price;
  public $quantity;

  // Constructor
  public function __construct($id, $name, $price, $quantity) {
    $this->id = $id;
    $this->name = $name;
    $this->price = $price;
    $this->quantity = $quantity;
  }

  // Methods
  public function getId() {
    return $this->id;
  }

  public function getName() {
    return $this->name;
  }

  public function getPrice() {
    return $this->price;
  }

  public function getQuantity() {
    return $this->quantity;
  }

  public function setQuantity($quantity) {
    $this->quantity = $quantity;
  }

  public function getTotal() {
    return $this->price * $this->quantity;
  }
}

// Define a class for the cart
class Cart {
  // Properties
  public $items;
  public $total;

  // Constructor
  public function __construct() {
    $this->items = array();
    $this->total = 0;
  }

  // Methods
  public function getItems() {
    return $this->items;
  }

  public function getTotal() {
    return $this->total;
  }

  public function addItem($item) {
    // Check if the item already exists in the cart
    if (array_key_exists($item->getId(), $this->items)) {
      // Increase the quantity of the existing item
      $this->items[$item->getId()]->setQuantity($this->items[$item->getId()]->getQuantity() + $item->getQuantity());
    } else {
      // Add the new item to the cart
      $this->items[$item->getId()] = $item;
    }
    // Update the total
    $this->total += $item->getTotal();
  }

  public function removeItem($item_id) {
    // Check if the item exists in the cart
    if (array_key_exists($item_id, $this->items)) {
      // Subtract the total of the item from the cart total
      $this->total -= $this->items[$item_id]->getTotal();
      // Remove the item from the cart
      unset($this->items[$item_id]);
    }
  }

  public function updateItem($item_id, $quantity) {
    // Check if the item exists in the cart
    if (array_key_exists($item_id, $this->items)) {
      // Subtract the old total of the item from the cart total
      $this->total -= $this->items[$item_id]->getTotal();
      // Set the new quantity of the item
      $this->items[$item_id]->setQuantity($quantity);
      // Add the new total of the item to the cart total
      $this->total += $this->items[$item_id]->getTotal();
    }
  }

  public function clear() {
    // Empty the cart items
    $this->items = array();
    // Reset the cart total
    $this->total = 0;
  }
}

// Create a new cart object
$cart = new Cart();

// Check if the session has a cart
if (isset($_SESSION['cart'])) {
  // Retrieve the cart from the session
  $cart = $_SESSION['cart'];
}

// Check if the user wants to add a product to the cart
if (isset($_POST['add'])) {
  // Validate the token
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
    // Get the product id and quantity from the form
    $product_id = $_POST['id'];
    $quantity = $_POST['quantity'];


    // Validate the input
    if (is_numeric($product_id) && is_numeric($quantity) && $quantity > 0) {
      // Prepare a query to get the product details from the database
      $sql = "SELECT * FROM products WHERE id = ?";
      $stmt = $con->prepare($sql);
      $stmt->bind_param('i', $product_id);
      $stmt->execute();
      $result = $stmt->get_result();
      // Check if the product exists
      if ($result->num_rows > 0) {
        // Fetch the product details
        $row = $result->fetch_assoc();
        // Create a new cart item object
        $item = new CartItem($row['id'], $row['name'], $row['price'], $quantity);
        // Add the item to the cart
        $cart->addItem($item);
        // Store the cart in the session
        $_SESSION['cart'] = $cart;
        // Redirect to the cart page
        header('Location: cart.php');
        exit();
      } else {
        // Product not found
        echo "No product found";
      }
    } else {
      // Invalid input
      echo "Invalid input";
    }
  } else {
    // Invalid token
    echo "Invalid token";
  }
}

// Check if the user wants to remove a product from the cart
if (isset($_POST['remove'])) {
  // Validate the token
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
    // Get the product id from the form
    $product_id = $_POST['id'];
    // Validate the input
    if (is_numeric($product_id)) {
      // Remove the item from the cart
      $cart->removeItem($product_id);
      // Store the cart in the session
      $_SESSION['cart'] = $cart;
      // Redirect to the cart page
      header('Location: cart.php');
      exit();
    } else {
      // Invalid input
      echo "Invalid input";
    }
  } else {
    // Invalid token
    echo "Invalid token";
  }
}

// Check if the user wants to update a product in the cart
if (isset($_POST['update'])) {
  // Validate the token
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
    // Get the product id and quantity from the form
    $product_id = $_POST['id'];
	
    $quantity = $_POST['quantity'];
    // Validate the input
    if (is_numeric($product_id) && is_numeric($quantity) && $quantity > 0) {
      // Update the item in the cart
      $cart->updateItem($product_id, $quantity);
      // Store the cart in the session
      $_SESSION['cart'] = $cart;
      // Redirect to the cart page
      header('Location: cart.php');
      exit();
    } else {
      // Invalid input
      echo "Invalid input";
    }
  } else {
    // Invalid token
    echo "Invalid token";
  }
}

// Check if the user wants to clear the cart
if (isset($_POST['clear'])) {
  // Validate the token
  if (isset($_POST['token']) && $_POST['token'] == $_SESSION['token']) {
    // Clear the cart
    $cart->clear();
    // Store the cart in the session
    $_SESSION['cart'] = $cart;
    // Redirect to the cart page
    header('Location: cart.php');
    exit();
  } else {
    // Invalid token
    echo "Invalid token";
  }
}



// Display the cart contents
// Get the current file name
$filename = basename($_SERVER['PHP_SELF']);
// Check if the file name is cart.php
if ($filename == 'cart.php') {
  // Check if the token is set in session or post
  if (!isset($_SESSION['token'])) {
    // Display an error message and exit
    echo "<p>خطا: توکن معتبر نیست.</p>";
    exit;
  }
  echo "<h1>سبد خرید</h1>";
  echo "<div style='overflow-x:auto;'>";

  echo "<table >";
  echo "<tr><th>نام محصول</th><th>قیمت</th><th>تعداد</th><th>جمع</th><th>شناسه محصول</th><th>عملیات</th></tr>";
  // Loop through the cart items
  foreach ($cart->getItems() as $item) {
    // Use htmlspecialchars() to prevent XSS attacks
    echo "<tr>";
    echo "<td>" . htmlspecialchars($item->getName()) . "</td>";
    echo "<td>" . htmlspecialchars($item->getPrice()) . " تومان</td>";
    echo "<td>" . htmlspecialchars($item->getQuantity()) . "</td>";
    echo "<td>" . htmlspecialchars($item->getTotal()) . " تومان</td>";
	echo "<td>" . htmlspecialchars($item->getid()) . " </td>";

    echo "<td>";
    // Use a form to update or remove the item
    echo "<form action='cart.php' method='post'>";
    echo "<input type='hidden' name='id' value='" . htmlspecialchars($item->getId()) . "'>";
    echo "<input type='hidden' name='token' value='" . htmlspecialchars($_SESSION['token']) . "'>";
    echo "<input type='number' name='quantity' value='" . htmlspecialchars($item->getQuantity()) . "' min='1'>";
    echo "<input type='submit' name='update' value='به روز رسانی'>";
    echo "<input type='submit' name='remove' value='حذف'>";
    echo "</form>";
    echo "</td>";
    echo "</tr>";
  }
  // Display the cart total
  echo "<tr><td colspan='3'>قیمت کل</td><td>" . htmlspecialchars($cart->getTotal()) . " تومان</td><td></td></tr>";
  echo "</table>";
    echo "</br>";

  // Use a form to clear the cart
  echo "<form action='cart.php' method='post'>";
  echo "<input type='hidden' name='token' value='" . htmlspecialchars($_SESSION['token']) . "'>";
  echo "<input type='submit' name='clear' value='پاک کردن سبد خرید'>";
  echo "</form>";
      echo "</br>";

  // Use a form to proceed to checkout
  echo "<form action='checkout.php' method='post'>";
  echo "<input type='hidden' name='token' value='" . htmlspecialchars($_SESSION['token']) . "'>";
  echo "<input type='submit' name='checkout' value='خرید چند محصول'>";
  echo "</form>";
  echo "</div>";
}

?>