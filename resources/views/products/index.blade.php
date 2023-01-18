@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{ route('product.search') }}" method="post" class="card-header">
            @csrf
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" id="title" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="variant" class="form-control">
                        <option value="" selected>Select a variant</option>
                        @foreach ($variants as $variant)
                            <optgroup label="{{ $variant->title }}">
                                @foreach ($variant->product_variants as $productVariant)
                                    <option value="{{ $productVariant->variant }}">{{ $productVariant->variant }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach


                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" id="price_from" name="price_from" aria-label="First name" placeholder="From"
                            class="form-control">
                        <input type="text" id="price_to" name="price_to" aria-label="Last name" placeholder="To"
                            class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" id="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-primary float-right" onclick="search()"><i
                            class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body" >
            <div class="table-response" id="productList">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Title</th>
                            <th width="35%">Description</th>
                            <th widht="30%">Variant</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $product->title }} <br> Created at :
                                    {{ date('d-M-Y', strtotime($product->created_at)) }}</td>
                                <td>{{ $product->description }}</td>
                                <td>
                                    @foreach ($product->product_variant_prices as $variant_price)
                                        <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                            <dt class="col-sm-4 pb-0">
                                                <pre>{{ ucfirst($variant_price->variant_one->variant) }} {{ $variant_price->variant_two ? '/ ' . ucfirst($variant_price->variant_two->variant) : '' }} {{ $variant_price->variant_three ? '/ ' . ucfirst($variant_price->variant_three->variant) : '' }}</pre>
                                            </dt>
                                            <dd class="col-sm-8">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-5 pb-0">Price :
                                                        {{ number_format($variant_price->price, 2) }}</dt>
                                                    <dd class="col-sm-7 pb-0">InStock :
                                                        {{ number_format($variant_price->stock, 2) }}</dd>
                                                </dl>
                                            </dd>
                                        </dl>
                                    @endforeach

                                    <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show
                                        more</button>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div id="searchResult"></div>
        </div>

        <div class="card-footer" id="productPagination">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ ($products->currentpage() - 1) * $products->perpage() + 1 }} to
                        {{ $products->currentpage() * $products->perpage() }} out of {{ $products->total() }}</p>
                </div>
                <div class="col-md-2">
                    {{ $products->links() }}
                </div>
            </div>
        </div>


    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="<script src=" https://cdnjs.cloudflare.com/ajax/libs/axios/1.2.3/axios.min.js"></script>
    <script>
        function search() {
            const title = $("#title").val();
            const variant = $('#variant').find(":selected").val();
            const price_from = $("#price_from").val();
            const price_to = $("#price_to").val();
            const date = $("#date").val();
            const _token = `{{ csrf_token() }}`

            axios.post("{{route('product.search')}}", {
                    title: title,
                    variant: variant,
                    price_from: price_from,
                    price_to: price_to,
                    date: date,
                    _token: _token,
                })
                .then((response)=>{
                    console.log(response);
                    $('#productList').remove()
                    $('#productPagination').remove()
                    $("#searchResult").html(response.data);
                })
                .catch(function(error) {
                    console.log(error);
                });
        }
    </script>
@endpush
