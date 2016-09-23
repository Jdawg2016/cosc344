<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 9/23/16
 * Time: 11:09 AM
 */
require_once('bookstoreTable.php');
if(!isset($_SESSION['id'])){
    session_start();
}

$theBookstore = null;

// if it is directed to this page by click the storeID
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = null;

    if ((isset($_GET['storeID'])) && !empty($_GET['storeID'])) {
        $id = $_GET['storeID'];
        $_SESSION['id'] = $id;

        if ($id != null) {
            $table = new BookstoreTable();
            $table->getAllBookStores();
            $theBookstore = $table->getBookstoreByID($id);
        }

    }

    $storeID = $theBookstore == null ? "" : $theBookstore->storeID;
    $city = $theBookstore == null ? "" : $theBookstore->city;
    $address = $theBookstore == null ? "" : $theBookstore->address;
    $account = $theBookstore == null ? "" : $theBookstore->account;
    $date_opened = $theBookstore == null ? "" : $theBookstore->date_opened;
    $total_salary = $theBookstore == null ? "0" : $theBookstore->total_salary;
}

?>

    <style type="text/css">
        form {
            display: table;
        }
        p {
            display: table-row;
        }

        label {
            display: table-cell;
        }

        input {
            display: table-cell;
        }

        select {
            display: table-cell;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="../js/formValidation.js"></script>
    <script>
        $(document).ready(function () {
            $("#date_opened").datepicker({
                changeMonth: true,
                changeYear: true
            });

//            $('#submit').on('click', function () {
//                alert("Bookstore ID: "+ $('#storeID').val() + "\n" +
//                        "City: "+$('#city').val() + "\n" +
//                        "Address: "+$('#address').val() + "\n" +
//                        "Account Number: "+$('#account').val() + "\n" +
//                        "Open date: "+$('#date_opened').val() + "\n" +
//                        "Total salary: "+$('#total_salary').value+"\n"
//                );
//            })

        });
    </script>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?>
    <div class="container">
        <form action="bookstoreForm.php" method="post" id="bookstoreForm" onsubmit="return validateBookstoreForm(this)">
            <p>
                <label>Bookstore ID</label>
                <input type="text" name="storeID" id="storeID" value="<?php echo $storeID; ?>" disabled>
            </p>
            <p>
                <label>City</label>
                <input type="text" name="city" id="city" value="<?php echo $city; ?>">
            </p>
            <p>
                <label>Address</label>
                <input type="text" name="address" id="address" value="<?php echo $address; ?>">
            </p>
            <p>
                <label>Account Number</label>
                <input type="text" name="account" id="account" value="<?php echo $account; ?>">
            </p>
            <p>
                <label>Open Date</label>
                <input type="text" name="date_opened" id="date_opened" value="<?php echo $date_opened; ?>">
            </p>
            <p>
                <label>Total Salary</label>
                <input type="text" name="total_salary" id="total_salary" value="<?php echo $total_salary; ?>" disabled>
            </p>
            <p>
                <input type="submit" value="Save" id="submit" name="submit">
            </p>
        </form>
    </div>
    <?php
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $table = new BookstoreTable();
    if ($_POST['submit'] == "Add") {
        $table->insertBookstore($_POST['storeID'], $_POST['city'], $_POST['address'], $_POST['account'], $_POST['date_opened']);
    } elseif ($_POST['submit'] == "Save") {
        $table->updateBookstoreWithID($_POST['city'], $_POST['address'], $_POST['account'], $_POST['date_opened'], $_SESSION['id']);
    }
}
?>
<p>
    <a href="../index.html">Go back to Home page</a>
</p>
<script>
    function validateBookstoreForm(form) {
        var fail = "";
        fail = checkCity(form.city.value);
        fail += checkAddress(form.address.value);
        fail += checkAccountNumber(form.account.value);
        fail += checkBookstoreID(form.storeID.value);

        if (isEmpty($('#date_opened').val())) {
            fail += "You must select open date\n";
        }
        
        if (isEmpty($('#total_salary').val())) {
            $('#date_opened').val(0);
        }

        if (fail == "") {
            return true;
        } else {
            alert(fail);
            return false;
        }
    }
</script>