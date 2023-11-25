<!doctype html>
<html lang="en">
<head>
    <title>Select Columns</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

</head>
<body class="bg-light">
<section class="py-5">
    <div class="container bg-white shadow py-5">
        <div class="row">
            <div class="col-md-12 mx-auto">
                <div class="text-center">
                    <h4 class="h1 border-bottom pb-2">All Available Columns</h4>
                </div>

                <div class="col-md-10 mx-atuo text-right pr-5 ml-4">
                </div>

                <div class="mx-atuo text-right ml-4">
                    <input type="checkbox" id="checkall-1" checked>
                    <label for="checkall-1">Check/uncheck all</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="checkbox" id="checkall-2" checked>
                    <label for="checkall-2">Check/uncheck all</label>
                </div>

                <form class="form-horizontal" action="generate.php" method="post">
                    <fieldset>
                        <?php



                        $checked_tables_counter=0;
                        if ( isset( $_POST['table'] ) )
                        {
                            foreach ( $_POST['table'] as $table )
                            {
                                $i=0;
                                if (isset($table['tablecheckbox']) && $table['tablecheckbox'] == 1) {
                                    $tablename = $table['tablename'];
                                    $tabledisplay = $table['tabledisplay'];
                                    echo "<div class='text-center mb-4'><b>Table: " . $tabledisplay . " (". $tablename .")</b></div>";
                                    $sql = "SHOW columns FROM $tablename";
                                    $primary_keys = get_primary_keys($tablename);
                                    $auto_keys = get_autoincrement_cols($tablename);
                                    $foreign_keys = get_foreign_keys($tablename);

                                    $result = mysqli_query($link,$sql);
                                    while ($column = mysqli_fetch_array($result)) {

                                        $column_type = get_col_types($tablename,$column[0]);
                                        $column_comment = get_col_comments($tablename,$column[0]);
                                        $column_nullable = get_col_nullable($tablename,$column[0]);

                                        if (in_array ("$column[0]", $primary_keys)) {
                                            $primary = "🔑";
                                            echo '<input type="hidden" name="'.$tablename.'columns['.$i.'][primary]" value="'.$primary.'"/>';
                                        }
                                        else {
                                            $primary = "";
                                        }

                                        if (in_array ("$column[0]", $auto_keys)) {
                                            $auto = "🔒";
                                            echo '<input type="hidden" name="'.$tablename.'columns['.$i.'][auto]" value="'.$auto.'"/>';
                                        }
                                        else {
                                            $auto = "";
                                        }

                                        if (in_array ("$column[0]", $foreign_keys)) {
                                            $fk = "🛅";
                                            echo '<input type="hidden" name="'.$tablename.'columns['.$i.'][fk]" value="'.$fk.'"/>';
                                        }
                                        else {
                                            $fk = "";
                                        }

                                        if ($column_nullable) {
                                            $nb = "🫙";
                                        }
                                        else {
                                            $nb = "";
                                        }

                                        echo "<span data-toggle='tooltip' data-placement='top' title='$column_comment'>";
                                        echo '<div class="row align-items-center mb-2">
                                    <div class="col-2 text-right"
                                        <label class="col-form-label" for="'.$tablename.'">'. $primary . $auto . $fk . $nb . $column[0] . ' </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][tablename]" value="'.$tablename.'"/>
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][tabledisplay]" value="'.$tabledisplay.'"/>
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][columnname]" value="'.$column[0].'"/>
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][columntype]" value="'.$column_type.'"/>
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][columncomment]" value="'.$column_comment.'"/>
                                        <input type="hidden" name="'.$tablename.'columns['.$i.'][columnnullable]" value="'.$column_nullable.'"/>
                                        <input id="textinput_'.$tablename. '-'.$i.'"name="'. $tablename. 'columns['.$i.'][columndisplay]" type="text" placeholder="Display field name in frontend" class="form-control rounded-0">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="checkbox"  name="'.$tablename.'columns['.$i.'][columnvisible]" id="checkboxes-'.$checked_tables_counter.'-'.$i.'" value="1" checked>
                                <label for="checkboxes-'.$checked_tables_counter.'-'.$i.'">Visible in overview?</label></div>
                                    <div class="col-md-2">
                                        <input type="checkbox"  name="'.$tablename.'columns['.$i.'][columninpreview]" id="checkboxes-'.$checked_tables_counter.'-'.$i.'-2" value="1" checked>
                                <label for="checkboxes-'.$checked_tables_counter.'-'.$i.'-2">Visible in preview?</label></div>
                     </div></span>';
                                        $i++;
                                    }
                                    $checked_tables_counter++;
                                }
                            }
                        }
                        ?>

                        <div class="row">
                            <div class="col-md-8 mx-auto">
                                <p class="form-check">
                                    <small id="passwordHelpBlock" class="form-text text-muted">
                                        Cruddiy will create a fresh startpage in the app/ sub-folder, with link<?php echo $checked_tables_counter > 1 ? 's' : '' ?> to manage the table<?php echo $checked_tables_counter > 1 ? 's' : '' ?> above.<br>
                                        If you have used Cruddiy on other tables before, your start page will be replaced by the fresh one, and previous links will be lost.
                                    </small>
                                    <input class="form-check-input" type="checkbox" value="true" id="keep_startpage" name="keep_startpage">
                                    <label class="form-check-label" for="keep_startpage">
                                        Keep previously generated startpage and CRUD pages if they exist
                                    </label>
                                    <br>
                                    <input class="form-check-input" type="checkbox" value="true" id="append_links" name="append_links">
                                    <label class="form-check-label" for="append_links">
                                        Append new link<?php echo $checked_tables_counter > 1 ? 's' : '' ?> to previously generated startpage if necessary
                                    </label>
                                </p>
                            </div>
                            <div class="col-md-8 mx-auto">
                                <button type="submit" id="singlebutton" name="singlebutton" class="btn btn-success btn-block rounded-0 shadow-sm">Generate Pages</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</section>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
<script>
$(document).ready(function () {
    $('#checkall-1').click(function(e) {
        var chb = $('.form-horizontal').find('input[name$="[columnvisible]"]');
        chb.prop('checked', !chb.prop('checked'));
    });
});
$(document).ready(function () {
    $('#checkall-2').click(function(e) {
        var chb = $('.form-horizontal').find('input[name$="[columninpreview]"]');
        chb.prop('checked', !chb.prop('checked'));
    });
});
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
</body>
</html>
