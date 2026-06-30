<?
	session_start();
	session_destroy();
	//header('Window-target: _parent');
	//header('location: index.php');
?>
<script>
	parent.location.href='index.php';
</script>
