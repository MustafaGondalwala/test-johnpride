@component('admin.layouts.main')

    @slot('title')
        Admin - CMS Pages - {{ config('app.name') }}
    @endslot
    <div class="row">

        <div class="col-md-12">

            <h1>CMS Pages ({{ count($pages) }})</h1>

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