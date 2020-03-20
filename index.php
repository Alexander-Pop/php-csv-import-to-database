<?php
require_once 'config.php';

if (isset($_POST["import"])) {

    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {

        $file = fopen($fileName, "r");

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            //http://dev.mysql.com/doc/refman/5.0/en/example-auto-increment.html
            /*
            No value was specified for the AUTO_INCREMENT column, so MySQL assigned sequence numbers automatically. You can also explicitly assign NULL or 0 to the column to generate sequence numbers.
            $insertQuery = "INSERT INTO workorders (`priority`, `request_type`) VALUES('$priority', '$requestType', ...)";
            $insertQuery = "INSERT INTO workorders VALUES(NULL, '$priority', ...)";
            $insertQuery = "INSERT INTO workorders VALUES(0, '$priority', ...";
            */
            $sqlInsert = "
                INSERT INTO users (
                    userId,
                    userName,
                    userEmail,
                    password,
                    firstName,
                    lastName
                ) VALUES (
                    null,
                    '" . $column[1] . "',
                    '" . $column[2] . "',
                    '" . $column[3] . "',
                    '" . $column[4] . "',
                    '" . $column[5] . "'
                )";

            $result = mysqli_query($conn, $sqlInsert);

            if (! empty($result)) {
                $type = "success";
                $message = "CSV Data Imported into the Database";
            } else {
                $type = "error";
                trigger_error(mysqli_error($conn));
                $message = "Problem in Importing CSV Data";
            }
        }
    }
}
?>
<?php //https://www.tutorialrepublic.com/php-tutorial/php-mysql-ajax-live-search.php ?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Import CSV to MySQL in PHP</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" crossorigin="anonymous">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>
    </head>

    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2><img src="import-csv.png" width="50px"/>Import CSV file into Mysql using PHP</h2>
                    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block "; } ?>">
                        <?php if(!empty($message)) { echo $message; } ?>
                    </div>
                    <div class="outer-scontainer">
                        <div class="row">
                            <form class="form-horizontal" action="" method="post" name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data">
                                <div class="input-row">
                                    <label class="col-md-4 control-label">Choose CSV File
                                    </label>
                                    <input type="file" name="file" id="file" accept=".csv">
                                    <br>
                                    <button type="submit" id="submit" name="import" class="btn btn-success">Import</button>
                                    <br />
                                </div>
                            </form>
                        </div>
                        <?php
                            $sqlSelect = "SELECT * FROM users";
                            $result = mysqli_query($conn, $sqlSelect);
                        ?>    
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <table id='userTable'>
                                <thead>
                                    <tr>
                                        <th>User ID</th>
                                        <th>User Name</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_array($result)): ?>
                                        <tr>
                                            <td><?php echo $row['userId']; ?></td>
                                            <td><?php echo $row['userName']; ?></td>
                                            <td><?php echo $row['firstName']; ?></td>
                                            <td><?php echo $row['lastName']; ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
       <script>
            $(document).ready(function() {
                $("#frmCSVImport").on("submit", function() {
                    $("#response").attr("class", "");
                    $("#response").html("");
                    var fileType = ".csv";
                    var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + fileType + ")$");

                    if (!regex.test($("#file").val().toLowerCase())) {
                        $("#response").addClass("error");
                        $("#response").addClass("display-block");
                        $("#response").html("Invalid File. Upload : <b>" + fileType + "</b> Files.");
                        return false;
                    }

                    return true;
                });
            });
        </script>
        
    </body>

</html>