<div class="table-response" >
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
                            <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
</div>
