<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\RateList;
use App\Model\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Model\Product;
use App\Model\Category;
use App\Model\WarehouseCategory;
use App\Model\WarehouseProduct;

class RateListController extends Controller
{
    public function __construct(
        private RateList $rate_list,
        private Product $product,
        private WarehouseCategory $warehouse_categories,
        private Category $category

    ){}

    /**
     * @param Request $request 
     * @return Application|Factory|View
     */
    function index(Request $request): View|Factory|Application
    {
        
        $query = $this->product;
        $authUser = auth('admin')->user();
        if ($authUser->admin_role_id == 3 || $authUser->admin_role_id == 5) {
            $assign_categories =  $this->warehouse_categories->where('warehouse_id', $authUser->warehouse_id)->pluck('category_id')->toArray();
            $query = $query->whereIn('category_id', $assign_categories);
        }

        $query_param = [];
        $search = $request['search'];
        if ($request->has('search') && $search) {
            $key = explode(' ', $request['search']);
            $query = $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%") 
                        ->orWhere('name', 'like', "%{$value}%")
                        ->orWhere('product_code', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }
        $products = $query->latest()->with('category')->paginate(Helpers::getPagination())->appends($query_param);
        $wh_assign_categories = $this->warehouse_categories->where('warehouse_id',$authUser->warehouse_id)->pluck('category_id');
        $categories = $this->category->whereIn('id',$wh_assign_categories)->get();
        $options = Helpers::getCategoryDropDown($categories);

         return view('admin-views.rate_list.index', compact('products','options', 'search'));
    }



    function get_product_by_cat(Request $request,$cat_id):  \Illuminate\Http\JsonResponse
    {
        $products= $this->product->where('category_id',$cat_id)->get();
        return response()->json([
            'success' => 1,
            'd_none_class' => 'd-none',
            'view' => view('admin-views.rate_list.product_details', compact('products'))->render(),
        ]);   

    }
    
    function create(Request $request): View|Factory|Application
    {
       return view('admin-views.unit.add');

    }


    /**
     * @param Request $request
     * @return Application|Factory|View
     */


   
 
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $key = explode(' ', $request['search']);
        $units = $this->unit->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('title', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.unit.unit', compact('units'))->render()
        ]);
    }

    /**
     * @return Factory|View|Application
     */
  
    /**
     * @param Request $request
     * @return RedirectResponse
     */
    function store(Request $request): RedirectResponse
    {

$array = $request->all();
        
   // Assuming there are two product IDs
$productIds = $array['product_id'];

// Process each product ID
foreach ($productIds as $productId) {
    // Initialize an array to store combined data
    $combinedData = [];

    // Process the first slot
    $firstSlotData = [];
    for ($i = 0; $i < count($array['1_slot']['quantity']); $i++) {
        $quantity = $array['1_slot']['quantity'][$i];
        $offerPrice = $array['1_slot']['offer_price'][$i];
        $marketPrice = $array['market_price'][$i];
        $margin = $array['1_slot']['margin'][$i];

        // Calculate discount percentage
        $discountPercentage = ($marketPrice - ($offerPrice/$quantity)) / $marketPrice * 100;

        $firstSlotData[] = [
            'quantity' => $quantity,
            'offer_price' => $offerPrice,
            'market_price' => $marketPrice,
            'margin' => $margin,
            'discount_percentage' => $discountPercentage,
        ];
    }

    // Process the second slot
    $secondSlotData = [];
    for ($i = 0; $i < count($array['2_slot']['quantity']); $i++) {
        $quantity = $array['2_slot']['quantity'][$i];
        $offerPrice = $array['2_slot']['offer_price'][$i];
        $marketPrice = $array['market_price'][$i];
        $margin = $array['2_slot']['margin'][$i];

        // Calculate discount percentage
        $discountPercentage = ($marketPrice - ($offerPrice/$quantity)) / $marketPrice * 100;

        $secondSlotData[] = [
            'quantity' => $quantity,
            'offer_price' => $offerPrice,
            'market_price' => $marketPrice,
            'margin' => $margin,
            'discount_percentage' => $discountPercentage,
        ];
    }

    // Process the third slot
    $thirdSlotData = [];
    for ($i = 0; $i < count($array['3_slot']['quantity']); $i++) {
        $quantity = $array['3_slot']['quantity'][$i];
        $offerPrice = $array['3_slot']['offer_price'][$i];
        $marketPrice = $array['market_price'][$i];
        $margin = $array['3_slot']['margin'][$i];

        // Calculate discount percentage
        $discountPercentage = ($marketPrice - ($offerPrice/$quantity)) / $marketPrice * 100;

        $thirdSlotData[] = [
            'quantity' => $quantity,
            'offer_price' => $offerPrice,
            'market_price' => $marketPrice,
            'margin' => $margin,
            'discount_percentage' => $discountPercentage,
        ];
    }

    // Combine the data for all slots
    $combinedData = array_merge($combinedData, $firstSlotData, $secondSlotData, $thirdSlotData);

    // Encode the combined data to JSON
    $jsonCombinedData = json_encode($combinedData, JSON_PRETTY_PRINT);
// dump($productId);
echo $jsonCombinedData;
    // Store the combined data in the database
    // WarehouseProduct::create([
    //     'product_id' => $productId,
    //     'product_details' => $jsonCombinedData,
    // ]);
}
        
         dd('stop');    

        $request->validate([
            'title' => 'required|unique:units',
            'description' => 'required',
        ]);

            if (strlen($request->title) > 10) {
                toastr::error(translate('Title is too long!'));
                return back();
            }
       
       


        //into db
        $unit = $this->unit;
        $unit->title = $request->title;
        $unit->description = $request->description;
        $unit->position = $request->position;
        $unit->save();
       

        Toastr::success(translate('unit Added Successfully!') );
        return redirect()->route('admin.unit.list');

    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $units = $this->unit->withoutGlobalScopes()->with('translations')->find($id);
        return view('admin-views.unit.edit', compact('units'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $unit = $this->unit->find($request->id);
        $unit->status = $request->status;
        $unit->save();
        Toastr::success(translate('Unit status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' =>  ['required',
                            Rule::unique('units')->ignore($id)
                        ],
       
            'description' => 'required',
     ]);

     
        if (strlen($request->title) > 10) {
            toastr::error(translate('Unit title is too long!'));
            return back();
        }

        $unit = $this->unit->find($id);
        $unit->title = $request->title;
        $unit->description = $request->description;
        $unit->save();
        
        Toastr::success( translate('Unit updated successfully!') );
        return redirect()->route('admin.unit.list');

    }

    /** 
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $unit = $this->unit->find($request->id);
       
        if ($unit) {
            $unit->delete();
            Toastr::success( translate('unit removed!')  );
        } else {
            Toastr::warning( translate('unit not removed!') );
        }
        return back();
    }
}
