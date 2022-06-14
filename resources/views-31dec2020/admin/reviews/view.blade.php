@component('admin.layouts.main')

@slot('title')
Admin - Manage Products - {{ config('app.name') }}
@endslot

<?php
$BackUrl = CustomHelper::BackUrl();
?>

<div class="row">
    <div class="col-md-12">
        <div class="titlehead">
            <h1 class="pull-left">Review</h1>

        </div>
    </div>
</div>




<div class="row">

    <div class="col-md-12">

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <?php
                if(!empty($review) && count($review) > 0){
                    $reviewUser = $review->reviewUser;
                    $reviewUserName = (isset($reviewUser->name))?$reviewUser->name:'';

                    $reviewProduct = $review->reviewProduct;
                    $reviewProductName = (isset($reviewProduct->name))?$reviewProduct->name:'';

                    $reviewDate = CustomHelper::DateFormat($review->created_at, 'd M Y');

                    $comments = CustomHelper::wordsLimit($review->comment, 30);
                    ?>
                    <tr>
                        <th class="text-center">Customer Name</th>
                        <td align="center">{{ $reviewUserName }}</td>
                    </tr>

                    <tr>
                        <th class="text-center">Product Name</th>
                        <td align="center">{{ $reviewProductName }}</td>
                    </tr>

                    <tr>
                        <th class="text-center">Heading</th>
                        <td align="center">{{$review->heading}}</td>
                    </tr>
                    
                    <tr>
                        <th class="text-center">Comment</th>
                        <td align="center">{{$review->comment}}</td>
                    </tr>
                    
                    <tr>
                        <th class="text-center">Rating</th>
                        <td align="center">{{$review->rating}}</td>
                    </tr>
                    
                    <tr>
                        <th class="text-center">Status</th>
                        <td align="center"><?php echo CustomHelper::getStatusHTML($review->status, '', '', '', '', 'Approved', 'Pending'); ?></td>
                    </tr>
                    
                    <tr>
                        <th class="text-center">Date</th>
                        <td align="center">{{$reviewDate}}</td>
                    </tr>
                    <?php
                }
                ?>

            </table>
        </div>


    </div>

</div>


@slot('bottomBlock')

@endslot



@endcomponent