<?php
include("db.php");
date_default_timezone_set('Asia/Karachi');
$Eokay = false;
$disErrorMessage = "";
$ErrorMessage    = "";
$SuccessMessage  = "";
$price           = "";
$member = $database->getReference("Members")->getValue();
$P_price = $database->getReference("Rates/Employee_Rates")->getChild("Photographer_Rates")->getValue();
$V_price = $database->getReference("Rates/Employee_Rates")->getChild("Videographer_Rates")->getValue();
$discount = $database->getReference("Rates/MemberShip")->getChild("Discount")->getValue();

function setTime(String $hours,String $minute)
{
	$Timezone = "";
	if($hours > 12)
	{
	  $Timezone = "PM";
	  $hours = $hours - 12;
	}
	if($hours == 12)
	{
	  $Timezone = "PM";
	}
	if($hours == 00)
	{
	    $Timezone = "AM";
		$hours = 12;
	}
	if($hours < 12 && $Timezone != "PM")
	{
		$Timezone = "AM";
	}
		
	$hrs = strval($hours);

	if($hours < 10)
	{
		$hrs = "0".$hrs;
	}

	return("$hrs:$minute $Timezone");
}

if(isset($_POST['okay']))
{
	header("Location: HKOnlineStudio.php");
}
?>
<!doctype html>
<html>
<head>
	<title>custom Event</title>
	<link rel="stylesheet" type="text/css" href="CustomEvent.css">
	<link rel="stylesheet" type="text/css" href="Website.css">
