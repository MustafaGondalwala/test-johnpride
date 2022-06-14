@component('layouts.master')

    @slot('title')
        Contact Us - {{ config('app.name') }}
    @endslot

    <div class="row">

        <div class="col-md-6">

            <div class="checkout-box">

                <h2>Contact Us</h2>

                <p>If you would like to send us an email please fill out the form below.</p>

                <div id="success"></div>

                <form method="POST" id="form-contact" action="{{ route('page.send_contact') }}" accept-charset="UTF-8" role="form">
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="control-label required">Name:</label>

                                <input type="text" id="name" class="form-control" name="name" value="{{ old('name') }}" maxlength="100" required />

                                @include('snippets.errors_first', ['param' => 'name'])
                            </div>

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="control-label required">Email:</label>

                                <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" maxlength="100" required />

                                @include('snippets.errors_first', ['param' => 'email'])
                            </div>

                            <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                <label for="phone" class="control-label">Phone:</label>

                                <input type="text" id="phone" class="form-control" name="phone" value="{{ old('phone') }}" maxlength="20" />

                                @include('snippets.errors_first', ['param' => 'phone'])
                            </div>

                            <div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
                                <label for="message" class="control-label required">Message:</label>

                                <textarea class="form-control" name="message" maxlength="2000" required></textarea>

                                @include('snippets.errors_first', ['param' => 'message'])
                            </div>

                            <div class="form-group">
                                <button class="btn btn-success" type="submit"><i class="fa fa-envelope"></i> Send</button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>

        </div>

        <div class="col-md-6">

            <div class="checkout-box">

                <h2>Where Are We?</h2>

                <p class="address">
                    200 N Spring St<br />
                    Los Angeles, CA. 90012
                </p>

                <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=en&amp;q=200+N+Spring+St,+Los Angeles,+CA200 N Spring St Los Angeles, CA. 90012&amp;t=m&amp;hq=&amp;output=embed"></iframe>

                <h2>Or Call Us</h2>

                <button onclick="window.open('tel:7141234567');" class="btn btn-success"><i class="fa fa-phone"></i> <strong>(714) 123 - 4567</strong></button>

            </div>

        </div>

    </div>

    @slot('bottomBlock')
        <script>
            (function($) {

                $('#form-contact').submit(function(event) {
                    event.preventDefault();

                    var _token = $('input[name="_token"]').val(),
                        name = $('input[name="name"]').val(),
                        email = $('input[name="email"]').val(),
                        phone = $('input[name="phone"]').val(),
                        message = $('textarea[name="message"]').val();

                    $('input[type="submit"]').val('Sending...');

                    $.ajax({
                        url: "/contact",
                        type: "POST",
                        data: {
                            _token: _token,
                            name: name,
                            email: email,
                            phone: phone,
                            message: message
                        },
                        cache: false,
                        success: function() {
                            // Success message
                            $('#success').html("<div class='alert alert-success'>");
                            $('#success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                                .append("</button>");
                            $('#success > .alert-success')
                                .append("<strong>Your message has been sent. </strong>");
                            $('#success > .alert-success')
                                .append('</div>');

                            $('#form-contact').hide();
                        },
                        error: function() {
                            // Fail message
                            $('#success').html("<div class='alert alert-danger'>");
                            $('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
                                .append("</button>");
                            $('#success > .alert-danger').append("<strong>Sorry " + name + ", it seems that my mail server is not responding. Please try again later!");
                            $('#success > .alert-danger').append('</div>');

                            $('#form-contact').hide();
                        }
                    });
                });

            })(jQuery);
        </script>
    @endslot

@endcomponent