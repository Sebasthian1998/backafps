<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Jobs\JobVerificarCuentaAfiliado;
use App\Models\Prueba;
use stdClass;
use Illuminate\Http\Request;

class PrometeoController extends Controller
{
    const PROMETEO = '2p3L88kuHAz7z7juTDW5A32rHj5iOIDs9j05nXrk0S7Y1Z1H0gJURmQFIOGMctVM';
    const URL_PRUEBA = 'https://account-validation.sandbox.prometeoapi.com/validate-account/';
    const PROMETEO_PRE = 'jYBcoiXwQDWqgPgNLFO6YNDqkBfwDuKfD4kbPo8bRdsxybeSU4ebSF9CGKarn5Px';
    const URL_PRE = 'https://account-validation.prometeoapi.net/validate-account/';
    const BANK_CODES = "002|003|009|011"; //002 BCP, 003 IBK, 009 SCOT, 011 BBVA 


    public function validateData($datosAfiliado,$dataBanco)
    {
        $response = true;
        $name = strtoupper($datosAfiliado['name']);
        $last_name = strtoupper($datosAfiliado['last_name']);
        $second_last_name = strtoupper($datosAfiliado['second_last_name']);
        $dataBanco = strtoupper($dataBanco);
        if (strstr($dataBanco, $name) !== false && strstr($dataBanco, $last_name) !== false && strstr($dataBanco, $second_last_name) !== false) {
            $response = true;
        } else {
            $response = false;
        }
        return $response;
    }

    public function LlamarCola(){
        // $job = new JobVerificarCuentaAfiliado();
        // JobVerificarCuentaAfiliado::dispatch()->onConnection('prometeo_queue')->onQueue('prometeo_queue'); //->delay(now()->addMinutes(1));
        JobVerificarCuentaAfiliado::dispatch()->onConnection('redis'); 
        // JobVerificarCuentaAfiliado::dispatch(); 
        $dataGuardar = ['name' => 'BD', 'cuenta' => 'BD', 'mensaje' => 'BD', 'bank_code' => 'BD', 'cod_response' => 'BD'];
        $this->guardarData($dataGuardar);
        return 'Job Ejecutado';
    }

    public function guardarData($data)
    {
      $kit = new Prueba();
      $kit->name = $data['name'];
      $kit->cuenta = $data['cuenta'];
      $kit->mensaje = $data['mensaje'];
      $kit->bank_code = $data['bank_code'];
      $kit->cod_response = $data['cod_response'];
      $kit->save();
    }

