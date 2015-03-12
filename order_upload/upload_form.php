<html>
	<body>

		<form action="upload_file.php" method="post" enctype="multipart/form-data">
		<label for="file">Filename:</label>
		<input type="file" name="file" id="file" />

		<br />
		Enter customer Email:
		<input type="text" name="email">
		<br />
		<br />
		Shipping Cost:
		<input type="text" name="shipping_cost">
		<br />
		<input type="submit" name="submit" value="Submit" />
		</form>
		Please make sure that your CSV is in this format:
		Please make sure that your CSV is in this format:
		<table border="1">
			<tr>
				<th>sku</th><th>quantity</th><th>price</th>
			</tr>
			<tr>
				<td>data</td><td>data</td><td>data<td>
			</tr>
		</table>
		The field names do not matter.<br />
		We currently have checks in the following places:<br />
		-If the sku does not exist in the system, the item is not included in the order and is displayed to the screen.<br />
		-Removal of $ signs from price<br />
	</body>
</html> 