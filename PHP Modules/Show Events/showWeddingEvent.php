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
ini_set('max_execution_time', 300); // Maximum time to Execute the code
$Name   = $database->getReference($_SESSION['ref'])->getChild('Client_Name')->getValue();
$phone  = $database->getReference($_SESSION['ref'])->getChild('Client_Phone')->getValue();
$Email  = $database->getReference($_SESSION['ref'])->getChild('Client_Email')->getValue();
$Pcount = $database->getReference("Rates/Wedding_Package")->getChild('Total_Photographers')->getValue();
$Vcount = $database->getReference("Rates/Wedding_Package")->getChild('Total_Videographers')->getValue();
$E_Type = ['Mehndi','Barat','Valima'];
$E_Time = [
	'Mehndi' => $database->getReference($_SESSION['ref'])->getChild('Mehndi_Time')->getValue(),
	'Barat'  => $database->getReference($_SESSION['ref'])->getChild('Barat_Time')->getValue(),
	'Valima' => $database->getReference($_SESSION['ref'])->getChild('Valima_Time')->getValue()
];
$E_Date = [
	'Mehndi' => $database->getReference($_SESSION['ref'])->getChild('Mehndi')->getValue(),
	'Barat'  => $database->getReference($_SESSION['ref'])->getChild('Barat')->getValue(),
	'Valima' => $database->getReference($_SESSION['ref'])->getChild('Valima')->getValue()
];
$location = [
	'Mehndi' => $database->getReference($_SESSION['ref'])->getChild('Mehndi_Location')->getValue(),
	'Barat'  => $database->getReference($_SESSION['ref'])->getChild('Barat_Location')->getValue(),
	'Valima' => $database->getReference($_SESSION['ref'])->getChild('Valima_Location')->getValue()
];
$hrs  = $database->getReference("Rates/Wedding_Package")->getChild('Total_Hours_Per_Event')->getValue();
$data = $database->getReference("Employees")->getValue();
$Cost = $database->getReference($_SESSION['ref'])->getChild('Event_Cost')->getValue();

$ErrorMessage   = "";
$photographers  = array();
$videographers  = array();
$Captain        = array();
$selectedPcount = 0;
$selectedVcount = 0;
$CaptainError   = "";

