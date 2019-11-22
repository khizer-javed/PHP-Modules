<?php
include("db.php");
session_start();

if(isset($_POST['Yes']))
{
	header("Location: HKOnlineStudio.php");
}

if(isset($_POST['view']))
{
	$ref = $_POST['ref'];
	$_SESSION['ref'] = $ref;
}

$Name     = $database->getReference($_SESSION['ref'])->getChild('Client_Name')->getValue();
$Vcount   = $database->getReference($_SESSION['ref'])->getChild('Number_of_Videographers')->getValue();
$Pcount   = $database->getReference($_SESSION['ref'])->getChild('Number_of_Photographers')->getValue();
$phone    = $database->getReference($_SESSION['ref'])->getChild('Client_Phone')->getValue();
$Email    = $database->getReference($_SESSION['ref'])->getChild('Client_Email')->getValue();
$E_Type   = $database->getReference($_SESSION['ref'])->getChild('Event_Type')->getValue();
$E_Time   = $database->getReference($_SESSION['ref'])->getChild('Event_Time')->getValue();
$E_Date   = $database->getReference($_SESSION['ref'])->getChild('Event_Date')->getValue();
$location = $database->getReference($_SESSION['ref'])->getChild('Location')->getValue();
$hrs      = $database->getReference($_SESSION['ref'])->getChild('hours')->getValue();
$Cost     = $database->getReference($_SESSION['ref'])->getChild('Event_Cost')->getValue();

//-------for Create Event button
$emps = $Vcount+$Pcount;
$buttonValue = "Continue";
if($emps == 1)
{
	$buttonValue = "Create Event";
}
$data = $database->getReference("Employees")->getValue();

$ErrorMessage   = "";
$photographers  = array();
$videographers  = array();
$Captain 	    = array();
$selectedPcount = 0;
$selectedVcount = 0;
$CaptainError   = "";
if(isset($_POST['CreateEvent_Leader']))
{
	if(isset($_POST['chkboxItemsP']) && isset($_POST['chkboxItemsV']))
	{
		$photographers = array($_POST['chkboxItemsP']);
		$videographers = array($_POST['chkboxItemsV']);
		$selectedPcount = count($_POST['chkboxItemsP']);
		$selectedVcount = count($_POST['chkboxItemsV']);
	}
	
	if($Vcount == 0)
		{
			$videographers = null;
			$selectedVcount = 0;
		}
	if($Pcount == 0)
		{
			$photographers = null;
			$selectedPcount = 0;
		}
	if(isset($_POST['chkboxItemsP']) && $Pcount != 0)
	{
		$photographers = array($_POST['chkboxItemsP']);
		$selectedPcount = count($_POST['chkboxItemsP']);
	}
	if(isset($_POST['chkboxItemsV']) && $Vcount != 0)
	{
		$videographers = array($_POST['chkboxItemsV']);
		$selectedVcount = count($_POST['chkboxItemsV']);
	}
	
	if(empty($_POST['chkboxItemsP']) && empty($_POST['chkboxItemsV']))
	{
		$ErrorMessage = "Please Select Suitable Employees";
	}
	elseif(empty($_POST['chkboxItemsP']) && $Pcount !=0)
	{
		$ErrorMessage = "Please Select Suitable Photographer[s]";
	}
	elseif(empty($_POST['chkboxItemsV']) && $Vcount !=0)
	{
		$ErrorMessage = "Please Select Suitable Videographer[s]";
	}
	elseif($selectedPcount != $Pcount)
	{
		$ErrorMessage = "Please Select ".$Pcount." Photographer[s]";
	}
	elseif($selectedVcount != $Vcount)
	{
		$ErrorMessage = "Please Select ".$Vcount." Videographer[s]";
	}
	else
	{
		if(empty($photographers[0])) // Only videographers were selected
		{
			$_SESSION['photographers'] = "";
			$_SESSION['videographers'] = $videographers[0];
		}
		if(empty($videographers[0])) // Only photographers were selected
		{
			$_SESSION['photographers'] = $photographers[0];
			$_SESSION['videographers'] = "";
		}
		if(!empty($photographers[0]) && !empty($videographers[0])) // Both were selected
		{
			$_SESSION['photographers'] = $photographers[0];
			$_SESSION['videographers'] = $videographers[0];
		}
		if(empty($_SESSION['photographers']) && !empty($_SESSION['videographers'])) // there are only videographers
		{
			if(count($_SESSION['videographers']) == 1) // there is only 1 videographer for event
			{
				$_SESSION['captain'] = $_SESSION['videographers'][0]; // then he is Captain
				setEvent($Name,$phone,$Email,$hrs,$E_Date,$E_Time,$E_Type,$location,$Status,$Cost);
			}
			else // there is more than 1 videographer for event
			{
				captain($Captain, $CaptainError);
			}
		}
		elseif(!empty($_SESSION['photographers']) && empty($_SESSION['videographers'])) // there are only photographers
		{
			if(count($_SESSION['photographers']) == 1) // there is only 1 photographers for event
			{
				$_SESSION['captain'] = $_SESSION['photographers'][0]; // then he is Captain
				setEvent($Name,$phone,$Email,$hrs,$E_Date,$E_Time,$E_Type,$location,$Status,$Cost);
			}
			else // there is more than 1 photographers for event
			{
				captain($Captain, $CaptainError);
			}
		}
		else
		{
			captain($Captain, $CaptainError);
		}
		
	}
}

