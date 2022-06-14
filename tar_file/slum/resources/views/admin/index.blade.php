@component('admin.layouts.main')

    @slot('title')
        Admin Panel - {{ config('app.name') }}
    @endslot


<!-- weekly_orders -->

    <div class="row">
        <div class="col-md-12">
            <h1>Dashboard</h1>
        </div>
    </div>

    <div class="row"></div>

@endcomponent