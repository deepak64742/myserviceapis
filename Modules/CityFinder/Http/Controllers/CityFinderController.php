<?php

namespace Modules\CityFinder\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Modules\CityFinder\Entities\City;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CityFinderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                '*.name' => ['required'],
                '*.params.query' => ['required'],
                '*.params.language' => ['required'],
                '*.params.locationType' => ['required'],
            ]);
        
            if($validator->fails()){
                return response()->json([
                    'status' => '002',
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ]);
            }

            $name = $request->get(0)['name'];
            $params['language'] = $request->get(0)['params']['language'];
            $params['locationType'] = $request->get(0)['params']['locationType'];
            $params['query'] = $request->get(0)['params']['query'];
        
            $cities = DB::table('cities')->join('states', 'cities.state_id', '=', 'states.id');
            $cities->join('countries', 'cities.country_id', '=', 'countries.id');
        
            $cities->selectRaw('cities.id as id');
            $cities->selectRaw('cities.name as city');
            $cities->selectRaw('states.name as adminDistrict');
            $cities->selectRaw('countries.name as country');
            $cities->selectRaw('cities.latitude as latitude');
            $cities->selectRaw('cities.longitude as longitude');
        
            $order = [];
            $query = trim($params['query']);
        
            foreach(explode(' ', $query) as $q){
                $extQry[] = "(CONCAT(' ', cities.name, ' ', states.name, ' ', countries.name, ' ') like '%$q%')";
                $extQry[] = "(CONCAT(' ', cities.name, ' ', states.name, ' ', countries.name, ' ') like '%$q %')";
                $extQry[] = "(CONCAT(' ', cities.name, ' ', states.name, ' ', countries.name, ' ') like '% $q%')";
                
                $order[] = implode(' + ', $extQry);
            }
        
            $order = implode(' + ', $order);
        
            $cities->orderByRaw("($order) DESC");
            $cities = $cities->paginate(10)->toArray()['data'];

            $location = array(
                'address' => [],
                'city' => [],
                'adminDistrict' => [],
                'country' => [],
                'latitude' => [],
                'longitude' => []
            );

            foreach($cities as $row){
                $location['address'][] = "$row->city, $row->adminDistrict, $row->country";
                $location['city'][] = $row->city;
                $location['adminDistrict'][] = $row->adminDistrict;
                $location['country'][] = $row->country;
                $location['latitude'][] = $row->latitude;
                $location['longitude'][] = $row->longitude;
            }

            $values = [];
            foreach($params as $key => $value){ $values[] = "$key:$value"; }
            $values = implode(';', $values);
        
            return response()->json([
                'dal' => [
                    $name => [
                        $values => [
                            'loading' => false,
                            'loaded' => true,
                            'data' => [
                                'location' => $location
                            ],
                            'status' => 200,
                            'statusText' => 'OK'
                        ]
                    ]
                ]
            ]);

            return $cities->toArray()['data'];

        }catch(\Exception $e){
            return response()->json([
                'status' => '002',
                'message' => $e->getMessage(),
            ]);
        }
    }

}
