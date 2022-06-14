<!-- User Profile Modal -->
  <div class="modal fade" id="profileModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Profile</h4>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
        </div>
      </div>
    </div>
  </div>

  <!-- End - User Profile Modal -->
<script type="text/javascript">
$(".view_user").click(function(){
        user_id = $(this).data('userid');
        //alert(user_id);
        _token = '{{csrf_token()}}';

        $.ajax({
                url: "{{ url('admin/customers') }}/ajax_profile",
                method: 'POST',
                data:{user_id},
                dataType:"JSON",
                headers:{'X-CSRF-TOKEN': _token},
                beforeSend:function(){},
                success: function(resp) {
                    if(resp.success){
                       $("#profileModal .modal-body").html(resp.view);
                       $("#profileModal").modal("show");
                    }
                    else {

                    }
                },
                error: function(resp) {

                }
            });
    });
</script>