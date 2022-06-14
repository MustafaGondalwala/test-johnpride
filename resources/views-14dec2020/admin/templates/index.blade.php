@component('layouts.admin')

    @slot('title')
        Admin - Brands - {{ config('app.name') }}
    @endslot

   
  <div class="row">
    <div class="col-md-12">
      <div class="titlehead">
        <h1 class="pull-left">{{ $title }}</h1>
       
      </div>
    </div>
  </div>         
            
    <div class="row">

        <div class="col-md-12">

            <?php
            show_flash_msg();
            ?>

            <div class="table-responsive">
                    <?php if(!empty($res)) { ?>
                    <table class="table table-striped table-bordered table-hover">
                        <tr>
                           
                            <th class="text-center1">S. No.</th>
                            <th class="text-center1">Name</th>
                            <th class="text-center1">Description</th>
                            <th class="text-center1">Short Codes</th>
                            <th class="text-center1">Type</th>
                            <th class="text-center1">Action</th>
                        </tr>

                       
                         <?php 
                        $i=1; 
                        ?>
                        @foreach ($res as $rec)
                        <?php
                        //prd($rec);
                        ?>
                       
                        <tr>
                           
                            <td class=""><?php echo $i; ?></td>
                            <td class=""><?php echo $rec->name ; ?></td>
                            <td class=""><?php echo $rec->description ; ?></td>
                            <td class=""><?php echo $rec->shortcodes  ; ?></td>
                            <td class=""><?php echo ucfirst($rec->type) ; ?></td>
                           
                            <td class=""> 
                            <a title="Edit" href="{{url('admin/templates/edit/'.$rec->id)}}" class="btn btn-sm btn-primary">Edit</i></a>
                            </td>
                        </tr>
                         <?php 
                         $i++;
                         ?>
                         @endforeach



                    </table>
                    <?php } ?>
            

        </div>
            <hr />


        </div>

    </div>

@endcomponent