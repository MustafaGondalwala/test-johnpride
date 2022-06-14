<!DOCTYPE html>
<html>
<head>

@include('common.head')

</head>
<body>

@include('common.header')
<section class="fullwidth innerheading">
	<div class="container">
		 <h1 class="heading">My Wallets</h1> 
	</div>
</section>
<section class="fullwidth accountpage">	
	<div class="container">
 
		@include('snippets.errors')
			@include('snippets.flash')
		@include('common.customer_left_nav')

		

		<div class="sccountsec">

        <div class="row">

        <form method="get" name="wallet_search_form" id="wallet_search_form" >

        <div class="col-md-4 form-group">
           <input type="text" class="form-control" name="from_date" id="from_date" placeholder="From Date" value="{{$fromdate}}"  >
        </div>

        <div class="col-md-4 form-group">
           <input type="text" class="form-control" name="to_date" id="to_date" placeholder="To Date" value="{{$todate}}" >
        </div>

         <div class="col-md-4 form-group">
           <input type="submit" class="btn form-control" name="search" id="" value="Search">
        </div>


        </form>

        </div>
		
		<div class="row">
        <div class="col-md-12">
        <p><strong>Available Balance:</strong> Rs. {{$total_balance}} </p>
		</div>
		</div>

		<?php
        if(!empty($res) && $res->count() > 0){
            ?>
            
            <div class="table-responsive">

                {{ $res->appends(request()->query())->links() }}

                <table class="table headth table-hover">
                    <tr>
                        <th>Credit Amount</th>
                        <th>Debit Amount</th>
                        <th>Description</th>
                        <th>Added on</th>
                    </tr>
                    <?php
                   
                    foreach($res as $rec)
                    {
                     
                      
                        ?>
                        <tr> 
                            <td>{{$rec->credit_amount}}</td>
                            <td>{{$rec->debit_amount}}</td>
                            <td>{{$rec->description}}</td>

                            <td> <?php $added_on = CustomHelper::DateFormat($rec->created, 'd F y'); ?>{{$added_on}}</td> 
                            
                        </tr>
                        <?php
                    }
                    ?>
                </table>

                {{ $res->appends(request()->query())->links() }}
            </div>
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">There are no records at the present.</div>
            <?php
        }
        ?>
		
			

		</div> 
	</div>
</section>
@include('common.footer')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
  

    $( function() 
    {
        $( "#from_date" ).datepicker({
            'dateFormat':'dd/mm/yy'
        });

        $( "#to_date" ).datepicker({
            'dateFormat':'dd/mm/yy'
        });


    });
    </script>
</body>
</html>