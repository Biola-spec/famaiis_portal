<div>
    {{-- Scoped Styles for modern shop --}}
    <style>
        .sp-search-wrap { position: relative; max-width: 600px; }
        .sp-search-wrap input { border-radius: 25px; padding-left: 44px; border: 2px solid #e8e8e8; font-size: 14px; height: 44px; transition: border-color 0.2s; }
        .sp-search-wrap input:focus { border-color: #764ba2; box-shadow: 0 0 0 3px rgba(118,75,162,0.12); }
        .sp-search-wrap .sp-search-icon { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #999; font-size: 16px; }

        .sp-cat-pills { display: flex; gap: 8px; flex-wrap: wrap; }
        .sp-cat-pill { display: inline-flex; align-items: center; gap: 5px; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer; border: 2px solid #e8e8e8; background: #fff; color: #555; transition: all 0.2s; text-decoration: none; }
        .sp-cat-pill:hover { border-color: #764ba2; color: #764ba2; }
        .sp-cat-pill.active { background: #764ba2; color: #fff; border-color: #764ba2; }

        .sp-card { background: #fff; border-radius: 12px; overflow: hidden; transition: all 0.25s ease; border: 1px solid #eee; position: relative; }
        .sp-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.12); transform: translateY(-4px); }
        .sp-card-img-wrap { position: relative; overflow: hidden; background: #f8f8f8; }
        .sp-card-img-wrap img { width: 100%; height: 200px; object-fit: cover; transition: transform 0.4s ease; }
        .sp-card:hover .sp-card-img-wrap img { transform: scale(1.08); }
        .sp-card-img-placeholder { width: 100%; height: 200px; display: flex; align-items: center; justify-content: center; background: #f8f8f8; color: #ddd; font-size: 48px; }
        .sp-badge-new { position: absolute; top: 10px; left: 10px; background: #ff6b35; color: #fff; font-size: 10px; font-weight: 700; padding: 3px 10px; border-radius: 3px; text-transform: uppercase; letter-spacing: 0.5px; z-index: 2; }
        .sp-badge-cat { position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.6); color: #fff; font-size: 10px; font-weight: 600; padding: 3px 10px; border-radius: 3px; z-index: 2; }
        .sp-quick-add { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(118,75,162,0.92); color: #fff; text-align: center; padding: 10px; font-weight: 700; font-size: 13px; cursor: pointer; transform: translateY(100%); transition: transform 0.25s ease; z-index: 2; }
        .sp-card:hover .sp-quick-add { transform: translateY(0); }
        .sp-quick-add:hover { background: rgba(118,75,162,1); }
        .sp-quick-add.disabled { background: rgba(180,180,180,0.9); cursor: not-allowed; }
        .sp-card-body { padding: 14px; }
        .sp-card-title { font-size: 14px; font-weight: 600; color: #333; margin-bottom: 4px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .sp-card-title a { color: inherit; text-decoration: none; }
        .sp-card-title a:hover { color: #764ba2; }
        .sp-card-desc { font-size: 12px; color: #999; margin-bottom: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sp-card-price { font-size: 20px; font-weight: 800; color: #764ba2; margin-bottom: 6px; }
        .sp-stock-bar { height: 4px; border-radius: 2px; background: #eee; overflow: hidden; margin-top: 8px; }
        .sp-stock-bar-fill { height: 100%; border-radius: 2px; transition: width 0.3s; }
        .sp-stock-text { font-size: 11px; margin-top: 4px; }
        .sp-stock-in { color: #2e7d32; }
        .sp-stock-low { color: #e65100; }
        .sp-stock-out { color: #c62828; }

        .sp-sort-select { border: 2px solid #e8e8e8; border-radius: 8px; padding: 8px 14px; font-size: 13px; background: #fff; cursor: pointer; min-width: 180px; }
        .sp-sort-select:focus { border-color: #764ba2; outline: none; }

        .sp-result-count { font-size: 13px; color: #888; }
        .sp-result-count strong { color: #333; }

        .sp-empty-wrap { text-align: center; padding: 60px 20px; }
        .sp-empty-wrap .sp-empty-icon { font-size: 80px; opacity: 0.2; margin-bottom: 16px; }

        .sp-filter-chip { display: inline-flex; align-items: center; gap: 4px; padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: 600; cursor: pointer; border: 1px solid #e0e0e0; background: #fff; transition: all 0.2s; }
        .sp-filter-chip:hover { border-color: #764ba2; }
        .sp-filter-chip.active { background: #f3e5f5; border-color: #764ba2; color: #764ba2; }
    </style>

    {{-- Search Bar --}}
    <div class="sp-search-wrap mx-auto mb-3">
        <i class="ti-search sp-search-icon"></i>
        <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Search for products, brands, categories...">
    </div>

    {{-- Category Pills --}}
    <div class="sp-cat-pills mb-3 justify-content-center">
        <button wire:click="$set('category', '')" class="sp-cat-pill {{ $category === '' ? 'active' : '' }}">
            <i class="ti-layout-grid3-alt"></i> All
        </button>
        @foreach($categories as $cat)
            <button wire:click="$set('category', '{{ $cat }}')" class="sp-cat-pill {{ $category === $cat ? 'active' : '' }}">
                {{ $cat }}
            </button>
        @endforeach
    </div>

    {{-- Toolbar: Filters + Sort + Count --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2" style="padding: 0 4px;">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- In Stock Filter --}}
            <button wire:click="$toggle('inStockOnly')" class="sp-filter-chip {{ $inStockOnly ? 'active' : '' }}">
                <i class="fa {{ $inStockOnly ? 'fa-check-circle' : 'fa-circle-o' }}"></i>
                In Stock Only
            </button>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="sp-result-count">
                <strong>{{ $totalProducts }}</strong> product{{ $totalProducts !== 1 ? 's' : '' }} available
            </span>
            <select wire:model.live="sortBy" class="sp-sort-select">
                <option value="newest">Newest First</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
                <option value="name">Name: A-Z</option>
            </select>
        </div>
    </div>

    {{-- Product Grid --}}
    <div class="row" style="row-gap: 16px;">
        @forelse($products as $product)
            @php
                $isNew = $product->created_at && $product->created_at->diffInDays(now()) <= 14;
                $stockPct = $product->stock_quantity > 0 ? min(100, ($product->stock_quantity / 50) * 100) : 0;
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="sp-card">
                    {{-- Image --}}
                    <div class="sp-card-img-wrap">
                        <a href="{{ route('shop.show', $product->id) }}">
                            @if($product->image)
                                <img src="{{ url('storage/'.$product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="sp-card-img-placeholder"><i class="ti-image"></i></div>
                            @endif
                        </a>
                        @if($isNew)
                            <span class="sp-badge-new">New</span>
                        @endif
                        @if($product->category)
                            <span class="sp-badge-cat">{{ Str::limit($product->category, 15) }}</span>
                        @endif
                        {{-- Quick Add Overlay --}}
                        @if($product->stock_quantity > 0)
                            <div class="sp-quick-add" wire:click="addToCart({{ $product->id }})">
                                <i class="ti-shopping-cart mr-1"></i> Add to Cart
                            </div>
                        @else
                            <div class="sp-quick-add disabled">Out of Stock</div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="sp-card-body">
                        <div class="sp-card-title">
                            <a href="{{ route('shop.show', $product->id) }}">{{ $product->name }}</a>
                        </div>
                        @if($product->description)
                            <div class="sp-card-desc">{{ $product->description }}</div>
                        @endif
                        <div class="sp-card-price">₦{{ number_format($product->price, 2) }}</div>

                        {{-- Stock Bar --}}
                        @if($product->stock_quantity > 10)
                            <div class="sp-stock-text sp-stock-in"><i class="fa fa-circle mr-1" style="font-size:7px;vertical-align:middle"></i>{{ $product->stock_quantity }} in stock</div>
                            <div class="sp-stock-bar"><div class="sp-stock-bar-fill" style="width:{{ $stockPct }}%;background:#4caf50"></div></div>
                        @elseif($product->stock_quantity > 0)
                            <div class="sp-stock-text sp-stock-low"><i class="fa fa-circle mr-1" style="font-size:7px;vertical-align:middle"></i>Only {{ $product->stock_quantity }} left!</div>
                            <div class="sp-stock-bar"><div class="sp-stock-bar-fill" style="width:{{ $stockPct }}%;background:#ff9800"></div></div>
                        @else
                            <div class="sp-stock-text sp-stock-out"><i class="fa fa-times-circle mr-1"></i>Out of stock</div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="sp-empty-wrap">
                    <div class="sp-empty-icon">🛒</div>
                    <h4 style="font-weight: 700; color: #333; margin-bottom: 6px;">No products found</h4>
                    <p style="color: #999; font-size: 14px;">Try adjusting your search or filters to find what you're looking for.</p>
                    @if($search || $category || $inStockOnly)
                        <button wire:click="$set('search', ''); $set('category', ''); $set('inStockOnly', false);" class="btn btn-outline-primary btn-rounded mt-2">
                            <i class="ti-reload mr-1"></i> Clear All Filters
                        </button>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="d-flex justify-content-center mt-4">
        <nav>
            <ul class="pagination" style="gap: 4px;">
                {{-- Previous --}}
                @if($products->onFirstPage())
                    <li class="page-item disabled"><span class="page-link" style="border-radius:8px;border:1px solid #eee;color:#ccc;">&lsaquo;</span></li>
                @else
                    <li class="page-item"><a wire:click="gotoPage({{ $products->currentPage() - 1 }})" class="page-link" style="border-radius:8px;border:1px solid #eee;color:#764ba2;cursor:pointer;">&lsaquo;</a></li>
                @endif

                {{-- Page Numbers --}}
                @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                    @if($page == $products->currentPage())
                        <li class="page-item active"><span class="page-link" style="border-radius:8px;background:#764ba2;border-color:#764ba2;font-weight:700;">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a wire:click="gotoPage({{ $page }})" class="page-link" style="border-radius:8px;border:1px solid #eee;color:#555;cursor:pointer;">{{ $page }}</a></li>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($products->hasMorePages())
                    <li class="page-item"><a wire:click="gotoPage({{ $products->currentPage() + 1 }})" class="page-link" style="border-radius:8px;border:1px solid #eee;color:#764ba2;cursor:pointer;">&rsaquo;</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link" style="border-radius:8px;border:1px solid #eee;color:#ccc;">&rsaquo;</span></li>
                @endif
            </ul>
        </nav>
    </div>
    @endif
</div>