    public function SolicitudPrometeo()
    {   //Validar afiliado sea titular, la cuenta no sea mancomunada, la cuenta este en soles 
        $dataEnviada = [
            'cuenta' => '19170059764036', //2053239163940, 00320501323916394029 bcp cci rod 00219110479728403951
            'bank_code' => '002'
        ];
        $datosAfiliado = [
            'name'=>'sebasthian',
            'last_name'=>'ampuero',
            'second_last_name'=>'cossio'
        ];
        
        $cuenta = $dataEnviada['cuenta'];
        $bank_code =  $dataEnviada['bank_code'];
        $object = new stdClass();
        $object->success=false;
        $status=200;
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', self::URL_PRE, [
                'form_params' => [
                    'account_number' => $cuenta,
                    'bank_code' => $bank_code,
                    'country_code' => 'PE'
                ],
                'headers' => [
                    'X-API-Key' => self::PROMETEO_PRE,
                    'accept' => 'application/json',
                    'content-type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $respuesta = $response->getBody()->getContents();

            if($response->getStatusCode() == 200){
                $jsonData = json_decode($respuesta, true);  
                $data = $jsonData['data'];
                var_dump($data);
                if($data['account_currency'] !== 'PEN'){
                    $object->not_available=true;
                    $object->message = "El tipo de moneda de esta cuenta no es Soles";
                    $object->code = $status;
                    return response()->json($object, $status);
                }
                
                $responseNombre = $this->validateData($datosAfiliado,$data['beneficiary_name']);
                if(!$responseNombre){
                    $object->not_available=true;
                    $object->message = "Esta cuenta no pertenece exclusivamente al cliente.";
                    $object->code = $status;
                    return response()->json($object, $status);
                }
                $object->success=true;
                $object->data = $data;
                return response()->json($object, $status);
            }else{
                $object->not_available=true;
                $object->message = "El servicio se encuentra en validación, por favor intente mas tarde";
                $statusCode = $response->getResponse()->getStatusCode();
                $object->code = $statusCode;
                return response()->json($object, $statusCode);
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            Log::info("Error en la consulta a Prometeo: ".json_encode($e->getMessage()));
            $object->not_available=true;
            $object->message = "No se encuentran activos los servicios en este momento, por favor intente mas tarde";
            $object->code = $statusCode;
            return response()->json($object, $statusCode);

            // Manejar el error de cliente (4xx)

            return $statusCode;
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            Log::info("Error en la consulta a Prometeo: ".json_encode($e->getMessage()));
            $object->not_available=true;
            $object->message = "No se encuentran activos los servicios en este momento, por favor intente mas tarde";
            $object->code = $statusCode;
            return response()->json($object, $statusCode);

            // Manejar el error del servidor (5xx)

            return $statusCode;
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            Log::info("Error en la consulta a Prometeo: ".json_encode($e->getMessage()));
            $object->not_available=true;
            $object->message = "No se encuentran activos los servicios en este momento, por favor intente mas tarde";
            return response()->json($object, $status);
        }
    }
}

/*
{ 19104797284039
  "data": {
    "valid": true,
    "message": "Cuenta valida",
    "account_number": "19170059764036",
    "bank_code": "002",
    "country_code": "PE",
    "branch_code": null,
    "document_type": null,
    "document_number": null,
    "beneficiary_name": "SACO MEDINA LUIS ALFREDO",
    "account_currency": "PEN",
    "account_type": "SAVINGS"
  },
  "errors": null
}

{
  "data": {
    "valid": true,
    "message": "Cuenta valida",
    "account_number": "19290650946010",
    "bank_code": "002",
    "country_code": "PE",
    "branch_code": null,
    "document_type": null,
    "document_number": null,
    "beneficiary_name": "AMPUERO COSSIO SEBASTHIAN ANTONIO",
    "account_currency": "PEN",
    "account_type": "SAVINGS"
  },
  "errors": null
}


select * from table(zeus.zonaprivada_datos_personales('00', '01195965'))


IBK
{
  "data": {
    "valid": true,
    "message": "Cuenta valida",
    "account_number": "8983212878023",
    "bank_code": "003",
    "country_code": "PE",
    "branch_code": null,
    "document_type": "DNI",
    "document_number": "72864101",
    "beneficiary_name": "VILLANCA ROSALES ANDREA CLAUDIA",
    "account_currency": "USD",
    "account_type": "SAVINGS"
  },
  "errors": null
}

BCP

{
  "data": {
    "valid": true,
    "message": "Cuenta valida",
    "account_number": "1931101888080",
    "bank_code": "002",
    "country_code": "PE",
    "branch_code": null,
    "document_type": null,
    "document_number": null,
    "beneficiary_name": "LIGA PERUANA DE LUCHA CONTRA EL CANCER",
    "account_currency": "PEN",
    "account_type": "CHECKING"
  },
  "errors": null
}

BBVA
{
  "data": {
    "valid": true,
    "message": "Cuenta valida",
    "account_number": "001101790100082130",
    "bank_code": "011",
    "country_code": "PE",
    "branch_code": null,
    "document_type": null,
    "document_number": null,
    "beneficiary_name": "CONSULTORA LOMBARDIA SAC",
    "account_currency": "PEN",
    "account_type": null
  },
  "errors": null
}

SCOT
{
  "data": {
    "valid": true,
    "message": "Cuenta valida",
    "account_number": "0000323759",
    "bank_code": "009",
    "country_code": "PE",
    "branch_code": null,
    "document_type": "RUC",
    "document_number": "20601904901",
    "beneficiary_name": "JUGUETE PENDIENTE",
    "account_currency": "PEN",
    "account_type": "CHECKING"
  },
  "errors": null
}
*/
