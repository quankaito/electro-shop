<?php

namespace App\Livewire\Frontend\Product;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Attribute;
use Livewire\Component;
use Livewire\WithPagination;

class ProductList extends Component
{
    use WithPagination;

    public $categorySlug;

    // Các biến cho filter/search
    public $searchQuery        = '';
    public $brandSlugs         = [];
    public $categoryIds        = [];
    public $priceRange         = [
        'min' => null,
        'max' => null,
    ];
    public $selectedAttributes = []; // ['attribute_slug' => ['value1','value2']]

    // Biến cho sort
    public $sortBy = 'created_at';

    protected $queryString = [
        'searchQuery'        => ['except' => '', 'as' => 'q'],
        'brandSlugs'         => ['except' => [], 'as' => 'brands'],
        'priceRange'         => ['except' => ['min' => null, 'max' => null], 'as' => 'price'],
        'selectedAttributes' => ['except' => [], 'as' => 'attrs'],
        'sortBy'             => ['except' => 'created_at'],
        // 'categoryIds'      => ['except' => [], 'as' => 'cats'], // có thể bật nếu cần
    ];

    public function mount($categorySlug = null)
    {
        $this->categorySlug = $categorySlug;

        // Khởi tạo priceRange với min/max thực từ CSDL
        $priceAgg = Product::where('is_active', true)
                            ->selectRaw('MIN(regular_price) as min_price, MAX(regular_price) as max_price')
                            ->first();

        $this->priceRange['min'] = $priceAgg->min_price;
        $this->priceRange['max'] = $priceAgg->max_price;
    }

    // Reset page khi bất kỳ filter/sort nào thay đổi
    public function updatingSearchQuery()
    {
        $this->resetPage();
    }
    public function updatingBrandSlugs()
    {
        $this->resetPage();
    }
    public function updatingCategoryIds()
    {
        $this->resetPage();
    }
    public function updatingPriceRange()
    {
        $this->resetPage();
    }
    public function updatingSelectedAttributes()
    {
        $this->resetPage();
    }
    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function applyFilter()
    {
        // Trigger re-render + reset pagination
        $this->resetPage();
    }

    public function render()
    {
        $query = Product::where('is_active', true);

        // 1. Filter theo categorySlug và các con
        if ($this->categorySlug) {
            $category = Category::where('slug', $this->categorySlug)->first();
            if ($category) {
                $childIds    = $category->children()->pluck('id')->toArray();
                $categoryIds = array_merge([$category->id], $childIds);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // 2. Filter theo user chọn thêm category con
        if (!empty($this->categoryIds)) {
            $query->whereIn('category_id', $this->categoryIds);
        }

        // 3. Filter search
        if ($this->searchQuery) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchQuery.'%')
                  ->orWhere('sku', 'like', '%'.$this->searchQuery.'%')
                  ->orWhere('short_description', 'like', '%'.$this->searchQuery.'%');
            });
        }

        // 4. Filter thương hiệu
        if (!empty($this->brandSlugs)) {
            $query->whereHas('brand', function ($q) {
                $q->whereIn('slug', $this->brandSlugs);
            });
        }

        // 5. Filter khoảng giá
        if ($this->priceRange['min'] !== null) {
            $query->where('regular_price', '>=', $this->priceRange['min']);
        }
        if ($this->priceRange['max'] !== null) {
            $query->where('regular_price', '<=', $this->priceRange['max']);
        }

        // 6. Filter theo attribute (variants/options)
        if (!empty($this->selectedAttributes)) {
            foreach ($this->selectedAttributes as $attrSlug => $valueSlugs) {
                if (!empty($valueSlugs)) {
                    $query->whereHas('variants.options.attribute', function ($qA) use ($attrSlug) {
                        $qA->where('slug', $attrSlug);
                    })->whereHas('variants.options', function ($qV) use ($valueSlugs) {
                        $qV->whereIn('slug', $valueSlugs);
                    });
                }
            }
        }

        // 7. Sort (dấu '-' ở đầu => DESC)
        $sortField = $this->sortBy;
        $sortDir   = 'asc';
        if (substr($this->sortBy, 0, 1) === '-') {
            $sortField = substr($this->sortBy, 1);
            $sortDir   = 'desc';
        }
        $query = $query->orderBy($sortField, $sortDir);

        // 8. Paginate
        $products = $query->paginate(12);

        // Dữ liệu cho sidebar
        $categories = Category::where('is_active', true)
                              ->whereNull('parent_id')
                              ->with('children')
                              ->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $attributes = Attribute::with('values')->get();

        $priceAgg = Product::where('is_active', true)
                           ->selectRaw('MIN(regular_price) as min_price, MAX(regular_price) as max_price')
                           ->first();
        $priceRangeAll = [
            'min' => $priceAgg->min_price ?? 0,
            'max' => $priceAgg->max_price ?? 0,
        ];

        return view('livewire.frontend.product.product-list', [
            'products'            => $products,
            'categoriesForFilter' => $categories,
            'brandsForFilter'     => $brands,
            'attributesForFilter' => $attributes,
            'priceRangeAll'       => $priceRangeAll,
        ]);
    }
}
