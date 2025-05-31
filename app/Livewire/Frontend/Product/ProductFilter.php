<?php

namespace App\Livewire\Frontend\Product;

use Livewire\Component;
use App\Models\Category;
use App\Models\Brand;     // Giả sử bạn có model Brand
use Illuminate\Support\Facades\DB;

class ProductFilter extends Component
{
    // Các thuộc tính để binding với form (nếu muốn lọc động)
    public $selectedSubcategories = []; // mảng chứa id các danh mục con được chọn
    public $minPrice;
    public $maxPrice;
    public $selectedBrands = [];        // mảng chứa id các thương hiệu được chọn

    // Để truyền dữ liệu ra view
    public $subcategoryList;
    public $brandList;
    public $priceRange; // ['min' => ..., 'max' => ...]

    public function mount($currentCategoryId = null)
    {
        /**
         * Nếu bạn đang trong trang category.blade (xem sản phẩm theo category),
         * bạn có thể lấy danh sách $subcategoryList = $category->children
         * Còn trong trang all products, bạn có thể lấy tất cả các category gốc hoặc tùy ý.
         */

        if ($currentCategoryId) {
            // Lấy danh sách danh mục con của category hiện tại
            $this->subcategoryList = Category::where('parent_id', $currentCategoryId)->get();
        } else {
            // Ở trang all products, ví dụ lấy tất cả danh mục gốc
            $this->subcategoryList = Category::whereNull('parent_id')->get();
        }

        // Giả sử bạn có bảng brands
        $this->brandList = Brand::orderBy('name')->get();

        // Lấy khoảng giá: giả sử cột "price" ở bảng products
        $priceAgg = DB::table('products')->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();
        $this->priceRange = [
            'min' => $priceAgg->min_price ?? 0,
            'max' => $priceAgg->max_price ?? 0,
        ];

        // Khởi tạo giá trị mặc định (nếu cần)
        $this->minPrice = $this->priceRange['min'];
        $this->maxPrice = $this->priceRange['max'];
    }

    public function render()
    {
        return view('livewire.frontend.product.product-filter');
    }

    /**
     * Bạn có thể thêm các phương thức để “reset” filter, hoặc
     * event emit để component ProductList bắt được filter mới
     * Ví dụ khi người dùng click “Áp dụng” thì emit event:
     */
    public function applyFilters()
    {
        $this->emit('filterUpdated', [
            'subcategories' => $this->selectedSubcategories,
            'brands'        => $this->selectedBrands,
            'price_min'     => $this->minPrice,
            'price_max'     => $this->maxPrice,
        ]);
    }

    public function resetFilters()
    {
        $this->selectedSubcategories = [];
        $this->selectedBrands = [];
        $this->minPrice = $this->priceRange['min'];
        $this->maxPrice = $this->priceRange['max'];
        $this->emit('filterUpdated', [
            'subcategories' => [],
            'brands'        => [],
            'price_min'     => $this->minPrice,
            'price_max'     => $this->maxPrice,
        ]);
    }
}
