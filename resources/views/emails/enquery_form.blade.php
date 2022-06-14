<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Johnpride</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Maven+Pro&display=swap" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tbody>
			<tr>
				<td>
					<table width="800" border="0" cellspacing="0" cellpadding="0" align="center" bgcolor="#ffffff" style="border: 1px solid #ddd;">
						<tr>
							<td>
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
									<tbody>
										<tr>
											<td style="padding: 20px 0px 20px 40px;">
												<a href="{{url('')}}"><img src="<?php echo url('images/logo.png'); ?>" alt="logo" width="186" height="31"></a>
											</td>
										</tr>
										<tr bgcolor="#A77736">
											<td align="left" style="color: #fff; font-size: 20px; padding: 20px 40px;">Enquiry-Form - <?php echo date('d M Y H:i A'); ?> </td>
											
										</tr>
										<tr>
											<td style="padding: 30px 0px 0 40px;">
												<span style="font-size: 24px; color: #3f4041; font-family: 'Roboto', sans-serif, Arial;">Hi Admin!</span>
												<p style="font-size: 16px; font-family: 'Roboto', sans-serif, Arial; color: #626365; line-height: 32px; margin-bottom: 0; margin-top: 10px;">
													Your have an new enquiry, details given below:
												</p>
											</td>
										</tr>

										<tr>
											<td style="padding: 10px 0px 10px 40px;">
												<table>
													<tr>
														<th style="text-align: left;vertical-align: top;">Name :&nbsp; </th> <td><?php echo $name; ?></td>
													</tr>

													<tr>
														<th style="text-align: left;vertical-align: top;">Phone :&nbsp; </th> <td><?php echo $phone; ?></td>
													</tr>

													<tr>
														<th style="text-align: left;vertical-align: top;">Alternate Phone :&nbsp; </th> <td><?php echo $alternate_phone; ?></td>
													</tr>

													<tr>
														<th style="text-align: left;vertical-align: top;">Email :&nbsp; </th> <td><?php echo $email; ?></td>
													</tr>

													<tr>
														<th style="text-align: left;vertical-align: top;">Profession :&nbsp; </th> <td><?php echo $profession; ?></td>
													</tr>
													
													<tr>
														<th style="text-align: left;vertical-align: top;">Existing Business :&nbsp; </th> <td><?php echo $business; ?></td>
													</tr>

													<tr>
														<th style="text-align: left;vertical-align: top;">City :&nbsp; </th> <td><?php echo $city; ?></td>
													</tr>

													<tr>
														<th style="text-align: left;vertical-align: top;">Location :&nbsp; </th> <td><?php echo $location; ?></td>
													</tr>

													<tr>
														<th style="text-align: left;vertical-align: top;">Market :&nbsp; </th> <td><?php echo $market; ?></td>
													</tr>

													<tr>
														<th style="text-align: left;vertical-align: top;">Location Address :&nbsp; </th> <td><?php echo $location_address; ?></td>
													</tr>

													<tr>
														<th style="text-align: left; vertical-align: top;">Size of the Store :&nbsp; </th> <td><?php echo $store_size; ?></td>
													</tr>
												</table>
											</td>
										</tr>

										<tr>
											<td style="padding: 10px 0px 10px 40px;">						
												<p style="color: #3a3a3c; font-size: 17px; font-weight: 400; font-family: Roboto; margin: 4px 0px;">Johnpride</p>
											</td>
										</tr>

										<tr bgcolor="#A77736">
											<td colspan="2" height="4"></td>
										</tr>
										<tr bgcolor="#ffffff">
											<td colspan="2" height="1"></td>
										</tr>
										<tr bgcolor="#3f4041">
											<td colspan="2" height="8"></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</body>
</html>