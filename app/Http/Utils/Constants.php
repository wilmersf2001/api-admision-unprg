<?php

namespace App\Http\Utils;

class Constants
{
  // TIEMPO DE EXPIRACIÓN DEL TOKEN DE INSCRIPCIÓN EN MINUTOS
  public const TOKEN_EXPIRATION_MINUTES = 5;
  //RUTAS QR
  public const RUTA_FOTO_QR = 'temp/';
  public const RUTA_FOTO_ANTIGUA_QR = 'temp/qr-antiguo/';
  // CARPETAS DE ARCHIVOS
  public const CARPETA_ARCHIVOS_VALIDOS = 'archivos_validos/';
  public const CARPETA_ARCHIVOS_OBSERVADOS = 'archivos_observados/';
  public const CARPETA_ARCHIVOS_RECTIFICADOS = 'archivos_rectificados/';
  public const CARPETA_FOTO_CARNET = 'foto_carnet/';
  public const CARPETA_DNI_ANVERSO = 'dni_anverso/';
  public const CARPETA_DNI_REVERSO = 'dni_reverso/';
  // RUTAS DE ARCHIVOS
  public const RUTA_FOTO_CARNET_INSCRIPTO = 'archivos_inscripcion/foto_carnet/';
  public const RUTA_DNI_ANVERSO_INSCRIPTO = 'archivos_inscripcion/dni_anverso/';
  public const RUTA_DNI_REVERSO_INSCRIPTO = 'archivos_inscripcion/dni_reverso/';

  public const RUTAS_FOTOS_VALIDAS = [Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_FOTO_CARNET, Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_DNI_ANVERSO, Constants::CARPETA_ARCHIVOS_VALIDOS . Constants::CARPETA_DNI_REVERSO];
  public const RUTAS_FOTOS_OBSERVADAS = [Constants::CARPETA_ARCHIVOS_OBSERVADOS . Constants::CARPETA_FOTO_CARNET, Constants::CARPETA_ARCHIVOS_OBSERVADOS . Constants::CARPETA_DNI_ANVERSO, Constants::CARPETA_ARCHIVOS_OBSERVADOS . Constants::CARPETA_DNI_REVERSO];
  public const RUTAS_FOTOS_RECTIFICADAS = [Constants::CARPETA_ARCHIVOS_RECTIFICADOS . Constants::CARPETA_FOTO_CARNET, Constants::CARPETA_ARCHIVOS_RECTIFICADOS . Constants::CARPETA_DNI_ANVERSO, Constants::CARPETA_ARCHIVOS_RECTIFICADOS . Constants::CARPETA_DNI_REVERSO];

  //ESTADOS DE POSTULANTES
  public const ESTADO_INSCRITO = '1';
  public const ESTADO_OBSERVADO = '2';
  public const ESTADO_ENVIO_OBSERVADO = '3';
  public const ESTADO_VALIDO = '4';
  public const ESTADO_CARNET_IMPRESO_PENDIENTE = '5';
  public const ESTADO_HUELLA_DIGITAL = '6';
  public const ESTADO_CARNET_ENTREGADO = '7';
  public const ESTADO_INSCRIPCION_ANULADA = '8';
  public const ESTADO_FICHA_ENTREGADA = [Constants::ESTADO_HUELLA_DIGITAL, Constants::ESTADO_CARNET_ENTREGADO];
  //DISCO STORAGE
  public const DISK_STORAGE = 'public';
  //TIPOS DE ARCHIVOS
  public const ESTADOS_VALIDOS_POSTULANTE_ADMISION = [Constants::ESTADO_VALIDO, Constants::ESTADO_CARNET_IMPRESO_PENDIENTE, Constants::ESTADO_HUELLA_DIGITAL, Constants::ESTADO_CARNET_ENTREGADO];
  public const ESTADOS_OBSERVADOS_POSTULANTE = [Constants::ESTADO_OBSERVADO, Constants::ESTADO_ENVIO_OBSERVADO];
  public const ESTADOS_VALIDOS_POSTULANTE = [Constants::ESTADO_VALIDO, Constants::ESTADO_CARNET_IMPRESO_PENDIENTE];
  //CODIGOS DE CONCEPTO ADMISION
  public const NUMERO_CONCEPTO_ADMISION = ['345', '346', '997', '998', '999'];

  // ID OTROS COLEGIOS NACIONAL Y PARTICULAR
  public const ID_OTROS_COLEGIOS_NACIONAL = 15496;
  public const ID_OTROS_COLEGIOS_PARTICULAR = 15497;
  public const ID_PERU = 134;

  //ESTADOS TITULADO TRASLADO EXTERNO NAC. E INTERNAC.
  public const ESTADO_TITULADO_TRASLADO = ['3', '4'];

  //RUTAS DE ARCHIVOS BACKUP
  public const RUTA_FOTO_CARNET_VALIDA_BACKUP = 'backup_archivos_validos/foto_carnet/';
  public const RUTA_DNI_ANVERSO_VALIDA_BACKUP = 'backup_archivos_validos/dni_anverso/';
  public const RUTA_DNI_REVERSO_VALIDA_BACKUP = 'backup_archivos_validos/dni_reverso/';
}