function captain($Captain, $error)
{
	?>
		<form method="post">
		<div id="blurbackground"></div>
		<div id ="selectLeader">
			<h1>SELECT TEAM CAPTAIN</h1>
			<div id="LeaderNames">
				<table id="leaderTable">
					<?php
					if($_SESSION['photographers'] == "")
					{
						$Emps_for_Captain = $_SESSION['videographers'];
					}
					elseif($_SESSION['videographers'] == "")
					{
						$Emps_for_Captain = $_SESSION['photographers'];
					}
					else
					{
						$Emps_for_Captain = array_merge($_SESSION['photographers'],$_SESSION['videographers']);
					}
						foreach($Emps_for_Captain as $names)
						{
						?><tr>
							<td><input name='leaderName[]' type="checkbox" value="<?php echo $names; ?>"
							<?php echo(in_array($names,$Captain))?'checked':'' ?>/></td>
							<td><?php echo $names;?></td>
						</tr><?php
						}
					?>
				</table>
			</div>
			<div id="captainError">
				<span><?php echo $error; ?></span>
			</div>
			<br><input class="leaderCreate" type="submit" name="CreateEvent" value="Create Event"></form>
			<input class="leaderCancel" id="cancelLeader" type="submit" name="cancel" onClick="disLeader()" value="Cancel">
		</div>
		<?php
}