</head>
<body>
<header>
		<div class="main">
			<div>
				<img class="logo" src="images/logo.png">
				<h1>HK ONLINE STUDIO</h1>
			</div>
			<ul>
				<li><a href="HKOnlineStudio.php">Home</a></li>
				<li><a href="Gallery.html">Gallery</a></li>
				<li><a href="membership.php">Membership</a></li>
			</ul>
		</div>
		
		<div class="Packages">
			<h1 id="heading">CUSTOM EVENT</h1>
			<form  method="post">
				<?php
				include("db.php");
				?>
				<table>
					<tr>
						<td>Number of Photographers:</td>
						<td><input id="P" name="P_Count" autocomplete="off" type="number"></td>
					</tr>
					<tr>
						<td>Number of Videographers:</td>
						<td><input id="V" name="V_Count" autocomplete="off" type="number"></td>
					</tr>
					<tr>
						<td>Number of Hours:</td>
						<td><input id="hrs" name="hours" autocomplete="off" type="number"></td>
					</tr>
					<tr>
						<td>Member Discount:<br>(for Members Only)</td>
						<td><input id="memberDis" name="memberdiscount" placeholder="Enter Phone Number" autocomplete="off" type="password"></td>
					</tr>
					<tr>
						<td>Total Price:</td>
						<td><input id="price" type="text" readonly ></td>
					</tr>
				</table>
					<div class="btn">
						<input type="submit" class="getPackage" name="Calculate" value="Calculate Price">
					</div>
					<?php
					if(isset($_POST['Calculate']))
					{
						$okay = true;
						if(empty($_POST['P_Count']) && empty($_POST['V_Count']))
						{
							$okay = false;
							?><script>
								document.getElementById('P').style.border = "1px solid red";
								document.getElementById('V').style.border = "1px solid red";
							</script><?php
						}
						if(empty($_POST['hours']))
						{
							$okay = false;
							?><script>
								document.getElementById('hrs').style.border = "1px solid red";
							</script><?php
						}
						if($okay)
						{
							$P_Count = $_POST['P_Count']; 
									 
							$V_Count = $_POST['V_Count'];
									
							$hours = $_POST['hours'];

							$discountKey = $_POST['memberdiscount'];

							if(empty($discountKey))
							{
								$price = $hours*( ($P_Count * $P_price) + ($V_Count * $V_price) );
							}
							else
							{
								$count = 0;
								if(strlen($discountKey) == 11)
								{
									foreach ($member as $key => $value) 
									{
										if($value['Client_Phone'] == $discountKey)
										{
											$count++;
										}
										else
										{
											continue;
										}
									}

									if($count > 0)
									{
										$price = $hours*( ($P_Count * $P_price) + ($V_Count * $V_price) );
										$dis = ($price * $discount)/100;
										$price = ($price - $dis);
									}
									elseif($count == 0)
									{
										$disErrorMessage = "Warning: Member does not exist!";
									}
								}
								else
								{
									$disErrorMessage = "Warning: Invalid Member key!";
								}
							}?>

							<input id="a" type="text" hidden value="<?php echo $price.' Rs/'; ?>">
							<input id="b" type="text" hidden value="<?php echo $P_Count; ?>">
							<input id="c" type="text" hidden value="<?php echo $V_Count; ?>">
							<input id="d" type="number" hidden value="<?php echo $hours; ?>">
							<input id="e" type="text" hidden value="<?php echo $discountKey; ?>">

							<script>
								var price  = document.getElementById('a').value;
								var pcount = document.getElementById('b').value;
								var vcount = document.getElementById('c').value;
								var hrs    = document.getElementById('d').value;
								var key    = document.getElementById('e').value;

								document.getElementById('price').value = price;
								document.getElementById('P').value = pcount;
								document.getElementById('V').value = vcount;
								document.getElementById('hrs').value = hrs;
								document.getElementById('memberDis').value = key;
							</script><?php
						}?>	
						<div class="error">
							<span><?php echo $disErrorMessage;?></span>
						</div><?php
					}?>
				<table>
					<tr>
						<td>Name:</td>
						<td><input id="Name" name="Client_Name" placeholder="Enter Full Name" autocomplete="off" type="text" ></td>
					</tr>
					<tr>
						<td>Phone #:</td>
						<td><input id="Phone" name="Client_Phone" autocomplete="off" type="number"></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><input id="Email" name="Client_Email" autocomplete="off" type="email"></td>
					</tr>
					<tr>
						<td>Event Type:</td>
						<td><input id="E_Type" name="Event_Type" type="text" placeholder="e.g. Concert, Shooting etc."></td>
					</tr>
					<tr>
						<td>Event Date:</td>
						<td><input id="E_Date" name="Event_Date" type="date"></td>
					</tr>
					<tr>
						<td>Start Time:</td>
						<td><input id="E_Time" name="Event_Time" type="time"></td>
					</tr>
					<tr>
						<td>Location:</td>
						<td><input id="Loc" name="Location" onClick="My_Map(this.id)" autocomplete="off" type="text" ></td>
					</tr>
				</table>
				<div class="btn">
					<input class="getPackage" type="submit" name="submit" value="Book Event">
				</div>
			<?php
			if(isset($_POST['submit']))
			{
				$okay = true;
				if(empty($_POST['Client_Name']))
				{
					$okay=false;
					?><script>
						document.getElementById('Name').style.border = '1px solid red';
					</script>"<?php
				}
				if(empty($_POST['Client_Phone']))
				{
					$okay=false;
					?><script>
						document.getElementById('Phone').style.border = '1px solid red';
					</script>"<?php
				}
				if(empty($_POST['Client_Email']))
				{
					$okay=false;
					?><script>
						document.getElementById('Email').style.border = '1px solid red';
					</script>"<?php
				}
				if(empty($_POST['P_Count']) && empty($_POST['V_Count']))
				{
					$okay=false;
					?><script>
						document.getElementById('P').style.border = '1px solid red';
						document.getElementById('V').style.border = '1px solid red';
					</script>"<?php
				}
				if(empty($_POST['hours']))
				{
					$okay=false;
					?><script>
						document.getElementById('hrs').style.border = '1px solid red';
					</script>"<?php
				}
				if(empty($_POST['Event_Date']))
				{
					$okay=false;
					?><script>
						document.getElementById('E_Date').style.border = '1px solid red';
					</script>"<?php
				}
				if(empty($_POST['Event_Type']))
				{
					$okay=false;
					?><script>
						document.getElementById('E_Type').style.border = '1px solid red';
					</script>"<?php
				}
				if(empty($_POST['Event_Time']))
				{
					$okay=false;
					?><script>
						document.getElementById('E_Time').style.border = '1px solid red';
					</script>"<?php
				}
				if(empty($_POST['Location']))
				{
					$okay=false;
					?><script>
						document.getElementById('Loc').style.border = '1px solid red';
					</script>"<?php
				}

				if ($okay) 
				{
					$Client_Name  = ucwords($_POST['Client_Name']);
					$Client_Phone = $_POST['Client_Phone'];
					$Client_Email = $_POST['Client_Email'];
					$discountKey  = $_POST['memberdiscount'];
					$P_Count      = $_POST['P_Count'];
					$V_Count      = $_POST['V_Count'];
					$hours        = $_POST['hours'];
					$Event_Date   = $_POST['Event_Date'];
					$Event_Type   = $_POST['Event_Type'];
					$Event_Time   = $_POST['Event_Time'];
					$Location     = $_POST['Location'];
					
					$hrs = substr($Event_Time,0,2);
					$mnt = substr($Event_Time,3,2);

					$year  = substr($Event_Date,0,4) ;
					$month = substr($Event_Date,5,2) ;
					$day   = substr($Event_Date,8,2) ;

					if($year < date('Y'))
					{
						$ErrorMessage = "Please select Valid Date";
					}
					elseif($year == date('Y'))
					{
						if($month < date('m'))
						{
							$ErrorMessage = "Please select Valid Date";
						}
						elseif($month == date('m'))
						{
							if($day < date('d'))
							{
								$ErrorMessage = "Please select Valid Date";
							}
							elseif($day == date('d'))
							{
								if($hrs < date('H'))
								{
									$ErrorMessage = "Please select Valid Date";
								}
								elseif($hrs == date('H'))
								{
									$ErrorMessage = "There must be atleast 2 hours of interval before the Event ";
								}
								elseif($hrs > date('H') && $hrs < (date('H')+2))
								{
									$ErrorMessage = "There must be atleast 2 hours of interval before the Event ";
								}
								else
								{
									$Event_Date = $year."/".$month."/".$day;
									$Event_Time = setTime($hrs,$mnt);
									$Eokay = true;
								}
							}
							else
							{
								$Event_Date = $year."/".$month."/".$day;
								$Event_Time = setTime($hrs,$mnt);
								$Eokay = true;
							}
						}
						else
						{
							$Event_Date = $year."/".$month."/".$day;
							$Event_Time = setTime($hrs,$mnt);
							$Eokay = true;
						}
					}
					else
					{
						$Event_Date = $year."/".$month."/".$day;
						$Event_Time = setTime($hrs,$mnt);
						$Eokay = true;
					}

					if($Eokay)
					{
						if(strlen($Client_Phone) != 11)
						{
							$ErrorMessage = "Warning: Invalid Phone #!";
						}
						elseif (substr($Client_Phone,0,2) != "03") 
						{
							$ErrorMessage = "Warning: Invalid Phone #!";
						}
						else
						{
							if(empty($price))
							{
								if(empty($discountKey))
								{
									$price = $hours*( ($P_Count * $P_price) + ($V_Count * $V_price) );
									$data = [
												'Client_Name'  => $Client_Name,
												'Client_Phone' => $Client_Phone,
												'Client_Email' => $Client_Email,
												'Number_of_Photographers' => $P_Count,
												'Number_of_Videographers' => $V_Count,
												'hours'      => intval($hours),
												'Event_Date' => $Event_Date,
												'Event_Type' => $Event_Type,
												'Event_Time' => $Event_Time,
												'Location'   => $Location,
												'Event_Cost' => $price
												];

											$pushData = $database->getReference("Event_Request/Custom_Event")->push($data);
											$SuccessMessage = "success";
								}
								else
								{
									$count = 0;
									if(strlen($discountKey) == 11)
									{
										foreach ($member as $key => $value) 
										{
											if($value['Client_Phone'] == $discountKey)
											{
												$count++;
											}
											else
											{
												continue;
											}
										}

										if($count > 0)
										{
											$price = $hours*( ($P_Count * $P_price) + ($V_Count * $V_price) );
											$dis = ($price * $discount)/100;
											$price = ($price - $dis);

											$data = [
												'Client_Name'  => $Client_Name,
												'Client_Phone' => $Client_Phone,
												'Client_Email' => $Client_Email,
												'Number_of_Photographers' => $P_Count,
												'Number_of_Videographers' => $V_Count,
												'hours'      => intval($hours),
												'Event_Date' => $Event_Date,
												'Event_Type' => $Event_Type,
												'Event_Time' => $Event_Time,
												'Location'   => $Location,
												'Event_Cost' => $price
												];

											$pushData = $database->getReference("Event_Request/Custom_Event")->push($data);
											$SuccessMessage = "success";
										}
										elseif($count == 0)
										{
											$ErrorMessage = "Warning: Member does not exist!";
										}
									}
									else
									{
										$ErrorMessage = "Warning: Invalid Member key!";
									}
								}
							}
							else
							{
								$data = [
									'Client_Name'  => $Client_Name,
									'Client_Phone' => $Client_Phone,
									'Client_Email' => $Client_Email,
									'Number_of_Photographers' => $P_Count,
									'Number_of_Videographers' => $V_Count,
									'hours'      => intval($hours),
									'Event_Date' => $Event_Date,
									'Event_Type' => $Event_Type,
									'Event_Time' => $Event_Time,
									'Location'   => $Location,
									'Event_Cost' => $price
									];

									$pushData = $database->getReference("Event_Request/Custom_Event")->push($data);
									$SuccessMessage = "success";
							}
						}
					}
				}
			}
			?>
			<div class="error">
				<span><?php echo $ErrorMessage; ?></span>
			</div>
			</form>	
			<div class="success">
				<span>
					<?php 
						if($SuccessMessage=="success")
							{
								?>
								<form action="?" method="post">
									<div id="blurbackground"></div>
									<div id ="success">
										<h1>SUCCESS</h1>
										<p>Event submitted Successfully!</p><p>Your request will be viewed shortly...</p>
										<br><input class="okay" type="submit" name="okay" value="Okay">
									</div>
								</form><?php
							}
					?>
				</span>
			</div>
		</div>
		<div id="mapBackground">
			<div id="search">
				<label for="">Search: <input id="map-search" class="controls" type="text" placeholder="Search Your Location" size="60"></label><br>
				<div id="hide">
					<label>Lat: <input type="text" class="latitude"></label>
					<label>Long: <input type="text" class="longitude"></label>
					<label>City <input type="text" class="reg-input-city" placeholder="City"></label>
				</div>
			</div>
		
			<div id="mapOptions">
				<input type="submit" value="Get Location" name="getloc" onClick="getLoc()">
				<input type="submit" value="Cancel" name="cancelMap" onClick="closeMap()">
			</div>
			
			<div id="locErrorid">
				<input type="text" id="locationError" readonly>
			</div>
		</div>
		
		
		<div id="map"></div>
		
		<script src="GoogleMaps.js"></script>
		<script src="javascript.js"></script>
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAnXhYGl0mrd6f170E9g9pypIDhW4opejg&libraries=places&callback=initialize"></script>	
	</header>	
</body>
</html>