@component('admin.layouts.main')

    @slot('title')
        Admin - CMS Pages - {{ config('app.name') }}
    @endslot
    <div class="row">

        <div class="col-md-12">

            <h1>CMS Pages ({{ count($pages) }})
                <a href="{{ route('admin.cms.add') }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add CMS</a>
            </h1>

            @include('snippets.errors')
            @include('snippets.flash')

            <?php
            if(!empty($pages) && count($pages) > 0){
                ?>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">

                        <tr>
                            <th class="text-center">Title</th>
                            <th class="text-center">Heading</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Date Created</th>
                            <th class="text-center">Action</th>
                        </tr>

                        
                        <?php
                        foreach($pages as $page){
                        ?>
                            <tr>
                                <td>{{ $page->title }}</td>
                                <td>{{ $page->heading }}</td>
                                <td>{{ CustomHelper::getStatusStr($page->status) }}</td>
                                <td>{{ CustomHelper::DateFormat($page->created_at, 'd/m/Y') }}</td>

                                <td class="text-center">
                                    <a href="{{ route('admin.cms.edit', $page->id) }}" class=""><i class="fas fa-edit"></i> </a>

                                    <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$page->id}}" title="Delete Page"><i class="fas fa-trash-alt"></i></i></a>
                                
                                    <form method="POST" action="{{ route('admin.cms.delete', $page->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Page?');" id="delete-form-{{$page->id}}">
                                        {{ csrf_field() }}
                                        {{ method_field('POST') }}
                                        <input type="hidden" name="page_id" value="<?php echo $page->id; ?>">

                                    </form>
                                </td>

                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                </div>
           <?php
       }else{
        ?>
        <div class="alert alert-warning">There are no CMS Pages at the present.</div>
        <?php

       }
           ?>

        </div>

    </div>

@endcomponent

    <script type="text/javaScript">
        $('.sbmtDelForm').click(function(){
            var id = $(this).attr('id');
            $("#delete-form-"+id).submit();
        });
    </script>
