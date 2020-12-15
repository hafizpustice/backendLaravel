@extends('layouts.app')
@push('css')
<style>
    .add-product {
        float: right;
        margin-top: -43px;
    }

    .image-preview {
        border: 2px solid black;
        width: 25%;
        height: 206px;
        border-radius: 3px;
    }

    .image-preview img {
        width: 263px;
        height: 201px;
        border: 1px solid #fffeef border-radious:3px;
    }
</style>
@endpush
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"> Create Product</h3>
                </div>

                <form action="{{ route('admin.product.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Product title</label>
                            <input type="text" name="title" class="form-control" placeholder="Enter email">
                        </div>

                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea type="text" name="description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" name="price" class="form-control" placeholder="product price">
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" name="image" class="form-control" onclick="change_image()" id="image">
                        </div>
                        <div class="image-preview">
                            <img src="{{ asset('upload_image/a.png') }}" alt="preview Image"
                                id="image_preview_container">
                        </div>

                    </div>
                    <div class="card-footer">
                        <a href="{{ route('admin.product.index') }}" class="btn btn-warning">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function () {
      
    });
    function change_image(){
    console.log('preview funcrion start')
    $('#image').change(function(e){
    console.log("change "+e.target)
    let reader = new FileReader();
    
    reader.onload = (e) => {
    $('#image_preview_container').attr('src', e.target.result);
    }
    reader.readAsDataURL(this.files[0]);
    });
    }
</script>

@endpush