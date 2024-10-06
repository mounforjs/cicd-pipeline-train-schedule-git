<!-- Bootstrap Core CSS -->
<link rel="stylesheet" href="https://opentdb.com/css/bootstrap.min.css" type="text/css">

<link rel="stylesheet" href="https://opentdb.com/css/trivia.css" type="text/css">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>


<!-- Menu Dropdowns -->
<style>
    ul.nav li.dropdown:hover ul.dropdown-menu {
        display: block;
    }
</style>
<link rel='stylesheet' href='https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css'>

<script src='https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js'></script>

<script>
$(document).ready(function() {
 $('#myAdvancedTable').DataTable({
   autoWidth: false,
   "scrollX": true,
  "order": [[ 0, 'asc' ]]
 });

});

</script>

    <div class="container">
        <h2> Questions for Quiz : <?php echo $quiz->name; ?> </h2>
        <table class="table table-bordered" id="myAdvancedTable">
            <thead>
                <tr>
                    <th><b>ID</b> <a href="?sort=asc"><i class="fa fa-sort"></i></a></th>
                    <th><b>Category</b></th>
                    <th><b>Type</b></th>
                    <th><b>Difficulty</b></th>
                    <th><b>Question / Statement</b></th>
                    <th><b>Options</b></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($question as $key => $value) { ?>
                    <tr>
                        <td><?php echo $value['id']; ?></td>
                        <td><?php echo $value['category_name']; ?></td>
                        <td><?php echo $value['type']; ?></td>
                        <td><?php echo $value['difficulty']; ?></td>
                        <td><?php echo $value['question']; ?></td>
                        <td>
                            <?php if ($value['type'] == 'multiple') { ?>
                                <?php echo "Correct :<br>". json_decode($value['correct_answer'])[0];

                                if (isset(json_decode($value['correct_answer'])[1])) {
                                    echo ", ".json_decode($value['correct_answer'])[1];
                                }

                                echo "<br>InCorrect :<br>";
                                if (isset(json_decode($value['incorrect_answer'])[0])) {
                                    echo json_decode($value['incorrect_answer'])[0];
                                }
                                if (isset(json_decode($value['incorrect_answer'])[1])) {
                                    echo ", ".json_decode($value['incorrect_answer'])[1];
                                }
                                if (isset(json_decode($value['incorrect_answer'])[1])) {
                                    echo ", ".json_decode($value['incorrect_answer'])[1];
                                }
                                ?>
                            <?php } ?>
                            <?php if ($value['type'] == 'boolean') { ?>
                                <?php echo "Correct : ".($value['boolean_answer'] == 1) ? 'True' : 'False'; ?>
                            <?php } ?>
                            <?php if ($value['type'] == 'one') { ?>
                                <?php echo "Correct : ".json_decode($value['correct_answer'])[0]; ?>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
