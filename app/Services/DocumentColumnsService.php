<?php

namespace App\Services;

use Reflection;
use ReflectionClass;

class DocumentColumnsService
{
    // UNUSED
    const NUMERO_COLUMN =['value'=>1,'name'=>'NO','order'=>1,'width'=>5];
    const JEFATURA_COLUMN = ['value'=>2,'name'=>'Jefatura','order'=>2,'width'=>10];
    const GESTOR_COLUMN = ['value'=>3,'name'=>'GESTOR','order'=>3,'width'=>10];
    const SALDO_USD_COLUMN = ['value'=>9,'name'=>'Saldo $','order'=>9,'width'=>10];
    const SALDO_TOTAL_USD_COLUMN= ['value'=>10,'name'=>'Saldo total $','order'=>10,'width'=>10];
    const CARTERA_COLUMN = ['value'=>17,'name'=>'Cartera','order'=>17,'width'=>10];
    const TIPO_EMPRESA_COLUMN = ['value'=>18,'name'=>'Tipo de Empresa','order'=>18,'width'=>10];
    const BASE_LABORAL_COLUMN =['value'=>19,'name'=>'BASE LABORAL','order'=>19,'width'=>10];
    const BIENES_INMUEBLES_COLUMN = ['value'=>21,'name'=>'Bienes Imuebles','order'=>21,'width'=>10];
    const MONTO_ALTO_COLUMN = ['value'=>22,'name'=>'Monto Alto (>$8000)','order'=>22,'width'=>10];
    const CAMBIO_DIRECCIONES_COLUMN = ['value'=>23,'name'=>'Cambio de direcciones','order'=>23,'width'=>10];
    const CAMBIO_TELEFONOS_COLUMN = ['value'=>25,'name'=>'Cambio de Telefonos','order'=>25,'width'=>10];
    const HISTORICO_GESTIONES_COLUMN = ['value'=>27,'name'=>'Historico de Gestiones','order'=>27,'width'=>10];
    const GESTION_DIARIA_COLUMN = ['value'=>28,'name'=>'Gestión Diaria','order'=>28,'width'=>10];
    const FECHA_ULTIMA_GESTION_COLUMN = ['value'=>29,'name'=>'FECHA ULTIMA GESTION','order'=>29,'width'=>10];
    const SUBCARACTERIZACION_COLUMN = ['value'=>32,'name'=>'Sub caracterización','order'=>32,'width'=>10];
    const CLIENTE_CONTACTADO_MES_COLUMN = ['value'=>33,'name'=>'Cliente contactado mes','order'=>33,'width'=>10];
    const CARACTERIZACION_JUDICIAL_COLUMN = ['value'=>40,'name'=>'Caracterización Judicial','order'=>40,'width'=>10];
    const FECHA_ULTIMO_MOVIMIENTO_JUDICIAL_COLUMN = ['value'=>41,'name'=>'Fecha último movimiento judicial','order'=>41,'width'=>10];
    // CLIENT / ACCOUNT
    const NUMERO_CLIENTE_COLUMN=['value'=>4,'name'=>'Cliente Unico','order'=>4,'width'=>10];
    const NUMERO_VASA_COLUMN= ['value'=>5,'name'=>'No. Vasa/ Ptmo','order'=>5,'width'=>10];
    const NOMBRE_COLUMN= ['value'=>6,'name'=>'Nombre Del Cliente','order'=>6,'width'=>30];
    const IDENTIDAD_COLUMN =['value'=>7,'name'=>'No. Identidad','order'=>7,'width'=>10];
    const SALDO_COLUMN = ['value'=>8,'name'=>'Saldo L.','order'=>8,'width'=>10];
    const ESTADO_COLUMN = ['value'=>11,'name'=>'Estado de Cartera','order'=>11,'width'=>10];
    const FECHA_ASIGNACION_COLUMN = ['value'=>12,'name'=>'Fecha de Asignación','order'=>12,'width'=>10];
    const PRODUCTO_COLUMN = ['value'=>13,'name'=>'Producto','order'=>13,'width'=>10];
    const SEGMENTACION_COLUMN =['value'=>14,'name'=>'Segementacion Producto','order'=>14,'width'=>10];
    const TIPO_PRODUCTO_COLUMN = ['value'=>15,'name'=>'Tipo de Producto','order'=>15,'width'=>10];
    const FECHA_SEPARACION_COLUMN = ['value'=>16,'name'=>'Fecha de separación','order'=>16,'width'=>10];
    const TRABAJO_COLUMN=['value'=>20,'name'=>'Lugar de Trabajo','order'=>20,'width'=>10];
    const CORREO_COLUMN= ['value'=>24,'name'=>'Correo Electrónico','order'=>24,'width'=>10];
    const CELULAR_COLUMN = ['value'=>26,'name'=>'Celular','order'=>26,'width'=>10];
    const CARACTERIZACION_COLUMN = ['value'=>30,'name'=>'Caracterizacion','order'=>30,'width'=>10];
    const CODIGO_COLUMN = ['value'=>31,'name'=>'Codigo Resultado de la Gestion','order'=>31,'width'=>10];
    // PAYMENT PROMISE
    const VALOR_PROMESA_COLUMN = ['value'=>34,'name'=>'Valor Promesa','order'=>34,'width'=>10];
    const FECHA_PROMESA_COLUMN = ['value'=>35,'name'=>'Fecha de Promesa','order'=>35,'width'=>10];
    // DEMAND
    const FECHA_SOLICITUD_DOCUMENTO_COLUMN = ['value'=>36,'name'=>'Fecha solicitud de Documento','order'=>36,'width'=>10];
    const FECHA_RECIBIDO_DOCUMENTO_COLUMN = ['value'=>37,'name'=>'Fecha de Recibo de Documento','order'=>37,'width'=>10];
    const FECHA_PRESENTACION_DEMANDA_COLUMN= ['value'=>38,'name'=>'Fecha de Presentación','order'=>38,'width'=>10];
    const TIPO_DEMANDA_COLUMN=['value'=>39,'name'=>'Tipo de Demanda','order'=>39,'width'=>10];
    const CANTIDAD_RETENIDA_COLUMN =['value'=>42,'name'=>'Cantidad retenida mensual','order'=>42,'width'=>10];
    const APARTIR_DE_COLUMN = ['value'=>43,'name'=>'A partir de:','order'=>43,'width'=>10];
    const JUZGADO_COLUMN = ['value'=>44,'name'=>'Juzgado','order'=>44,'width'=>10];
    const EXPEDIENTE_COLUMN = ['value'=>45,'name'=>'No. Expediente','order'=>45,'width'=>10];
    const JUEZ_COLUMN =['value'=>46,'name'=>'No. Juez','order'=>46,'width'=>10];
    CONST CIUDAD_DEMANDA_COLUMN = ['value'=>47,'name'=>'Ciudad de Presentacion Demanda','order'=>47,'width'=>10];

    public static function getAllColumnsByOrder(){
        $columns = new ReflectionClass(DocumentColumnsService::class);
        $columns = $columns->getConstants();
        usort($columns, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });
        return  $columns;
    }
}