function setEvent($Name,$phone,$Email,$hrs,$E_Date,$E_Time,$E_Type,$location,$Status,$Cost)
{
	include("db.php");
	$photographers = $_SESSION['photographers'];
	$videographers = $_SESSION['videographers'];
	$EventCaptain  = $_SESSION['captain'];
	$Status = "Pending";
	$Event  = array();
	$data   = $database->getReference("Employees")->getValue();
			if($photographers == "") // if NO Photographers are selected
			{
				$Event = [
					'Client_Name'   => $Name,
					'Client_Phone'  => $phone,
					'Client_Email'  => $Email,
					'hours'         => $hrs,
					'Event_Cost'	=> $Cost,
					'Event_Date'    => $E_Date,
					'Event_Type'    => $E_Type,
					'Event_Time'    => $E_Time,
					'Location'      => $location,
					'Videographers' => $videographers,
					'Photographers' => ["0"=>"none"],
					'Status'        => $Status,
					'Captain'       => $EventCaptain,
					'Payment'       => "Pending",
					'Rating_given'  => 0
					];
				
			}
			elseif($videographers == "") // if NO Videographers are selected
			{
				$Event = [
					'Client_Name'   => $Name,
					'Client_Phone'  => $phone,
					'Client_Email'  => $Email,
					'hours'         => $hrs,
					'Event_Cost'	=> $Cost,
					'Event_Date'    => $E_Date,
					'Event_Type'    => $E_Type,
					'Event_Time'    => $E_Time,
					'Location'      => $location,
					'Photographers' => $photographers,
					'Videographers' => ["0"=>"none"],
					'Status' 		=> $Status,
					'Captain' 		=> $EventCaptain,
					'Payment'       => "Pending",
					'Rating_given'  => 0
					];
			}
			else // if Both are selected
			{
				$Event = [
				'Client_Name'   => $Name,
				'Client_Phone'  => $phone,
				'Client_Email'  => $Email,
				'hours'         => $hrs,
				'Event_Cost'	=> $Cost,
				'Event_Date'    => $E_Date,
				'Event_Type'    => $E_Type,
				'Event_Time'    => $E_Time,
				'Location'      => $location,
				'Photographers' => $photographers,
				'Videographers' => $videographers,
				'Status' 		=> $Status,
				'Captain' 		=> $EventCaptain,
				'Payment'       => "Pending",
				'Rating_given'  => 0
				];
			}
			$database->getReference("Event_List")->push($Event);
			$database->getReference("Client/".$phone)->push($Event);
			if(!is_null($photographers))
			{
				foreach($photographers as $Pname)
				{
					foreach($data as $key => $values)
					{
						$name = $values["Name"];

						if($name == $Pname)
						{
							$Emp_Schedule = [
									'Client_Name'  => $Name,
									'Client_Phone' => $phone,
									'Event_Date'   => $E_Date,
									'Event_Type'   => $E_Type,
									'Event_Time'   => $E_Time,
									'Location'     => $location,
									'hours'		   => $hrs,
									'Captain'      => $EventCaptain
							];
							$database->getReference("Employees/".$key."/Scheduled_Events")->push($Emp_Schedule);
							$jobqueque = $database->getReference("Employees/".$key."/Job_queue")->getValue();
							$database->getReference("Employees/".$key."/Job_queue")->set(++$jobqueque);
							
							$blnc = $database->getReference("Employees/".$key."/Balance")->getValue();
							$blnc = $blnc + 500;
							$database->getReference("Employees/".$key."/Balance")->set($blnc);
						}
					}
				}
			}
			if(!is_null($videographers))
			{
				foreach($videographers as $Vname)
				{
					foreach($data as $key => $values)
					{
						$name = $values['Name'];
						if($name == $Vname)
						{
							$Emp_Schedule = [
									'Client_Name'  => $Name,
									'Client_Phone' => $phone,
									'Event_Date'   => $E_Date,
									'Event_Type'   => $E_Type,
									'Event_Time'   => $E_Time,
									'Location'     => $location,
									'hours'		   => $hrs,
									'Captain'      => $EventCaptain
							];
							$database->getReference("Employees/".$key."/Scheduled_Events")->push($Emp_Schedule);
							$jobqueque = $database->getReference("Employees/".$key."/Job_queue")->getValue();
							$database->getReference("Employees/".$key."/Job_queue")->set(++$jobqueque);
							
							$blnc = $database->getReference("Employees/".$key."/Balance")->getValue();
							$blnc = $blnc + 1000;
							$database->getReference("Employees/".$key."/Balance")->set($blnc);
						}
					}
				}
			}
			//$database->getReference($_SESSION['ref'])->remove();
			session_unset();
			session_destroy();
			header("Location: Eventlist.php");
			exit();
}

if(isset($_POST['CreateEvent']))
{
	if(isset($_POST["leaderName"]))
	{
		$Captain = $_POST["leaderName"];
		if(count($Captain) > 1)
		{
			$CaptainError = "Note: Select 1 Captain";
			captain($Captain, $CaptainError);
		}
		elseif(count($Captain) == 1)
		{
			$_SESSION['captain'] = $Captain[0];
			setEvent($Name,$phone,$Email,$hrs,$E_Date,$E_Time,$E_Type,$location,$Status,$Cost);
		}
	}
	else
	{
		$CaptainError = "Note: Select a Captain";
		captain($Captain, $CaptainError);
	}
}

