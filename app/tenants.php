<?php require_once("../lib/init.php"); ?>
<?php if(!$session->isLoggedIn()) { redirect_to("../index.php"); } ?>
<?php
	// Current Page Number
	$page = (!empty($_GET['page'])) ? (int)$_GET['page'] : 1;
	// Records Per Page
	$per_page = 2;
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
    <a class="new-item" href="choose_prop.php" title="Add tenant(s) to the system">New Tenant(s)</a>    
    <?php 
        $mesg = $session->message();
        echo output_message($mesg);
    ?>
    
    <div id="tenant-search">
    <fieldset>
    <legend>Find Tenant</legend>
    <form action="tenant_search.php" method="get">
    <table cellpadding="5">
    <tr>
    	<td><label for="tenant">Enter Tenant Name
        <span class="required-field">*</span></label></td>
        <td><input type="text" name="tenant" id="tenant" /></td>
        <td><input type="submit" name="submit" value="Search" /></td>
    </tr>
    </table>
    </form>  
    </fieldset>       
    </div>
    
    <h2>Tenants</h2>
    
    <?php foreach($properties as $property): ?>
    <div class="name-of-prop">
        <p><?php echo $property->getPropertyName(); ?></p>
    </div>
    <table cellpadding="5" class="bordered">
    <thead>
    <tr>
        <th>Name of Tenant</th>
        <th>Room Label</th>
        <th>Business Name</th>
        <th>Details</th>
    </tr>
    </thead>
    <tbody>
    <?php $tenants = Tenant::findByPropertyId($property->id); ?>
    <?php foreach($tenants as $tenant): ?>
    <?php $room = Room::findByTenantId($tenant->id); ?>
    <?php
        /**echo "Tenant: ";
        echo "<tt></pre>".var_dump($tenant)."</pre></tt>";
        echo "Room: ";
        echo "<tt><pre>".var_dump($room)."</pre></tt>";*/
    ?>
    <tr>
        <td><?php echo $tenant->getFullName(); ?></td>
        <td><?php echo $room->getRoomLabel(); ?></td>
        <td><?php echo $tenant->getBusinessName(); ?></td>
        <td><a href="tenant.php?tid=<?php echo $tenant->id; ?>" title="view tenant details">Details</a></td>
        <td><a href="edit_tenant.php?tid=<?php echo $tenant->id; ?>" title="edit tenant details">Edit</a></td>
        <td><a class="del-tenant" href="delete_tenant.php?tid=<?php echo $tenant->id; ?>" title="delete this tenant">Del</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    </table>
    <?php endforeach; ?>
    
    <!-- //////////////////// PAGINATION LINKS ///////////////// -->
        
    <div id="pagination">
    <?php
    // Check To See If Pagination Exists
    if($pagination->totalPages() > 1) {
        // Confirm That Previous Page Exists
        if($pagination->hasPreviousPage()) {
            $prev_link  = "&nbsp;<a href=\"tenants.php?page=";
            $prev_link .= $pagination->previousPage();
            $prev_link .= "\">&laquo; Previous </a>&nbsp;";
            echo $prev_link;	
        }
        
        // Output Pagination Links
        for($i = 1; $i <= $pagination->totalPages(); $i++) {
            if($i == $page) {
                echo "&nbsp;<span class=\"selected\">{$i}</span>&nbsp;";	
            } else {
                $pg_links  = "&nbsp;<a href=\"tenants.php?page={$i}\">{$i}</a>&nbsp;";
                echo $pg_links;	
            }	
        }
        
        // Confirm That Next Page Exists
        if($pagination->hasNextPage()) {
            $next_link  = "&nbsp;<a href=\"tenants.php?page=";
            $next_link .= $pagination->nextPage();
            $next_link .= "\">Next &raquo;</a>&nbsp;";
            echo $next_link;	
        }	
    }
    ?>
    </div> <!-- end pagination -->
    
    </div>
    </div> <!-- container -->

<?php include_layout_template("admin_footer.php"); ?>