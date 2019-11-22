<?php
include("db.php");
date_default_timezone_set('Asia/Karachi');
$ErrorMessage ="";
$SuccessMessage ="";
$Mokay = false;
$Bokay = false;
$Vokay = false;
if(isset($_POST['submit']))
{
	$Client_Name = ucwords($_POST['Client_Name']);
	$Client_Phone = $_POST['Client_Phone'];
	$Client_Email = $_POST['Client_Email'];
	$memberdiscount = $_POST['memberdiscount'];
	$mehndiDate = $_POST['mehndiDate'];
	$mehndiTime = $_POST['mehndiTime'];
	$mehndilocation = $_POST['mehndilocation'];
	$baratDate = $_POST['baratDate'];
	$baratTime = $_POST['baratTime'];
	$baratlocation = $_POST['baratlocation'];
	$valimaDate = $_POST['valimaDate'];
	$valimaTime = $_POST['valimaTime'];
	$valimalocation = $_POST['valimalocation'];


	$Mhrs = substr($mehndiTime,0,2);
	$Mmnt = substr($mehndiTime,3,2);

	$Bhrs = substr($baratTime,0,2);
	$Bmnt = substr($baratTime,3,2);

	$Vhrs = substr($valimaTime,0,2);
	$Vmnt = substr($valimaTime,3,2);

	$Myear = substr($mehndiDate,0,4) ;
	$Mmonth = substr($mehndiDate,5,2) ;
	$Mday = substr($mehndiDate,8,2) ;

	if($Myear < date('Y'))
	{
		$ErrorMessage = "Please select Valid Date";
	}
	elseif($Myear == date('Y'))
	{
		if($Mmonth < date('m'))
		{
			$ErrorMessage = "Please select Valid Mehndi Date";
		}
		elseif($Mmonth == date('m'))
		{
			if($Mday < date('d'))
			{
				$ErrorMessage = "Please select Valid Mehndi Date";
			}
			elseif($Mday == date('d'))
			{
				if($Mhrs < date('H'))
				{
					$ErrorMessage = "Please select Valid Mehndi Date";
				}
				elseif($Mhrs == date('H'))
				{
					$ErrorMessage = "There must be atleast 2 hours of interval before the Event ";
				}
				elseif($Mhrs > date('H') && $Mhrs < (date('H')+2))
				{
					$ErrorMessage = "There must be atleast 2 hours of interval before the Event ";
				}
				else
				{
					$mehndiDate = $Myear."/".$Mmonth."/".$Mday;
					$mehndiTime = setTime($Mhrs,$Mmnt);
					$Mokay = true;
				}
			}
			else
			{
				$mehndiDate = $Myear."/".$Mmonth."/".$Mday;;
				$mehndiTime = setTime($Mhrs,$Mmnt);
				$Mokay = true;
			}
		}
		else
		{
			$mehndiDate = $Myear."/".$Mmonth."/".$Mday;;
			$mehndiTime = setTime($Mhrs,$Mmnt);
			$Mokay = true;
		}
	}
	else
	{
		$mehndiDate = $Myear."/".$Mmonth."/".$Mday;;
		$mehndiTime = setTime($Mhrs,$Mmnt);
		$Mokay = true;
	}

	$Byear = substr($baratDate,0,4) ;
	$Bmonth = substr($baratDate,5,2) ;
	$Bday = substr($baratDate,8,2) ;

	if($Byear < date('Y'))
	{
		$ErrorMessage = "Please select Valid Barat Date";
	}
	elseif($Byear == date('Y'))
	{
		if($Bmonth < date('m'))
		{
			$ErrorMessage = "Please select Valid Barat Date";
		}
		elseif($Bmonth == date('m'))
		{
			if($Bday < date('d'))
			{
				$ErrorMessage = "Please select Valid Barat Date";
			}
			elseif($Bday == date('d'))
			{
				if($Bhrs < date('H'))
				{
					$ErrorMessage = "Please select Valid Barat Date";
				}
				elseif($Bhrs == date('H'))
				{
					$ErrorMessage = "There must be atleast 2 hours of interval before the Event ";
				}
				elseif($Bhrs > date('H') && $Bhrs < (date('H')+2))
				{
					$ErrorMessage = "There must be atleast 2 hours of interval before the Event ";
				}
				else
				{
					$baratDate = $Byear."/".$Bmonth."/".$Bday;
					$baratTime = setTime($Bhrs,$Bmnt);
					$Bokay = true;
				}
			}
			else
			{
				$baratDate = $Byear."/".$Bmonth."/".$Bday;
				$baratTime = setTime($Bhrs,$Bmnt);
				$Bokay = true;
			}
		}
		else
		{
			$baratDate = $Byear."/".$Bmonth."/".$Bday;
			$baratTime = setTime($Bhrs,$Bmnt);
			$Bokay = true;
		}
	}
	else
	{
		$baratDate = $Byear."/".$Bmonth."/".$Bday;
		$baratTime = setTime($Bhrs,$Bmnt);
		$Bokay = true;
	}

	$Vyear = substr($valimaDate,0,4) ;
	$Vmonth = substr($valimaDate,5,2) ;
	$Vday = substr($valimaDate,8,2) ;

	if($Vyear < date('Y'))
	{
		$ErrorMessage = "Please select Valid Valima Date";
	}
	elseif($Vyear == date('Y'))
	{
		if($Vmonth < date('m'))
		{
			$ErrorMessage = "Please select Valid Valima Date";
		}
		elseif($Vmonth == date('m'))
		{
			if($Vday < date('d'))
			{
				$ErrorMessage = "Please select Valid Valima Date";
			}
			elseif($Vday == date('d'))
			{
				if($Vhrs < date('H'))
				{
					$ErrorMessage = "Please select Valid Valima Date";
				}
				elseif($Vhrs == date('H'))
				{
					$ErrorMessage = "There must be atleast 2 hours of interval before the Event ";
				}
				elseif($Vhrs > date('H') && $Vhrs < (date('H')+2))
				{
					$ErrorMessage = "There must be atleast 2 hours of interval before the Event ";
				}
				else
				{
					$valimaDate = $Vyear."/".$Vmonth."/".$Vday;
					$valimaTime = setTime($Vhrs,$Vmnt);
					$Vokay = true;
				}
			}
			else
			{
				$valimaDate = $Vyear."/".$Vmonth."/".$Vday;
				$valimaTime = setTime($Vhrs,$Vmnt);
				$Vokay = true;
			}
		}
		else
		{
			$valimaDate = $Vyear."/".$Vmonth."/".$Vday;
			$valimaTime = setTime($Vhrs,$Vmnt);
			$Vokay = true;
		}
	}
	else
	{
		$valimaDate = $Vyear."/".$Vmonth."/".$Vday;
		$valimaTime = setTime($Vhrs,$Vmnt);
		$Vokay = true;
	}

	if($Mokay && $Bokay && $Vokay)
	{
		$rate = $database->getReference('Rates/Wedding_Package')->getChild('Wedding_Rate')->getValue();
		$discount = $database->getReference('Rates/MemberShip')->getChild('Discount')->getValue();
		$dis = ($rate * $discount)/100;
		$price = ($rate - $dis);
		if(empty($memberdiscount))
		{
			$data = [
			'Client_Name' => $Client_Name,
			'Client_Phone' => $Client_Phone,
			'Client_Email' => $Client_Email,
			'Mehndi' => $mehndiDate,
			'Mehndi_Time' => $mehndiTime,
			'Mehndi_Location' => $mehndilocation,
			'Barat' => $baratDate,
			'Barat_Time' => $mehndiTime,
			'Barat_Location' => $baratlocation,
			'Valima' => $valimaDate,
			'Valima_Time' => $mehndiTime,
			'Valima_Location' => $valimalocation,
			'Event_Cost' => 32000
			];

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
				$pushData = $database->getReference("Event_Request/Wedding_Package")->push($data);
				$SuccessMessage = "success";
			}				
		}
		elseif(strlen($memberdiscount) != 11)
		{
			$ErrorMessage = "Warning: Invalid Member key!";
		}
		else
		{
			$member = $database->getReference("Members")->getValue();
			$count = 0;
			foreach ($member as $key => $value) 
			{
				if($value['Client_Phone'] == $memberdiscount)
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
				$data = [
				'Client_Name' => $Client_Name,
				'Client_Phone' => $Client_Phone,
				'Client_Email' => $Client_Email,
				'Mehndi' => $mehndiDate,
				'Mehndi_Time' => $mehndiTime,
				'Mehndi_Location' => $mehndilocation,
				'Barat' => $baratDate,
				'Barat_Time' => $mehndiTime,
				'Barat_Location' => $baratlocation,
				'Valima' => $valimaDate,
				'Valima_Time' => $mehndiTime,
				'Valima_Location' => $valimalocation,
				'Event_Cost' => $price
				];

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
					$pushData = $database->getReference("Event_Request/Wedding_Package")->push($data);
					$SuccessMessage = "success";
				}
			}
			else
			{
				$ErrorMessage = "Warning: Member does not exist!";
			}
		}
	}
}

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
	<title>Wedding Packages</title>
	<link rel="stylesheet" type="text/css" href="getpackage.css">
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
			<h1 id="heading">WEDDING PACKAGE</h1>
			<form action="?" method="post">
				<?php
				include("db.php");
				?>
				<h1>Client Details</h1>
				<table>
					<tr>
						<td>Name:</td>
						<td><input name="Client_Name" type="text" autocomplete="off" required></td>
						<td>Phone #:</td>
						<td><input name="Client_Phone" type="number" autocomplete="off" required></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><input name="Client_Email" type="email" autocomplete="off" required></td>
						<td>Member Discount:<br>(for Members Only)</td>
						<td><input  name="memberdiscount" placeholder="Enter Phone Number" autocomplete="off" type="password"></td>
					</tr>
					</table>
					<h1>Event Details</h1>
					<table>
					<th>MEHNDI:</th>
					<tr>
						<td>Date:</td>
						<td><input name="mehndiDate" type="date" required></td>
						<td>Time:</td>
						<td><input name="mehndiTime" type="time" required></td>
						<td>Location:</td>
						<td><input onClick="My_Map(this.id)" class="mehndi" id="M_loc" name="mehndilocation" type="text" required></td>
					</tr>
					<th>BARAT:</th>
					<tr>
						<td>Date:</td>
						<td><input name="baratDate" type="date" required></td>
						<td>Time:</td>
						<td><input name="baratTime" type="time" required></td>
						<td>Location:</td>
						<td><input class="barat" id="B_loc" onClick="My_Map(this.id)" name="baratlocation" type="text" required></td>
					</tr>
					<th>VALIMA:</th>
					<tr>
						<td>Date:</td>
						<td><input name="valimaDate" type="date" required></td>
						<td>Time:</td>
						<td><input name="valimaTime" type="time" required></td>
						<td>Location:</td>
						<td><input class="valima" id="V_loc" onClick="My_Map(this.id)" name="valimalocation" type="text" required></td>
					</tr>
				</table>
				<div class="btn">
					<input class="getPackage" type="submit" name="submit" value="Book Event">
				</div>
				<div class="error">
					<span><?php echo($ErrorMessage);?></span>
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
</body>
</html>