<?php
    $useJqueryUI = true;
    include_once 'header.php';
    $selected = "features";
    include_once 'topnav.php';
    session_start();
    if(!isset($_SESSION['uid'])) {
        header("Location: index.php");
        exit;
    }

    include_once 'admin.inc.php';
?>
<script>
  $( function() {
    $( "tbody#sortable" ).sortable({
      placeholder: "ui-state-highlight",
      update: function(event, ui) {
          var new_order = [];
          $('tbody#sortable').children('tr').each(function(i) {
              if(i!== 0) {
                  // Feature this.id is now in position i
                  new_order[this.id] = i;
              }
          })
          $('#hidden_input').val(JSON.stringify(new_order));
      },
      items: "tr:not(.ui-state-disabled)"
    });
  } );
</script>
<style>

table{
    border-collapse: collapse;
    position: relative;
    margin-top: 20px;
    margin-left: 50%;
    transform: translateX(-50%);
    border: 1px solid black;
}

tr.ui-state-disabled th{
    background: var(--main-color);
    border: 1px solid black;
    border-bottom: 3px solid black;
    color: white;
}

tr.ui-state-default {
    padding:0;
    margin: 0;
    cursor: grab;
    border: 1px solid black;
    background: white
}

tr.ui-sortable-helper {
    margin: 0;
    padding-bottom : 1px;
    cursor: grabbing;
    box-shadow: 2px 2px 8px -2px black;
}

tr.ui-state-highlight {
    background-color: #909090;
}

td,th{
    height:20px;
    padding:10px;
    margin: 0;
}

th{
    height: 25px;
}

td.id_cell, td.actions_cell{
    width:100px;
}

td.title_cell {
    width:1000px;
}

</style>



<div class="message_wrapper">
    <?php
    // Here we gonna deal with updating the positions
    if(isset($_GET['submit']) && !empty($_GET['new_order'])) {
        $_GET['new_order'] = json_decode($_GET['new_order']);
        // Only need to do anything if somebody hit submit after changing the order
        foreach ($_GET['new_order'] as $feature_id => $position) {
            $sql = "UPDATE feature_cards SET position = :pos WHERE feature_id = :id";
            $stmt = $admin_pdo->prepare($sql);
            $stmt->bindValue('pos',$position);
            $stmt->bindValue('id',$feature_id);
            $stmt->execute();
        }
        echo "<p>Order updated</p>";
    }

    ?>
    <table>
        <tbody id="sortable">
        <tr class='ui-state-disabled'>
            <th>Feature ID</th>
            <th>Title</th>
            <th>Actions</th>
        </tr>

    <?php
        $sql = "SELECT f.*, r.title FROM feature_cards f, recipes r WHERE f.recipe_id=r.id ORDER BY f.position ASC";
        $stmt = $admin_pdo->prepare($sql);
        $stmt->execute();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr class ='ui-state-default' id='".$row['feature_id']."'>";
            echo "<td class='id_cell'>".$row['feature_id']."</td>";
            echo "<td class='title_cell'>".$row['title']."</td>";
            echo "<td class='actions_cell'>";
            echo "<a href='edit_feature.php?id=".$row['feature_id']."'><i class='fas fa-pencil-alt'></i></a>";
            echo " &nbsp; &nbsp; ";
            echo "<a href='delete_feature.php?id=".$row['feature_id']."'><i class='fas fa-trash-alt'></i></a>";
            echo "</td></tr>";
        }
    ?>
    </tbody>
    </table>
    <form action="features.php" method="get">
        <input type="hidden" name="new_order" id="hidden_input">
        <button type="submit" name="submit" class="action_button">Submit</button>
    </form>
</div>
<div class="new_post_button"><a href="new_feature.php">+</a></div>
<?php include_once 'footer.php'; ?>
