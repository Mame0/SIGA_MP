<?php
	//require_once 'include/cabecera.php';
	session_start();
	if (isset( $_SESSION['iden_oper'] ) )
	{
		header('location: home.php');
	}
?>
<html>
<head>
	<link type="text/css" rel="stylesheet" href="css/login.css" />
		<title>MPFN - DF Arequipa</title>
</head>
<body><BR><BR><BR><BR>
<div class="container">
	<section id="content">
		<form action="login.php" method="post">
			<h1 >MPFN - Arequipa</h1>
			<div>
				<input type="text" placeholder="Usuario" required="" name="username" id="username" value=""/>
			</div>
			<div>
				<input type="password" placeholder="Password" required="" name="password" id="password" value=""/>
			</div>
			<div><center>
				<table border=0 width=100%><tr>
					<td width=53%></td>
					<td align=center>
						<input type="submit" value="Ingresar" />
					</td>
				</tr></table>
			</div>
		</form><!-- form -->
		<div class="button"><center>
			<a href="#">Manual de Usuario</a>
			<a href="#">Olvid&oacute; su Password?</a>
		</div><!-- button -->
	</section><!-- content -->
</div><!-- container -->
</body>
</html>
