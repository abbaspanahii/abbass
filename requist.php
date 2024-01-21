<?php
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// بررسی وجود متغیر buy در POST
if (isset($_POST['buy'])) {
    // بررسی وجود متغیرهای product_id, quantity, url_download, username, emails, address, postal_code و tel در POST
    if (isset($_POST['product_id'], $_POST['quantity'], $_POST['url_download'], $_POST['username'], $_POST['emails'], $_POST['address'], $_POST['postal_code'], $_POST['tel'])) {
        $product_id = htmlspecialchars($_POST['product_id']);
        // در اینجا مقدار input با نام quantity را دریافت می‌کنیم
        $quantity = htmlspecialchars($_POST['quantity']);
        $url_download = htmlspecialchars($_POST['url_download']);

        $username = htmlspecialchars($_POST['username']);
        $emails = htmlspecialchars($_POST['emails']);
        $address = htmlspecialchars($_POST['address']);
        $postal_code = htmlspecialchars($_POST['postal_code']);
        $tel = htmlspecialchars($_POST['tel']);

        $sql = "SELECT name, price ,url_download FROM products WHERE id = $product_id";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            $name = htmlspecialchars($product['name']);
            $url_download = htmlspecialchars($product['url_download']);

            $price = htmlspecialchars($product['price']);

            // در اینجا یک عامل ضربی با نام factor تعریف می‌کنیم
            $factor = 1;
            // در اینجا قیمت نهایی را با ضرب قیمت اولیه در تعداد و عامل ضربی محاسبه می‌کنیم
            $final_price = $price * $quantity * $factor;
            $MerchantID = "$mancherid"; // شناسه 
            $Description = 'خرید محصول ' . $name; // توضیحات پرداخت
            // در اینجا عبارت شرطی را برای تغییر $CallbackURL استفاده کرده‌ایم
            $CallbackURL = !empty($url_download) ? "$url/admin/shop/verify2.php" : "$url/admin/shop/verify.php";
            // در اینجا مقدار قیمت نهایی را به جای قیمت اولیه به وب سرویس زرین‌پال ارسال می‌کنیم
            $Amount = htmlspecialchars($final_price);
            session_start();
            $_SESSION['product_id'] = htmlspecialchars($product_id);
            // در اینجا مقدار input با نام quantity را در session ذخیره می‌کنیم
            $_SESSION['quantity'] = htmlspecialchars($quantity);
            $_SESSION['url_download'] = htmlspecialchars($url_download);

            // در اینجا مقدار username را در session ذخیره می‌کنیم
            // در اینجا مقادیر فیلدهای فرم را در session ذخیره می‌کنیم
            $_SESSION['username'] = htmlspecialchars($username);
            $_SESSION['emails'] = htmlspecialchars($emails);
            $_SESSION['address'] = htmlspecialchars($address);
            $_SESSION['postal_code'] = htmlspecialchars($postal_code);
            $_SESSION['tel'] = htmlspecialchars($tel);


            try {
                $client = new SoapClient('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
                $result = $client->PaymentRequest(
                    [
                        'MerchantID' => $MerchantID,
                        'Amount' => $Amount,
                        'Description' => $Description,
                        'CallbackURL' => $CallbackURL,
                    ]
                );
                if ($result->Status == 100) {
                    header('Location: https://sandbox.zarinpal.com/pg/StartPay/' . $result->Authority);
                } else {
                    echo 'خطا در ایجاد درخواست پرداخت. کد خطا: ' . $result->Status;
                }
            } catch (Exception $e) {
                echo 'خطا در اتصال به وب سرویس زرین‌پال. پیغام خطا: ' . $e->getMessage();
            }
        } else {
            echo 'محصول مورد نظر یافت نشد.';
        }
    } else {
        // نمایش پیام خطا یا بازگشت به صفحه قبل
    }
} else {
    echo 'درخواست نامعتبر.';
}
$con->close();
?>


