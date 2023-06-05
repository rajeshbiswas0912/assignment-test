<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Data;
use Illuminate\Support\Facades\File;

class FormController extends Controller
{
    public function index()
    {
        $data = $this->getDataFromFile();
        return response()->json($data);
    }

    public function store(Request $request) {

        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|max:2048',
            'name' => 'required',
            'address' => 'required',
            'gender' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $this->getDataFromFile();

            // Generate an incremental ID
            $id = count($data) > 0 ? count($data) + 1 : 1;

            // Move the image file into folder
            $imageName = '';
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads'), $imageName);
            }

            //Store the data
            $data = $this->getDataFromFile();
            $newData = new Data();
            $newData->id = $id;
            $newData->name = $request->name;
            $newData->address = $request->address;
            $newData->gender = $request->gender;
            $newData->image = $imageName;
            $data[] = $newData;
            $this->storeDataToFile($data);
            return response()->json($newData, 201);

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong, please try again.']);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|max:2048',
            'name' => 'required',
            'address' => 'required',
            'gender' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {

            $data = $this->getDataFromFile();
            $index = $this->findDataIndex($data, $request->id);

            // Move the image file into folder
            $imageName = '';
            if ($request->hasFile('photo')) {
                $image = $request->file('photo');
                $imageName = time().'.'.$image->getClientOriginalExtension();
                $image->move(public_path('uploads'), $imageName);
            }

            if ($index !== false) {
                $data[$index]->name = $request->name;
                $data[$index]->address = $request->address;
                $data[$index]->gender = $request->gender;
                $data[$index]->image = $imageName;
                $this->storeDataToFile($data);
                return response()->json($data[$index]);
            }
            return response()->json(['error' => 'Data not found'], 404);

        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong, please try again.'], 520);
        }
    }

    public function destroy(Request $request)
    {
        $data = $this->getDataFromFile();
        $index = $this->findDataIndex($data, $request->id);
        if ($index !== false) {
            $deletedData = $data[$index];
            array_splice($data, $index, 1);
            $this->storeDataToFile($data);
            return response()->json($deletedData);
        }
        return response()->json(['error' => 'Data not found'], 404);
    }

    private function getDataFromFile()
    {
        $filePath = storage_path('app/data.json');
        if (File::exists($filePath)) {
            $data = json_decode(File::get($filePath));
            return $data;
        }
        return [];
    }

    private function storeDataToFile($data)
    {
        $filePath = storage_path('app/data.json');
        File::put($filePath, json_encode($data));
    }

    private function findDataIndex($data, $id)
    {
        foreach ($data as $index => $item) {
            if ($item->id == $id) {
                return $index;
            }
        }
        return false;
    }

    public function get_data(Request $request) {

        $data = $this->getDataFromFile();

        // Find the data entry with the specified ID
        $entry = collect($data)->firstWhere('id', $request->id);

        if ($entry) {
            return response()->json($entry);
        }
        return response()->json(['error' => 'Data not found'], 404);
    }

    public function sort_data(Request $request) {
       $field_name = $request->field_name;
        $sort_by = $request->sort_by;
        $data = $this->getDataFromFile();

        if ($field_name === 'name' && $sort_by === 'asc') {
            $sortedData = collect($data)->sortBy('name')->values()->all();
        } else if ($field_name === 'name' && $sort_by === 'desc') {
            $sortedData = collect($data)->sortByDesc('name')->values()->all();
        } else if ($field_name === 'id' && $sort_by === 'desc') {
            $sortedData = collect($data)->sortByDesc('id')->values()->all();
        } else if ($field_name === 'id' && $sort_by === 'asc') {
            $sortedData = collect($data)->sortBy('id')->values()->all();
        }

        return response()->json($sortedData);
    }
}
