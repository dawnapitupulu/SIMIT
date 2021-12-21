<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Permintaan;
use App\Models\Asset;
use App\Models\Company;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PermintaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $permintaans = Permintaan::with(['categories', 'assets', 'divisions', 'companies'])->get();
        $categories = Category::all();
        $assets = Asset::all();
        $divisions = Division::all();
        $companies = Company::all();
        if($request->ajax()){
            return DataTables::of($permintaans)
            ->addIndexColumn()
            ->addColumn('action', function($data){
                $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="'.$data->id.'" class="btn btn-primary btn-sm editPermintaan"><i class="far fa-edit"></i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        return view('permintaan.index', compact('categories', 'assets', 'divisions', 'companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'username' => 'required',
            'quantity' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['status' => 400, 'errors' => $validator->errors()]);
        }
        else
        {
            $getBy  = Auth::user()->name;
            $getUtc = Carbon::now();
            $getDate = Carbon::parse($request->input('date'))->format('Y-m-d');

            $data = new Permintaan();
            $data -> date = $getDate;
            $data -> username = $request->input('username');
            $data -> division_id = $request->input('division_id');
            $data -> company_id = $request->input('company_id');
            $data -> category_id = $request->input('category_id');
            $data -> asset_id = $request->input('asset_id');
            $data -> quantity = $request->input('quantity');
            $data -> description = $request->input('description');
            $data -> createdBy = $getBy;
            $data -> createdUtc = $getUtc;
            $data -> save();

            return response()->json(['status' => 200, 'messages' => 'Data telah ditambahkan']);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permintaans = Permintaan::with(['categories', 'assets', 'divisions', 'companies'])->where('id', $id)->first();;
        $categories = Category::all();
        $assets = Asset::all();
        $divisions = Division::with('permintaans')->where('id', $permintaans->division_id)->first();
        $companies = Company::all();
        $getDate = Carbon::createFromFormat('Y-m-d', $permintaans->date)->format('m/d/Y');
        if($permintaans)
        {
            $html =
            '
            <div class="modal-body1">
                <div class="form-group">
                    <label name="date" class="col-sm-4 control-label"> Tanggal </label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="date" value="'.$getDate.'" aria-label="date" aria-describedby="basic-addon1">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="date"><i class="fa fa-calendar-alt" id="date"></i></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-body1">
                <div class="form-group">
                    <label name="username" class="col-sm-4 control-label"> User </label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="username" name="username" value="'.Auth::user()->name.'" readonly>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label name="division_id" class="col-sm-4 control-label"> Pilih Divisi </label>
                <select class="form-control" id="division_id" name="division_id">
                    <option value="'.$divisions->id.'">'.$divisions->division_name.'</option>
                </select>
            </div>
            ';

            return response()->json(['status' => 200, 'html'=> $html, 'permintaans' => $permintaans]);
        }
        else
        {
            return response()->json(['status' => 404, 'messages' => 'Tidak ada data ditemukan.']);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
