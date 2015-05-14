<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script>
        $(function() {
            $("#datepicker").datepicker();
        });
    </script>
    <script src="sorttable.js"></script>
    <title>PHP Test</title>
    <link rel="stylesheet" type="text/css" href="mystyle.css">
</head>

<body>
	<script>
		function myFunction() {
			var name = document.getElementById("nameinput").value;
			if(name == ""){
				alert("You need to enter a name!");
			}
			else{
				document.getElementById("inserter").submit();
			}
		}
	</script>
		<form action="" method="POST">
        <table id="gamelist" class="sortable">
            <tr>
                <th>Name of Game</th>
                <th>Game Beaten</th>
                <th>Physical Copy Owned</th>
                <th>Release Date</th>
                <td class="sorttable_nosort">
                    <input class="button" type="submit" name="modify" value="Delete">
                </td>
				<td class="sorttable_nosort"><input class="button" type="submit" name="modify" value="Save"></td>
            </tr>
			<?php include 'prephp.php'; ?>
			</form>
            <tfoot>
				<tr>
                <form id="inserter" action="" method="POST">
					<td><input id="nameinput" type="text" name="Name"></td>
                    <td><input class="button" type="checkbox" name="Beaten" value="yes"></td>
					<td><input class="button" type="checkbox" name="Physical" value="yes"></td>
					<td><input type="text" name="Release" id="datepicker"></td>
					<td><input class="button" type="button" value="Submit" onclick="myFunction()"></td>
                </form>
			</tfoot>
		</table>

</body>

</html>