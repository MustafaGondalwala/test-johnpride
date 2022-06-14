@component('admin.layouts.main')

    @slot('title')
        Admin - Manage Blogs - {{ config('app.name') }}
    @endslot

    <?php $back_url=CustomHelper::BackUrl(); ?>
    <div class="row">

        <div class="col-md-12">

            <h2>Manage Blogs
                <a href="{{ route('admin.blogs.add').'?back_url='.$back_url }}" class="btn btn-sm btn-success pull-right"><i class="fa fa-plus"></i> Add Blog</a>
            </h2>

            @include('snippets.errors')
            @include('snippets.flash')

            <?php
            if(!empty($blogs) && count($blogs) > 0){
                ?>

                <div class="table-responsive">

                    <table class="table table-striped">
                        <tr>
                            <th>Title</th>
                            <th>Sub-title</th>  
                            <th>Description</th>
                            <th>Category</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php
                        foreach($blogs as $blog){
                            $content = CustomHelper::wordsLimit($blog->content,35);

                            $blog_category = $blog->Category;

                            $category_name = (isset($blog_category->name))?$blog_category->name:'';
                            ?>
                        
                            <tr>
                                <td><?php echo $blog->title; ?></td>
                                <td>{{ $blog->subtitle }}</td>
                                <td>{{ strip_tags($content) }}</td>
                                <td>{{ $category_name }}</td>
                                <td>{{ CustomHelper::getStatusStr($blog->featured) }}</td>
                                <td>{{ CustomHelper::getStatusStr($blog->status) }}</td>

                                <td>
                                    <a href="{{ route('admin.blogs.edit', $blog->id.'?back_url='.$back_url) }}" title="Edit Blog"><i class="fas fa-edit"></i></a>

                                    <a href="javascript:void(0)" class="sbmtDelForm"  id="{{$blog->id}}" title="Delete Blog"><i class="fas fa-trash-alt"></i></i></a>
                                
                                    <form method="POST" action="{{ route('admin.blogs.delete', $blog->id) }}" accept-charset="UTF-8" role="form" onsubmit="return confirm('Do you really want to remove this Banner?');" id="delete-form-{{$blog->id}}">
                                        {{ csrf_field() }}
                                        {{ method_field('POST') }}
                                        <input type="hidden" name="banner_id" value="<?php echo $blog->id; ?>">

                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
                 {{ $blogs->appends(request()->query())->links() }}

            
            <?php
        }
        else{
            ?>
            <div class="alert alert-warning">No blogs found.</div>
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