      <div id="footer">
	  <?php
		  $session = $GLOBALS['session'];
		  if($session->isLoggedIn()) { echo "<p>".$session->sessionVar('user'); }
		  echo "&nbsp;";
		  echo "<a href=\"logout.php\" title=\"sign out\">Sign Out</a></p>";
      ?>
      </div> <!-- footer -->
</div> <!-- wrapper -->
	<!-- JAVASCRIPTS -->
    <script type="text/javascript">
		$(function() {
			$("#added, #joined, #left, #start_date, #end_date, #paid, #refunded, #date").datepicker({
				dateFormat : "yy-mm-dd",	
				changeMonth: true,
				changeYear: true
			});
			
			$("a.del-room, a.del-prop, a.del-tenant, a.del-arrears, a.del-deposit, a.del-rent, a.del-exp, a.del-user, a.del-log").click(
				function(evt){
					var className = $(this).attr("class");
					if(className == "del-room"){
						var confirmDelete = confirm("Are you sure you want to delete this room?");
						if(!confirmDelete){
							return false;	
						}	
					}
					if(className == "del-prop"){
						var confirmDelete = confirm("Deleting this property will delete all the rooms and tenants assigned to it. Are you sure you want proceed?");
						if(!confirmDelete){
							return false;	
						}	
					}
					if(className == "del-tenant"){
						var confirmDelete = confirm("Are you sure you want to delete this tenant?");
						if(!confirmDelete){
							return false;	
						}	
					}
					if(className == "del-arrears"){
						var confirmDelete = confirm("Are you sure you want to delete this arrears?");
						if(!confirmDelete){
							return false;	
						}	
					}
					if(className == "del-deposit"){
						var confirmDelete = confirm("Are you sure you want to delete this deposit?");	
						if(!confirmDelete){
							return false;	
						}
					}
					if(className == "del-rent"){
						var confirmDelete = confirm("Are you sure you want to delete this rent payment?");
						if(!confirmDelete){
							return false;	
						}	
					}
					if(className == "del-exp"){
						var confirmDelete = confirm("Are you sure you want to delete this expense");
						if(!confirmDelete){
							return false;
						}	
					}
					if(className == "del-user"){
						var confirmDelete = confirm("Are you sure you want to delete this user?");
						if(!confirmDelete){
							return false;
						}
					}
					if(className == "del-log"){
						var confirmDelete = confirm("Are you sure you want to delete this log?");
						if(!confirmDelete){
							return false;	
						}	
					}
				}
			);
			
		});
		
		$("a#move-out").click(
			function(evt){
				var confirmDelete = confirm("Are you sure you want to move out this tenant?");
				if(!confirmDelete){
					return false;	
				}	
			}
		);
		
		function printWithCss()
		{
			// Get the HTML pf div
			var title = document.title;
			var divElements = document.getElementById('outerHTML').innerHTML;
			var printWindow = window.open('report', '_blank', 'left=0, top=0, scrollbars=1');	
			// Open the window
			printWindow.document.open();
			// Write the HTML to the new window, link to CSS file
			printWindow.document.write('<html><head><title>' + title + '</title><link rel="stylesheet" type="text/css" href="../css/print.css" media="all" /></head><body>');
			printWindow.document.write(divElements);
			printWindow.document.write('</body></html>');
			printWindow.document.close();
			printWindow.focus();
			printWindow.print();
			printWindow.close();
		}
		
	</script>
    
</body>
</html>