<?php require_once("../lib/init.php"); ?>
<?php
	//$properties = Property::findAll();
	// Current Page Number
	$page = (!empty($_GET['page'])) ? (int)$_GET['page'] : 1;
	// Records Per Page
	$per_page = 3;
	$property_count = Property::countAll();
	$pagination = new Pagination($page, $per_page, $property_count);
	$properties = Property::getSubsetOfProperties($pagination);
?>
<?php include_layout_template("admin_header.php"); ?>

	<div id="container">
	<h3>Actions</h3>
    <div id="side-bar">
	<?php
        $actions = array("tenant_search" => "Search Tenant", "tenants_old" => "Previous Tenants");
        echo create_action_links($actions);
    ?>
    </div>
    <div id="main-content">
    <a class="new-item" href="choose_prop.php" title="Add room(s) to a property">New Room(s)</a>
    <h2>View Rooms</h2>
    
    <?php 
        $mesg = $session->message(); 
        echo output_message($mesg);
    ?>
    
    <?php foreach($properties as $property): ?>
    <div class="prop-rooms">
    <div class="name-of-prop">
        <h3><?php echo $property->getPropertyName(); ?></h3>
    </div>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
        <th>Room Label</th>
        <th>Vacant</th>
        <th>Rent/Month</th>
    </tr>
    </thead>
    <tbody>
    <?php $rooms = Room::findByPropertyId($property->id); ?>
    <?php foreach($rooms as $room): ?>
    <tr>
        <td align="right"><?php echo $room->getRoomLabel(); ?></td>
        <td align="right"><?php echo $room->isVacant() ? "YES" : "NO"; ?></td>
        <td align="right"><?php echo $room->getRent(); ?></td>
        <td align="right"><a href="edit_room.php?rid=<?php echo $room->id; ?>" title="edit details of this room">Edit</a></td>
        <td align="right"><a class="del-room" href="delete_room.php?rid=<?php echo $room->id; ?>" title="delete this room">Del</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    </div>
    <?php endforeach; ?>
    
    <!-- //////////////////// PAGINATION LINKS ///////////////// -->
        
    <div id="pagination">
    <?php
    // Check To See If Pagination Exists
    if($pagination->totalPages() > 1) {
        // Confirm That Previous Page Exists
        if($pagination->hasPreviousPage()) {
            $prev_link  = "&nbsp;<a href=\"rooms.php?page=";
            $prev_link .= $pagination->previousPage();
            $prev_link .= "\">&laquo; Previous </a>&nbsp;";
            echo $prev_link;	
        }
        
        // Output Pagination Links
        for($i = 1; $i <= $pagination->totalPages(); $i++) {
            if($i == $page) {
                echo "&nbsp;<span class=\"selected\">{$i}</span>&nbsp;";	
            } else {
                $pg_links  = "&nbsp;<a href=\"rooms.php?page={$i}\">{$i}</a>&nbsp;";
                echo $pg_links;	
            }	
        }
        
        // Confirm That Next Page Exists
        if($pagination->hasNextPage()) {
            $next_link  = "&nbsp;<a href=\"rooms.php?page=";
            $next_link .= $pagination->nextPage();
            $next_link .= "\">Next &raquo;</a>&nbsp;";
            echo $next_link;	
        }	
    }
    ?>
    </div> <!-- end pagination -->
    
    </div> <!-- main-content -->
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>