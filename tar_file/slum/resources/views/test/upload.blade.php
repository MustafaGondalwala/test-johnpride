<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')

<section class="fullwidth accountpage">	
	<div class="container">

		<h2>My Profile  </h2>
		@include('common.customer_left_nav')

			
		<div class="sccountsec">
		<div class="row">
			<h1>Upload</h1>
			<form action="" method="post" enctype="multipart/form-data" >
			{{ csrf_field() }}
				<div class="col-sm-3">
					<input type="file" name="image">
				</div>
				<div class="col-sm-3">
					<input type="submit" name="upload" value="Upload">
				</div>
			</form>
		</div>
			

		</div> 
	</div>
</section>

@include('common.footer')


 
</body>
</html>