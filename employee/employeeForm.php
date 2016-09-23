<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 9/19/16
 * Time: 4:49 PM
 */
require_once('employeeTable.php');
if(!isset($_SESSION['id'])){
    session_start();
}
$theEmployee = null;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = null;
    if (!isset($_GET['addNewEmployee'])) {
        $ird = $_GET['ird'];
        $_SESSION['id'] = $ird;

        if ($ird != null) {
            $table = new EmployeeTable();
            $table->update();
            $theEmployee = $table->getEmployeeByIRD($ird);

        }
    }
    
    $fname = $theEmployee == null ? "" : $theEmployee->fname;
    $middle_name = $theEmployee == null ? "" : $theEmployee->middle_init;
    $lname = $theEmployee == null ? "" : $theEmployee->lname;
    $ird_number = $theEmployee == null ? "" : $theEmployee->ird_number;
    $contanct_number = $theEmployee == null ? "" : $theEmployee->contact_number;
    $weekly_hours = $theEmployee == null ? "select weekly hours" : $theEmployee->weekly_hours;
    $hourly_rate = $theEmployee == null ? "select weekly hours first" : $theEmployee->hourly_rate;
    $bookstoreAddress = $theEmployee == null ? "select bookstore address" : $theEmployee->baddress;
}
?>
<style type="text/css">
    form  { display: table;      }
    p     { display: table-row;  }
    label { display: table-cell; }
    input { display: table-cell; }
    select{ display: table-cell; }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script src="../js/formValidation.js"></script>
<script>
    $(document).ready(function () {
        $('#weekly_hours').on('change', function () {
            var selectedValue = $(this).val();
            if (selectedValue) {
                $.ajax('ajaxData.php', {
                    type: 'POST',
                    data: 'selected_weekly_hours=' + selectedValue,
                    success: function (html) {
                        $('#hourly_rate').html(html);
                    }
                });
            } else {
                $('#hourly_rate').html('<option value="">Select Hourly Rate</option>');
            }
        });
    });
</script>

<?php
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        ?>
        <div class="container">
            <form action="employeeForm.php" method="post" id="employeeForm" onsubmit="return validateEmployeeForm(this)">
                <p>
                    <label>First Name</label>
                    <input type="text" name="fname" id="fname" value="<?php echo $fname; ?>">
                </p>
                <p>
                    <label>Middle Name</label>
                    <input type="text" name="middle_name" id="middle" value="<?php echo $middle_name; ?>">
                </p>
                <p>
                    <label>Last Name</label>
                    <input type="text" name="lname" id="lname" value="<?php echo $lname; ?>">
                </p>
                <p>
                    <label>IRD Number</label>
                    <input type="text" name="ird_number" id="ird" value="<?php echo $ird_number; ?>" disabled >
                </p>
                <p>
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" id="contact" value="<?php echo $contanct_number; ?>">
                </p>
                <p>
                    <label>Weekly Hours</label>
                    <select name="weekly_hours" id="weekly_hours">
                        <?php
                        // for employee wage selection
                        $connection = new Connection();
                        if ($connection->testConnection()) {
                            $conn = $connection->getConnection();
                            $queryString = "select distinct(weekly_hours) from employee_wage";
                            $stid = oci_parse($conn, $queryString);
                            $result = oci_execute($stid);

                            if ($weekly_hours == "select weekly hours") {
                                echo "<option value=''>" . $weekly_hours . "</option>";
                            }

                            if ($result) {
                                while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                                    if ($row['WEEKLY_HOURS'] == $weekly_hours) {
                                        echo "<option value='{$row['WEEKLY_HOURS']}' selected='selected'>" . $row['WEEKLY_HOURS'] . "</option>";
                                    } else {
                                        echo "<option value='{$row['WEEKLY_HOURS']}'>" . $row['WEEKLY_HOURS'] . "</option>";
                                    }
                                }
                            }
                            oci_free_statement($stid);
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <label>Hourly Rate</label>
                    <select name="hourly_rate" id="hourly_rate">
                        <option value=<?php echo $hourly_rate; ?>><?php echo $hourly_rate; ?></option>
                    </select>
                </p>
                <p>
                    <label>Bookstore Address</label>
                    <select name="baddress" id="baddress">
                        <?php
                        if ($connection->testConnection()) {
                            $conn = $connection->getConnection();
                            $queryString = "select distinct(address) from bookstore";
                            $stid = oci_parse($conn, $queryString);

                            $result = oci_execute($stid);
                            if ($bookstoreAddress == "select bookstore address") {
                                echo "<option value='" . $bookstoreAddress . "'>" . $bookstoreAddress . "</option>";
                            }
                            while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS)) {
                                $address = $row['ADDRESS'];
                                if($address == $bookstoreAddress){
                                    echo "<option value='" . $address . "' selected='selected'>" . $address ."</option>";

                                }else {
                                    echo "<option value='" . $address . "'>" . $address . "</option>";
                                }
                            }
                            oci_free_statement($stid);
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <input type="submit" value="Save" id="submit" name="submit">
                </p>
            </form>
        </div>
        <?php
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {


        $table = new EmployeeTable();
        $dom = new DOMDocument();
        if ($_POST['submit'] == "Save") {
            $newEmployee = new Employee($_POST['fname'], $_POST['middle_name'], $_POST['lname'], $_SESSION['id']
                ,$_POST['contact_number'], $_POST['weekly_hours'], $_POST['hourly_rate'], $_POST['baddress']);
            $table->updateEmployee($newEmployee, $_SESSION['id']);
            session_destroy();
        } elseif ($_POST['submit'] == "Add") {
            $table->insertEmployee($_POST['fname'], $_POST['middle_name'], $_POST['lname'], $_POST['ird_number']
                ,$_POST['contact_number'], $_POST['weekly_hours'], $_POST['hourly_rate'], $_POST['baddress']);
        }

//        header('Location: '.'http://localhost/cosc344/employeeRecords.php');
    }
?>
<p>
    <a href="../index.html">Go back to Home page</a>
</p>
<script>
    function validateEmployeeForm(form) {
        var fail = "";
        fail = checkName(form.fname.value);
        fail += checkName(form.lname.value);
        fail += checkMiddle_init(form.middle_name.value);
        fail += checkIRD(form.ird_number.value);
        fail += checkContactNumber(form.contact_number.value);


        if (isEmpty($('#weekly_hours').val())) {
            fail += "You must select weekly hours\n";
        }
        if ($('#baddress').val() == "select bookstore address") {
            fail += "Please select the bookstore\n";
        }

        if (fail == "") {
            return true;
        } else {
            alert(fail);
            return false;
        }
    }
</script>

