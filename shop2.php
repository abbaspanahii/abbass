<?php
 //تابع خرید فروشگاه به همراه نمایش  پست
function shop_karid() {
require('admin/admin/conn.php');
// Use $_POST instead of $_GET to prevent SQL injection
if (isset ($_POST ['id'])) { // بررسی وجود مقدار id
  $id = $_POST['id'];

// Use prepared statements instead of mysqli_real_escape_string to prevent SQL injection
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
  $product = $result->fetch_assoc();
  echo "<div class='col-md-3'>";
  echo "<img src='" .htmlspecialchars(filter_var($product["image"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."' alt='" .htmlspecialchars(filter_var($product["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "'>";

  echo "<title>" .htmlspecialchars(filter_var($product["name"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "-$namewebsite</title>";



  echo "<h1>" . htmlspecialchars($product['name']) . "</h1>";
echo "<h2 style=\"color: white; background-color: green;\">قیمت" .htmlspecialchars(filter_var($product["price"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). " تومان</h2>";
echo '<h4><p style="color: white;background-color:400500;"> ***اگر در حال خرید دانلود هستید قسمت  تعداد را تغییر ندهید*** </p></h4><hr />';

echo "<form action='../admin/shop/requist.php' method='post' >";
echo '<h2 style="color: blue;">فرم خرید </h2>';

// Add a hidden input with name='id' and value=$product['id']
echo "<input type='hidden' name='id' value='" .htmlspecialchars(filter_var($product["id"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES)."'>";
echo "<input type='hidden' name='product_id' value='" .htmlspecialchars(filter_var($product["id"], FILTER_SANITIZE_FULL_SPECIAL_CHARS), ENT_QUOTES). "'>";
echo "<input type='hidden' name='url_download'>";

// خواندن مقدار url_download از دیتابیس
$sql = "SELECT url_download FROM products WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
  $product = $result->fetch_assoc();
  $url_download = $product['url_download'];
}

// بررسی شرط برای غیرفعال یا فعال کردن قسمت تعداد
// بررسی شرط برای غیرفعال یا فعال کردن قسمت تعداد
if (!empty($url_download)){ 
  // غیرفعال کردن قسمت تعداد
  echo '<h4><label for="number">تعداد:</label>';
  echo "<input type='number' name='quantity' value='".htmlspecialchars(1)."' min='".htmlspecialchars(1)."' max='".htmlspecialchars(10)."' disabled></p>";
  // اضافه کردن یک input مخفی با همان نام و مقدار
  echo "<input type='hidden' name='quantity' value='".htmlspecialchars(1)."'>";
} else {
  // فعال کردن قسمت تعداد
  echo '<h4><label for="number">تعداد:</label>';
  echo "<input type='number' name='quantity' value='".htmlspecialchars(1)."' min='".htmlspecialchars(1)."' max='".htmlspecialchars(10)."'></p>";
}
echo '<h4><label for="username">نام و نام خانوادگی شما:</label>';
echo "<input type='text' name='username' id='username' placeholder='نام کامل شما' pattern='[آ-یA-Za-z]+'".htmlspecialchars('نام کاربری')."'></p></h4>";
echo '<h4><label for="email">ایمیل:</label>';
if (isset($_SESSION['email'])) {
    // جلسه تنظیم شده است و می‌توانید از $_SESSION['email'] استفاده کنید
    echo "<input type='email' name='emails' id='email' placeholder='' value='" . $_SESSION['email'] . "'>";
} else {
    // جلسه تنظیم نشده است و نباید از $_SESSION['email'] استفاده کنید
    echo "<input type='email' name='emails' id='email' placeholder='ایمیل خود را وارد کنید' value=''>";
}
echo '<h4><label for="address">ادرس گیرنده:</label>';
echo "<input type='text' name='address' id='address' placeholder='".htmlspecialchars('آدرس استان، شهر، خیابان، کوچه')."' pattern='[آ-یA-Za-z]+'></p></h4>";
echo '<h4><label for="postal_code">کد پستی:</label>';
echo "<input type='text' name='postal_code' id='postal_code' placeholder='کد پستی 10 رقمی' pattern='[0-9]{10}' ".htmlspecialchars('کد پستی')."'></p></h4>";
echo '<h4><label for="tel">تلفن:</label>';

echo "<input type='tel' name='tel' id='tel' placeholder='تلفن 11 رقمی با 09 شروع میشود' pattern='09[0-9]{9}' ".htmlspecialchars('تلفن')."'></p></h4>";
echo '<h4><p><input type="checkbox" id="agree" name="agree" value="yes">';
echo '<label for="agree">من <a href="https://example.com/rules">قوانین و مقررات</a> را خوانده و قبول دارم</label></h4>';
// Add a security token to prevent CSRF attacks
echo '<input type="hidden" name="token" value="' . $_SESSION['token'] . '">';
echo "<button type='button' name='buy' id='buy' onclick='checkAndSubmit()'>خرید </button>";
echo "</form>";
  echo "<h5>!توجه:اگر ثبت نام کرده و وارد شوید میتوانید با ایمیل خودتان خرید کنید</h5>";

  echo "</div>";
} else {
  echo "متاسفانه خطایی رخ داده است.";
}
} else {
  echo "متاسفانه خطایی رخ داده است.";
}

mysqli_close($con);
echo "<script>";
echo "function checkAndSubmit() {";
echo "  var username = document.getElementById(\"username\").value;";
echo "  var email = document.getElementById(\"email\").value;";
echo "  var address = document.getElementById(\"address\").value;";
echo "  var postal_code = document.getElementById(\"postal_code\").value;";
echo "  var tel = document.getElementById(\"tel\").value;";
echo "  var agree = document.getElementById(\"agree\").checked;";
echo "  if (username && email && address && postal_code && tel && agree) {";
echo "    document.getElementById(\"buy\").type = \"submit\";";
echo "    document.getElementById(\"buy\").click();";
echo "  } else {";
echo "    alert(\"لطفا همه فیلدهای فرم را پر کنید و قوانین و مقررات را قبول داشته باشید.\");";
echo "  }";
echo "}";
echo "</script>";

}
?>
