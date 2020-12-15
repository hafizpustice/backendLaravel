@extends('layouts.app')
@push('css')
<style>
    .add-product {
        float: right;
        margin-top: -43px;
    }

    img {
        width: 70px;
        height: 70px;
        border-radius: 50%;
    }
</style>
@endpush
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if(Session::has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Product</strong> {{Session::get('message') }}.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Product List</h3>
                    <div class="add-product">
                        <a href="{{ route('admin.product.create') }}" class="btn btn-success">Add Product</a>
                    </div>

                </div>

                <!-- /.card-header -->
                <div class="card-body table-responsive">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="35%">Title</th>
                                <th width="10%">price</th>
                                <th width="30%">Image</th>
                                <th width="20%">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($products as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->title }} </td>
                                <td>{{ $item->price }}</td>
                                <td>
                                    <img src="{{ asset($item->image) }}" alt="Image">
                                </td>

                                <td>
                                    <a href="{{ route('admin.product.edit',$item->id) }}" data-id="{{ $item->id }}">
                                        Edit
                                    </a>
                                    |
                                    <a href="javascript:void(0)" id="delete-user" data-id="{{ $item->id }}">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script>
    $(document).ready(function() {
    
        $('body').on('click', '#delete-user', function() {
            confirm("Are you sure delete this product?");
            console.log('fuunfsa')
            var product_id = $(this).data("id");
            $.ajax({
                type: "DELETE",
                url: " {{ url('admin/product') }}" + '/' + product_id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    console.log(data)
                    window.location.assign("product?message=deleted Successfully.")
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        });
    });
</script>

@endpush