if(isset($_POST['CreateEvent_Leader']))
{
	if(isset($_POST['chkboxItemsP']) && isset($_POST['chkboxItemsV']))
	{
		$photographers  = array($_POST['chkboxItemsP']);
		$videographers  = array($_POST['chkboxItemsV']);
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
		$photographers  = array($_POST['chkboxItemsP']);
		$selectedPcount = count($_POST['chkboxItemsP']);
	}
	if(isset($_POST['chkboxItemsV']) && $Vcount != 0)
	{
		$videographers  = array($_POST['chkboxItemsV']);
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
		$_SESSION['photographers'] = $photographers[0];
		$_SESSION['videographers'] = $videographers[0];
		captain($Captain, $CaptainError);
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
						$Emps_for_Captain = array_merge($_SESSION['photographers'],$_SESSION['videographers']);
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
			$photographers = $_SESSION['photographers'];
			$videographers = $_SESSION['videographers'];
			$Event_Date="";
			$Event_Type="";
			$Event_Time="";
			$loc="";
			$Status = "Pending";
			$Event = array();
				for($i=0;$i<3;$i++)
				{
					if($i==0)
					{
						$Event_Date = $E_Date['Mehndi'];
						$Event_Type = $E_Type[0];
						$Event_Time = $E_Time['Mehndi'];
						$loc = $location['Mehndi'];
					}
					if($i==1)
					{
						$Event_Date = $E_Date['Barat'];
						$Event_Type = $E_Type[1];
						$Event_Time = $E_Time['Barat'];
						$loc = $location['Barat'];
					}
					if($i==2)
					{
						$Event_Date = $E_Date['Valima'];
						$Event_Type = $E_Type[2];
						$Event_Time = $E_Time['Valima'];
						$loc = $location['Valima'];
					}
					if($photographers == "") // if NO Photographers are selected
					{
						$Event = [
							'Client_Name'   => $Name,
							'Client_Phone'  => $phone,
							'Client_Email'  => $Email,
							'hours'         => $hrs,
							'Event_Date'    => $Event_Date,
							'Event_Type'    => $Event_Type,
							'Event_Time'    => $Event_Time,
							'Location'      => $loc,
							'Videographers' => $videographers,
							'Photographers' => ["0"=>"none"],
							'Status'        => $Status,
							'Captain'       => $Captain[0],
							'Payment'       => "Pending",
							'Event_Cost'	=> $Cost,
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
							'Event_Date'    => $Event_Date,
							'Event_Type'    => $Event_Type,
							'Event_Time'    => $Event_Time,
							'Location'      => $loc,
							'Photographers' => $photographers,
							'Videographers' => ["0"=>"none"],
							'Status' 		=> $Status,
							'Captain' 		=> $Captain[0],
							'Payment'       => "Pending",
							'Event_Cost'	=> $Cost,
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
						'Event_Date'    => $Event_Date,
						'Event_Type'    => $Event_Type,
						'Event_Time'    => $Event_Time,
						'Location'      => $loc,
						'Photographers' => $photographers,
						'Videographers' => $videographers,
						'Status' 		=> $Status,
						'Captain' 		=> $Captain[0],
						'Payment'       => "Pending",
						'Event_Cost'	=> $Cost,
						'Rating_given'  => 0
						];
					}
					$database->getReference("Event_List")->push($Event);
					$database->getReference("Client/".$phone)->push($Event);
				}

				if(!is_null($photographers))
				{
					foreach($photographers as $Pname)
					{
						foreach($data as $key => $values)
						{
							$name = $values["Name"];

							if($name == $Pname)
							{
								for($i=0;$i<3;$i++)
								{
									if($i==0)
									{
										$Event_Date = $E_Date['Mehndi'];
										$Event_Type = $E_Type[0];
										$Event_Time = $E_Time['Mehndi'];
										$loc = $location['Mehndi'];
									}
									if($i==1)
									{
										$Event_Date = $E_Date['Barat'];
										$Event_Type = $E_Type[1];
										$Event_Time = $E_Time['Barat'];
										$loc = $location['Barat'];
									}
									if($i==2)
									{
										$Event_Date = $E_Date['Valima'];
										$Event_Type = $E_Type[2];
										$Event_Time = $E_Time['Valima'];
										$loc = $location['Valima'];
									}
									$Emp_Schedule = [
										'Client_Name' => $Name,
										'Client_Phone' => $phone,
										'Event_Date' => $Event_Date,
										'Event_Type' => $Event_Type,
										'Event_Time' => $Event_Time,
										'Location' => $loc,
										'hours' => $hrs,
										'Captain' => $Captain[0]
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
								for($i=0;$i<3;$i++)
								{
									if($i==0)
									{
										$Event_Date = $E_Date['Mehndi'];
										$Event_Type = $E_Type[0];
										$Event_Time = $E_Time['Mehndi'];
										$loc = $location['Mehndi'];
									}
									if($i==1)
									{
										$Event_Date = $E_Date['Barat'];
										$Event_Type = $E_Type[1];
										$Event_Time = $E_Time['Barat'];
										$loc = $location['Barat'];
									}
									if($i==2)
									{
										$Event_Date = $E_Date['Valima'];
										$Event_Type = $E_Type[2];
										$Event_Time = $E_Time['Valima'];
										$loc = $location['Valima'];
									}
								$Emp_Schedule = [
										'Client_Name' => $Name,
										'Client_Phone' => $phone,
										'Event_Date' => $Event_Date,
										'Event_Type' => $Event_Type,
										'Event_Time' => $Event_Time,
										'Location' => $loc,
										'hours' => ($hrs),
										'Captain' => $Captain[0]
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
			}
			//$database->getReference($_SESSION['ref'])->remove();
			session_unset($_SESSION['ref'],$_SESSION['photographers'],$_SESSION['videographers']);
			header("Location: Eventlist.php");
			exit();
		}
	}
	else
	{
		$CaptainError = "Note: Select a Team Captain";
		captain($Captain, $CaptainError);
	}
		
}
if(isset($_POST['RejectEvent']))
{
	$database->getReference($_SESSION['ref'])->remove();
	session_unset($_SESSION['ref']);
	header("Location: EventRequest.php");
	exit();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Wedding Event</title>
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
	<h1 class="mainhead">WEDDING EVENT</h1>
	<div class="form">
	<form method="post" action="?">
		<h1 class="header">CLIENT DETAILS</h1>
		<table class="data">
			<tr>
				<td>Name:</td>
				<td class="values"><?php echo $Name; ?></td>
			</tr>
			<tr>
				<td>Phone #:</td>
				<td class="values"><?php echo $phone; ?></td>
			</tr>
			<tr>
				<td>Email:</td>
				<td class="values"><?php echo $Email; ?></td>
			</tr>
		</table>
		<h1 class="header">EVENT DETAILS</h1>
		<table class="data">
			<tr>
				<?php
					foreach($E_Type as $E => $val)
					{
						?><td class="values"><?php echo $val ?></td><?php
					}
				?>
			</tr>
			<tr>
				<?php
					foreach($E_Date as $E => $val)
					{
						?><td class="values"><?php echo $val ?></td><?php
					}
				?>
			</tr>
			<tr>
				<?php
					foreach($E_Time as $E => $val)
					{
						?><td class="values"><?php echo $val ?></td><?php
					}
				?>
			</tr>
			<tr>
				<?php
					foreach($location as $E => $val)
					{
						?><td class="values"><?php echo $val ?></td><?php
					}
				?>
			</tr>
			</table>
			<table id="cost">
				<tr>
					<td>Cost: <?php echo $Cost; ?> Rs/</td>
				</tr>
			</table>
			<h1 class="header">REQUIREMENT DETAILS</h1>
			<table class="data">
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
									if($values["employee_type"] == "Photographer" && $Pcount != 0) //Photographer table Show/no Show
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
												
													if($E_Date['Mehndi'] != $date && $E_Date['Barat'] != $date && $E_Date['Valima'] != $date)
													{
														$AVcount++;
													}
													else
													{
														$time1hrs = substr($Time,0,2);
														$time2hrsM = substr($E_Time['Mehndi'],0,2);
														$time2hrsB = substr($E_Time['Barat'],0,2);
														$time2hrsV = substr($E_Time['Valima'],0,2);

														$hrs1 = $hours + $time1hrs;
														$hrs2M = $hrs + $time2hrsM;
														$hrs2B = $hrs + $time2hrsB;
														$hrs2V = $hrs + $time2hrsV;

														if($time1hrs < $time2hrsM && $time1hrs < $time2hrsB && $time1hrs < $time2hrsV && $hrs1 < $time2hrsM && $hrs1 < $time2hrsB && $hrs1 < $time2hrsV)
														{
															$AVcount++;
														}
														elseif($time1hrs > $hrs2M && $time1hrs > $hrs2B && $time1hrs > $hrs2V && $hrs1 > $hrs2M && $hrs1 > $hrs2B && $hrs1 > $hrs2V)
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
									if($values["employee_type"] == "Videographer" && $Vcount != 0) //Videographer table Show/no Show
									{
										if($values["Job_queue"] == 0)
										{
											?><tr>
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
												
													if($E_Date['Mehndi'] != $date && $E_Date['Barat'] != $date && $E_Date['Valima'] != $date)
													{
														$AVcount++;
													}
													else
													{
														$time1hrs = substr($Time,0,2);
														$time2hrsM = substr($E_Time['Mehndi'],0,2);
														$time2hrsB = substr($E_Time['Barat'],0,2);
														$time2hrsV = substr($E_Time['Valima'],0,2);

														$hrs1 = $hours + $time1hrs;
														$hrs2M = $hrs + $time2hrsM;
														$hrs2B = $hrs + $time2hrsB;
														$hrs2V = $hrs + $time2hrsV;

														if($time1hrs < $time2hrsM && $time1hrs < $time2hrsB && $time1hrs < $time2hrsV && $hrs1 < $time2hrsM && $hrs1 < $time2hrsB && $hrs1 < $time2hrsV)
														{
															$AVcount++;
														}
														elseif($time1hrs > $hrs2M && $time1hrs > $hrs2B && $time1hrs > $hrs2V && $hrs1 > $hrs2M && $hrs1 > $hrs2B && $hrs1 > $hrs2V)
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
		<input class="decisionA" type="submit" name="CreateEvent_Leader" value="Continue">
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