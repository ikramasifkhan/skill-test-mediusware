@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Product</h1>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="form-group">
                        <label for="">Product Name</label>
                        <input type="text" name="title" value="{{ $product->title }}" placeholder="Product Name"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Product SKU</label>
                        <input type="text" name="sku" value="{{ $product->sku }}" placeholder="Product Name"
                            class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Description</label>
                        <textarea name="description" id="" cols="30" rows="4" class="form-control">{{ $product->description }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                </div>
                <div class="card-body border">
                    <form method="post" action="{{route('product.file.edit')}}" enctype="multipart/form-data" class="dropzone" id="dropzone">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Variants</h6>
                </div>
                <div class="card-body">

                    @foreach ($variantInfo as $info)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">

                                    <label for="">Option</label>
                                    <select class="form-control">
                                        <option value="">{{ $info['variant']['title'] }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label v-if="product_variant.length != 1"
                                        @click="product_variant.splice(index,1); checkVariant"
                                        class="float-right text-primary" style="cursor: pointer;">Remove</label>
                                    <select class="form-control" multiple="multiple" id="product_variant_{{$info['variant']['id']}}" onchange="changeVariant({{$info['variant']['id']}})">
                                        @foreach ($info['product_variants'] as $productVariant)
                                            <option value="{{ $productVariant->id }}" selected>
                                                {{ $productVariant->variant }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="card-header text-uppercase">Preview</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>Variant</td>
                                    <td>Price</td>
                                    <td>Stock</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($productVariantPrices as $productVariantPrice)
                                    <tr>
                                        <td>{{ $productVariantPrice->variant_one->variant }} /
                                            {{ $productVariantPrice->variant_two->variant }} /
                                            {{ $productVariantPrice->variant_three->variant }}</td>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $productVariantPrice->price }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $productVariantPrice->stock }}">
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js"></script>
    <script type="text/javascript">
        Dropzone.options.dropzone =
         {
            maxFilesize: 12,
            renameFile: function(file) {
                var dt = new Date();
                var time = dt.getTime();
               return time+file.name;
            },
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            timeout: 5000,
            success: function(file, response)
            {
                console.log(response);
            },
            error: function(file, response)
            {
               return false;
            }
        };
    </script>
    <script>
        $(document).ready(function() {
        //    const $productVariants = $("#product_variant").find(":selected").val();
        //    console.log($productVariants);
        });

        function changeVariant(id){
            const productVariants = $(`#product_variant_${id}`).find(":selected").val();

            const variant = [];
            for (let index = 0; index < productVariants.length; index++) {
                variant.push(productVariants[index]);

            }
            // $productVariants.each(($productVariant) => {
            //     variant.push($productVariant)
            // });

            console.log(variant);
        }
    </script>
@endpush
