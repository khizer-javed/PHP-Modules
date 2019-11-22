<?php

if(isset($_POST['Yes']))
{
	header("Location: HKOnlineStudio.php");
}
if(isset($_POST['No']))
{
	header("Location: index.php");
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Event Requests</title>
<link rel="stylesheet" href="EventRequest.css">
<link rel="stylesheet" href="sidebar.css">
	<link rel="stylesheet" type="text/css" href="logout.css">
</head>

<body>

<div id="sidebar">
	<div id="slidetop">
		<img class="logo" src="images/logo.png">
		<h1>HK ONLINE STUDIO</h1>
	</div>
		<ul id="content">
			<a href="index.php"><li><img src="images/home.png">Home</li></a>
			<a href="Employees.php"><li><img src="images/show_employee.png">Employees</li></a>
			<a href="Eventlist.php"><li><img src="images/events.png">Event List</li></a>
			<a href="EventRequest.php"><li><img src="images/eventRequest.png">Event Requests</li></a>
			<a href="Rates.php"><li><img src="images/dollar.png">Rates</li></a>
			<a href="MemberRequest.php"><li><img src="images/eventRequest.png">Membership Requests</li></a>
			<a href="Member.php"><li><img src="images/members.png">Members</li></a>
			<a href="#" onClick="signout()"><li><img src="images/signout.png">Signout</li></a>
		</ul>
</div>

<div id="eventlist">
	<h1>CUSTOM EVENTS</h1>

	<div>
		<table class="table">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Client Name</th>
					<th scope="col">Client Phone</th>
					<th scope="col">Client Email</th>
					<th scope="col">Event Date</th>
					<th scope="col">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
					include("db.php");
					$ref = "Event_Request/Custom_Event";
					$data = $database->getReference($ref)->getValue();
				  	$i=0;
					if(!empty($data))
					{
						foreach($data as $key => $values)
						{
							$i++;
					?>
					<tr>
					<form action="showCustomEvent.php" method="post">
						<td scope="row"><?php echo $i?></td>
						<td scope="row"><?php echo $values['Client_Name']?></td>
						<td scope="row"><?php echo $values['Client_Phone']?></td>
						<td scope="row"><?php echo $values['Client_Email']?></td>							
						<td scope="row"><?php echo $values['Event_Date']?></td>
						<td scope="row"><input class="view" name="view" type="submit" value="View"><input type="hidden" name="ref" value="Event_Request/Custom_Event/<?php echo $key;?>">
					</form>
						</td>
					<?php
						}
					}
					else
					{
						?><tr>
							<td>---</td>
							<td>---</td>
							<td>---</td>
							<td>---</td>
							<td>---</td>
							<td>---</td>
						</tr><?php
					}
				?>
			</tbody>
		</table>
	</div>
	<h1>WEDDING EVENTS</h1>

	<div>
		<table class="table">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Client Name</th>
					<th scope="col">Client Phone</th>
					<th scope="col">Client Email</th>
					<th scope="col">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
					include("db.php");
					$ref = "Event_Request/Wedding_Package";
					$data = $database->getReference($ref)->getValue();
				  	$i=0;
					if(!empty($data))
					{
						foreach($data as $key => $values)
						{
							$i++;
					?>
					<tr>
					<form action="showWeddingEvent.php" method="post">
						<td scope="row"><?php echo $i?></td>
						<td scope="row"><?php echo $values['Client_Name']?></td>
						<td scope="row"><?php echo $values['Client_Phone']?></td>
						<td scope="row"><?php echo $values['Client_Email']?></td>
						<td scope="row"><input class="view" name="view" type="submit" value="View"><input type="hidden" name="ref" value="Event_Request/Wedding_Package/<?php echo $key;?>">
					</form>
						</td>
					<?php
						}
					}
				else{
					?><tr>
						<td>---</td>
						<td>---</td>
						<td>---</td>
						<td>---</td>
						<td>---</td>
					</tr><?php
				}
				?>
			</tbody>			
		</table>
	</div>
</div>
<div id="logblurbackground"></div>
	<div id="logout">
		<form action="?" method="post">
			<div id ="success">
				<h1>LOGOUT</h1>
				<p>Are you sure you want to Logout?</p>
				<input class="Yes" type="submit" name="Yes" value="Yes">
				<input class="cancel" type="submit" name="cancel" value="Cancel" onClick="close()">
			</div>
		</form>
	</div>
	<script>
		function close()
		{
			document.getElementById('logout').style.display = "none";
			document.getElementById('logblurbackground').style.display = "none";
		}
	</script>
<script src="signout.js"></script>
</body>
</html>