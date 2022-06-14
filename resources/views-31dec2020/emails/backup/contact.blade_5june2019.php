<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>Slumber Jill</title>
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
												<a href="#"><img src="<?php echo url('public/images/logo.png'); ?>" alt="logo" width="186" height="31"></a>
											</td>
										</tr>
										<tr bgcolor="#e41881">
											<td align="left" style="color: #fff; font-size: 20px; padding: 20px 40px;">Contact us enquiry on - <?php echo date('d M Y H:i A'); ?> </td>
											
										</tr>
										<tr>
											<td style="padding: 30px 0px 30px 40px;">
												<span style="font-size: 24px; color: #3f4041; font-family: 'Roboto', sans-serif, Arial;">Hi Admin!</span>
												<p style="font-size: 16px; font-family: 'Roboto', sans-serif, Arial; color: #626365; line-height: 32px;">
													Your have an new enquiry, details given below:
												</p>
											</td>
										</tr>

										<tr>
											<td style="padding: 30px 0px 30px 40px;">
												<table>
													<tr>
														<th>Name: </th> <td><?php echo $name; ?></td>
													</tr>
													<tr>
														<th>Email: </th> <td><?php echo $email; ?></td>
													</tr>
													<tr>
														<th>Phone: </th> <td><?php echo $phone; ?></td>
													</tr>
													<tr>
														<th>Subject: </th> <td><?php echo $subject; ?></td>
													</tr>
													<tr>
														<th>Message: </th> <td><?php echo $msg; ?></td>
													</tr>
												</table>
											</td>
										</tr>

										<tr>
											<td style="padding: 30px 0px 30px 40px;">						
												<p style="color: #3a3a3c; font-size: 17px; font-weight: 400; font-family: Roboto; margin: 4px 0px;">SlumberJill</p>
											</td>
										</tr>

										<tr bgcolor="#e41881">
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