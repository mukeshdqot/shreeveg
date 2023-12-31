<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Unit;
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

class UnitController extends Controller
{
    public function __construct(
        private Unit $unit
    ){}

    /**
     * @param Request $request 
     * @return Application|Factory|View
     */
    function index(Request $request): View|Factory|Application
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $units = $this->unit->where(['position' => 0])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                    $q->orWhere('description', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $units = $this->unit->where(['position' => 0]);
        }
        $units = $units->latest()->paginate(Helpers::getPagination())->appends($query_param);
        return view('admin-views.unit.index', compact('units', 'search'));
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