if(isset($_POST['RejectEvent']))
{
	$database->getReference($_SESSION['ref'])->remove();
	session_unset();
	session_destroy();
	header("Location: EventRequest.php");
	exit();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Custom Event</title>
<link rel="stylesheet" href="showEvent.css">
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
		<a href="#" onClick="signout()"><li><img src="images/signout.png" >Signout</li></a>
	</ul>
</div>

<div class="CustomEvent">
	<h1 class="mainhead">CUSTOM EVENT</h1>
	<div class="form">
	<form method="post" action="?">
		<h1 class="header"><?php echo $Name; ?></h1>
		<table class="data">
			<tr>
				<td>Phone #:</td>
				<td class="values"><?php echo $phone; ?></td>
			</tr>
			<tr>
				<td>Email:</td>
				<td class="values"><?php echo $Email; ?></td>
			</tr>
			<tr>
				<td>Event Type:</td>
				<td class="values"><?php echo $E_Type; ?></td>
			</tr>
			<tr>
				<td>Event Date:</td>
				<td class="values"><?php echo $E_Date; ?></td>
			</tr>
			<tr>
				<td>Event Time:</td>
				<td class="values"><?php echo $E_Time; ?></td>
			</tr>
			<tr>
				<td>Location:</td>
				<td class="values"><?php echo $location; ?></td>
			</tr>
			<tr>
				<td>Photographers:</td>
				<td class="values"><?php echo $Pcount; ?></td>
			</tr>
			<tr>
				<td>Videographers:</td>
				<td class="values"><?php echo $Vcount; ?></td>
			</tr>
			<tr>
				<td>Total Hours:</td>
				<td class="values"><?php echo $hrs; ?></td>
			</tr>
			<tr>
				<td>Cost:</td>
				<td class="values"><?php echo $Cost; ?> Rs/</td>
			</tr>
		</table>
		<table class="PVSelector">
			<tr>
				<td class="dlg-content">Available Photographers</td>
				<td class="dlg-content">
					<div class="multiselect">
						<div class="selectbox" onClick="showCheckboxesP()">
							<select>
								<option>Select Photographers</option>
							</select>
							<div class="overselect"></div>
						</div>
						<div id="checkboxesP" class="checkboxes">
							<table class="pvs">
								<th>Select</th>
								<th>Names</th>
								<th>Events Done</th>
								<th>Job queue</th>
							<?php
							foreach($data as $key => $values)
								{
									if($values["employee_type"] == "Photographer" && $Pcount != 0)
									{
										if($values["Job_queue"] == 0)
										{
											?><tr>
												<td><input name='chkboxItemsP[]' type="checkbox" value="<?php echo $values["Name"]; ?>"
												<?php echo(in_array($values["Name"],$photographers))?'checked':'' ?>/></td>
												<td><?php echo $values["Name"];?></td>
												<td><?php echo $values["Events_Done"]?></td>
												<td><?php echo $values["Job_queue"]?></td>
											</tr><?php
										}
										else
										{
											$Schedule = $values["Scheduled_Events"];
											$Ecount=0;
											$AVcount=0;
											foreach($Schedule as $key => $event)
											{
												$Ecount++;
												
												$date = $event['Event_Date'];
												$Time = $event['Event_Time'];
												$hours = $event['hours'];
												
												if($E_Date != $date)
												{
													$AVcount++;
												}
												else
												{
													$time1hrs = substr($Time,0,2);
													$time2hrs = substr($E_Time,0,2);

													$hrs1 = $hours + (int)$time1hrs;
													$hrs2 = $hrs + (int)$time2hrs;


													if($time1hrs < $time2hrs && $hrs1 < $time2hrs)
													{
														$AVcount++;
													}
													elseif($time1hrs > $hrs2 && $hrs1 > $hrs2)
													{
														$AVcount++;
													}
													else
													{
														continue;
													}
												}
											}
											if($Ecount == $AVcount) //No clashes in all schedules
											{
												?><tr>
													<td><input name='chkboxItemsP[]' type="checkbox" value="<?php echo $values["Name"]; ?>"
													<?php echo(in_array($values["Name"],$photographers))?'checked':'' ?>/></td>
													<td><?php echo $values["Name"];?></td>
													<td><?php echo $values["Events_Done"]?></td>
													<td><?php echo $values["Job_queue"]?></td>
												</tr><?php
											}
										}
									}
									else
									{
										continue;
									}
								}
							?>
						</table>
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="dlg-content">Available Videographers</td>
				<td class="dlg-content">
					<div class="multiselect">
						<div class="selectbox" onClick="showCheckboxesV()">
							<select>
								<option>Select Videographers</option>
							</select>
							<div class="overselect"></div>
						</div>
						<div id="checkboxesV" class="checkboxes">
							<table class="pvs">
								<th>Select</th>
								<th>Names</th>
								<th>Events Done</th>
								<th>Job queue</th>
							<?php
							foreach($data as $key => $values)
								{
									if($values["employee_type"] == "Videographer" && $Vcount != 0)
									{
										if($values["Job_queue"] == 0)
										{
											?>
											<tr>
												<td><input name='chkboxItemsV[]' type="checkbox" value="<?php echo $values["Name"]; ?>"
													<?php echo(in_array($values["Name"],$videographers))?'checked':'' ?>/></td>
												<td><?php echo $values["Name"];?></td>
												<td><?php echo $values["Events_Done"]?></td>
												<td><?php echo $values["Job_queue"]?></td>
											</tr><?php
										}
										else
										{
											$Schedule = $values["Scheduled_Events"];
											$Ecount=0;
											$AVcount=0;
											foreach($Schedule as $key => $event)
											{
												$Ecount++;
												
												$date = $event['Event_Date'];
												$Time = $event['Event_Time'];
												$hours = $event['hours'];
												
												if($E_Date != $date)
												{
													$AVcount++;
												}
												else
												{
													$time1hrs = substr($Time,0,2);
													$time2hrs = substr($E_Time,0,2);

													$hrs1 = $hours + (int)$time1hrs;
													$hrs2 = $hrs + (int)$time2hrs;


													if($time1hrs < $time2hrs && $hrs1 < $time2hrs)
													{
														$AVcount++;
													}
													elseif($time1hrs > $hrs2 && $hrs1 > $hrs2)
													{
														$AVcount++;
													}
													else
													{
														continue;
													}
												}
											}
											if($Ecount == $AVcount) //No clashes in all schedules
											{
												?>
												<tr>
													<td><input name='chkboxItemsV[]' type="checkbox" value="<?php echo $values["Name"]; ?>"
														<?php echo(in_array($values["Name"],$videographers))?'checked':'' ?>/></td>
													<td><?php echo $values["Name"];?></td>
													<td><?php echo $values["Events_Done"]?></td>
													<td><?php echo $values["Job_queue"]?></td>
												</tr><?php
											}
										}
									}
									else
										{
											continue;
										}
								}
							?>
						</table>
						</div>
					</div>
				</td>
			</tr>
		</table>
	<div class="error">
		<span><?php echo($ErrorMessage);?></span>
	</div>
	<div id="btns">
		<input class="decisionA" type="submit" name="CreateEvent_Leader" value="<?php echo $buttonValue; ?>">
		<input class="decisionB" type="submit" name="RejectEvent" value="Reject Event">	
	</div>
	</form>
	</div>
</div>
<script>
	function disLeader()
	{
		document.getElementById('selectLeader').style.display = "none";
		document.getElementById('blurbackground').style.display = "none";
	}
	</script>
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
	<script src="showEvent.js"></script>
</body>
</html>