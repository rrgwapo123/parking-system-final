<?php
session_start();
error_reporting(0);
include('includes/dbconn.php');

if (strlen($_SESSION['vpmsaid']==0)) {
    header('location:logout.php');
} else {
    if (isset($_POST['submit-vehicle'])) {
        $parkingnumber = mt_rand(10000, 99999);
        $catename = $_POST['catename'];
        $vehcomp = $_POST['vehcomp'];
        $vehreno = $_POST['vehreno'];
        $ownername = $_POST['ownername'];
        $ownercontno = $_POST['ownercontno'];
        $numhours = $_POST['numhours'];
        
        // Fetch the hourly rate for the selected category
        $query = mysqli_query($con, "SELECT HourlyRate FROM vcategory WHERE VehicleCat='$catename'");
        $result = mysqli_fetch_array($query);
        $hourlyrate = $result['HourlyRate'];
        
        // Calculate the total parking charge
        $parkingcharge = $hourlyrate * $numhours;
        
        // Insert vehicle information along with the hourly rate, parking charge, and parking timer
        $query = mysqli_query($con, "INSERT INTO vehicle_info (ParkingNumber, VehicleCategory, VehicleCompanyname, RegistrationNumber, OwnerName, OwnerContactNumber, HourlyRate, ParkingCharge, ParkingTimer) 
                                     VALUES ('$parkingnumber', '$catename', '$vehcomp', '$vehreno', '$ownername', '$ownercontno', '$hourlyrate', '$parkingcharge', '$numhours')");
        
        if ($query) {
            echo "<script>alert('Vehicle Entry Detail has been added');</script>";
            echo "<script>window.location.href ='dashboard.php'</script>";
        } else {
            echo "<script>alert('Something Went Wrong');</script>";       
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VPS</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/datepicker3.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    
    <!--Custom Font-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <?php
    $page = "manage-vehicles";
    include 'includes/sidebar.php';
    ?>
    
    <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
        <div class="row">
            <ol class="breadcrumb">
                <li><a href="dashboard.php">
                    <em class="fa fa-home"></em>
                </a></li>
                <li class="active">Manage Vehicle</li>
            </ol>
        </div><!--/.row-->
        
        <div class="row">
            <div class="col-lg-12">
                <!-- <h1 class="page-header">Vehicle Management</h1> -->
            </div>
        </div><!--/.row-->
        
        <div class="panel panel-default">
            <div class="panel-heading">Vehicle Entry</div>
            <div class="panel-body">
                <div class="col-md-12">
                    <form method="POST">
                        <div class="form-group">
                            <label>Registration Number</label>
                            <input type="text" class="form-control" placeholder="License Plate" id="vehreno" name="vehreno" required>
                        </div>

                        <div class="form-group">
                            <label>Vehicle Make</label>
                            <input type="text" class="form-control" placeholder="Ex. Honda" id="vehcomp" name="vehcomp" required>
                        </div>

                        <div class="form-group">
                            <label>Vehicle Category</label>
                            <select class="form-control" name="catename" id="catename" required>
                                <option value="">Select Category</option>
                                <?php 
                                $query = mysqli_query($con, "SELECT * FROM vcategory");
                                while ($row = mysqli_fetch_array($query)) { 
                                ?>    
                                    <option value="<?php echo $row['VehicleCat']; ?>"><?php echo $row['VehicleCat']; ?></option>
                                <?php } ?> 
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Hourly Rate</label>
                            <input type="text" class="form-control" id="hourlyrate" readonly>
                        </div>

                        <div class="form-group">
                            <label>Number of Hours</label>
                            <input type="number" class="form-control" id="numhours" name="numhours" required>
                        </div>

                        <div class="form-group">
                            <label>Owner's Full Name</label>
                            <input type="text" class="form-control" placeholder="Enter Here.." id="ownername" name="ownername" required>
                        </div>

                        <div class="form-group">
                            <label>Owner's Contact</label>
                            <input type="text" class="form-control" placeholder="Enter Here.." maxlength="10" pattern="[0-9]+" id="ownercontno" name="ownercontno" required>
                        </div>

                        <button type="submit" class="btn btn-success" name="submit-vehicle">Submit</button>
                        <button type="reset" class="btn btn-default">Reset</button>
                    </form>
                </div> <!-- col-md-12 ends -->
            </div>
        </div><!--/.panel-->
        
        <?php include 'includes/footer.php'; ?>
    </div><!--/.main-->
    
    <script src="js/jquery-1.11.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/chart.min.js"></script>
    <script src="js/chart-data.js"></script>
    <script src="js/easypiechart.js"></script>
    <script src="js/easypiechart-data.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>
    <script src="js/custom.js"></script>
    <script>
        $(document).ready(function() {
            $('#catename').change(function() {
                var category = $(this).val();
                if (category) {
                    $.ajax({
                        url: 'fetch_rate.php',
                        type: 'POST',
                        data: { category: category },
                        success: function(data) {
                            $('#hourlyrate').val(data);
                        }
                    });
                } else {
                    $('#hourlyrate').val('');
                }
            });
        });
    </script>
</body>
</html>
